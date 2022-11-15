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
use Elastica\Aggregation\Filter;
use Symfony\Component\Validator\Constraints\Date;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints\Json;
use function Doctrine\Common\Cache\Psr6\get;


class TournoiController extends AbstractController
{
    private const LAST_WEEK = "lastweek";

    private const THIS_WEEK = "thisweek";

    private const ALL = "all";

    private const THIS_MONTH = "thismonth";

    private const LAST_MONTH = "lastmonth";

    private const THIS_YEAR = "thisyear";

    private const LAST_YEAR = "lastyear";

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

    #[Route('/winpartyfiltered', name: 'app_win_party_filtered_tour')]
    public function winPartiesFiltered(Request $request): JsonResponse
    {
        $rangeQuery = new \Elastica\Query\Range();
        $filterDate = $this->getLimiteDate($request->query->get('data'));
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

    #[Route('/gainsfiltered', name: 'app_gains_filtered_tour')]
    public function gainsFiltered(Request $request): JsonResponse
    {
        $rangeQuery = new \Elastica\Query\Range();
        $filterDate = $this->getLimiteDate($request->query->get('data'));
        $rangeQuery->addField('date',$filterDate);
        $queryTournois = new Query();
        $queryTournois->setSize(500000);
        $queryTournois->setSort(['date' => 'ASC']);
        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery->addMust($rangeQuery);
        $queryTournois->setQuery($boolQuery);
        $gain = 0;
        $result = [];
        foreach($this->indexManager->getIndex('tournois')->search($queryTournois)->getResults() as $key => $resultSearch){
            $result['labels'][] = 'tournoi'.$key;
            if ($resultSearch->getData()['win']) {
                $gain += $resultSearch->getData()['money'];
            } else {
                $gain -= $resultSearch->getData()['buyin'];
            }
            $result['result'][] = $gain;
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

    private function getLimiteDate(string $data): array
    {
        $result = [];
        switch ($data){
            case self::ALL:
                $result = ["gte" => "01/01/1970","format" => 'dd/MM/yyyy'];
                break;
            case self::THIS_WEEK:
                $result = ["gte" => date("d/m/Y", strtotime('monday this week')),"format" => 'dd/MM/yyyy'];
                break;
            case self::LAST_WEEK:
                $mondayLastWeek = date("d/m/Y", strtotime('last week sunday'));
                $sundayLastWeek = date("d/m/Y", strtotime('last week'));
                $result = ["gte" => $sundayLastWeek,"lte" => $mondayLastWeek,"format" => 'dd/MM/yyyy'];
                break;
            case self::THIS_MONTH:
                $firstDay = date("d/m/Y", strtotime('first day of this month'));
                $lastDay = date("d/m/Y", strtotime('last day of this month'));
                $result = ["gte" => $firstDay,"lte" => $lastDay,"format" => 'dd/MM/yyyy'];
                break;
            case self::LAST_MONTH:
                $firstDay = date("d/m/Y", strtotime('first day of last month'));
                $lastDay = date("d/m/Y", strtotime('last day of last month'));
                $result = ["gte" => $firstDay,"lte" => $lastDay,"format" => 'dd/MM/yyyy'];
                break;
            case self::THIS_YEAR:
                $firstDay = date("d/m/Y", strtotime('first day of January this year'));
                $lastDay = date("d/m/Y", strtotime('last day of December this year'));
                $result = ["gte" => $firstDay,"lte" => $lastDay,"format" => 'dd/MM/yyyy'];
                break;
            case self::LAST_YEAR:
                $firstDay = date("d/m/Y", strtotime('first day of January last year'));
                $lastDay = date("d/m/Y", strtotime('last day of December last year'));
                $result = ["gte" => $firstDay,"lte" => $lastDay,"format" => 'dd/MM/yyyy'];
                break;
        }

        return $result;
    }
}
