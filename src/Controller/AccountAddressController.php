<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    //Pour enregistrer la data et donc enregistré l'adresse
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
       $this->entityManager = $entityManager;
    }


    #[Route('/compte/adresses', name: 'account_address')]
    public function index(): Response
    {

        return $this->render('account/address.html.twig', [

        ]);
    }

    #[Route('/compte/ajouter-une-adresse', name: 'account_address_add')]
    public function add(Request $request): Response
    {
        // on crée une instance adresse.
        $address = new Address();

        // on crée notre formulaire adresse.
        $form = $this->createForm(AddressType::class, $address);

        //on écoute la requête
        $form->handleRequest($request);
        // on verifie s'il est soumis et valide
        if($form->isSubmitted() && $form->isValid()) {
            // on récupère l'utilisateur connecté
            $address->setUser($this->getUser());
            // on fige la donnée (adresse)
            $this->entityManager->persist($address);
            $this->entityManager->flush();
           return $this->redirectToRoute('account_address');

        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()

        ]);
    }

    #[Route('/compte/modifier-une-adresse/{id}', name: 'account_address_edit')]
    public function edit(Request $request, $id): Response
    {
        // on récupère l'objet adresse à modifier avec l'id
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);
        // on vérifie si l'adresse n'existe pas et pour la sécurisation de l'URL, on vérifie si l'adresse est différente de l'user connecté.
        if(!$address || $address->getUser() != $this->getUser()) {
            // si oui
            return $this->redirectToRoute('account_address');
        }

        // on crée notre formulaire adresse.
        $form = $this->createForm(AddressType::class, $address);

        //on écoute la requête
        $form->handleRequest($request);
        // on verifie s'il est soumis et valide
        if($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();
            return $this->redirectToRoute('account_address');

        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()

        ]);
    }

    #[Route('/compte/supprimer-une-adresse/{id}', name: 'account_address_delete')]
    public function delete($id): Response
    {
        // on récupère l'objet adresse à supprimer avec l'id
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);
        // on vérifie si l'adresse existe et pour la sécurisation de l'URL, on vérifie si l'adresse est celle de l'user connecté.
        if($address && $address->getUser() == $this->getUser()) {
            // Si oui, on la supprime
             $this->entityManager->remove($address);
             $this->entityManager->flush();
        }


            return $this->redirectToRoute('account_address');


    }
}
