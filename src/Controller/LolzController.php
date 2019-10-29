<?php

namespace App\Controller;

use App\Service\LolzProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LolzController extends AbstractController
{
    /**
     * @Route("/", name="lolz")
     * @return Response
     */
    public function index()
    {
        return $this->render('lolz/index.html.twig');
    }
}
