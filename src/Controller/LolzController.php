<?php

namespace App\Controller;

use App\Service\LolzProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LolzController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('/', name: 'lolz')]
    public function index(): Response
    {
        return $this->render('lolz/index.html.twig');
    }
}
