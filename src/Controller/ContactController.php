<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Elastica\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\ElasticaBundle\Index\IndexManager;

#[Route('/contact')]
class ContactController extends AbstractController
{

    private $indexManager;

    public function __construct(IndexManager $indexManager)
    {
        $this->indexManager = $indexManager;
    }

    #[Route('/', name: 'app_contact_index', methods: ['GET'])]
    public function index(ContactRepository $contactRepository): Response
    {
        return $this->render('contact/index.html.twig', [
            'contacts' => $contactRepository->findAll(),
            'menu_active' => 'contacts',
        ]);
    }

    #[Route('/ajaxcontacts', name: 'app_ajax_contacts')]
    public function ajaxContacts(Request $request): JsonResponse
    {

        $contacts = [];
        $queryContacts = new Query();
        $size = $request->query->get('limit')?:50000;
        $order = $request->query->get('order')?:'asc';
        $sort = $request->query->get('sort')?:'pseudo';
        $offset = $request->query->get('offset')?:0;
        $search = $request->query->get('search')?:false;
        $queryContacts->setFrom($offset);
        $queryContacts->setSize($size);
        $queryContacts->setSort([$sort=>$order]);
       /* $type = $request->query->get('type')?'true':'false';
        $matchQuery = new Query\MatchQuery();
        $matchQuery->setField('valid',$type);
        $queryContacts->setQuery($matchQuery);*/

        if($search){
            $queryString = new Query\QueryString();
            $queryString->setDefaultField('pseudo');
            $queryString->setQuery('*'.$search.'*');
            $queryContacts->setQuery($queryString);
        }
        $contactsES = $this->indexManager->getIndex('contacts')->search($queryContacts)->getResults();

        foreach($contactsES as $key =>$contactES){

            $contactData = $contactES->getData();
            $contacts[$key]['valid'] = $contactData['valid'];
            $contacts[$key]['pseudo'] = $contactData['pseudo'];
            $contacts[$key]['age'] = $contactData['age'];
            $contacts[$key]['sexe'] = $contactData['sexe']?'H':'F';
            $contacts[$key]['ville'] = $contactData['ville'];
            $contacts[$key]['comment'] = $contactData['comment'];
            $routeEdit = $this->generateUrl('app_contact_edit',['id' => $contactES->getId()]);
            $routeShow =  $this->generateUrl('app_contact_show',['id' => $contactES->getId()]);
            $contacts[$key]['action'] = '<a href="'.$routeShow.'">show</a>
                    <a href="'.$routeEdit.'">edit</a>';

        }

        return new JsonResponse([
            'rows' => $contacts,
            'total'=> $this->indexManager->getIndex('contacts')->count()
        ]);
    }

    #[Route('/new', name: 'app_contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContactRepository $contactRepository): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRepository->add($contact);
            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form,
            'menu_active' => 'contacts',
        ]);
    }

    #[Route('/{id}', name: 'app_contact_show', methods: ['GET'])]
    public function show(Contact $contact): Response
    {
        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contact $contact, ContactRepository $contactRepository): Response
    {
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRepository->add($contact);
            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
            'menu_active' => 'contacts',
        ]);
    }

    #[Route('/{id}', name: 'app_contact_delete', methods: ['POST'])]
    public function delete(Request $request, Contact $contact, ContactRepository $contactRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->request->get('_token'))) {
            $contactRepository->remove($contact);
        }

        return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
    }


}
