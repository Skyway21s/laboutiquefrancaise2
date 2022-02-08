<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderValidateController extends AbstractController
{
    // On a besoin de l'entityManager pour récupérer toutes les données de notre objet
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }



    #[Route('/commande/merci/{stripeSessionId}', name: 'order_validate')]
    public function index($stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        // Sécurisation URL (order n'existe pas ou si la commande générer n'est pas du bon utilisateur).
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // modifier le statut isPaid de notre commande en mettant 1

        if(!$order->getIsPaid()) {
            $order->setIspaid(1);
            $this->entityManager->flush();
        }

        // envoyer un emeil à notre client pour lui confirmer sa commande




        return $this->render('order_validate/index.html.twig', [
            // Afficher les quelques informations de la commande de l'utilisateur
             'order' => $order
        ]);
    }
}
