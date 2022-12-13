<?php

namespace App\Controller;

use App\Entity\Tournoi;
use App\Form\TournoiType;
use App\Repository\TournoiRepository;
use Elastica\Query;
use FOS\ElasticaBundle\Index\IndexManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/tournoi')]
class BackTournoiController extends AbstractController
{
    private $indexManager;

    public function __construct(IndexManager $indexManager)
    {
        $this->indexManager = $indexManager;
    }

    #[Route('/', name: 'app_back_tournoi_index', methods: ['GET'])]
    public function index(TournoiRepository $tournoiRepository): Response
    {
        return $this->render('back_tournoi/index.html.twig', [
            'tournois'      => $tournoiRepository->findAll(),
            'menu_active'   => 'tournois'
        ]);
    }

    #[Route('/new', name: 'app_back_tournoi_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TournoiRepository $tournoiRepository): Response
    {
        $tournoi = new Tournoi();
        $form = $this->createForm(TournoiType::class, $tournoi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tournoiRepository->add($tournoi);
            return $this->redirectToRoute('app_back_tournoi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back_tournoi/new.html.twig', [
            'tournoi' => $tournoi,
            'form' => $form,
        ]);
    }
/*
    #[Route('/{id}', name: 'app_back_tournoi_show', methods: ['GET'])]
    public function show(Tournoi $tournoi): Response
    {
        return $this->render('back_tournoi/show.html.twig', [
            'tournoi' => $tournoi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_back_tournoi_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tournoi $tournoi, TournoiRepository $tournoiRepository): Response
    {
        $form = $this->createForm(TournoiType::class, $tournoi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tournoiRepository->add($tournoi);
            return $this->redirectToRoute('app_back_tournoi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back_tournoi/edit.html.twig', [
            'tournoi' => $tournoi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_back_tournoi_delete', methods: ['POST'])]
    public function delete(Request $request, Tournoi $tournoi, TournoiRepository $tournoiRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tournoi->getId(), $request->request->get('_token'))) {
            $tournoiRepository->remove($tournoi);
        }

        return $this->redirectToRoute('app_back_tournoi_index', [], Response::HTTP_SEE_OTHER);
    }*/

    #[Route('/ajaxtournoisback', name: 'app_ajax_tournois_back')]
    public function ajaxTournoisBack(Request $request): JsonResponse
    {
        $tournois = [];
        $queryPlayers = new Query();
        $size = $request->query->get('limit')?:50000;
        $order = $request->query->get('order')?:'asc';
        $sort = $request->query->get('sort')?:'identifiant';
        $offset = $request->query->get('offset')?:0;
        $search = $request->query->get('search')?:false;
        $queryPlayers->setFrom($offset);
        $queryPlayers->setSize($size);
        $queryPlayers->setSort([$sort=>$order]);
        if($search){
            $queryString = new Query\QueryString();
            $queryString->setDefaultField('identifiant');
            $queryString->setQuery('*'.$search.'*');
            $queryPlayers->setQuery($queryString);
        }
        $tournoisES = $this->indexManager->getIndex('tournois')->search($queryPlayers)->getResults();
        foreach($tournoisES as $key =>$tournoiES){

            $tournoiData = $tournoiES->getData();
            $date = new \DateTime();
            $date->setTimestamp(strtotime($tournoiData['date']));
            $dateData = $date->format("d/m/Y");
            $tournois[$key]['identifiant'] = $tournoiData['identifiant'];
            $tournois[$key]['buyin'] = $tournoiData['buyin'];
            $tournois[$key]['nbplayer'] = $tournoiData['nbplayer'];
            $tournois[$key]['prizepool'] = $tournoiData['prizepool'];
            $tournois[$key]['win'] = $tournoiData['win'];
            $tournois[$key]['position'] = $tournoiData['position'];
            $tournois[$key]['date'] = $dateData;
            $tournois[$key]['money'] = $tournoiData['money'];
        }
        return new JsonResponse([
            'rows' => $tournois,
            'total'=> $this->indexManager->getIndex('tournois')->count()
        ]);
    }
}
