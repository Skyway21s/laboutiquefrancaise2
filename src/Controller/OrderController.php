<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    // On a besoin de l'entityManager pour récupérer toutes les données de notre objet
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/commande', name: 'order')]
    public function index(Cart $cart, Request $request): Response
    {
        // Si l'utilisateur connecté n'a pas renseigné d'adresse on le redirige vers la page d'ajout d'adresse.
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('account_address_add');
        }

        // on crée notre formulaire lié à la classe order et l'utilisateur connecté(sinon récupère toutes les adresses)
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'order_recap', methods: 'POST')]
    public function add(Cart $cart, Request $request): Response
    {


        // on crée notre formulaire lié à la classe order et l'utilisateur connecté(sinon récupère toutes les adresses)
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère la date
            $date = new \DateTimeImmutable();
            // on récupère le transporteur
            $carriers = $form->get('carriers')->getData();
            // On construit l'adresse de livraison
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname() . '' . $delivery->getLastname();
            $delivery_content .= '<br/>' . $delivery->getPhone();

            if ($delivery->getCompany()) {
                $delivery_content .= '<br/>' . $delivery->getCompany();
            }
            $delivery_content .= '<br/>' . $delivery->getAddress();
            $delivery_content .= '<br/>' . $delivery->getPostal() . ' ' . $delivery->getCity();
            $delivery_content .= '<br/>' . $delivery->getCountry();


            // Enregistrer ma commande Order()
            $order = new Order();
            $reference = $date->format('dmY') . '-' . uniqid();
            $order->setReference($reference);
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);

            $this->entityManager->persist($order);


            // Pour chaque produit du panier on veut une nouvelle entrée dans order-details et les liés les deux entity
            // Enregistrer mes produits OrderDetails()
            foreach ($cart->getFull() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $this->entityManager->persist($orderDetails);
            }


            $this->entityManager->flush();


            // le return rentre dans le if au cas où l'adresse est rentré sans passé par le chemin de paiement
            return $this->render('order/add.html.twig', [

                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
                'reference' => $order->getReference()


            ]);


        }
        // si
        return $this->redirectToRoute('cart');
    }
}
