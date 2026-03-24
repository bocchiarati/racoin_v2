<?php

namespace service;

use model\Annonce;
use model\Annonceur;
use model\Photo;

class AnnonceService
{
    public function getAll()
    {
        $temp_annonces    = Annonce::with("Annonceur")->orderBy('id_annonce', 'desc')->take(12)->get();
        $annonces = [];
        foreach ($temp_annonces as $annonce) {
            $annonce->nb_photo = Photo::where("id_annonce", "=", $annonce->id_annonce)->count();
            if ($annonce->nb_photo > 0) {
                $annonce->url_photo = Photo::select("url_photo")
                    ->where("id_annonce", "=", $annonce->id_annonce)
                    ->first()->url_photo;
            } else {
                $annonce->url_photo = '/img/noimg.png';
            }
            $annonce->nom_annonceur = Annonceur::select("nom_annonceur")
                ->where("id_annonceur", "=", $annonce->id_annonceur)
                ->first()->nom_annonceur;
            array_push($annonces, $annonce);
        }
        return $annonce;
    }
}