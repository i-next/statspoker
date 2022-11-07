<?php

namespace App\Controller;

use Elastica\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TournoiRepository;
use App\Repository\TournoiResultRepository;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use FOS\ElasticaBundle\Index\IndexManager;
use Symfony\Component\Validator\Constraints\Date;
use DateTimeInterface;


class TournoiController extends AbstractController
{
    private $finder;

    private $indexManager;

    public function __construct(PaginatedFinderInterface $finder, IndexManager $indexManager)
    {
        $this->finder = $finder;
        $this->indexManager = $indexManager;
    }

    #[Route('/tournoi', name: 'app_tournoi')]
    public function index(TournoiRepository $tournoiRepository, TournoiResultRepository $tournoiResultRepository): Response
    {

        $query = new Query();
        $query->setSort(['buyin' => 'ASC','prizepool' => 'asc']);
        $query->setSize(500);
        /*$fieldQuery = new Query\MatchQuery();
        $fieldQuery->setFieldQuery('ticket', 'false');
        $query->setQuery($fieldQuery);*/
        $results = $this->finder->find($query);
        $allRanges = [];

        foreach ($results as $result){
            $allRanges[$result->getIdentifiant().'('.$result->getNbtour().')'] = $result->getWin() / $result->getNbtour();
        }

        $queryTournois = new Query();
        $queryTournois->setSort(['date'=>'ASC']);
        $tournois =$this->indexManager->getIndex('tournois')->search($queryTournois)->getResults();
        $tournoisGains = [];
        $tournoisWinGame[0] = 0;
        $tournoiWinResult = [];
        $tournoiWinPerDay = [];
        $tournoiWinPerMonth = [];
        $gain = 0;
        $i = 1;
        foreach($tournois as $key=>$tournoiEs){

            $tournoiData = $tournoiEs->getData();
            $tournoiDate = new \DateTime();
            $tournoiDate->setTimestamp(strtotime($tournoiData['date']));
            if(!array_key_exists($tournoiDate->format('d/m/Y'),$tournoiWinPerDay)){
                $tournoiWinPerDay[$tournoiDate->format('d/m/Y')] = 0;
            }
            if(!array_key_exists($tournoiDate->format('m/Y'),$tournoiWinPerMonth)){
                $tournoiWinPerMonth[$tournoiDate->format('m/Y')] = 0;
            }
            $tournoiWinPerMonth[$tournoiDate->format('m/Y')] += $tournoiData['money'];

            if($tournoiData['win']){
                $gain += $tournoiData['money'];
                $tournoiWinPerDay[$tournoiDate->format('d/m/Y')] += $tournoiData['money'];

                $tournoisWinGame[$i] = $tournoisWinGame[$i-1] + 1;
            }else{
                $gain -= $tournoiData['buyin'];
                $tournoiWinPerDay[$tournoiDate->format('d/m/Y')] -=$tournoiData['buyin'];
                $tournoisWinGame[$i] = $tournoisWinGame[$i-1] - 1;
            }
            $tournoiWinResult['tournoi'.$i] = $tournoisWinGame[$i];
            $i++;
            $tournoisGains['tournoi'.$key] = $gain;
        }
        return $this->render('tournoi/index.html.twig', [
            'controller_name'       => 'TournoiController',
            'menu_active'           => 'tournoi',
            'ranges'                => $allRanges,
            'tournois_gains'        => $tournoisGains,
            'tournament_win_party'  => $tournoiWinResult,
            'tournoi_win_per_day'   => $tournoiWinPerDay,
            'tournoi_win_per_month' => $tournoiWinPerMonth
        ]);
    }

    #[Route('/ajaxtournoi', name: 'app_ajax_all_tournoi')]
    public function ajaxAllTournoi(TournoiRepository $tournoiRepository, TournoiResultRepository $tournoiResultRepository): JsonResponse
    {
        $allTournoi = $tournoiRepository->findAll();
        $results = $tournoiResultRepository->findBy([],['buyin'=>'ASC']);
        $response = [];
        foreach ($results as $result){
            /*if($result->getWin() <> 0){
                $response[$result->getIdentifiant()] = $result->getNbtour()/$result->getWin();
            }else{
                $response[$result->getIdentifiant()] = 0;
            }*/
            $response[$result->getIdentifiant()] = $result->getWin() / $result->getNbtour();
        }
        return new JsonResponse([
            'result' => $result
        ]);
    }

    #[Route('/ajaxresumetour', name: 'app_ajax_resume_tour')]
    public function ajaxResumeTour(TournoiResultRepository $tournoiResultRepository): JsonResponse
    {
        $results = $tournoiResultRepository->findBy([],['buyin'=>'ASC']);
        $response = [];
        foreach ($results as $result){
            /*if($result->getWin() <> 0){
                $response[$result->getIdentifiant()] = $result->getNbtour()/$result->getWin();
            }else{
                $response[$result->getIdentifiant()] = 0;
            }*/
            $response[$result->getIdentifiant()] = $result->getWin() / $result->getNbtour();
        }
        dump($response);die;
        return new JsonResponse($response);
    }
}
