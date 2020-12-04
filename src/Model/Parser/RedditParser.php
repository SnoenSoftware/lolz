<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@gmail.com>
 * @copyright Visma Digital Commerce 2019
 */

namespace App\Model\Parser;


use App\Entity\Lol;
use App\Model\Api\ParserAbstract;
use PHPHtmlParser\Dom;
use function GuzzleHttp\Psr7\str;

class RedditParser extends ParserAbstract
{
    /**
     * @var \SimpleXMLElement
     */
    protected $feed;

    /** @var int */
    protected $pointer = 0;

    /**
     * @return Lol|null
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    public function next(): ?Lol
    {
        $current = $this->getItem($this->pointer++);
        if ($current == null) {
            return null;
        }

        try {
            $imageHref = $this->extractImageHref($current);
            if ($imageHref == null) {
                throw new \Exception();
            }
        } catch (\Exception $e) {
            return $this->next();
        }

        $lol = new Lol();
        $lol->setImageUrl($imageHref)
            ->setFetched($this->getNow())
            ->setTitle($current->title)
            ->setUrl($current->link['href']);

        return $lol;
    }

    /**
     * @param int $pointer
     * @return \SimpleXMLElement|null
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    protected function getItem(int $pointer): ?\SimpleXMLElement
    {
        return $this->getFeed()->entry[$pointer];
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
     * @param \SimpleXMLElement|null $current
     * @return string|null
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\CurlException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     * @throws \PHPHtmlParser\Exceptions\UnknownChildTypeException
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    private function extractImageHref(?\SimpleXMLElement $current): ?string
    {
        $content = new Dom();
        $content->load((string)$current->content);
        $links = $content->find('a');
        $imageHref = null;
        /** @var Dom\HtmlNode $link */
        foreach ($links->toArray() as $link) {
            if ($link->innerHtml() == "[link]") {
                $imageHref = $link->tag->getAttribute('href')['value'];
            } elseif (strpos($link->tag->getAttribute('href')['value'], 'i.redd.it') !== false) {
                $imageHref = $link->tag->getAttribute('href')['value'];
                break;
            } elseif (strpos($link->tag->getAttribute('href')['value'], 'v.redd.it') !== false) {
                $imageHref = $link->tag->getAttribute('href')['value'];
                break;
            }
        }
        return $imageHref;
    }
}
