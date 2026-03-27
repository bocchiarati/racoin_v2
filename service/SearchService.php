<?php

namespace service;

use Illuminate\Database\Eloquent\Collection;
use model\Annonce;
use model\Categorie;

class SearchService
{
    /**
     * @param string $motcle
     * @param string $postcode
     * @param string $categorie
     * @param float $min_price
     * @param float $max_price
     * @return Collection
     */
    function applyFiltre(string $motcle, string $postcode, string $categorie, float $min_price, float $max_price) : Collection
    {
        $nospace_mc = str_replace(' ', '', $motcle);
        $nospace_cp = str_replace(' ', '', $postcode);

        $query = Annonce::query(); // Utilisation de query() pour démarrer le Builder

        // On définit les états par défaut basés sur des nombres (0.0 signifie "pas de limite")
        $isDefaultCategory = in_array($categorie, ["Toutes catégories", "-----"]);
        $isDefaultPrice = ($min_price <= 0) && ($max_price <= 0);

        // Si tout est vide, on renvoie tout
        if (empty(trim($nospace_mc)) && empty(trim($nospace_cp)) && $isDefaultCategory && $isDefaultPrice) {
            return Annonce::all();
        }

        // Filtre Mot-clé
        if ($nospace_mc !== "") {
            $query->where('description', 'like', '%' . $motcle . '%');
        }

        // Filtre Code Postal (Ville)
        if ($nospace_cp !== "") {
            $query->where('ville', '=', $postcode);
        }

        // Filtre Catégorie
        if (!$isDefaultCategory) {
            $query->where('id_categorie', '=', $categorie);
        }

        // Filtre Prix (Logique numérique)
        if ($min_price > 0 && $max_price > 0) {
            // Tranche de prix
            $query->whereBetween('prix', [$min_price, $max_price]);
        } elseif ($min_price > 0) {
            // Uniquement prix mini
            $query->where('prix', '>=', $min_price);
        } elseif ($max_price > 0) {
            // Uniquement prix maxi
            $query->where('prix', '<=', $max_price);
        }

        return $query->get();
    }
}