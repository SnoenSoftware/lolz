<?php

use App\Command\FeedRegenerateCommand;
use App\Entity\Feed;
use App\Entity\Lol;
use App\Kernel;
use App\Model\Parser\GenericParser;
use App\Model\ParserRepository;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpFoundation\Request;

if (ini_get('zend.assertions') !== '1' || !getenv('LOLZ_CRUNZ_MARKER')) {
    throw new RuntimeException('Run this fixture through the isolated runtime test.');
}

require dirname(__DIR__).'/bootstrap.php';

$kernel = new Kernel('prod', false);
$application = new Application($kernel);
foreach (['doctrine:database:create', 'doctrine:migrations:migrate'] as $name) {
    $tester = new CommandTester($application->find($name));
    assert($tester->execute([], ['interactive' => false]) === 0, $tester->getDisplay());
}
$manager = $kernel->getContainer()->get('doctrine')->getManager();
$connection = $manager->getConnection();

// Preserve the serialized array format already stored by the pre-upgrade application.
$sources = [['src' => 'https://example.com/clip.mp4', 'type' => 'video/mp4']];
$connection->insert('lol', [
    'url' => 'https://example.com/existing',
    'image_url' => 'https://example.com/existing.png',
    'fetched' => '2026-11-19 12:00:00',
    'video_sources' => serialize($sources),
]);
$existing = $manager->getRepository(Lol::class)->findOneBy(['url' => 'https://example.com/existing']);
assert($existing->getVideoSources() === $sources);

$feed = (new Feed())->setUrl('https://example.com/feed')->setParser(GenericParser::class);
$manager->persist($feed);
$manager->flush();
$rss = '<rss version="2.0"><channel><title>Test</title><link>https://example.com</link><description>Test</description>'
    . '<item><title>Clip</title><link>https://example.com/new</link><description><![CDATA['
    . '<video><source src="https://example.com/clip.mp4" type="video/mp4"></video>'
    . ']]></description></item></channel></rss>';
$command = new class (new ParserRepository(), $manager->getRepository(Feed::class), $manager, $manager->getRepository(Lol::class)) extends FeedRegenerateCommand {
    public ClientInterface $testClient;

    protected function getClient(): ClientInterface
    {
        return $this->testClient;
    }
};
$command->testClient = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200, [], $rss)]))]);
$tester = new CommandTester($command);
assert($tester->execute([]) === 0, $tester->getDisplay());
$manager->clear();
$fetched = $manager->getRepository(Lol::class)->findOneBy(['url' => 'https://example.com/new']);
assert($fetched !== null);
assert($fetched->getVideoSources() === $sources);
assert($connection->fetchOne('SELECT video_sources FROM lol WHERE url = ?', ['https://example.com/new']) === serialize($sources));

foreach (['/', '/api/more/page/0', '/api/imgur?url=https://example.com/image.png'] as $url) {
    $request = Request::create($url);
    $response = $kernel->handle($request);
    assert($response->getStatusCode() === 200, $response->getContent());
    if ($url === '/api/more/page/0') {
        $items = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        assert(count($items) === 2);
        assert($items[0]['videoSources'] === $sources);
    }
    $kernel->terminate($request, $response);
}
$request = Request::create('/graphql', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    'query' => '{lolz(page:0,pageSize:10){url imageUrl fetched videoSources}}',
]));
$response = $kernel->handle($request);
assert($response->getStatusCode() === 200, $response->getContent());
$result = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
assert(!isset($result[0]['errors']), $response->getContent());
assert(count($result[0]['data']['lolz']) === 2);
$kernel->terminate($request, $response);
$kernel->shutdown();

$development = new Kernel('dev', true);
$development->boot();
$development->shutdown();
echo "Runtime smoke checks passed.\n";
