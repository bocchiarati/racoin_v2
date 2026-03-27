<?php
/**
 * Created by PhpStorm.
 * User: ponicorn
 * Date: 26/01/15
 * Time: 00:25
 */

namespace controller;
use model\Annonceur;
use service\AnnonceService;

class AnnonceurController {
    private AnnonceService $annonceService;
    public function __construct()
    {
        $this->annonceService = new AnnonceService();
    }

    function afficherAnnonceur($twig, $menu, $chemin, $id, $categories) {
        $annonceur = annonceur::find($id);
        if(!isset($annonceur)){
            echo "404";
            return;
        }

        $template = $twig->load("annonceur.html.twig");
        echo $template->render(array('nom' => $annonceur,
            "chemin" => $chemin,
            "annonces" => $this->annonceService->getAnnonceurAnnonces($id, $chemin),
            "categories" => $categories));
    }
}
