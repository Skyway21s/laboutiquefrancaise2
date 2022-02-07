<?php

namespace App\Classe;

use App\Entity\Category;

//On crée la classe search qui représente mon objet de recherche
class Search
{
    // on utilise des publics pour simplifier notre code
    /**
     * @var string
     */
    public $string = '';
    /**
     * @var Category[]
     */
    public array $categories = [];

    /**
     * @return string
     */
    public function __toString()
    {
        return '';
    }




}