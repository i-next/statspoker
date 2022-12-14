<?php

namespace App\Controller;

use App\Entity\Plant;
use App\Form\PlantType;
use App\Repository\PlantRepository;
use App\Repository\SeedRepository;
use Elastica\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

#[Route('/plant')]
class PlantController extends AbstractController
{
    private CONST FLO_STATUS = 'Floraison';

    private CONST GER_STATUS = 'Germination';

    private $finder;

    public function __construct(PaginatedFinderInterface $finder)
    {
        $this->finder = $finder;
    }


    #[Route('/', name: 'app_plant_index', methods: ['GET'])]
    public function index(): Response
    {
        $query = new Query();
        $query->setSort(['status' => 'ASC', 'seed.name' => 'ASC']);
        $query->setSize(500);
        $result = $this->finder->find($query);
        $queryMatch = new Query\MatchQuery();
        $queryMatch->setField('status','Germination');
        $query->setQuery($queryMatch);
        $count['Germination'] = count($this->finder->find($query));
        $queryMatch->setField('status','Croissance');
        $query->setQuery($queryMatch);
        $count['Croissance'] = count($this->finder->find($query));
        $queryMatch->setField('status','Floraison');
        $query->setQuery($queryMatch);
        $count['Floraison'] = count($this->finder->find($query));
        $queryMatch->setField('status','Recolte');
        $query->setQuery($queryMatch);
        $count['Recolte'] = count($this->finder->find($query));

        return $this->render('plant/index.html.twig', [
            'plants'        => $result,
            'menu_active'   => 'plants',
            'count'         => $count
        ]);
    }

    #[Route('/new', name: 'app_plant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PlantRepository $plantRepository, SeedRepository $seedRepository): Response
    {
        $plant = new Plant();
        $form = $this->createForm(PlantType::class, $plant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->getData()->getStatus() === self::GER_STATUS){
                $seed = $form->getData()->getSeed();
                $seed->setQuantity($seed->getQuantity()-1);
                $seedRepository->add($seed);
            }
            $plantRepository->add($plant);
            return $this->redirectToRoute('app_plant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('plant/new.html.twig', [
            'plant' => $plant,
            'form' => $form,
            'menu_active'       => 'plants',
        ]);
    }

    #[Route('/{id}', name: 'app_plant_show', methods: ['GET'])]
    public function show(Plant $plant): Response
    {
        return $this->render('plant/show.html.twig', [
            'plant' => $plant,
            'menu_active'       => 'plants',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_plant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Plant $plant, PlantRepository $plantRepository): Response
    {
        $form = $this->createForm(PlantType::class, $plant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->getData()->getStatus() === self::FLO_STATUS){
                $duration =  '+'.$form->getData()->getSeed()->getDuration().' weeks';
                $dateRecolte = $form->getData()->getDateUpdated();
                $dateRecolte->modify($duration);
                $dateRecolte->modify('+10 days');
                $plant->setComment($dateRecolte->format('d/m/Y'));
            }
            $plantRepository->add($plant);
            return $this->redirectToRoute('app_plant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('plant/edit.html.twig', [
            'plant' => $plant,
            'form' => $form,
            'menu_active'       => 'plants',
        ]);
    }

    #[Route('/{id}', name: 'app_plant_delete', methods: ['POST'])]
    public function delete(Request $request, Plant $plant, PlantRepository $plantRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plant->getId(), $request->request->get('_token'))) {
            $plantRepository->remove($plant);
        }

        return $this->redirectToRoute('app_plant_index', [], Response::HTTP_SEE_OTHER);
    }
}
