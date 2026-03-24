<?php

namespace controller;

use model\Annonce;
use model\Categorie;
use service\SearchService;

class Search {
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
            array('href' => $chemin,
                'text' => 'Acceuil'),
            array('href' => $chemin."/search",
                'text' => "Résultats de la recherche")
        );

        $annonces = $this->searchService->applyFiltre($array["motcle"], $array["postcode"], $array["categorie"], $array["min_price"], $array["max_price"]);

        echo $template->render(array("breadcrumb" => $menu, "chemin" => $chemin, "annonces" => $annonces, "categories" => $cat));

    }

}
