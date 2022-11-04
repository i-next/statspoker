<?php

namespace App\Controller;

use App\Entity\Seed;
use App\Form\SeedType;
use App\Repository\SeedRepository;
use Elastica\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

#[Route('/seeds')]
class SeedController extends AbstractController
{
    private $finder;

    public function __construct(PaginatedFinderInterface $finder)
    {
        $this->finder = $finder;
    }

    #[Route('/', name: 'app_seed', methods: ['GET'])]
    public function index(SeedRepository $seedRepository): Response
    {
        $query = new Query();
        $query->setSort(['name' => 'ASC']);
        $result = $this->finder->find($query);
        return $this->render('seed/index.html.twig', [
            'seeds' => $result,
            'menu_active'       => 'seeds',
        ]);
    }

    #[Route('/new', name: 'app_seed_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SeedRepository $seedRepository): Response
    {
        $seed = new Seed();
        $form = $this->createForm(SeedType::class, $seed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $seedRepository->add($seed);
            return $this->redirectToRoute('app_seed', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('seed/new.html.twig', [
            'seed' => $seed,
            'form' => $form,
            'menu_active'       => 'seeds',
        ]);
    }

    #[Route('/{id}', name: 'app_seed_show', methods: ['GET'])]
    public function show(Seed $seed): Response
    {
        return $this->render('seed/show.html.twig', [
            'seed' => $seed,
            'menu_active'       => 'seeds',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_seed_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Seed $seed, SeedRepository $seedRepository): Response
    {
        $form = $this->createForm(SeedType::class, $seed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $seedRepository->add($seed);
            return $this->redirectToRoute('app_seed_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('seed/edit.html.twig', [
            'seed' => $seed,
            'form' => $form,
            'menu_active'       => 'seeds',
        ]);
    }

    #[Route('/{id}', name: 'app_seed_delete', methods: ['POST'])]
    public function delete(Request $request, Seed $seed, SeedRepository $seedRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$seed->getId(), $request->request->get('_token'))) {
            $seedRepository->remove($seed);
        }

        return $this->redirectToRoute('app_seed_index', [], Response::HTTP_SEE_OTHER);
    }
}
