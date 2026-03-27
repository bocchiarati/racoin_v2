<?php

namespace controller;

use model\Annonce;
use model\Categorie;
use service\SearchService;

class SearchController {
    private SearchService $searchService;

    public function __construct()
    {
        $this->searchService = new SearchService();
    }

    function show($twig, $menu, $chemin, $categories) {
        $template = $twig->load("search.html.twig");
        $menu = array(
            array('href' => $chemin,
                'text' => 'Acceuil'),
            array('href' => $chemin."/search",
                'text' => "Recherche")
        );
        echo $template->render(array("breadcrumb" => $menu, "chemin" => $chemin, "categories" => $categories));
    }

    function research($array, $twig, $menu, $chemin, $cat) {
        $template = $twig->load("index.html.twig");
        $menu = array(
            array('href' => $chemin, 'text' => 'Acceuil'),
            array('href' => $chemin."/search", 'text' => "Résultats de la recherche")
        );

        // 1. On récupère les valeurs textuelles
        $motcle    = $array["motcle"] ?? '';
        $postcode  = $array["postcode"] ?? '';
        $categorie = $array["categorie"] ?? 'Toutes catégories';

        // 2. IMPORTANT : On force la conversion en float.
        // Si le champ est vide (''), (float) transformera cela en 0.0
        $min_price = (float)($array["min_price"] ?? 0);
        $max_price = (float)($array["max_price"] ?? 0);

        // 3. Appel du service (les arguments 4 et 5 sont maintenant des nombres)
        $annonces = $this->searchService->applyFiltre(
            $motcle,
            $postcode,
            $categorie,
            $min_price,
            $max_price
        );

        echo $template->render(array(
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonces" => $annonces,
            "categories" => $cat
        ));
    }
}