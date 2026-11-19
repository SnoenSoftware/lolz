<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@gmail.com>
 * @copyright BRBcoffee 2020
 */

namespace App\Command;


use App\Entity\Feed;
use App\Model\Parser\DevHumorParser;
use App\Model\Parser\GenericParser;
use App\Model\Parser\RedditParser;
use App\Model\Parser\XkcdParser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(name: 'feed:install')]
class InstallKnownFeeds extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    protected $knownFeeds = [
        [
            'url' => 'https://xkcd.com/atom.xml',
            'parser' => XkcdParser::class
        ],
        [
            'url' => 'https://www.reddit.com/r/ProgrammerHumor/.rss',
            'parser' => RedditParser::class
        ],
        [
            'url' => 'http://devhumor.com/feed',
            'parser' => DevHumorParser::class
        ],
        [
            'url' => 'https://thecodinglove.com/feed',
            'parser' => GenericParser::class
        ],
        [
            'url' => 'https://www.smbc-comics.com/comic/rss',
            'parser' => GenericParser::class
        ],
        [
            'url' => 'https://www.commitstrip.com/en/feed/',
            'parser' => GenericParser::class
        ],
    ];

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->knownFeeds as $knownFeed) {
            $feed = new Feed();
            $feed->setParser($knownFeed['parser'])->setUrl($knownFeed['url']);
            $this->entityManager->persist($feed);
        }
        $io = new SymfonyStyle($input, $output);
        try {
            $this->entityManager->flush();
            $io->success(":)");
        } catch (Throwable $t) {
            // Probably already installed, whatever
            $io->warning("Couldn't install feeds, probably already installed");
        }
        return 0;
    }
}
