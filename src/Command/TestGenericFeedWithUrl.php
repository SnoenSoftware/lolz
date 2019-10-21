<?php

namespace App\Command;

use App\Entity\Feed;
use App\Model\Parser\GenericParser;
use App\Model\ParserRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestGenericFeedWithUrl extends Command
{
    protected static $defaultName = 'feed:test';

    protected function configure()
    {
        $this->setDescription('Test a feed with the generic parser');
        $this->addArgument('url', InputArgument::REQUIRED, 'the feed url to test');
        $this->setAliases(['test']);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $feed = $input->getArgument('url');
        $client = new Client();
        $response = $client->get($feed);
        $parser = new GenericParser($response);

        while ($lol = $parser->next()) {
            print_r($lol);
        }
    }
}
