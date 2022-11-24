<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\ElasticaBundle\Index\IndexManager;
use Elastica\Query;
use App\Service\DataService;
use App\Entity\Cards;


class CardsController extends AbstractController
{
    private $dataService;

    private $indexManager;

    public function __construct(IndexManager $indexManager, DataService $dataService)
    {
        $this->indexManager = $indexManager;
        $this->dataService = $dataService;
    }

    #[Route('cards/stats', name: 'cards_stats')]
    public function cardsStat(Request $request): Response
    {

        $sort = $request->query->get('sort')?:'mycard';
        $size = $request->query->get('size')?:10;
        if($size === "all"){
            $size = 500;
        }
        $query = new Query();
        $query->setSize($size);
        $query->setSort([$sort => 'DESC']);
        $results = $this->indexManager->getIndex('cards')->search($query)->getResults();

        foreach($results as $result){
            $data = $result->getData();
            $card = new Cards();
            $card->setValue($data['value']);
            $card->setColor($data['color']);
            $card->setMycard($data['mycard']);
            $card->setFlopcard($data['flopcard']);
            $card->setRivercars($data['rivercars']);
            $card->setTurncard($data['turncard']);
            $response[] = $card;
        }
        return $this->render('cards/helper/tablesinglecard.html.twig', [
            'cards' => $response,
            'sort'  => $sort,
            'size'  => $size
        ]);
    }
    #[Route('cards/statsdashboard', name: 'cards_stats_dashboard')]
    public function indexDashboard(CardsRepository $cardsRepository): Response
    {
        $cards = $cardsRepository->findBy([],['count' => 'DESC'],5);
        return $this->render('cards/helper/tablesinglecard.html.twig', [
            'cards' => $cards,
        ]);
    }

}
