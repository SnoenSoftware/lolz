<?php

namespace App\Controller;

use App\Service\LolzProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LolzController extends AbstractController
{
    /**
     * @Route("/", name="lolz")
     * @param LolzProvider $lolzProvider
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(LolzProvider $lolzProvider)
    {
        return $this->render('lolz/index.html.twig', [
            'lolz' => $lolzProvider->next()
        ]);
    }
}
