<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\ElasticaBundle\Index\IndexManager;
use Elastica\Query;


class PlayersController extends AbstractController
{
    private $indexManager;

    public function __construct(IndexManager $indexManager)
    {
        $this->indexManager = $indexManager;
    }

    #[Route('/players', name: 'app_players')]
    public function index(): Response
    {
        return $this->render('players/index.html.twig', [
            'menu_active'   => 'players',
        ]);
    }


    #[Route('/ajaxplayers', name: 'app_ajax_players')]
    public function ajaxPlayers(): JsonResponse
    {
        $players = [];
        $queryPlayers = new Query();
        $queryPlayers->setSize(50000);
        $playersES = $this->indexManager->getIndex('joueurs')->search($queryPlayers)->getResults();
        foreach($playersES as $key =>$playerES){

            $playerData = $playerES->getData();
            $players[$key]['pseudo'] = $playerData['pseudo'];
            $players[$key]['hand'] = $playerData['hand_win'];
            $players[$key]['tour'] = $playerData['tour_win'];
        }
        return new JsonResponse([
            'rows' => $players,
            'total'=> count($playersES)
        ]);
    }
}
