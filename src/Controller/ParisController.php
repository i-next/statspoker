<?php

namespace App\Controller;

use App\Entity\Paris;
use App\Form\ParisType;
use App\Repository\ParisRepository;
use Elastica\Query;
use FOS\ElasticaBundle\Index\IndexManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/paris')]
class ParisController extends AbstractController
{
    private $indexManager;

    public function __construct(IndexManager $indexManager)
    {
        $this->indexManager = $indexManager;
    }

    #[Route('/', name: 'app_paris_index', methods: ['GET'])]
    public function index(ParisRepository $parisRepository): Response
    {
        return $this->render('paris/index.html.twig', [
            'paris' => $parisRepository->findAll(),
            'menu_active' => 'paris',
        ]);
    }

    #[Route('/new', name: 'app_paris_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ParisRepository $parisRepository): Response
    {
        $pari = new Paris();
        $form = $this->createForm(ParisType::class, $pari);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parisRepository->add($pari);
            return $this->redirectToRoute('app_paris_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('paris/new.html.twig', [
            'pari' => $pari,
            'form' => $form,
            'menu_active' => 'paris'
        ]);
    }

    #[Route('/ajaxparis', name: 'app_ajax_paris')]
    public function ajaxParis(Request $request): JsonResponse
    {
        $paris = [];
        $queryParis = new Query();
        $size = $request->query->get('limit')?:50000;
        $order = $request->query->get('order')?:'desc';
        $sort = $request->query->get('sort')?:'date';
        $offset = $request->query->get('offset')?:0;
        $queryParis->setFrom($offset);
        $queryParis->setSize($size);
        $queryParis->setSort([$sort=>$order]);

        $parisES = $this->indexManager->getIndex('paris')->search($queryParis)->getResults();
        foreach($parisES as $key =>$pariES){

            $pariData = $pariES->getData();
            dump($pariData);die;
            /*$players[$key]['pseudo'] = $playerData['pseudo'];
            $players[$key]['hand_win'] = $playerData['hand_win'];
            $players[$key]['tour_win'] = $playerData['tour_win'];*/
        }
        return new JsonResponse([
            'rows' => $paris,
            'total'=> $this->indexManager->getIndex('paris')->count()
        ]);
    }

    #[Route('/{id}', name: 'app_paris_show', methods: ['GET'])]
    public function show(Paris $pari): Response
    {
        return $this->render('paris/show.html.twig', [
            'pari' => $pari,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_paris_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Paris $pari, ParisRepository $parisRepository): Response
    {
        $form = $this->createForm(ParisType::class, $pari);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parisRepository->add($pari);
            return $this->redirectToRoute('app_paris_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('paris/edit.html.twig', [
            'pari' => $pari,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_paris_delete', methods: ['POST'])]
    public function delete(Request $request, Paris $pari, ParisRepository $parisRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pari->getId(), $request->request->get('_token'))) {
            $parisRepository->remove($pari);
        }

        return $this->redirectToRoute('app_paris_index', [], Response::HTTP_SEE_OTHER);
    }


}
