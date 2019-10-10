<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@visma.com>
 * @copyright Visma Digital Commerce 2019
 */

namespace App\Model;


use App\Entity\Lol;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

/**
 * Class Twitter
 * @author Bjørn Snoen <bjorn.snoen@visma.com>
 */
class Twitter
{
    public const OEMBED_URL = 'https://publish.twitter.com/oembed';
    /**
     * @param Lol $lol
     * @return bool
     * @author Bjørn Snoen <bjorn.snoen@visma.com>
     */
    public function isTweet(Lol $lol): bool
    {
        return count(preg_grep('/(twitter.com).+?(status)/', [$lol->getImageUrl()]));
    }

    /**
     * @param Lol $lol
     * @return string
     * @author bjorn
     */
    public function getContent(Lol $lol): string
    {
        return sprintf('<tweet data-url="%s"/>', $lol->getImageUrl());
    }

    /**
     * @param string $tweetUrl
     * @return array
     * @author bjorn
     */
    public function getTweet(string $tweetUrl): array
    {
        $client = $this->getTwitterClient();
        $response = $client->get(self::OEMBED_URL, [
            RequestOptions::QUERY => [
                'url' => $tweetUrl
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @return Client
     * @author bjorn
     */
    private function getTwitterClient(): Client
    {
        $client = new Client([
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'User-Agent' => 'bjorn lolz generator'
            ],
        ]);
        return $client;
    }
}
