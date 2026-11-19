<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@gmail.com>
 * @copyright BRBcoffee 2020
 */

namespace App\Model\Parser;


use App\Entity\Lol;
use App\Model\Api\ParserAbstract;
use Symfony\Component\DomCrawler\Crawler;

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
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    private function extractImageHref(?\SimpleXMLElement $current): ?string
    {
        $content = new Crawler();
        $content->addHtmlContent((string)$current->content);
        $links = $content->filter('a');
        $imageHref = null;
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            if ($link->textContent == "[link]") {
                $imageHref = $href;
            } elseif (strpos($href, 'i.redd.it') !== false) {
                $imageHref = $href;
                break;
            } elseif (strpos($href, 'v.redd.it') !== false) {
                $imageHref = $href;
                break;
            }
        }
        return $imageHref;
    }
}
