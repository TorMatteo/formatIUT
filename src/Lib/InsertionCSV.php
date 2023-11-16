<?php

namespace App\FormatIUT\Lib;

use App\FormatIUT\Modele\DataObject\ConventionStage;
use App\FormatIUT\Modele\DataObject\Entreprise;
use App\FormatIUT\Modele\DataObject\Etudiant;
use App\FormatIUT\Modele\DataObject\Formation;
use App\FormatIUT\Modele\DataObject\Ville;
use App\FormatIUT\Modele\Repository\ConventionStageRepository;
use App\FormatIUT\Modele\Repository\EntrepriseRepository;
use App\FormatIUT\Modele\Repository\EtudiantRepository;
use App\FormatIUT\Modele\Repository\FormationRepository;
use App\FormatIUT\Modele\Repository\VilleRepository;
use DateTime;

class InsertionCSV
{

    public static function insererPstage($ligne): void {

        $login = $ligne[2];
        $login .= $ligne[3][0]; //surement à changer si un étudiant à le même nom et le même prenom
        $etudiant = new Etudiant($ligne[1], $ligne[3], $ligne[2], $login, $ligne[42], $ligne[7], $ligne[6], $ligne[5], "XX", "XXXXX", 1, 1, "R1", 0);
        //echo '<pre>'; print_r($etudiant); echo '</pre>';
        (new EtudiantRepository())->creerObjet($etudiant);

        //il faut changer l'effectif,
       $entreprise = new Entreprise($ligne[55], "", $ligne[62], 0, $ligne[65], $ligne[66], "ligne[56]", "V1", 0);
      (new EntrepriseRepository())->creerObjet($entreprise);

        $dateConv1= $ligne[13]; // Chaîne représentant la date
        $dateDebutConv = DateTime::createFromFormat('d/m/Y', $dateConv1);

        $dateConv2 = $ligne[14];
        $dateFinConv = DateTime::createFromFormat("d/m/Y", $dateConv2);

        $conventionValidee = $ligne[28] == "oui" ? 1 : 0;
            $convention = new ConventionStage($ligne[0], $conventionValidee, $dateDebutConv, $dateFinConv, 0, "", $ligne[20], $ligne[37]);
            (new ConventionStageRepository())->creerObjet($convention);

        $dateString = $ligne[13]; // Chaîne représentant la date
        $dateDebut = DateTime::createFromFormat('d/m/Y', $dateString); // Créer un objet DateTime à partir de la chaîne

        $timestampe = $ligne[14];
        $dateFin = DateTime::createFromFormat("d/m/Y", $timestampe);


        $formation = new Formation("F25", $dateDebut, $dateFin, $ligne[1], "TP1", $ligne[55], $ligne[0], 1, 2);
        (new FormationRepository())->creerObjet($formation);

        $ville = new Ville("V23", $ligne[47], $ligne[45]);
        (new VilleRepository())->creerObjet($ville);

        $ville2 = new Ville("V24", $ligne[60], $ligne[59]);
        (new VilleRepository())->creerObjet($ville2);


    }

}