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
use Symfony\Component\DomCrawler\Crawler;

class GenericParser extends ParserAbstract
{
    protected FeedInterface $feed;

    /** @var Lol[] */
    protected array $fetchedThisRun = [];

    /**
     * @return Lol|null
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    public function next(): ?Lol
    {
        $feed = $this->getFeed();
        if (!$feed->valid()) {
            return null;
        }
        /** @var EntryInterface $next */
        $next = $feed->current();
        $feed->next();

        $content = $next->getContent();
        $dom = new Crawler();
        try {
            $dom->addHtmlContent($content);
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

    private function getVideoSources(Crawler $dom): array
    {
        try {
            $videos = $dom->filter('video');
        } catch (\Exception $e) {
            return [];
        }
        $returnSources = [];

        if ($videos->count() > 0) {
            foreach ($videos->first()->filter('source') as $source) {
                $returnSources[] = [
                    'src' => $source->getAttribute('src'),
                    'type' => $source->getAttribute('type')
                ];
            }
        }

        return $returnSources;
    }

    private function getImageUrl(Crawler $dom): string
    {
        $img = $this->getImage($dom);
        if (is_null($img)) {
            return "";
        }
        return $img->attr('src') ?? '';
    }

    private function getImageTitle(Crawler $dom): ?string
    {
        $img = $this->getImage($dom);
        if (is_null($img)) {
            return null;
        }
        return $img->attr('title') ?? $img->attr('alt') ?? '';
    }

    private function getImage(Crawler $dom): ?Crawler
    {
        try {
            $imgs = $dom->filter('img');
        } catch (\Exception $e) {
            return null;
        }
        if ($imgs->count() == 0) {
            return null;
        }
        return $imgs->first();
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
