<?php

namespace App\Controller;

use App\Entity\Plant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\PlantsFormType;
use App\Repository\PlantRepository;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class PlantsController extends AbstractController
{
    #[Route('/plants', name: 'app_plants')]
    public function index(Request $request, PlantRepository $plantRepository, ManagerRegistry $managerRegistry): Response
    {

        $plant = new Plant();
        $form = $this->createForm(PlantsFormType::class, $plant);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $plantBdd = $form->getData();
            $em = $managerRegistry->getManager();
            $em->persist($plantBdd);
            $em->flush();
        }
        $plants = $plantRepository->findBy([],['id'=>'DESC']);

        return $this->render('plants/index.html.twig', [
            'form'              => $form->createView(),
            'menu_active'       => 'plants',
            'controller_name'   => 'PlantsController',
            'plants'            => $plants
        ]);
    }

    #[Route('/plants/list', name: 'plants_list')]
    public function getPlants(Request $request, PlantRepository $plantRepository): JsonResponse
    {
        $plants = $plantRepository->findPlants();
        foreach ($plants as $key=>$plant){
            $plants[$key]['dateflo'] = $plant['dateflo']->format('d/m/Y');
            $plants[$key]['daterec'] = $plant['daterec']->format('d/m/Y');

        }
        //$result = $serializer->serialize($plants,'json', ['json_encode_options' => \JSON_PRESERVE_ZERO_FRACTION]);
        //dump($plants,);die;
        return new JsonResponse($plants);
    }
}
