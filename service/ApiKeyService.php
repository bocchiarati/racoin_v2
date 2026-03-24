<?php

namespace service;

use model\ApiKey;

class ApiKeyService
{
    function checkName($name)
    {
        $nospace_nom = str_replace(' ', '', $name);
        return $nospace_nom === '';
    }
    function generateKey($nom)
    {
        // Génere clé unique de 13 caractères
        $key = uniqid();
        // Ajouter clé dans la base
        $apikey = new ApiKey();

        $apikey->id_apikey = $key;
        $apikey->name_key = htmlentities($nom);
        $apikey->save();

    }
}