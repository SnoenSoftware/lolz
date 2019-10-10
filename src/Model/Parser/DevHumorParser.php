<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@visma.com>
 * @copyright Visma Digital Commerce 2019
 */

namespace App\Model\Parser;


use App\Entity\Lol;
use App\Model\Api\ParserAbstract;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPHtmlParser\Dom;

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
     * @author Bjørn Snoen <bjorn.snoen@visma.com>
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
     * @return \SimpleXMLElement
     * @author Bjørn Snoen <bjorn.snoen@visma.com>
     */
    protected function getCurrentItem(): \SimpleXMLElement
    {
        return $this->getItem($this->pointer);
    }

    /**
     * @param int $pointer
     * @return \SimpleXMLElement
     * @author Bjørn Snoen <bjorn.snoen@visma.com>
     */
    protected function getItem(int $pointer): \SimpleXMLElement
    {
        return $this->getFeed()->xpath('//item')[$pointer];
    }

    /**
     * @return \SimpleXMLElement
     * @author Bjørn Snoen <bjorn.snoen@visma.com>
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
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\CurlException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     * @author Bjørn Snoen <bjorn.snoen@visma.com>
     */
    protected function fetchImage(string $url): string
    {
        $page = $this->getClient()->request(
            'GET',
            $url
        )->getBody()->getContents();


        $dom = new Dom();
        $dom->load($page);
        /** @var Dom\HtmlNode $image */
        $image = $dom->find('.single-media.margin-bottom')->toArray()[0];
        return $image->tag->getAttribute('src')['value'];
    }

    /**
     * @return Client
     * @author Bjørn Snoen <bjorn.snoen@visma.com>
     */
    protected function getClient(): Client
    {
        return $this->httpClient ?? new Client();
    }
}
