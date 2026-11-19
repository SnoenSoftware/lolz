<?php

namespace App\Tests\Model\Parser;

use App\Model\Parser\DevHumorParser;
use App\Model\Parser\GenericParser;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class MediaMarkupTest extends TestCase
{
    public function testGenericImagesVideosAndDuplicateSkipping(): void
    {
        $items = [
            '<img src="https://example.com/one.png" title="Bjørn &amp; coffee" alt="Ignored">',
            '<img src="https://example.com/one.png">',
            '<img src="https://example.com/two.png" alt="Fallback">',
            '<video><source src="clip.mp4" type="video/mp4"><source src="clip.webm" type="video/webm"></video>'
                . '<video><source src="ignored.mp4" type="video/mp4"></video>',
            '<p>No media</p>',
        ];
        $rss = '<rss version="2.0"><channel><title>Test</title><link>https://example.com</link><description>Test</description>';
        foreach ($items as $i => $html) {
            $rss .= "<item><title>Item $i</title><link>https://example.com/$i</link>"
                . "<description><![CDATA[$html]]></description></item>";
        }
        $parser = new GenericParser(new Response(200, [], $rss.'</channel></rss>'));
        $first = $parser->next();
        self::assertSame('https://example.com/one.png', $first->getImageUrl());
        self::assertSame('Bjørn & coffee', $first->getCaption());
        $second = $parser->next();
        self::assertSame('https://example.com/two.png', $second->getImageUrl());
        self::assertSame('Fallback', $second->getCaption());
        $video = $parser->next();
        self::assertSame('https://example.com/3', $video->getImageUrl());
        self::assertSame([
            ['src' => 'clip.mp4', 'type' => 'video/mp4'],
            ['src' => 'clip.webm', 'type' => 'video/webm'],
        ], $video->getVideoSources());
        self::assertSame('', $parser->next()->getImageUrl());
        self::assertNull($parser->next());
    }

    public function testDevHumorSkipsPagesWithoutAnImage(): void
    {
        $rss = '<rss><channel>'
            . '<item><title>Missing</title><link>https://example.com/missing</link></item>'
            . '<item><title>Comic</title><link>https://example.com/comic</link></item>'
            . '</channel></rss>';
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([
            new Response(200, [], '<p>No image</p>'),
            new Response(200, [], '<img class="single-media margin-bottom" src="https://example.com/comic.png">'),
        ]))]);
        $parser = new class (new Response(200, [], $rss)) extends DevHumorParser {
            public function setClient(Client $client): void
            {
                $this->httpClient = $client;
            }
        };
        $parser->setClient($client);
        $lol = $parser->next();
        self::assertSame('https://example.com/comic', $lol->getUrl());
        self::assertSame('https://example.com/comic.png', $lol->getImageUrl());
        self::assertNull($parser->next());
    }
}
