<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Repository\TournamentRepository;

class TournamentController extends AbstractController
{
    #[Route('/tournament', name: 'app_tournament')]
    public function index(HttpClientInterface $client): Response
    {
        return $this->render('tournament/index.html.twig', [
            'controller_name' => 'TournamentController',
        ]);
    }

    #[Route('/ajax/historicevolution', name: 'app_ajax_tournament_historic')]
    public function historicEvolution(TournamentRepository $tournamentRepository): JsonResponse
    {
        $tournamentPositions = [];
        $dataTournamentPositions = $tournamentRepository->countPosition()->getResult();
        foreach ($dataTournamentPositions as $data){
            $tournamentPositions[$data['finalposition']] = $data[1];
        }
        $tournaments = $tournamentRepository->findBy([], ['date' => 'asc']);
        $dateref = "";
        $daterefMonth = "";
        $result = [];
        $resultGain = [];
        $resultMonth = [];
        $resultGainMonth = [];
        $return = [];
        foreach ($tournaments as $tournament) {

            if ($tournament->getFinalposition() === 1) {
                $tournamentPizepool = (int)$tournament->getPrizepool() - $tournament->getBuyin();
                if ($daterefMonth !== $tournament->getDate()->format('m-Y')) {
                    $daterefMonth = $tournament->getDate()->format('m-Y');
                    $resultMonth[$daterefMonth] = 1;
                    $resultGainMonth[$daterefMonth] = $tournamentPizepool;
                } else {
                    $resultMonth[$daterefMonth] = $resultMonth[$daterefMonth] + 1;
                    $resultGainMonth[$daterefMonth] = $resultGainMonth[$daterefMonth] + $tournamentPizepool;
                }
                if ($dateref !== $tournament->getDate()->format('d-m-Y')) {
                    $dateref = $tournament->getDate()->format('d-m-Y');
                    $result[$dateref] = 1;
                    $resultGain[$dateref] = $tournamentPizepool;
                } else {
                    $result[$dateref] = $result[$dateref]+1;
                    $resultGain[$dateref] = $resultGain[$dateref] + $tournamentPizepool;
                }

            } else {

                $tournamentPizepool = $tournament->getBuyin();
                if ($daterefMonth !== $tournament->getDate()->format('m-Y')) {
                    $daterefMonth = $tournament->getDate()->format('m-Y');
                    $resultMonth[$daterefMonth] = -1;
                    $resultGainMonth[$daterefMonth] = 0-$tournamentPizepool;
                } else {
                    $resultMonth[$daterefMonth] = $resultMonth[$daterefMonth] - 1;
                    $resultGainMonth[$daterefMonth] = $resultGainMonth[$daterefMonth] - $tournamentPizepool;
                }
                if ($dateref !== $tournament->getDate()->format('d-m-Y')) {
                    $dateref = $tournament->getDate()->format('d-m-Y');
                    $result[$dateref] = -1;
                    $resultGain[$dateref] = $tournamentPizepool;
                } else {
                    $result[$dateref] = $result[$dateref] -1;
                    $resultGain[$dateref] = $resultGain[$dateref] - $tournamentPizepool;
                }
            }
        }
        $return['win'] = $result;
        $return['prizepool'] = $resultGain;
        $return['win_month'] = $resultMonth;
        $return['prizepool_month'] = $resultGainMonth;
        $return['tournament_positions'] = $tournamentPositions;
        return new JsonResponse($return);
    }

    #[Route('/ajax/historicevolutiondashboard', name: 'app_ajax_tournament_historic_dashboard')]
    public function historicEvolutionDashboard(TournamentRepository $tournamentRepository): JsonResponse
    {
        $tournamentPositions = [];
        $dataTournamentPositions = $tournamentRepository->countPosition()->getResult();
        foreach ($dataTournamentPositions as $data){
            $tournamentPositions[$data['finalposition']] = $data[1];
        }
        $tournaments = $tournamentRepository->findBy([], ['date' => 'asc']);
        $dateref = "";
        $daterefMonth = "";
        $result = [];
        $resultGain = [];
        $resultMonth = [];
        $resultGainMonth = [];
        $return = [];
        foreach ($tournaments as $tournament) {

            if ($tournament->getFinalposition() === 1) {
                $tournamentPizepool = (int)$tournament->getPrizepool() - $tournament->getBuyin();
                if ($daterefMonth !== $tournament->getDate()->format('m-Y')) {
                    $daterefMonth = $tournament->getDate()->format('m-Y');
                    $resultMonth[$daterefMonth] = 1;
                    $resultGainMonth[$daterefMonth] = $tournamentPizepool;
                } else {
                    $resultMonth[$daterefMonth] = $resultMonth[$daterefMonth] + 1;
                    $resultGainMonth[$daterefMonth] = $resultGainMonth[$daterefMonth] + $tournamentPizepool;
                }
                if ($dateref !== $tournament->getDate()->format('d-m-Y')) {
                    $dateref = $tournament->getDate()->format('d-m-Y');
                    $result[$dateref] = 1;
                    $resultGain[$dateref] = $tournamentPizepool;
                } else {
                    $result[$dateref] = $result[$dateref]+1;
                    $resultGain[$dateref] = $resultGain[$dateref] + $tournamentPizepool;
                }

            } else {

                $tournamentPizepool = $tournament->getBuyin();
                if ($daterefMonth !== $tournament->getDate()->format('m-Y')) {
                    $daterefMonth = $tournament->getDate()->format('m-Y');
                    $resultMonth[$daterefMonth] = -1;
                    $resultGainMonth[$daterefMonth] = 0-$tournamentPizepool;
                } else {
                    $resultMonth[$daterefMonth] = $resultMonth[$daterefMonth] - 1;
                    $resultGainMonth[$daterefMonth] = $resultGainMonth[$daterefMonth] - $tournamentPizepool;
                }
                if ($dateref !== $tournament->getDate()->format('d-m-Y')) {
                    $dateref = $tournament->getDate()->format('d-m-Y');
                    $result[$dateref] = -1;
                    $resultGain[$dateref] = $tournamentPizepool;
                } else {
                    $result[$dateref] = $result[$dateref] -1;
                    $resultGain[$dateref] = $resultGain[$dateref] - $tournamentPizepool;
                }
            }
        }

        $return['win'] = array_slice($result, -10);
        $return['prizepool'] = array_slice($resultGain, -10);
        $return['win_month'] = array_slice($resultMonth,-5);
        $return['prizepool_month'] = array_slice($resultGainMonth,-5);
        $return['tournament_positions'] = $tournamentPositions;
        return new JsonResponse($return);
    }
}
