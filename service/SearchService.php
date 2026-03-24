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

        $query = Annonce::select();

        $isEmptySearch = empty(trim($nospace_mc)) && empty(trim($nospace_cp));

        $isDefaultCategory = in_array($categorie, ["Toutes catégories", "-----"]);

        $isDefaultPrice = $min_price == "Min" &&
            in_array($max_price, ["Max", "nolimit"]);

        if ($isEmptySearch && $isDefaultCategory && $isDefaultPrice) {
            return Annonce::all();
        }
            // A REFAIRE SÉPARER LES TRUCS
        if( ($nospace_mc !== "") ) {
            $query->where('description', 'like', '%'.$motcle.'%');
        }

        if( ($nospace_cp !== "") ) {
            $query->where('ville', '=', $postcode);
        }

        if ( ($categorie !== "Toutes catégories" && $categorie !== "-----") ) {
            $categ = Categorie::select('id_categorie')->where('id_categorie', '=', $categorie)->first()->id_categorie;
            $query->where('id_categorie', '=', $categ);
        }

        if ( $min_price != "Min" && $max_price != "Max") {
            if($max_price != "nolimit") {
                $query->whereBetween('prix', array($min_price, $max_price));
            } else {
                $query->where('prix', '>=', $min_price);
            }
        } elseif ( $max_price != "Max" && $max_price != "nolimit") {
            $query->where('prix', '<=', $max_price);
        } elseif ( $min_price != "Min" ) {
            $query->where('prix', '>=', $min_price);
        }

        return $query->get();

    }
}