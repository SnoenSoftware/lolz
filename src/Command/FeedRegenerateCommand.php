<?php

namespace App\Command;

use App\Entity\Feed;
use App\Model\ParserRepository;
use App\Repository\FeedRepository;
use App\Repository\LolRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FeedRegenerateCommand extends Command
{
    protected static $defaultName = 'feed:regenerate';
    /**
     * @var ParserRepository
     */
    private $parserRepository;
    /**
     * @var FeedRepository
     */
    private $feedRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var PromiseInterface[]
     */
    private $promises;
    /**
     * @var LolRepository
     */
    private $lolRepository;

    public function __construct(
        ParserRepository $parserRepository,
        FeedRepository $feedRepository,
        EntityManagerInterface $entityManager,
        LolRepository $lolRepository
    ) {
        parent::__construct(self::$defaultName);
        $this->parserRepository = $parserRepository;
        $this->feedRepository = $feedRepository;
        $this->entityManager = $entityManager;
        $this->lolRepository = $lolRepository;
    }

    protected function configure()
    {
        $this->setDescription('Get all feeds and dump');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $feeds = $this->feedRepository->findAll();
        foreach ($feeds as $feed) {
            $this->fetch(
                $feed,
                function (ResponseInterface $result) use ($feed) {
                    $parser = $this->parserRepository->getParserForFeed($feed, $result);
                    while ($lol = $parser->next()) {
                        if (!$this->lolRepository->alreadyExists($lol)) {
                            $this->entityManager->persist($lol);
                        }
                    }
                }
            );
        }

        foreach ($this->promises as $promise) {
            $promise->wait();
        }
        $this->entityManager->flush();
    }

    protected function fetch(Feed $feed, \Closure $onFetched)
    {
        $promise = $this->getClient()->requestAsync(
            'GET',
            $feed->getUrl()
        );

        $promise->then(
            $onFetched,
            function () {
            }
        );
        $this->promises[] = $promise;
    }

    protected function getClient(): ClientInterface
    {
        if (!isset($this->client)) {
            $this->client = new Client();
        }
        return $this->client;
    }
}
