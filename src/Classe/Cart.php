<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class Cart
{
    private $stack;
    private $entityManager;

    public function __construct(RequestStack $stack, EntityManagerInterface $entityManager)

    {
        $this->entityManager = $entityManager;
        return $this->stack = $stack;
    }

    public function add($id)
    {

        $session = $this->stack->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }


        $session->set('cart', $cart);
    }

    public function get()
    {
        $methodget = $this->stack->getSession();
        return $methodget->get('cart');
    }

    public function remove()
    {

        $methodremove = $this->stack->getSession();
        return $methodremove->remove('cart');
    }

    public function delete($id)
    {
        $session = $this->stack->getSession();
        $cart = $session->get('cart', []);
        unset($cart[$id]);
        return $session->set('cart', $cart);

    }

    public function decrease($id)
    {
       // on récupère notre panier
        $session = $this->stack->getSession();
        $cart = $session->get('cart', []);

        //je vérifie si la quantité de notre produit est supérieure à 1
        if($cart[$id] > 1){
            //si oui j'enlève à la quantité
            $cart[$id] = $cart[$id] - 1;
            // ou cart[$id] --;
        } else {
            // supprimer mon produit
            unset($cart[$id]);
        }

        $session->set('cart', $cart);
    }

    public function getFull()
    {
        // on créé une variable
        $cartComplete = [];
        // A l'aide de la boucle foreach on récupère toutes les données du produit à la condition d'avoir quelque chose au panier.
        if ($this->get()) {
            foreach ($this->get() as $id => $quantity) {
                // on crée une variable pour sécuriser notre URL.
                $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);
                // si le produit n'existe pas on le supprime avec la function delete
                if (!$product_object) {
                    $this->delete($id);
                    // On sort de cette boucle et on passe au produit suivant en sortant de cette boucle.
                    continue;
                }
                $cartComplete[] = [
                    'product' => $product_object,
                    'quantity' => $quantity
                ];
            }

        }
        return $cartComplete;
    }
}