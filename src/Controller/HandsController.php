<?php

namespace App\Controller;

use Elastica\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MesMainsRepository;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

class HandsController extends AbstractController
{


    public function __construct()
    {

    }

    #[Route('/hands', name: 'app_hands')]
    public function index(MesMainsRepository $mesMainsRepository): Response
    {
        /*$query = new Query();
        $query->setSort(['win'=>'DESC']);
        $query->setSize(5);
        $bestHands = $this->finder->find($query);
        $query2 = new Query();
        $query2->setSort(['win'=>'ASC']);
        $query2->setSize(5);
        $worstHands = $this->finder->find($query2);
       // dump($worstHands);die;
        return $this->render('hands/index.html.twig', [
            'menu_active' => 'hands',
            'best_hands'    => $bestHands,
            'worst_hands'   => $worstHands
        ]);*/
    }
}
