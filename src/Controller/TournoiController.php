<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use FOS\ElasticaBundle\Index\IndexManager;
use Elastica\Query\Range;
use Elastica\Aggregation;
use App\Repository\TournoiRepository;
use App\Repository\TournoiResultRepository;

use App\Service\DataService;


class TournoiController extends AbstractController
{


    private $finder;

    private $indexManager;

    private $dataService;

    public function __construct(PaginatedFinderInterface $finder, IndexManager $indexManager, DataService $dataService)
    {
        $this->finder = $finder;
        $this->indexManager = $indexManager;
        $this->dataService = $dataService;
    }

    #[Route('/tournoi', name: 'app_tournoi')]
    public function index(TournoiRepository $tournoiRepository, TournoiResultRepository $tournoiResultRepository): Response
    {
        $agg = new Aggregation\Terms('uniq_buyin');
        $agg->setOrder("_term","asc");
        $agg->setField('buyin');
        $agg->setSize(500);
        $query = new Query();
        $query->setSize(0);
        $query->addAggregation($agg);

        $buyins = $this->indexManager->getIndex('tournois_result')->search($query)->getAggregations()['uniq_buyin']['buckets'];
        return $this->render('tournoi/index.html.twig', [
            'menu_active'   => 'tournoi',
            'buyins'        => $buyins
        ]);
    }

    #[Route('/ajaxtournoi', name: 'app_ajax_all_tournoi')]
    public function ajaxAllTournoi(TournoiRepository $tournoiRepository, TournoiResultRepository $tournoiResultRepository): JsonResponse
    {
        $allTournoi = $tournoiRepository->findAll();
        $results = $tournoiResultRepository->findBy([], ['buyin' => 'ASC']);
        $response = [];
        foreach ($results as $result) {
            $response[$result->getIdentifiant()] = $result->getWin() / $result->getNbtour();
        }
        return new JsonResponse([
            'result' => $result
        ]);
    }

    #[Route('/winpartyfiltered', name: 'app_win_party_filtered_tour')]
    public function winPartiesFiltered(Request $request): JsonResponse
    {
        $rangeQuery = new Range();
        $filterDate = $this->dataService->getLimiteDate($request->query->get('data'));
        $rangeQuery->addField('date',$filterDate);
        $queryTournois = new Query();
        $queryTournois->setSize(500000);
        $queryTournois->setSort(['date' => 'ASC']);
        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery->addMust($rangeQuery);
        $queryTournois->setQuery($boolQuery);
        $result['labels'] = [];
        $result['result'] = [];
        $tournoisWinGame[0] = 0;
        $tournoiWinResult = [];
        $i = 1;
        foreach($this->indexManager->getIndex('tournois')->search($queryTournois)->getResults() as $key => $tournoiEs){
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

    #[Route('/buyinsfiltered', name: 'app_buyins_filtered_tour')]
    public function buyinsFiltered(Request $request): JsonResponse
    {
        $dataQuery = $request->query->get('data');

        if($dataQuery !== "all"){
            $queryBuyin = new Query();
            $queryBuyin->setSort(['buyin' => 'ASC', 'prizepool' => 'ASC']);
            $queryBuyinMatch = new Query\MatchQuery();
            $queryBuyinMatch->setField('buyin',$dataQuery);
            $boolQuery = new \Elastica\Query\BoolQuery();
            $boolQuery->addMust($queryBuyinMatch);
            $queryBuyin->setQuery($boolQuery);

        }else{
            $queryBuyin = new Query();
            $queryBuyin->setSort(['buyin' => 'ASC', 'prizepool' => 'ASC']);
            $queryBuyin->setSize(500);
        }


        $allRanges = [];
        $results = $this->indexManager->getIndex('tournois_result')->search($queryBuyin)->getResults();

        foreach ($results as $result) {
            $data = $result->getData();
            $allRanges['labels'][] = $data['identifiant'] . '(' . $data['nbtour'] . ')';
            $allRanges['result'][] = $data['win'] / $data['nbtour'];
        }
        return new JsonResponse(json_encode($allRanges));
    }

    #[Route('/gainsfiltered', name: 'app_gains_filtered_tour')]
    public function gainsFiltered(Request $request): JsonResponse
    {
        $rangeQuery = new Range();
        $filterDate = $this->dataService->getLimiteDate($request->query->get('data'));
        $rangeQuery->addField('date',$filterDate);
        $queryTournois = new Query();
        $queryTournois->setSize(500000);
        $queryTournois->setSort(['date' => 'ASC']);
        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery->addMust($rangeQuery);
        $queryTournois->setQuery($boolQuery);
        $gain = 0;
        $result = [];
        $paris = $this->indexManager->getIndex('paris')->search($queryTournois)->getResults();
        $resultParis = [];
        foreach($paris as $pari){
            $data = $pari->getData();
            $date = new \DateTime();
            $date->setTimestamp(strtotime($data['date']));
            $dateData = $date->format("d/m/Y");
            if(array_key_exists($dateData,$resultParis)){
                $resultParis[$dateData] += $data['win'];
            }else{
                $resultParis[$dateData] = $data['win'];
            }
        }
        $i = 0;
        foreach($this->indexManager->getIndex('tournois')->search($queryTournois)->getResults() as $key => $resultSearch){
            $tournoiDate = new \DateTime();
            $tournoiDate->setTimestamp(strtotime($resultSearch->getData()['date']));
            if(array_key_exists($tournoiDate->format("d/m/Y"),$resultParis)){
                $gain += $resultParis[$tournoiDate->format("d/m/Y")];
                $result['labels'][] = 'tournoi'.$i;
                $result['result'][] = $gain;
                $i++;
                unset($resultParis[$tournoiDate->format("d/m/Y")]);
            }
            $result['labels'][] = 'tournoi'.$i;
            $gain += $resultSearch->getData()['money'];
            $result['result'][] = $gain;
            $i++;
        }
        return new JsonResponse(json_encode($result));
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
        $paris = $this->indexManager->getIndex('paris')->search($queryTournois)->getResults();
        $tournoiWinPerMonth = [];
        $result['labels'] = [];
        $result['result'] = [];

        foreach ($tournois as $key => $tournoiEs) {
            $tournoiData = $tournoiEs->getData();
            $tournoiDate = new \DateTime();
            $tournoiDate->setTimestamp(strtotime($tournoiData['date']));
            if (!array_key_exists($tournoiDate->format('m/Y'), $tournoiWinPerMonth)) {
                $tournoiWinPerMonth[$tournoiDate->format('m/Y')] = 0;
            }
            $tournoiWinPerMonth[$tournoiDate->format('m/Y')] += $tournoiData['money'];

        }
        foreach ($paris as $key => $pari) {
            $pariData = $pari->getData();
            $pariDate = new \DateTime();
            $pariDate->setTimestamp(strtotime($pariData['date']));
            if (!array_key_exists($pariDate->format('m/Y'), $tournoiWinPerMonth)) {
                $tournoiWinPerMonth[$pariDate->format('m/Y')] = 0;
            }

            $tournoiWinPerMonth[$pariDate->format('m/Y')] += $pariData['win'];
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
        $paris = $this->indexManager->getIndex('paris')->search($queryTournois)->getResults();
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
                $tournoiWinPerDay[$tournoiDate->format('d/m/Y')] += $tournoiData['money'];
        }
        foreach ($paris as $key => $pari) {
            $pariData = $pari->getData();
            $pariDate = new \DateTime();
            $pariDate->setTimestamp(strtotime($pariData['date']));
            if (!array_key_exists($pariDate->format('d/m/Y'), $tournoiWinPerDay)) {
                $tournoiWinPerDay[$pariDate->format('d/m/Y')] = 0;
            }

            $tournoiWinPerDay[$pariDate->format('d/m/Y')] += $pariData['win'];
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
        $paris = $this->indexManager->getIndex('paris')->search($queryTournois)->getResults();
        $resultParis = [];
        foreach($paris as $pari){
            $data = $pari->getData();
            $date = new \DateTime();
            $date->setTimestamp(strtotime($data['date']));
            $dateData = $date->format("d/m/Y");
            if(array_key_exists($dateData,$resultParis)){
                $resultParis[$dateData] += $data['win'];
            }else{
                $resultParis[$dateData] = $data['win'];
            }
        }
        $result['labels'] = [];
        $result['result'] = [];
        $gain = 0;
        $tournoisGains = [];
        $i = 0;
        foreach ($tournois as $key => $tournoiEs) {
            $tournoiData = $tournoiEs->getData();
            $tournoiDate = new \DateTime();
            $tournoiDate->setTimestamp(strtotime($tournoiData['date']));
            if(array_key_exists($tournoiDate->format("d/m/Y"),$resultParis)){
                $gain += $resultParis[$tournoiDate->format("d/m/Y")];
                $tournoisGains['tournoi' . $i] = $gain;
                $i++;
                unset($resultParis[$tournoiDate->format("d/m/Y")]);
            }
            $gain += $tournoiData['money'];
            $tournoisGains['tournoi' . $i] = $gain;
            $i++;
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
