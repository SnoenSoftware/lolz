<?php

namespace App\Controller;

use App\Repository\LolRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LolzController extends AbstractController
{
    /**
     * @Route("/", name="lolz")
     * @param LolRepository $lolRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(LolRepository $lolRepository)
    {
        return $this->render('lolz/index.html.twig', [
            'lolz' => $lolRepository->findBy([], ['fetched' => 'DESC'])
        ]);
    }
}
