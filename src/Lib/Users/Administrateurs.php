<?php

namespace App\FormatIUT\Lib\Users;

use App\FormatIUT\Lib\ConnexionUtilisateur;
use App\FormatIUT\Lib\Users\Utilisateur;

class Administrateurs extends Personnels
{

    public function getTypeConnecte(): string
    {
        return "Administrateurs";
    }

    /**
     * @return array le menu présent dans le bandeau latéral du site.
     */
    public function getMenu(): array
    {
        $menu = parent::getDebutMenu();
        $menu[] = array("image" => "../ressources/images/csv.png", "label" => "Mes CSV", "lien" => "?action=afficherVueCSV&controleur=AdminMain");
        $menu[]= array("image" => "../ressources/images/document.png", "label"=> "Liste des conventions", "lien" =>"?action=afficherConventionAValider&controleur=AdminMain");
        $menu[] = array("image" => "../ressources/images/stats.png", "label" => "Statistiques", "lien" => "?action=afficherVueStatistiques&controleur=AdminMain");
        $menu[] = parent::getFinMenu();
        return $menu;
    }
}