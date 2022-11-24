<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    public function ajaxPlayers(Request $request): JsonResponse
    {
        $players = [];
        $queryPlayers = new Query();
        $size = $request->query->get('limit')?:50000;
        $order = $request->query->get('order')?:'asc';
        $sort = $request->query->get('sort')?:'pseudo';
        $offset = $request->query->get('offset')?:0;
        $search = $request->query->get('search')?:false;
        $queryPlayers->setFrom($offset);
        $queryPlayers->setSize($size);
        $queryPlayers->setSort([$sort=>$order]);
        if($search){
            $queryString = new Query\QueryString();
            $queryString->setDefaultField('pseudo');
            $queryString->setQuery('*'.$search.'*');
            $queryPlayers->setQuery($queryString);
        }
        $playersES = $this->indexManager->getIndex('joueurs')->search($queryPlayers)->getResults();
        foreach($playersES as $key =>$playerES){

            $playerData = $playerES->getData();
            $players[$key]['pseudo'] = $playerData['pseudo'];
            $players[$key]['hand_win'] = $playerData['hand_win'];
            $players[$key]['tour_win'] = $playerData['tour_win'];
        }
        return new JsonResponse([
            'rows' => $players,
            'total'=> $this->indexManager->getIndex('joueurs')->count()
        ]);
    }
}
