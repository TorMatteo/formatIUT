<?php

namespace App\FormatIUT\Service;

use App\FormatIUT\Configuration\Configuration;
use App\FormatIUT\Controleur\ControleurMain;
use App\FormatIUT\Lib\ConnexionUtilisateur;
use App\FormatIUT\Lib\FiltresSQL;
use App\FormatIUT\Lib\MessageFlash;
use App\FormatIUT\Lib\PrivilegesUtilisateursRecherche;
use App\FormatIUT\Lib\Recherche\AffichagesRecherche\DefaultRecherche;
use App\FormatIUT\Modele\Repository\AbstractRepository;
use DOMDocument;
use newrelic\DistributedTracePayload;

class ServiceRecherche
{

    /**
     * @return void permet à l'utilisateur de rechercher grâce à la barre de recherche
     */
    public static function rechercher(): void
    {
        if (self::verifRecherche()) {
            $recherche = $_REQUEST['recherche'];
            $morceaux = explode(" ", $recherche);
            $liste = array();
            $count = 0;
            $privilege = ConnexionUtilisateur::getUtilisateurConnecte()->getFiltresRecherche();
            $sansfiltres = true;
            foreach ($privilege as $item => $value) {
                if (isset($_REQUEST[$item . "s"])) {
                    $sansfiltres = false;
                }
            }
            foreach ($privilege as $repository => $filtres) {
                $listeFiltres = self::filtresFunction($repository, $filtres);
                if (isset($_REQUEST[$repository . 's']) || $sansfiltres) {
                    $nomDeClasseRepository = "App\FormatIUT\Modele\Repository\\" . $repository . "Repository";
                    $re = "recherche";
                    if (class_exists($nomDeClasseRepository)) {
                        $element = (new $nomDeClasseRepository)->$re($morceaux, $listeFiltres);

                        $arrayRecherche = array();
                        $nomAffichageRecherche = "App\FormatIUT\Lib\Recherche\AffichagesRecherche\\" . $repository . "Recherche";
                        foreach ($element as $objet) {
                            if (class_exists($nomAffichageRecherche)) {
                                $arrayRecherche[] = new $nomAffichageRecherche($objet);
                            }else {
                                $arrayRecherche[]=new DefaultRecherche($objet);
                            }
                        }
                        $liste[$repository] = $arrayRecherche;
                        $count += sizeof($liste[$repository]);
                    }
                }
            }

            MessageFlash::ajouter("success", "$count résultats trouvés.");
            $_REQUEST["recherche"] = $recherche;
            $_REQUEST["liste"] = $liste;
            $_REQUEST["count"] = $count;
            ControleurMain::afficherRecherche();
        } else {
            header("Location: $_SERVER[HTTP_REFERER]");;
        }
    }

    /**
     * @param string $repository le type de l'élément recherchable
     * @param array $filtres la liste des filtres
     * @return array regroupe les filtres actif et exécute leurs fonctions dans les classes FiltresSQL
     */
    private static function filtresFunction(string $repository, array $filtres): array
    {
        $listeFiltres = array();
        foreach ($filtres as $filtre) {
            if (in_array("obligatoire", $filtre) || isset($_REQUEST[$filtre["value"]])) {
                $fonction = $filtre["value"];
                $nomDeClasseFiltre = "App\FormatIUT\Lib\Recherche\FiltresSQL\Filtres" . $repository;
                if (class_exists($nomDeClasseFiltre)) {
                    $listeFiltres[] = $nomDeClasseFiltre::$fonction();
                }
            }
        }
        return $listeFiltres;
    }

    /**
     * @return bool vérifie si la recherche est valide
     */
    private static function verifRecherche(): bool
    {
        if (!isset($_REQUEST['recherche'])) {
            MessageFlash::ajouter("warning", "Veuillez renseigner une recherche.");
            return false;
        }
        ////si la recherche ne contient que un ou des espaces
        if (preg_match('/^\s+$/', $_REQUEST['recherche'])) {
            MessageFlash::ajouter("warning", "Veuillez renseigner une recherche valide.");
            return false;
        }
        return true;
    }

}