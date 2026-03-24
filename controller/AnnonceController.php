<?php

namespace controller;

use model\Annonce;
use model\Photo;
use model\Annonceur;
use service\AnnonceService;

class AnnonceController
{
    private AnnonceService $annonceService;

    public function __construct()
    {
        $this->annonceService = new AnnonceService();
    }

    public function displayAllAnnonce($twig, $menu, $chemin, $cat)
    {
        $template = $twig->load("index.html.twig");
        $menu     = array(
            array(
                'href' => $chemin,
                'text' => 'Acceuil'
            ),
        );

        echo $template->render(array(
            "breadcrumb" => $menu,
            "chemin"     => $chemin,
            "categories" => $cat,
            "annonces"   => $this->annonceService->getAll()
        ));
    }
}
