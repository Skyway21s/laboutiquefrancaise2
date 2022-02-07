<?php

namespace App\Repository;

use App\Classe\Search;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Requête qui me permet de récupérer les produits en fonction de la recherche utilisateur
     * @return Product[]
     */
    public function findWithSearch(Search $search)
    {
        // création de la requête
        $query = $this
            // on l'associe avec la table produit
            ->createQueryBuilder('p')
            // on détermine la selection à l'intérieur de la query
            ->select('c','p')
            // la jointure se fera entre la categorie du produit de la table category
            ->join('p.category', 'c');

        // Lorsque et uniquement lorsque des categories ont été coché on peut filtrer avec un andwhere

        if(!empty($search->categories)) {
            // on récupère la query
            $query = $query
                // on filtre avec l'id
            ->andWhere('c.id IN (:categories)')
                //on lui donne un paramètre et la valeur de categorie
            ->setParameter('categories', $search->categories);
        }

        // dans ce cas on gère la recherche textuelle
        if (!empty($search->string)){
            $query = $query
                // ici on filtre avec le nom du produit
            ->andWhere('p.name LIKE :string')
            ->setParameter('string', "%$search->string%");
        }

        return $query->getQuery()->getResult();

    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
