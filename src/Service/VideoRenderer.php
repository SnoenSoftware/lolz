<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@visma.com>
 * @copyright Visma Digital Commerce 2019
 */

namespace App\Service;


use App\Entity\Lol;
use Twig\Environment;

class VideoRenderer
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render(Lol $lol): string
    {
        return $this->twig->render('lolz/video-player.html.twig', ['lol' => $lol]);
    }
}
