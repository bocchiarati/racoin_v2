<?php

namespace controller;

use model\Categorie;
use model\Annonce;
use model\Photo;
use model\Annonceur;
use service\CategorieService;

class CategorieController {
    private CategorieService $categorieService;
    public function __construct()
    {
        $this->categorieService = new CategorieService();
    }

    public function displayCategorie($twig, $menu, $chemin, $cat, $n) {
        $template = $twig->load("index.html.twig");
        $menu = array(
            array('href' => $chemin,
                'text' => 'Acceuil'),
            array('href' => $chemin."/cat/".$n,
                'text' => Categorie::find($n)->nom_categorie)
        );

        $annonces = $this->categorieService->getCategorieContent($chemin, $n);
        echo $template->render(array(
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "categories" => $cat,
            "annonces" => $annonces));
    }
}
