<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    //on a besoin pour enregistrer dans la base de donnée, de doctrine qui interagit avec celle-ci
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }


    #[Route('/inscription', name: 'register')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        // On crée le nouvel utilisateur connecté
        $user = new User();
        // On crée le formulaire d'inscription
        $form = $this->createForm(RegisterType::class, $user);

        //il faut analyser l'objet requete entrant
        $form->handleRequest($request);

        //Ensuite on lui demande si le formulaire à été envoyé et s'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Si oui on rappelle l'objet user et on y injecte toutes les données

            $user = $form->getData();

            // on hash le mdp à l'aide de l'injection de dépendance
            $password = $passwordHasher->hashPassword($user, $user->getPassword());
            // ensuite on l'injecte dans l'objet user
            $user->setPassword($password);



            // on fige la data de l'objet pour l'enregistrer
            $this->entityManager->persist($user);
            // on exécute la persistance et on enregistre
            $this->entityManager->flush();
        }



        return $this->render('register/index.html.twig', [
            'form'=> $form->createView()
        ]);
    }
}
