<?php

namespace App\Controller;

use App\Entity\Cards;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\ElasticaBundle\Index\IndexManager;
use Elastica\Query;
use function Symfony\Config\numberFormat;


class HandsController extends AbstractController
{

    private $indexManager;


    public function __construct(IndexManager $indexManager)
    {
        $this->indexManager = $indexManager;
    }

    #[Route('/hands', name: 'app_hands')]
    public function index(): Response
    {
        return $this->render('hands/index.html.twig', [
            'menu_active'       => 'hands',
        ]);
    }

    #[Route('/hands/stats', name: 'app_hands_stats')]
    public function handsStats(Request $request): Response
    {
        $sort = $request->query->get('sort')?:'ratio';
        $size = 500;
        $query = new Query();
        $query->setSize($size);
        if($sort !== 'ratio'){
            $query->setSort([$sort => 'DESC']);
        }

        $hands = $this->indexManager->getIndex('mes_mains')->search($query)->getResults();
        foreach ($hands as $hand){
            $data = $hand->getData();
            $card1 = new Cards();
            $card1->setValue($data['card1'][0]);
            $card1->setColor($data['card1'][1]);
            $card2 = new Cards();
            $card2->setValue($data['card2'][0]);
            $card2->setColor($data['card2'][1]);
            $win = $data['win']?:0;
            $results[$hand->getId()]['cards']['card1'] = $card1;
            $results[$hand->getId()]['cards']['card2'] = $card2;
            $results[$hand->getId()]['count'] = $data['count'];
            $results[$hand->getId()]['win'] = $win;
            $results[$hand->getId()]['ratio'] = number_format($win/$data['count'],2);
        }
        if($sort === 'ratio'){
            usort($results,function($a,$b){
                return $a['ratio'] < $b['ratio'];
            });
        }

        if($request->query->get('size') !== 'all'){
            $results = array_slice($results,0,$request->query->get('size')?:500);
        }

        return $this->render('cards/helper/tabledoublecard.html.twig', [
            'hands' => $results,
            'sort'  => $sort,
            'size'  => $size
        ]);
    }
}
