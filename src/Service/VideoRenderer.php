<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@gmail.com>
 * @copyright BRBcoffee 2020
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
