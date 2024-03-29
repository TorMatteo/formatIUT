<?php

namespace App\FormatIUT\Lib\Users;

use App\FormatIUT\Configuration\Configuration;
use App\FormatIUT\Controleur\ControleurAdminMain;
use App\FormatIUT\Controleur\ControleurMain;
use App\FormatIUT\Lib\ConnexionUtilisateur;
use App\FormatIUT\Lib\Users\Utilisateur;
use App\FormatIUT\Modele\Repository\ProfRepository;

class Personnels extends Utilisateur
{

    /**
     * @return string le controleur de l'utilisateur connecté
     */
    public function getControleur(): string
    {
        return "AdminMain";
    }

    /**
     * @return string l'image de profil de l'utilisateur connecté
     */
    public function getImageProfil():string
    {
        $prof=(new ProfRepository())->getParLogin($this->getLogin());
        return Configuration::getUploadPathFromId($prof->getImg());
    }

    /**
     * @return string le type de connexion de l'utilisateur connecté
     */
    public function getTypeConnecte(): string
    {
        return "Personnels";
    }

    /**
     * @return array le menu présent dans le bandeau latéral du site.
     */
        public function getMenu(): array
    {
        $menu=$this->getDebutMenu();
        $menu[]=$this->getFinMenu();
        return $menu;
    }

    /**
     * @return array le menu présent dans le bandeau latéral du site.
     */
    protected function getDebutMenu()
    {
        $accueil = ConnexionUtilisateur::getTypeConnecte();
        $menu = array(
            array("image" => "../ressources/images/accueil.png", "label" => "Accueil $accueil", "lien" => "?action=afficherAccueilAdmin&controleur=AdminMain"),
            array("image" => "../ressources/images/etudiants.png", "label" => "Liste Étudiants", "lien" => "?action=afficherListeEtudiant&controleur=AdminMain"),
            array("image" => "../ressources/images/liste.png", "label" => "Liste des Offres", "lien" => "?action=afficherListeOffres&controleur=AdminMain"),
            array("image" => "../ressources/images/entreprise.png", "label" => "Liste Entreprises", "lien" => "?action=afficherListeEntreprises&controleur=AdminMain"),
        );

        if (ControleurAdminMain::getPageActuelleAdmin() == "Détails de l'enseignant") {
            $menu[] = array("image" => "../ressources/images/profil.png", "label" => "Détails de l'enseignant", "lien" => "?action=afficherAccueilAdmin&controleur=AdminMain");
        }

        if (ControleurAdminMain::getPageActuelleAdmin() == "Modifier un étudiant") {
            $menu[] = array("image" => "../ressources/images/parametres.png", "label" => "Modifier un étudiant", "lien" => "?action=afficherAccueilAdmin&controleur=AdminMain");
        }

        if (ControleurAdminMain::getPageActuelleAdmin() == "Modifier une Offre") {
            $menu[] = array("image" => "../ressources/images/parametres.png", "label" => "Modifier une Offre", "lien" => "?action=afficherAccueilAdmin&controleur=AdminMain");
        }

        if (ControleurAdminMain::getPageActuelleAdmin() == "Modifier une entreprise") {
            $menu[] = array("image" => "../ressources/images/parametres.png", "label" => "Modifier une entreprise", "lien" => "?action=afficherAccueilAdmin&controleur=AdminMain");
        }


        if (ControleurAdminMain::getPageActuelleAdmin() == "Détails de l'offre") {
            $menu[] = array("image" => "../ressources/images/emploi.png", "label" => "Détails de l'offre", "lien" => "?action=afficherAccueilAdmin&controleur=AdminMain");
        }

        if (ControleurAdminMain::getPageActuelleAdmin() == "Mon Compte") {
            $menu[] = array("image" => "../ressources/images/profil.png", "label" => "Mon Compte", "lien" => "?action=afficherProfil&controleur=AdminMain");
        }

        if (ControleurAdminMain::getPageActuelleAdmin() == "Détails d'un Étudiant") {
            $menu[] = array("image" => "../ressources/images/profil.png", "label" => "Détails d'un Étudiant", "lien" => "?action=afficherDetailEtudiant&numEtudiant=$_GET[numEtudiant]");
        }

        if (ControleurAdminMain::getPageActuelleAdmin() == "Détails d'une Entreprise") {
            $menu[] = array("image" => "../ressources/images/equipe.png", "label" => "Détails d'une Entreprise", "lien" => "?action=afficherDetailEntreprise&siret=$_GET[siret]");
        }

        if (ControleurMain::getPageActuelle() == "Résultats de la recherche") {
            $menu[] = array("image" => "../ressources/images/rechercher.png", "label" => "Résultats de la recherche", "lien" => "?action=afficherAccueilAdmin&controleur=AdminMain");
        }

        return $menu;
    }
    protected function getFinMenu()
    {
        return array("image" => "../ressources/images/se-deconnecter.png", "label" => "Se déconnecter", "lien" => "?action=seDeconnecter&controleur=Main");
    }

    /**
     * @return array[] retourne l'ensemble des filtres appliquables pour un admin, rangés par éléments recherchables
     */

    public function getFiltresRecherche(): array
    {
        return array(

            "Entreprise"=>array(
                "filtre1"=>array("label"=>"Entreprises Validées","value"=>"entreprise_validee"),
                "filtre2"=>array("label"=>"Entreprises Non Validées","value"=>"entreprise_non_validee"),
            ),
            "Etudiant"=>array(
                "filtre3"=>array("label"=>"Etudiants A1","value"=>"etudiant_A1"),
                "filtre4"=>array("label"=>"Etudiants A2","value"=>"etudiant_A2"),
                "filtre5"=>array("label"=>"Etudiants A3","value"=>"etudiant_A3"),
                "filtre6"=>array("label"=>"Etudiants Avec Formation","value"=>"etudiant_avec_formation"),
                "filtre7"=>array("label"=>"Etudiants Sans Formation","value"=>"etudiant_sans_formation"),
                "filtre8"=>array("label"=>"Stagiaires","value"=>"etudiant_stage"),
                "filtre9"=>array("label"=>"Alternants","value"=>"etudiant_alternance"),
            ),
            "Formation"=>array(
                "filtre10"=>array("label"=>"Stages","value"=>"formation_stage"),
                "filtre11"=>array("label"=>"Alternances","value"=>"formation_alternance"),
                "filtre12"=>array("label"=>"Formations Validées","value"=>"formation_validee"),
                "filtre13"=>array("label"=>"Formations Non Validées","value"=>"formation_non_validee"),
                "filtre17"=>array("label"=>"Formations Disponibles","value"=>"formation_disponible"),
                "filtre18"=>array("label"=>"Formations Non Disponibles","value"=>"formation_non_disponible")
            ),
            "Prof"=>array(
                "filtre14"=>array("label"=>"Profs","value"=>"personnel_prof"),
                "filtre15"=>array("label"=>"Administrateurs","value"=>"personnel_admin"),
                "filtre16"=>array("label"=>"Secretariat","value"=>"personnel_secretariat")
            )
        );
    }
}