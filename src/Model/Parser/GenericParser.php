<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@gmail.com>
 * @copyright BRBcoffee 2022
 */

namespace App\Model\Parser;

use App\Entity\Lol;
use App\Model\Api\ParserAbstract;
use Laminas\Feed\Reader\Feed\FeedInterface;
use Laminas\Feed\Reader\Reader;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;

class GenericParser extends ParserAbstract
{
    protected FeedInterface $feed;

    /** @var Lol[] */
    protected array $fetchedThisRun = [];

    /**
     * @return Lol|null
     * @throws ChildNotFoundException
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    public function next(): ?Lol
    {
        $feed = $this->getFeed();
        /** @var EntryInterface $next */
        try {
            $next = $feed->current();
        } catch (\ErrorException $e) {
            return null;
        } catch (\TypeError $e) {
            return null;
        }
        $feed->next();

        $content = $next->getContent();
        $dom = new Dom();
        try {
            $dom->loadStr($content);
        } catch (\Exception $e) {
            return $this->next();
        }
        $videos = $this->getVideoSources($dom);

        $lol = new Lol();
        if ($videos) {
            $lol->setVideoSources($videos)->setImageUrl($next->getLink());
        } else {
            $lol->setImageUrl($this->getImageUrl($dom));
            $lol->setCaption($this->getImageTitle($dom));
        }

        $lol->setFetched($this->getNow())->setUrl($next->getLink())->setTitle($next->getTitle());

        foreach ($this->fetchedThisRun as $alreadyUsedLol) {
            if ($alreadyUsedLol->getUrl() == $lol->getUrl() || $alreadyUsedLol->getImageUrl() == $lol->getImageUrl()) {
                return $this->next();
            }
        }

        $this->fetchedThisRun[] = $lol;
        return $lol;
    }

    private function getVideoSources(Dom $dom): array
    {
        try {
            $videos = $dom->find('video');
        } catch (\Exception $e) {
            return [];
        }
        $returnSources = [];

        if (!empty($videos) && $videos->count() > 0) {
            /** @var Dom\HtmlNode $video */
            $video = $videos->getIterator()->current();
            try {
                $sources = $video->find('source');
            } catch (ChildNotFoundException $e) {
                return [];
            }
            /** @var Dom\HtmlNode $source */
            foreach ($sources as $source) {
                $returnSources[] = [
                    'src' => $source->getTag()->getAttribute('src')['value'],
                    'type' => $source->getTag()->getAttribute('type')['value']
                ];
            }
        }

        return $returnSources;
    }

    private function getImageUrl(Dom $dom): string
    {
        $img = $this->getImage($dom);
        if (is_null($img)) {
            return "";
        }
        return $img->getTag()->getAttribute('src')['value'];
    }

    private function getImageTitle(Dom $dom): ?string
    {
        $img = $this->getImage($dom);
        if (is_null($img)) {
            return null;
        }
        $title = $img->getTag()->getAttribute('title') ?? $img->getTag()->getAttribute('alt');

        return $title ? $title['value'] : '';
    }

    private function getImage(Dom $dom): ?Dom\HtmlNode
    {
        try {
            $imgs = $dom->find('img');
        } catch (\Exception $e) {
            return null;
        }
        if ($imgs->count() == 0) {
            return null;
        }
        /** @var Dom\HtmlNode $img */
        $img = $imgs->getIterator()->current();
        return $img;
    }

    protected function getFeed(): FeedInterface
    {
        if (isset($this->feed)) {
            return $this->feed;
        }
        $feedContents = $this->getResponse()->getBody()->getContents();
        $feed = Reader::importString($feedContents);
        $this->feed = $feed;
        return $feed;
    }
}
