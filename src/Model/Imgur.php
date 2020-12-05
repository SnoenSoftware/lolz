<?php
/**
 * @license Proprietary
 * @author bjorn
 * @copyright BRBcoffee 2020
 */

namespace App\Model;


use App\Entity\Lol;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

/**
 * Class Imgur
 * @author bjorn
 */
class Imgur
{
    private const CLIENT_ID = 'f51e9c749055cfd';
    private const API_BASE = 'https://api.imgur.com/3/';

    /**
     * @param Lol $lol
     * @return bool
     * @author bjorn
     */
    public function isImgur(Lol $lol): bool
    {
        return count(preg_grep('/(imgur.com)/', [$lol->getImageUrl()]));
    }

    public function getContent(Lol $lol): string
    {
        return sprintf('<imgur data-url="%s"/>', $lol->getImageUrl());
    }

    /**
     * @param string $lolUrl
     * @return array
     * @author bjorn
     */
    public function getRealImageData(string $lolUrl): array
    {
        $parts = parse_url($lolUrl);
        $path = $parts['path'];
        $hash = ltrim($path, '/');
        $response = $this->getClient()->get(self::API_BASE . '/image/' . $hash);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $lolUrl
     * @return array
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    public function getAlbumData(string $lolUrl): array
    {
        $parts = parse_url($lolUrl);
        $path = $parts['path'];
//        $hash = ltrim($path, '/');
        $pathParts = explode('/', $path);
        $hash = $pathParts[array_key_last($pathParts)];
        $response = $this->getClient()->get(self::API_BASE . '/album/' . $hash);
        return json_decode($response->getBody()->getContents(), true);
    }

    protected function getClient(): Client
    {
        $client = new Client([
            RequestOptions::HEADERS => [
                'Authorization' => "Client-ID " . self::CLIENT_ID,
                'Accept' => 'application/json',
                'User-Agent' => 'bjorn lolz generator'
            ]
        ]);
        return $client;
    }
}
