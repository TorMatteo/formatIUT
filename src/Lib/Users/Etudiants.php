<?php

namespace App\FormatIUT\Lib\Users;

use App\FormatIUT\Configuration\Configuration;
use App\FormatIUT\Controleur\ControleurEtuMain;
use App\FormatIUT\Controleur\ControleurMain;
use App\FormatIUT\Modele\DataObject\AbstractDataObject;
use App\FormatIUT\Modele\Repository\EtudiantRepository;
use App\FormatIUT\Modele\Repository\FormationRepository;
use App\FormatIUT\Modele\Repository\PostulerRepository;
use App\FormatIUT\Modele\Repository\UploadsRepository;

class Etudiants extends Utilisateur
{

    public function getRecherche(): array
    {
        return array(
            "Formation",
            "Entreprise"
        );
    }

    public function getControleur(): string
    {
        return "EtuMain";
    }

    public function getImageProfil()
    {
        $etu=(new EtudiantRepository())->getObjectParClePrimaire((new EtudiantRepository())->getNumEtudiantParLogin($this->getLogin()));

        return Configuration::getUploadPathFromId($etu->getImg());
    }

    public function getTypeConnecte(): string
    {
        return "Etudiants";
    }
    /**
     * @return array[] qui représente le contenu du menu dans le bandeauDéroulant
     */
    public function getMenu(): array
    {
        $etu=(new EtudiantRepository())->getObjectParClePrimaire((new EtudiantRepository())->getNumEtudiantParLogin($this->getLogin()));

        $menu = array(
            array("image" => "../ressources/images/accueil.png", "label" => "Accueil Etudiants", "lien" => "?action=afficherAccueilEtu&controleur=EtuMain"),
            array("image" => "../ressources/images/stage.png", "label" => "Offres de Stage/Alternance", "lien" => "?action=afficherCatalogue&controleur=EtuMain"),
            array("image" => "../ressources/images/signet.png", "label" => "Mes Offres", "lien" => "?action=afficherMesOffres&controleur=EtuMain"),
        );

        $formation = (new EtudiantRepository())->aUneFormation($etu->getNumEtudiant());
        if ($formation && ControleurEtuMain::getTitrePageActuelleEtu() != "Détails de l'offre") {
            $menu[] = array("image" => "../ressources/images/mallette.png", "label" => " Mon Offre", "lien" => "?action=afficherVueDetailOffre&controleur=EtuMain&idFormation=" . $formation['idFormation']);
        }
        if (ControleurEtuMain::getTitrePageActuelleEtu() == "Mon Compte") {
            $menu[] = array("image" => "../ressources/images/profil.png", "label" => "Mon Compte", "lien" => "?action=afficherProfil&controleur=EtuMain");
        }

        if (ControleurEtuMain::getTitrePageActuelleEtu() == "Détails de l'offre") {
            $menu[] = array("image" => "../ressources/images/mallette.png", "label" => "Détails de l'offre", "lien" => "?afficherVueDetailOffre&controleur=EtuMain&idFormation=".$_REQUEST['idFormation']);
        }

        $offre = (new FormationRepository())->trouverOffreDepuisForm($etu->getNumEtudiant());
        if ($offre && $offre->getDateCreationConvention() == null) {
            $offreValidee = (new PostulerRepository())->getOffreValider($etu->getNumEtudiant());
            if ($offreValidee) {
                $offre = (new FormationRepository())->getObjectParClePrimaire($offreValidee->getidFormation());
                if ($offre->getTypeOffre() == "Stage")
                    $menu[] = array("image" => "../ressources/images/document.png", "label" => "Remplir ma convention"
                    , "lien" => "?controleur=EtuMain&action=afficherFormulaireConventionStage");
                else if ($offre->getTypeOffre() == "Alternance")
                    $menu[] = array("image" => "../ressources/images/document.png", "label" => "Ma convention alternance", "lien" => "?controleur=EtuMain&action=afficherFormulaireConventionAlternance");
            }
        } else if ($offre!= false && $offre->getDateCreationConvention() != null) {
            $menu[] = array("image" => "../ressources/images/document.png", "label" => "Ma convention", "lien" => "?controleur=EtuMain&action=afficherMaConvention");
        }

        $menu[] = array("image" => "../ressources/images/se-deconnecter.png", "label" => "Se déconnecter", "lien" => "?action=seDeconnecter&controleur=Main");
        return $menu;
    }
}