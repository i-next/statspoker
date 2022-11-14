<?php

namespace App\Controller;

use Elastica\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TournoiRepository;
use App\Repository\TournoiResultRepository;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use FOS\ElasticaBundle\Index\IndexManager;
use Symfony\Component\Validator\Constraints\Date;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints\Json;


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
        return $this->render('tournoi/index.html.twig', [
            'menu_active' => 'tournoi'
        ]);
    }

    #[Route('/ajaxtournoi', name: 'app_ajax_all_tournoi')]
    public function ajaxAllTournoi(TournoiRepository $tournoiRepository, TournoiResultRepository $tournoiResultRepository): JsonResponse
    {
        $allTournoi = $tournoiRepository->findAll();
        $results = $tournoiResultRepository->findBy([], ['buyin' => 'ASC']);
        $response = [];
        foreach ($results as $result) {
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
        $results = $tournoiResultRepository->findBy([], ['buyin' => 'ASC']);
        $response = [];
        foreach ($results as $result) {
            /*if($result->getWin() <> 0){
                $response[$result->getIdentifiant()] = $result->getNbtour()/$result->getWin();
            }else{
                $response[$result->getIdentifiant()] = 0;
            }*/
            $response[$result->getIdentifiant()] = $result->getWin() / $result->getNbtour();
        }
        dump($response);
        die;
        return new JsonResponse($response);
    }

    #[Route('/gainsfiltered', name: 'app_gains_filtered_tour')]
    public function gainsFiltered(Request $request): JsonResponse
    {
        dump($request);
        die;
    }

    #[Route('/rangestournois', name: 'app_ranges_tour')]
    public function rangesTournois(): JsonResponse
    {
        $query = new Query();
        $query->setSort(['buyin' => 'ASC', 'prizepool' => 'asc']);
        $query->setSize(500);
        $results = $this->finder->find($query);
        $allRanges = [];

        foreach ($results as $result) {
            $allRanges['labels'][] = $result->getIdentifiant() . '(' . $result->getNbtour() . ')';
            $allRanges['result'][] = $result->getWin() / $result->getNbtour();
            //$allRanges[$result->getIdentifiant().'('.$result->getNbtour().')'] = $result->getWin() / $result->getNbtour();
        }

        return new JsonResponse(json_encode($allRanges));
    }

    #[Route('/winpermonthtournois', name: 'app_win_per_month_tour')]
    public function winsPerMounth(): JsonResponse
    {
        $queryTournois = new Query();
        $queryTournois->setSize(500000);
        $queryTournois->setSort(['date' => 'ASC']);
        $tournois = $this->indexManager->getIndex('tournois')->search($queryTournois)->getResults();
        $result['labels'] = [];
        $result['result'] = [];
        $tournoiWinPerMonth = [];
        foreach ($tournois as $key => $tournoiEs) {
            $tournoiData = $tournoiEs->getData();
            $tournoiDate = new \DateTime();
            $tournoiDate->setTimestamp(strtotime($tournoiData['date']));
            if (!array_key_exists($tournoiDate->format('m/Y'), $tournoiWinPerMonth)) {
                $tournoiWinPerMonth[$tournoiDate->format('m/Y')] = 0;
            }
            $tournoiWinPerMonth[$tournoiDate->format('m/Y')] += $tournoiData['money'];

        }
        foreach ($tournoiWinPerMonth as $key => $value) {
            $result['labels'][] = $key;
            $result['result'][] = $value;
        }

        return new JsonResponse(json_encode($result));
    }

    #[Route('/winperdaytournois', name: 'app_win_per_day_tour')]
    public function winsPerDay(): JsonResponse
    {
        $queryTournois = new Query();
        $queryTournois->setSize(500000);
        $queryTournois->setSort(['date' => 'ASC']);
        $tournois = $this->indexManager->getIndex('tournois')->search($queryTournois)->getResults();
        $result['labels'] = [];
        $result['result'] = [];
        $tournoiWinPerDay = [];
        foreach ($tournois as $key => $tournoiEs) {
            $tournoiData = $tournoiEs->getData();
            $tournoiDate = new \DateTime();
            $tournoiDate->setTimestamp(strtotime($tournoiData['date']));
            if (!array_key_exists($tournoiDate->format('d/m/Y'), $tournoiWinPerDay)) {
                $tournoiWinPerDay[$tournoiDate->format('d/m/Y')] = 0;
            }
            if ($tournoiData['win']) {
                $tournoiWinPerDay[$tournoiDate->format('d/m/Y')] += $tournoiData['money'];
            } else {
                $tournoiWinPerDay[$tournoiDate->format('d/m/Y')] -= $tournoiData['buyin'];
            }
        }
        foreach ($tournoiWinPerDay as $key => $value) {
            $result['labels'][] = $key;
            $result['result'][] = $value;
        }

        return new JsonResponse(json_encode($result));
    }

    #[Route('/gainstournois', name: 'app_gains_tour')]
    public function gainsTour(): JsonResponse
    {
        $queryTournois = new Query();
        $queryTournois->setSize(500000);
        $queryTournois->setSort(['date' => 'ASC']);
        $tournois = $this->indexManager->getIndex('tournois')->search($queryTournois)->getResults();
        $result['labels'] = [];
        $result['result'] = [];
        $gain = 0;
        $tournoisGains = [];
        foreach ($tournois as $key => $tournoiEs) {
            $tournoiData = $tournoiEs->getData();
            $tournoiDate = new \DateTime();
            $tournoiDate->setTimestamp(strtotime($tournoiData['date']));
            if ($tournoiData['win']) {
                $gain += $tournoiData['money'];
            } else {
                $gain -= $tournoiData['buyin'];
            }
            $tournoisGains['tournoi' . $key] = $gain;
        }
        foreach ($tournoisGains as $key => $value) {
            $result['labels'][] = $key;
            $result['result'][] = $value;
        }
        return new JsonResponse(json_encode($result));
    }

    #[Route('/winstournois', name: 'app_wins_tour')]
    public function winsTour(): JsonResponse
    {
        $queryTournois = new Query();
        $queryTournois->setSize(500000);
        $queryTournois->setSort(['date' => 'ASC']);
        $tournois = $this->indexManager->getIndex('tournois')->search($queryTournois)->getResults();
        $result['labels'] = [];
        $result['result'] = [];
        $tournoisWinGame[0] = 0;
        $tournoiWinResult = [];
        $i = 1;
        foreach ($tournois as $key => $tournoiEs) {
            $tournoiData = $tournoiEs->getData();
            $tournoiDate = new \DateTime();
            $tournoiDate->setTimestamp(strtotime($tournoiData['date']));
            if ($tournoiData['win']) {
                $tournoisWinGame[$i] = $tournoisWinGame[$i - 1] + 1;
            } else {
                $tournoisWinGame[$i] = $tournoisWinGame[$i - 1] - 1;
            }
            $tournoiWinResult['tournoi' . $i] = $tournoisWinGame[$i];
            $i++;
        }
        foreach ($tournoiWinResult as $key => $value) {
            $result['labels'][] = $key;
            $result['result'][] = $value;
        }
        return new JsonResponse(json_encode($result));
    }
}
