<?php

namespace App\Tests\Model\Parser;

use App\Model\Parser\DevHumorParser;
use App\Model\Parser\GenericParser;
use App\Model\Parser\RedditParser;
use App\Model\Parser\XkcdParser;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EndOfFeedTest extends TestCase
{
    #[DataProvider('feeds')]
    public function testEndOfFeedIsWarningFree(string $parserClass, int $entryCount): void
    {
        $atom = in_array($parserClass, [XkcdParser::class, RedditParser::class], true);
        $xml = $atom
            ? '<feed xmlns="http://www.w3.org/2005/Atom">'
            : '<rss version="2.0"><channel><title>Test</title><link>https://example.com</link><description>Test</description>';
        for ($i = 0; $i < $entryCount; $i++) {
            $url = "https://example.com/$i";
            $image = "$url.png";
            if ($atom) {
                $xml .= "<entry><title>Comic $i</title><link href=\"$url\"/>"
                    . "<summary type=\"html\">&lt;img src=\"$image\" title=\"Caption $i\"/&gt;</summary>"
                    . "<content type=\"html\">&lt;a href=\"$image\"&gt;[link]&lt;/a&gt;</content></entry>";
            } else {
                $xml .= "<item><title>Comic $i</title><link>$url</link>"
                    . "<description>&lt;img src=\"$image\" title=\"Caption $i\"/&gt;</description></item>";
            }
        }
        $xml .= $atom ? '</feed>' : '</channel></rss>';
        $response = new Response(200, [], $xml);
        if ($parserClass === DevHumorParser::class) {
            $parser = new class ($response) extends DevHumorParser {
                protected function fetchImage(string $url): string
                {
                    return "$url.png";
                }
            };
        } else {
            $parser = new $parserClass($response);
        }

        // Record warnings without throwing: parsers catching ErrorException must still fail this test.
        $warnings = [];
        set_error_handler(function ($severity, $message) use (&$warnings) {
            $warnings[] = $message;
            return true;
        }, E_WARNING | E_NOTICE);
        try {
            for ($i = 0; $i < $entryCount; $i++) {
                $lol = $parser->next();
                self::assertNotNull($lol);
                self::assertSame("https://example.com/$i", $lol->getUrl());
                self::assertSame("https://example.com/$i.png", $lol->getImageUrl());
            }
            self::assertNull($parser->next());
            self::assertNull($parser->next());
            self::assertSame([], $warnings);
        } finally {
            restore_error_handler();
        }
    }

    public static function feeds(): iterable
    {
        foreach ([GenericParser::class, XkcdParser::class, DevHumorParser::class, RedditParser::class] as $parser) {
            foreach ([0, 1, 10, 15, 20] as $count) {
                yield "$parser with $count entries" => [$parser, $count];
            }
        }
    }
}
