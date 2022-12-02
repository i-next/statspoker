<?php

namespace App\Controller;

use Elastica\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\ElasticaBundle\Index\IndexManager;
use Elastica\Query\Range;
use Elastica\Query\BoolQuery;
use App\Service\DataService;


class HomeController extends AbstractController
{
    private $dataService;

    private $indexManager;

    public function __construct(IndexManager $indexManager, DataService $dataService)
    {

        $this->indexManager = $indexManager;
        $this->dataService = $dataService;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'menu_active' => 'dashboard',
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/ajax7daysresult', name: 'app_data_7_days_result')]
    public function index2(): JsonResponse
    {
        $rangeQuery = new Range();
        $filterDate = $this->dataService->getLimiteDate("last7days");
        $rangeQuery->addField('date',$filterDate);
        $queryTournois = new Query();
        $queryTournois->setSize(500000);
        $queryTournois->setSort(['date' => 'ASC']);
        $boolQuery = new BoolQuery();
        $boolQuery->addMust($rangeQuery);
        $queryTournois->setQuery($boolQuery);
        $result['labels'] = [];
        $result['result'] = [];
        $tournoisWinGame[0] = 0;
        $tournoiWinResult = [];
        $oldIdentifiant = 0;
        foreach ($this->indexManager->getIndex('tournois')->search($queryTournois)->getResults() as $resultES){
            $tournoiData = $resultES->getData();
            $tournoiDate = new \DateTime();
            $tournoiDate->setTimestamp(strtotime($tournoiData['date']));
            if($oldIdentifiant === 0){
                $tournoisWinGame[$tournoiDate->format('d/m/Y')] = 0;
            }
            if ($tournoiData['win']) {
                $tournoisWinGame[$tournoiDate->format('d/m/Y')] = $tournoisWinGame[$oldIdentifiant] + 1;
            } else {
                $tournoisWinGame[$tournoiDate->format('d/m/Y')] = $tournoisWinGame[$oldIdentifiant] - 1;
            }
            $tournoiWinResult[$tournoiDate->format('d/m/Y')] = $tournoisWinGame[$tournoiDate->format('d/m/Y')];
            $oldIdentifiant = $tournoiDate->format('d/m/Y');
        }
        foreach ($tournoiWinResult as $key => $value) {
            $result['labels'][] = $key;
            $result['result'][] = $value;
        }
        return new JsonResponse(json_encode($result));
    }

    #[Route('/ajax7daysmoney', name: 'app_data_7_days_money')]
    public function ajaxDaysMoney(): JsonResponse
    {
        $rangeQuery = new Range();
        $filterDate = $this->dataService->getLimiteDate("last7days");
        $rangeQuery->addField('date',$filterDate);
        $queryTournois = new Query();
        $queryTournois->setSize(500000);
        $queryTournois->setSort(['date' => 'ASC']);
        $boolQuery = new BoolQuery();
        $boolQuery->addMust($rangeQuery);
        $queryTournois->setQuery($boolQuery);
        $result['labels'] = [];
        $result['result'] = [];
        $tournoisWinGame[0] = 0;
        $tournoiWinResult = [];
        $oldIdentifiant = 0;
        foreach ($this->indexManager->getIndex('tournois')->search($queryTournois)->getResults() as $resultES){
            $tournoiData = $resultES->getData();
            $tournoiDate = new \DateTime();
            $tournoiDate->setTimestamp(strtotime($tournoiData['date']));
            if($oldIdentifiant !== $tournoiDate->format('d/m/Y')){
                $tournoisWinGame[$tournoiDate->format('d/m/Y')] = 0;
            }
            $tournoisWinGame[$tournoiDate->format('d/m/Y')] = $tournoisWinGame[$tournoiDate->format('d/m/Y')] + $tournoiData['money'];
            $tournoiWinResult[$tournoiDate->format('d/m/Y')] = $tournoisWinGame[$tournoiDate->format('d/m/Y')];
            $oldIdentifiant = $tournoiDate->format('d/m/Y');
        }

        foreach ($this->indexManager->getIndex('paris')->search($queryTournois)->getResults() as $resultParis){
            $pari = $resultParis->getData();
            $pariDate = new \DateTime();
            $pariDate->setTimestamp(strtotime($pari['date']));
            if(array_key_exists($pariDate->format('d/m/Y'),$tournoiWinResult)){
                $tournoiWinResult[$pariDate->format('d/m/Y')] = $pari['win'];
            }else{
                $tournoiWinResult[$pariDate->format('d/m/Y')] += $pari['win'];
            }
        }
        foreach ($tournoiWinResult as $key => $value) {
            $result['labels'][] = $key;
            $result['result'][] = $value;
        }
        return new JsonResponse(json_encode($result));
    }

    #[Route('/ajaxspinandgoresult', name: 'app_data_spin_and_go_result')]
    public function ajaxSpinAndGoResult(): JsonResponse
    {
        $response['labels'] = [];
        $response['result'] = [];
        $queryBuyin = new Query();
        $queryBuyin->setSize(500);
        $queryBuyin->setSort(['buyin'=>'DESC']);
        $queryTerms = new Query\Terms('buyin',["0.25","1.0","2.0","5.0"]);

        $queryBuyin->setQuery($queryTerms);

        foreach($this->indexManager->getIndex('tournois_result')->search($queryBuyin)->getResults() as $result){
            $data = $result->getData();
            $buyin = (float)$data['buyin'];
            $prizepool = (float)$data['prizepool'];
            if($buyin < 1){
                $buyin = $buyin * 100;
                $prizepool = $prizepool * 100;
            }
            if( (float)$data['prizepool'] < 100 && $prizepool % $buyin == 0){
                $response['labels'][] = $data['identifiant'];
                $response['result'][] = $data['win'] / $data['nbtour'];
            }
        }
        return new JsonResponse(json_encode($response));
    }
    #[Route('/ajaxspinandgomoney', name: 'app_data_spin_and_go_money')]
    public function ajaxSpinAndGoMoney(): JsonResponse
    {
        $response['labels'] = [];
        $response['result'] = [];
        $queryBuyin = new Query();
        $queryBuyin->setSort(['buyin'=>'DESC']);
        $queryBuyin->setSize(500);
        $queryTerms = new Query\Terms('buyin',["0.25","1.0","2.0","5.0"]);

        $queryBool = new Query\BoolQuery();
        $queryBool->addMust($queryTerms);
        //$queryBool->addMust($range);
        $queryBuyin->setQuery($queryBool);
        foreach($this->indexManager->getIndex('tournois_result')->search($queryBuyin)->getResults() as $result){
            $data = $result->getData();
            if(50 >= (float)$data['prizepool']) {
                $response['labels'][] = $data['identifiant'];
                $response['result'][] = $data['money'];
            }
        }

        return new JsonResponse(json_encode($response));
    }
}
