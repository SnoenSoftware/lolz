<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@gmail.com>
 * @copyright BRBcoffee 2020
 */

namespace App\Model\Parser;


use App\Entity\Lol;
use App\Model\Api\ParserAbstract;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

class DevHumorParser extends ParserAbstract
{
    /** @var \SimpleXMLElement */
    protected $feed;

    /** @var int */
    protected $pointer = 0;

    /** @var Client */
    protected $httpClient;

    /**
     * @return Lol
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    public function next(): ?Lol
    {
        $current = $this->getCurrentItem();
        $this->pointer++;
        if (!$current) {
            return null;
        }

        $lol = new Lol();
        try {
            $lol->setTitle($current->title)
                ->setUrl($current->link)
                ->setCaption((string)$current->description)
                ->setFetched($this->getNow())
                ->setImageUrl($this->fetchImage($current->link));
        } catch (GuzzleException $e) {
            return $this->next();
        } catch (\Exception $e) {
            return $this->next();
        }

        return $lol;
    }

    /**
     * @return \SimpleXMLElement|null
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    protected function getCurrentItem(): ?\SimpleXMLElement
    {
        return $this->getItem($this->pointer);
    }

    /**
     * @param int $pointer
     * @return \SimpleXMLElement|null
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    protected function getItem(int $pointer): ?\SimpleXMLElement
    {
        return $this->getFeed()->xpath('//item')[$pointer] ?? null;
    }

    /**
     * @return \SimpleXMLElement
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    protected function getFeed(): \SimpleXMLElement
    {
        if (!isset($this->feed)) {
            $this->feed = new \SimpleXMLElement($this->getResponse()->getBody()->getContents());
        }
        return $this->feed;
    }

    /**
     * @param string $url
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    protected function fetchImage(string $url): string
    {
        $page = $this->getClient()->request(
            'GET',
            $url
        )->getBody()->getContents();


        $dom = new Crawler();
        $dom->addHtmlContent($page);
        $image = $dom->filter('.single-media.margin-bottom')->first();
        $src = $image->attr('src');
        if (!$src) {
            throw new \RuntimeException('DevHumor image has no source.');
        }
        return $src;
    }

    /**
     * @return Client
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    protected function getClient(): Client
    {
        return $this->httpClient ?? new Client();
    }
}
