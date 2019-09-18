<?php

namespace App\Command;

use App\Entity\Feed;
use App\Model\Parser\GenericParser;
use App\Model\ParserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class FeedAddCommand extends Command
{
    protected static $defaultName = 'feed:add';
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ParserRepository
     */
    private $parserRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ParserRepository $parserRepository
    ) {
        parent::__construct(self::$defaultName);
        $this->entityManager = $entityManager;
        $this->parserRepository = $parserRepository;
    }

    protected function configure()
    {
        $this->setDescription('Add a feed');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $url = $io->ask("What is the feed url?");

        $validUrl = filter_var($url, FILTER_VALIDATE_URL);

        if (!$validUrl) {
            $io->error("Invalid url {$url}");
            return;
        }

        $question = new Question("Which parser should we use?", GenericParser::class);
        $question->setAutocompleterValues($this->parserRepository->getParsers());
        $parser = $io->askQuestion($question);

        $feed = new Feed();
        $feed->setParser($parser)->setUrl($validUrl);

        $this->entityManager->persist($feed);
        $this->entityManager->flush();

        $io->success('Added feed');
    }
}
