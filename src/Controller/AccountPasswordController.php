<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    public $entityManager;

    /**
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/modifier-mon-mot-de-passe', name: 'account_password')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        // gestion notification
        $notification = null;
        // je récupère l'utilisateur connecté
        $user = $this->getUser();

        // j'appelle mon formulaire
        $form = $this->createForm(ChangePasswordType::class, $user);
        // On traite le formulaire
        $form->handleRequest($request);
        // on vérifie si le formulaire à été soumis et validé
        if ($form->isSubmitted() && $form->isValid()) {

            // on récupère d'abord l'ancien mdp
            $old_pwd = $form->get('old_password')->getData();

            // on appelle une methode qui compare le mdp avec celui de la bdd
            if ($passwordHasher->isPasswordValid($user, $old_pwd)) {
                // une fois le mdp récupéré, on va chercher le nouveau mdp
                $new_pwd = $form->get('new_password')->getData();
                // on encode le nouveau mdp
                $password = $passwordHasher->hashPassword($user, $new_pwd);
                // on set le nouveau mdp
                $user->setPassword($password);
                // on fige la data de l'objet pour l'enregistrer
                $this->entityManager->persist($user);
                // on exécute la persistance et on enregistre
                $this->entityManager->flush();
                $notification = "Votre mot de passe a bien été mis à jour.";
            } else {
                $notification = "Votre mot de passe actuel n'est pas le bon.";
            }

        }

        return $this->render('account/password.html.twig', [
            // enfin on passe un petit tableau d'option à twig pour l'affichage
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
