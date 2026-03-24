<?php

namespace controller;

use service\ApiKeyService;

class KeyGeneratorController {
    private ApiKeyService $apiKeyService;
    public function __construct()
    {
        $this->apiKeyService = new ApiKeyService();
    }


    function show($twig, $menu, $chemin, $cat) {
        $template = $twig->load("key-generator.html.twig");
        $menu = array(
            array('href' => $chemin,
                'text' => 'Acceuil'),
            array('href' => $chemin."/search",
                'text' => "Recherche")
        );
        echo $template->render(array("breadcrumb" => $menu, "chemin" => $chemin, "categories" => $cat));
    }

    function generateKey($twig, $menu, $chemin, $cat, $nom) {
        $menu = array(
            array('href' => $chemin,
                'text' => 'Acceuil'),
            array('href' => $chemin."/search",
                'text' => "Recherche")
        );

        if($this->apiKeyService->checkName($nom)) {
            $template = $twig->load("key-generator-error.html.twig");
            echo $template->render(array("breadcrumb" => $menu, "chemin" => $chemin, "categories" => $cat));
            return;
        }

        $template = $twig->load("key-generator-result.html.twig");
        $key = $this->apiKeyService->generateKey($nom);
        echo $template->render(array("breadcrumb" => $menu, "chemin" => $chemin, "categories" => $cat, "key" => $key));
    }

}
