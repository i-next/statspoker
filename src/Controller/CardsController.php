<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\CardsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CardsController extends AbstractController
{
    #[Route('cards/stats', name: 'cards_stats')]
    public function index(CardsRepository $cardsRepository): Response
    {
        $cards = $cardsRepository->findBy([],['count' => 'DESC']);
        return $this->render('cards/index.html.twig', [
            'cards' => $cards,
        ]);
    }
    #[Route('cards/statsdashboard', name: 'cards_stats_dashboard')]
    public function indexDashboard(CardsRepository $cardsRepository): Response
    {
        $cards = $cardsRepository->findBy([],['count' => 'DESC'],5);
        return $this->render('cards/index.html.twig', [
            'cards' => $cards,
        ]);
    }

}
