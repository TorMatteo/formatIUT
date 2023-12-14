<?php

namespace App\FormatIUT\Service;

use App\FormatIUT\Controleur\ControleurAdminMain;
use App\FormatIUT\Controleur\ControleurEtuMain;
use App\FormatIUT\Lib\ConnexionUtilisateur;
use App\FormatIUT\Modele\DataObject\Etudiant;
use App\FormatIUT\Modele\Repository\EtudiantRepository;

class ServiceEtudiant
{
    /**
     * @return void permet à l'admin connecté d'ajouter un étudiant avec ses informations primordiales
     */

    public static function ajouterEtudiant(): void
    {
        if (ConnexionUtilisateur::getTypeConnecte() == "Administrateurs") {
            if ((new EtudiantRepository())->getObjectParClePrimaire($_REQUEST['numEtudiant']) != null) {
                ControleurAdminMain::redirectionFlash("afficherFormulaireCreationEtudiant", "warning", "Un étudiant avec ce numéro existe déjà");
            } else {
                $etudiant=Etudiant::creerEtudiant($_REQUEST);
                (new EtudiantRepository())->creerObjet($etudiant);
                ControleurAdminMain::redirectionFlash("afficherAccueilAdmin", "success", "L'étudiant a bien été ajouté");
            }
        }
    }


    /**
     * @return void permet à l'admin connecté de supprimer(archiver) un étudiant
     */
    public static function supprimerEtudiant(): void
    {
        if (isset($_REQUEST["numEtu"])) {
            $etudiant = (new EtudiantRepository())->getObjectParClePrimaire($_REQUEST['numEtu']);
            if (!is_null($etudiant)) {
                if (ConnexionUtilisateur::getTypeConnecte() == "Administrateurs") {
                    (new EtudiantRepository())->supprimer($_REQUEST['numEtu']);
                    ControleurAdminMain::redirectionFlash("afficherListeEtudiant", "success", "L'étudiant a bien été supprimé");
                } else ControleurAdminMain::redirectionFlash("afficherDetailEtudiant", "danger", "Vous n'avez pas les droits requis");
            } else ControleurAdminMain::redirectionFlash("afficherListeEtudiant", "warning", "L'étudiant n'existe pas");
        } else ControleurAdminMain::redirectionFlash("afficherListeEtudiant", "danger", "L'étudiant n'est pas renseigné");

    }

    /**
    * @return void met à jour les informations de l'étudiant connecté
    */
public static function mettreAJour(): void
{
    if (isset($_REQUEST['numEtu'])) {
        if (ConnexionUtilisateur::getTypeConnecte() == "Etudiants" || ConnexionUtilisateur::getTypeConnecte() == "Administrateurs") {
            if (!empty($_FILES['pdp']['name'])) {
                ControleurEtuMain::updateImage();
            }
            (new EtudiantRepository())->mettreAJourInfos($_REQUEST['mailPerso'], $_REQUEST['numTel'], $_REQUEST['numEtu']);
            ControleurEtuMain::redirectionFlash("afficherProfil", "success", "Informations enregistrées");
        } else {
            ControleurEtuMain::redirectionFlash("afficherProfil", "danger", "Vous n'avez pas les droits requis");
        }
    } else {
        ControleurEtuMain::redirectionFlash("afficherProfil", "warning", "Des données sont manquantes");
    }
}


    /**
     * @return void modifie le numéroEtudiant et le sexe lors de la Première Connexion
     */
    public static function setNumEtuSexe(): void
    {
        $ancienNumEtu = $_REQUEST['oldNumEtu'];
        $numEtu = $_REQUEST['numEtu'];
        $sexe = $_REQUEST['sexe'];

        $etudiant = (new EtudiantRepository())->getObjectParClePrimaire($ancienNumEtu);
        $etudiant->setNumEtudiant($numEtu);
        $etudiant->setSexeEtu($sexe);
        (new EtudiantRepository())->modifierNumEtuSexe($etudiant, $ancienNumEtu);
        ControleurEtuMain::afficherAccueilEtu();
        echo "<script>afficherPopupPremiereCo(2)</script>";
    }

    /**
     * @return void modifie le téléphone et le mail perso lors de la Première Connexion
     */
    public static function setTelMailPerso(): void
    {
        $numEtu = $_REQUEST['numEtu'];
        $tel = $_REQUEST['telephone'];
        $mailPerso = $_REQUEST['mailPerso'];

        $etudiant = (new EtudiantRepository())->getObjectParClePrimaire($numEtu);
        $etudiant->setTelephone($tel);
        $etudiant->setMailPerso($mailPerso);
        (new EtudiantRepository())->modifierTelMailPerso($etudiant);
        ControleurEtuMain::afficherAccueilEtu();
        echo "<script>afficherPopupPremiereCo(3)</script>";
    }

    /**
     * @return void modifie le groupe et le parcours lors de la Première Connexion
     */
    public static function setGroupeParcours(): void
    {
        $numEtu = $_REQUEST['numEtu'];
        $groupe = $_REQUEST['groupe'];
        $parcours = $_REQUEST['parcours'];
        $etudiant = (new EtudiantRepository())->getObjectParClePrimaire($numEtu);
        $etudiant->setGroupe($groupe);
        $etudiant->setParcours($parcours);
        (new EtudiantRepository())->modifierGroupeParcours($etudiant);
        ControleurEtuMain::afficherAccueilEtu();
        echo "<script>afficherPopupPremiereCo(4)</script>";
    }
}