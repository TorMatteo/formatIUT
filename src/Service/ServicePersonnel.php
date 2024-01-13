<?php

namespace App\FormatIUT\Service;

use App\FormatIUT\Configuration\Configuration;
use App\FormatIUT\Controleur\ControleurAdminMain;
use App\FormatIUT\Lib\ConnexionUtilisateur;
use App\FormatIUT\Modele\DataObject\Annotation;
use App\FormatIUT\Modele\DataObject\Formation;
use App\FormatIUT\Modele\Repository\AnnotationRepository;
use App\FormatIUT\Modele\Repository\EntrepriseRepository;
use App\FormatIUT\Modele\Repository\EtudiantRepository;
use App\FormatIUT\Modele\Repository\FormationRepository;

class ServicePersonnel
{
    /**
     * @return void permet à un admin de promouvoir un prof
     */
    public static function promouvoirProf(): void
    {
        if (isset($_REQUEST["loginProf"])) {
            $prof = (new \App\FormatIUT\Modele\Repository\ProfRepository())->getObjectParClePrimaire($_REQUEST['loginProf']);
            if (!is_null($prof)) {
                if (ConnexionUtilisateur::getTypeConnecte() == "Administrateurs") {
                    if (!$prof->isEstAdmin()) {
                        $prof->setEstAdmin(true);
                        (new \App\FormatIUT\Modele\Repository\ProfRepository())->modifierObjet($prof);
                        ControleurAdminMain::redirectionFlash("afficherProfilAdmin", "success", "Permissions mises à jour");
                    } else ControleurAdminMain::redirectionFlash("afficherProfilAdmin", "warning", "Le professeur est déjà administrateur");
                } else ControleurAdminMain::redirectionFlash("afficherProfilAdmin", "danger", "Vous n'avez pas les droits requis");
            } else ControleurAdminMain::redirectionFlash("afficherProfilAdmin", "warning", "Le professeur n'existe pas");
        } else ControleurAdminMain::redirectionFlash("afficherProfilAdmin", "danger", "Le professeur n'est pas renseigné");
    }

    /**
     * @return void permet à un admin de rétrograder un prof
     */
    public static function retrograderProf(): void
    {
        if (isset($_REQUEST["loginProf"])) {
            $prof = (new \App\FormatIUT\Modele\Repository\ProfRepository())->getObjectParClePrimaire($_REQUEST['loginProf']);
            if (!is_null($prof)) {
                if (ConnexionUtilisateur::getTypeConnecte() == "Administrateurs") {
                    if ($prof->isEstAdmin()) {
                        $prof->setEstAdmin(false);
                        (new \App\FormatIUT\Modele\Repository\ProfRepository())->modifierObjet($prof);
                        ControleurAdminMain::redirectionFlash("afficherProfilAdmin", "success", "Permissions mises à jour");
                    } else ControleurAdminMain::redirectionFlash("afficherProfilAdmin", "warning", "Le professeur n'est pas administrateur");
                } else ControleurAdminMain::redirectionFlash("afficherProfilAdmin", "danger", "Vous n'avez pas les droits requis");
            } else ControleurAdminMain::redirectionFlash("afficherProfilAdmin", "warning", "Le professeur n'existe pas");
        } else ControleurAdminMain::redirectionFlash("afficherProfilAdmin", "danger", "Le professeur n'est pas renseigné");
    }

    /**
     * @return void permet à un prof de se proposer en tant que tuteur sur une formation
     */
    public static function seProposerEnTuteurUM(): void
    {
        if (ConnexionUtilisateur::getTypeConnecte() == "Administrateurs" || ConnexionUtilisateur::getTypeConnecte() == "Personnels") {
            $formation = (new FormationRepository())->trouverOffreDepuisForm($_REQUEST['numEtu']);
            if ($formation) {
                if ($formation->getloginTuteurUM() == null) {
                    $formation->setloginTuteurUM(ConnexionUtilisateur::getLoginUtilisateurConnecte());
                    (new FormationRepository())->modifierObjet($formation);
                    ControleurAdminMain::redirectionFlash("afficherAccueilAdmin", "success", "Vous vous êtes bien proposé en tuteur.");
                } else {
                    ControleurAdminMain::redirectionFlash("afficherAccueilAdmin", "warning", "Cet étudiant a déjà un tuteur de l'UM");
                }
            } else {
                ControleurAdminMain::redirectionFlash("afficherAccueilAdmin", "warning", "Cet étudiant n'a pas de formation");
            }
        } else {
            ControleurAdminMain::redirectionFlash("afficherAccueilAdmin", "danger", "Vous n'êtes pas un enseignant");
        }
    }

    /**
     * @return void
     * <br><br>
     * Passe l'attribut TuteurUMvalide à true;
     */
    public static function validerTuteurUM(): void
    {
        if (!isset($_GET['eleveId'])) {
            ControleurAdminMain::redirectionFlash("afficherListeEtudiant", "danger", "L'élève ID n'est pas renseigné.");
            return;
        }
        $etuID = $_GET['eleveId'];

        if (ConnexionUtilisateur::getTypeConnecte() != "Administrateurs") {
            ControleurAdminMain::redirectionFlash("afficherListeEtudiant", "danger", "Vous n'avez pas les droits nécessaires. Cet incident sera reporté.");
            return;
        }

        /** @var Formation $offreValide */
        $offreValide = (new EtudiantRepository())->getOffreValidee($etuID);
        if (is_null($offreValide)) {
            ControleurAdminMain::redirectionFlash("afficherListeEtudiant", "danger", "L'élève n'existe pas,, ou n'a pas d'offre valide.");
            return;
        }

        $offreValide->setTuteurUMvalide(true);
        (new FormationRepository())->modifierObjet($offreValide);

        ControleurAdminMain::redirectionFlash("afficherListeEtudiant", "success", "Le tuteur est désormais validé !");
    }

    /**
     * @return void
     */
    public static function refuserTuteurUM(): void
    {
        if (!isset($_GET['eleveId'])) {
            ControleurAdminMain::redirectionFlash("afficherListeEtudiant", "danger", "L'élève ID n'est pas renseigné.");
            return;
        }
        $etuID = $_GET['eleveId'];

        /** @var Formation $offreValide */
        $offreValide = (new EtudiantRepository())->getOffreValidee($etuID);
        if (is_null($offreValide)) {
            ControleurAdminMain::redirectionFlash("afficherListeEtudiant", "danger", "L'élève n'existe pas,, ou n'a pas d'offre valide.");
            return;
        }

        $offreValide->setloginTuteurUM(null);
        (new FormationRepository())->modifierObjet($offreValide);

        ControleurAdminMain::redirectionFlash("afficherListeEtudiant", "success", "Le tuteur a été refusé avec succès.");
    }

    public static function ajouterAnnotation()
    {
        if (isset($_REQUEST['idEntreprise']) && isset($_REQUEST['messageAnnotation']) && isset($_REQUEST['dateAnnotation']) && isset($_REQUEST['noteAnnotation'])) {
            $entreprise = (new EntrepriseRepository())->getObjectParClePrimaire($_REQUEST['idEntreprise']);
            if ($entreprise) {
                if (Configuration::getControleurName() == "AdminMain") {
                    if (!(new AnnotationRepository())->aDeposeAnnotation($_REQUEST["idEntreprise"], ConnexionUtilisateur::getLoginUtilisateurConnecte())) {
                        $annotation = (new Annotation($_REQUEST["loginProf"], $_REQUEST['idEntreprise'], $_REQUEST['messageAnnotation'], $_REQUEST['dateAnnotation'], $_REQUEST['noteAnnotation']));
                        (new AnnotationRepository())->creerAnnotationDepuisForm($annotation);
                        ControleurAdminMain::redirectionFlash("afficherAccueilAdmin", "success", "L'annotation a été ajoutée avec succès.");
                    } else {
                        ControleurAdminMain::redirectionFlash("afficherAccueilAdmin", "warning", "Vous avez déjà déposé une annotation pour cette entreprise.");
                    }
                } else {
                    ControleurAdminMain::redirectionFlash("afficherVueDetailOffre", "danger", "Vous n'avez pas les droits requis.");
                }
            } else {
                ControleurAdminMain::redirectionFlash("afficherVueDetailOffre", "danger", "L'entreprise n'existe pas.");
            }
        } else {
            ControleurAdminMain::redirectionFlash("afficherVueDetailOffre", "danger", "Des données sont manquantes.");
        }
    }
}