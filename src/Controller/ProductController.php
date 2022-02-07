<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    // on crée la private entityManager qui va nous servir via doctrine à chercher nos produits (findAll)

    private $entityManager;/**
 * @param $entityManager
 */public function __construct(EntityManagerInterface $entityManager)
{
    $this->entityManager = $entityManager;
}
    #[Route('/nos-produits', name: 'products')]
    public function index(Request $request): Response
    {

        // on instancie notre classe search
        $search = new Search();
        // on la passe à la  creation du formulaire
        $form = $this->createForm(SearchType::class, $search);
        // on écoute notre formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            //nouvelle méthode qu'on doit créer dans notre repository
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
        } else {

            $products = $this->entityManager->getRepository(Product::class)->findAll();
        }

        return $this->render('product/index.html.twig', [
           'products' => $products,
            'form' => $form->createView()
        ]);
    }

    #[Route('/produit/{slug}', name: 'product')]
    public function show($slug)
    {
        // Ici on a besoin de la methode pour un seul produit
        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);

        // si le produit n'existe pas on redirige vers la page produits
        if (!$product) {
            return $this->redirectToRoute('products');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }


}
