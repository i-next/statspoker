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

class TournoiController extends AbstractController
{
    private $finder;

    public function __construct(PaginatedFinderInterface $finder)
    {
        $this->finder = $finder;
    }

    #[Route('/tournoi', name: 'app_tournoi')]
    public function index(TournoiRepository $tournoiRepository, TournoiResultRepository $tournoiResultRepository): Response
    {
        //$results = $tournoiResultRepository->findBy([],['buyin'=>'ASC']);
        $query = new Query();
        $query->setSort(['buyin' => 'ASC','prizepool' => 'asc']);
        $query->setSize(500);
        $boolQuery = new Query\BoolQuery();
        $fieldQuery = new Query\MatchQuery();
        $fieldQuery->setFieldQuery('ticket', 'false');
        //$boolQuery->addMust($boolQuery);
        $query->setQuery($fieldQuery);
        $results = $this->finder->find($query);
        //$results = $this->finder->find('*');
        //dump($results);die;
        $allRanges = [];

        foreach ($results as $result){
            $allRanges[$result->getIdentifiant().'('.$result->getNbtour().')'] = $result->getWin() / $result->getNbtour();
        }
        $tournois = $tournoiRepository->findBy([],['date'=>'ASC']);
        $tournoisGains = [];
        $tournoisWinGame[0] = 0;
        $tournoiWinResult = [];
        $tournoiWinPerDay = [];
        $tournoiWinPerMonth = [];
        $gain = 0;
        $i = 1;
        foreach($tournois as $key=>$tournoi){
            if(!array_key_exists($tournoi->getDate()->format('d/m/Y'),$tournoiWinPerDay)){
                $tournoiWinPerDay[$tournoi->getDate()->format('d/m/Y')] = 0;
            }
            if(!array_key_exists($tournoi->getDate()->format('m/Y'),$tournoiWinPerMonth)){
                $tournoiWinPerMonth[$tournoi->getDate()->format('m/Y')] = 0;
            }
            $tournoiWinPerMonth[$tournoi->getDate()->format('m/Y')] += $tournoi->getMoney();

            if($tournoi->getWin()){
                $gain += $tournoi->getPrizepool();
                $tournoiWinPerDay[$tournoi->getDate()->format('d/m/Y')] += $tournoi->getPrizepool();

                $tournoisWinGame[$i] = $tournoisWinGame[$i-1] + 1;
            }else{
                $gain -= $tournoi->getBuyin();
                $tournoiWinPerDay[$tournoi->getDate()->format('d/m/Y')] -= $tournoi->getBuyin();
                //$tournoiWinPerMonth[$tournoi->getDate()->format('m/Y')] -= $tournoi->getBuyin();
                $tournoisWinGame[$i] = $tournoisWinGame[$i-1] - 1;
            }
            $tournoiWinResult['tournoi'.$i] = $tournoisWinGame[$i];
            $i++;
            $tournoisGains['tournoi'.$key] = $gain;
        }
        //dump($tournoiWinResult);die;
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
