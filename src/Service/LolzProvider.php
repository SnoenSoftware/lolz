<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@gmail.com>
 * @copyright BRBcoffee 2020
 */

namespace App\Service;


use App\Entity\Lol;
use App\Model\Imgur;
use App\Model\Reddit;
use App\Model\Twitter;
use App\Model\Youtube;
use App\Repository\LolRepository;

class LolzProvider
{
    /**
     * @var LolRepository
     */
    private $lolRepository;
    /**
     * @var Twitter
     */
    private $twitter;

    /** @var Lol[] */
    private $lolz;
    /**
     * @var Youtube
     */
    private $youtube;
    /**
     * @var Imgur
     */
    private $imgur;
    /**
     * @var Reddit
     */
    private $reddit;
    /**
     * @var VideoRenderer
     */
    private $videoRenderer;

    public function __construct(
        LolRepository $lolRepository,
        Twitter $twitter,
        Youtube $youtube,
        Imgur $imgur,
        Reddit $reddit,
        VideoRenderer $videoRenderer
    ) {
        $this->lolRepository = $lolRepository;
        $this->twitter = $twitter;
        $this->youtube = $youtube;
        $this->imgur = $imgur;
        $this->reddit = $reddit;
        $this->videoRenderer = $videoRenderer;
    }

    /**
     * @param int $limit
     * @param int $page
     * @return \Generator
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @author bjorn
     */
    public function next(int $limit = 30, int $page = 0): \Generator
    {
        foreach ($this->getLolz($limit, $page) as $lol) {
            if ($this->twitter->isTweet($lol)) {
                $lol->setContent($this->twitter->getContent($lol));
            } elseif ($this->youtube->isYoutube($lol)) {
                $lol->setContent($this->youtube->getContent($lol));
            } elseif ($this->imgur->isImgur($lol)) {
                $lol->setContent($this->imgur->getContent($lol));
            } elseif ($this->reddit->isVideo($lol) || $this->reddit->isNotImage($lol)) {
                $lol->setContent($this->reddit->embedComment($lol));
            } elseif ($lol->getVideoSources()) {
                $lol->setContent($this->videoRenderer->render($lol));
            }
            yield $lol;
        }
    }

    /**
     * @param int $limit
     * @param int $page
     * @return Lol[]
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    protected function getLolz(int $limit, int $page): array
    {
        if (!isset($this->lolz)) {
            $lolz = $this->lolRepository->findBy([], ['fetched' => 'DESC'], $limit, $limit * $page);
            return $lolz;
        }
        return $this->lolz;
    }
}
