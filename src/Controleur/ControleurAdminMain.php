<?php

namespace App\FormatIUT\Controleur;

use App\FormatIUT\Configuration\Configuration;
use App\FormatIUT\Lib\ConnexionUtilisateur;
use App\FormatIUT\Lib\MessageFlash;
use App\FormatIUT\Lib\TransfertImage;
use App\FormatIUT\Modele\Repository\ConventionEtat;
use App\FormatIUT\Modele\Repository\EntrepriseRepository;
use App\FormatIUT\Modele\Repository\EtudiantRepository;
use App\FormatIUT\Modele\Repository\FormationRepository;
use App\FormatIUT\Modele\Repository\ProfRepository;
use App\FormatIUT\Modele\Repository\UploadsRepository;
use App\FormatIUT\Modele\Repository\VilleRepository;
use App\FormatIUT\Service\ServiceConvention;
use App\FormatIUT\Service\ServiceEntreprise;
use App\FormatIUT\Service\ServiceEtudiant;
use App\FormatIUT\Service\ServiceFichier;
use App\FormatIUT\Service\ServiceFormation;
use App\FormatIUT\Service\ServicePersonnel;

class ControleurAdminMain extends ControleurMain
{
    private static string $pageActuelleAdmin = "Accueil Admin";


    /**
     * @return string
     */
    public static function getPageActuelleAdmin(): string
    {
        return self::$pageActuelleAdmin;
    }

    //FONCTIONS D'AFFICHAGES ---------------------------------------------------------------------------------------------------------------------------------------------

    /**
     * @return void affiche l'accueil pour un Administrateur connecté
     */
    public static function afficherAccueilAdmin(): void
    {
        $listeEtudiants = (new EtudiantRepository())->etudiantsSansOffres();
        $listeEntreprises = (new EntrepriseRepository())->entreprisesNonValide();
        $listeOffres = (new FormationRepository())->offresNonValides();
        $listeFomations = (new FormationRepository())->etudiantsSansConventionsValides();
        $accueil = ConnexionUtilisateur::getTypeConnecte();
        self::$pageActuelleAdmin = "Accueil Administrateurs";
        self::afficherVue("Accueil $accueil", "Admin/vueAccueilAdmin.php", ["listeEntreprises" => $listeEntreprises, "listeOffres" => $listeOffres, "listeEtudiants" => $listeEtudiants, "listeFormations" => $listeFomations]);
    }


    /**
     * @return void affiche le profil de l'administrateur connecté
     */
    public static function afficherProfil(): void
    {
        self::$pageActuelleAdmin = "Mon Compte";
        self::afficherVue("Mon Compte", "Admin/vueCompteAdmin.php");
    }

    /**
     * @return void affiche les informations d'un étudiant
     */
    public static function afficherDetailEtudiant(): void
    {
        $aFormation = (new FormationRepository())->trouverOffreDepuisForm($_REQUEST['numEtudiant']);
        self::$pageActuelleAdmin = "Détails d'un Étudiant";
        self::afficherVue("Détails d'un Étudiant", "Admin/vueDetailEtudiant.php", ["aFormation" => $aFormation]);
    }

    /**
     * @return void affiche la liste des étudiants
     */
    public static function afficherListeEtudiant(): void
    {
        $listeEtudiants = (new EtudiantRepository())->etudiantsEtats();
        self::$pageActuelleAdmin = "Liste Étudiants";
        self::afficherVue("Liste Étudiants", "Admin/vueListeEtudiants.php", ["listeEtudiants" => $listeEtudiants]);
    }

    /**
     * @return void affiche les informations d'une entreprise
     */
    public static function afficherDetailEntreprise(): void
    {
        self::$pageActuelleAdmin = "Détails d'une Entreprise";
        self::afficherVue("Détails d'une Entreprise", "Admin/vueDetailEntreprise.php", [
            "entreprise" => $entreprise = (new EntrepriseRepository())->getObjectParClePrimaire($_GET["siret"])
        ]);
    }

    /**
     * @return void affiche la liste des entreprises
     */
    public static function afficherListeEntreprises(): void
    {
        $listeEntreprises = (new EntrepriseRepository())->getListeObjet();
        self::$pageActuelleAdmin = "Liste Entreprises";
        self::afficherVue("Liste Entreprises", "Admin/vueListeEntreprises.php", ["listeEntreprises" => $listeEntreprises]);
    }

    /**
     * @return void affiche la page d'importation et d'exportation des csv
     */
    public static function afficherVueCSV(): void
    {
        self::$pageActuelleAdmin = "Mes CSV";
        self::afficherVue("Mes CSV", "Admin/vueCSV.php");
    }

    /**
     * @return void affiche la liste des offresc
     */

    public static function afficherListeOffres(): void
    {
        $listeOffres = (new FormationRepository())->getListeObjet();
        self::$pageActuelleAdmin = "Liste des Offres";
        self::afficherVue("Liste des Offres", "Admin/vueListeOffres.php", ["listeOffres" => $listeOffres]);
    }

    /**
     * @return void affiche la page d'ajout d'un étudiant
     */

    public static function afficherFormulaireCreationEtudiant(): void
    {
        self::$pageActuelleAdmin = "Ajouter un étudiant";
        self::afficherVue("Ajouter un étudiant", "Admin/vueFormulaireCreationEtudiant.php");
    }

    /**
     * @param string|null $idFormation l'id de la formation dont on affiche le detail
     * @return void affiche le détail d'une offre
     */

    public static function afficherVueDetailOffre(string $idFormation = null): void
    {
        if (!isset($_REQUEST['idFormation']) && is_null($idFormation))
            parent::afficherErreur("Il faut préciser la formation");

        self::$pageActuelleAdmin = "Détails de l'offre";
        /** @var ControleurMain $menu */
        $menu = Configuration::getCheminControleur();
        $liste = (new FormationRepository())->getListeidFormations();
        if ($idFormation || isset($_REQUEST["idFormation"])) {
            if (!$idFormation) $idFormation = $_REQUEST['idFormation'];
            if (in_array($idFormation, $liste)) {
                $offre = (new FormationRepository())->getObjectParClePrimaire($_REQUEST['idFormation']);
                $entreprise = (new EntrepriseRepository())->getObjectParClePrimaire($offre->getIdEntreprise());
                $client = "Admin";
                $chemin = ucfirst($client) . "/vueDetailOffre" . ucfirst($client) . ".php";
                self::afficherVue("Détails de l'offre", $chemin, ["offre" => $offre, "entreprise" => $entreprise]);
            } else {
                self::redirectionFlash("afficherListeOffres", "danger", "Cette offre n'existe pas");
            }
        } else {
            self::redirectionFlash("afficherListeOffres", "danger", "L'offre n'est pas renseignée");
        }
    }

    /**
     * @return void affiche le formulaire pour que les admins puissent modifier les informations d'un étudiant
     */

    public static function afficherFormulaireModifEtudiant(): void
    {
        if (ConnexionUtilisateur::getTypeConnecte() == "Administrateurs") {
            self::$pageActuelleAdmin = "Modifier un étudiant";
            self::afficherVue("Modifier un étudiant", "Admin/vueFormulaireModificationEtudiant.php");
        } else {
            self::redirectionFlash("afficherDetailEtudiant", "danger", "Vous ne pouvez pas accéder à cette page");
        }
    }

    /**
     * @return void affiche le formulaire pour que les admins puissent modifier les informations d'une offre
     */
    public static function afficherFormModificationOffre(): void
    {
        if (ConnexionUtilisateur::getTypeConnecte() != "Administrateurs") {
            self::redirectionFlash("afficherListeOffres", "danger", "Vous n'avez pas les droits. Cet incident sera reporté.");
            return;
        }
        if (!isset($_REQUEST['idFormation'])) {
            self::redirectionFlash("afficherListeOffres", "danger", "L'offre n'est pas renseignée");
            return;
        }
        $offre = (new FormationRepository())->getObjectParClePrimaire($_REQUEST['idFormation']);
        self::$pageActuelleAdmin = "Modifier une Offre";
        self::afficherVue("Modifier une Offre", "Entreprise/vueFormulaireModificationOffre.php", [
            'offre' => $offre
        ]);

    }

    /**
     * @return void
     * Affiche la liste des conventions à valider au secrétariat/admin
     */
    public static function afficherConventionAValider(): void
    {
        if (ConnexionUtilisateur::getTypeConnecte() == "Administrateurs" || ConnexionUtilisateur::getTypeConnecte() == "Secretariat") {
            $listeFormations = (new FormationRepository())->getListeObjet();
            self::$pageActuelleAdmin = "Liste des conventions";
            self::afficherVue("Liste des conventions", "Admin/vueListeConventions.php", ["listeFormations" => $listeFormations]);
        } else {
            self::redirectionFlash("afficherAccueilAdmin", "danger", "Vous n'avez pas accès à la liste des conventions à valider");
        }
    }

    /**
     * @return void
     * Affiche en détail la convention de l'étudiant au secrétariat/admin
     */
    public static function afficherDetailConvention(): void
    {
        if (ConnexionUtilisateur::getTypeConnecte() == "Administrateurs" || ConnexionUtilisateur::getTypeConnecte() == "Secretariat") {
            if (isset($_REQUEST['numEtudiant'])) {
                $formation = (new FormationRepository())->trouverOffreDepuisForm($_REQUEST['numEtudiant']);
                if ($formation->getDateCreationConvention() != null) {
                    if (!$formation->getConventionValidee()) {
                        $etudiant = (new EtudiantRepository())->getObjectParClePrimaire($_REQUEST['numEtudiant']);
                        $entreprise = (new EntrepriseRepository())->trouverEntrepriseDepuisForm($_REQUEST['numEtudiant']);
                        $villeEntr = (new VilleRepository())->getObjectParClePrimaire($entreprise->getIdVille());
                        self::afficherVue("Convention à valider", "Etudiant/vueConvention.php", [
                            "etudiant" => $etudiant,
                            "entreprise" => $entreprise,
                            "villeEntr" => $villeEntr,
                            "offre" => $formation,
                            "etat" => ConventionEtat::VisuAdmin
                        ]);
                    } else {
                        self::redirectionFlash("afficherConventionAValider", "danger", "Cette convention est déjà validée");
                    }
                } else {
                    self::redirectionFlash("afficherConventionAValider", "danger", "Cet étudiant n'a pas de convention");
                }
            } else {
                self::redirectionFlash("afficherConventionAValider", "danger", "Cet étudiant n'a pas de formation");
            }
        } else {
            self::redirectionFlash("afficherAccueilAdmin", "danger", "Vous n'êtes pas du secrétariat");
        }
    }

    /**
     * @return void affiche le formulaire pour que les admins puissent modifier les informations d'une entreprise
     */


    public static function afficherFormulaireModifEntreprise(): void
    {
        if (ConnexionUtilisateur::getTypeConnecte() == "Administrateurs") {
            self::$pageActuelleAdmin = "Modifier une entreprise";
            self::afficherVue("Modifier une entreprise", "Admin/vueFormulaireModificationEntreprise.php");
        } else {
            self::redirectionFlash("afficherDetailEntreprise", "danger", "Vous ne pouvez pas accéder à cette page");
        }
    }

    public static function afficherVueProf(): void
    {
        if (ConnexionUtilisateur::getTypeConnecte() == "Administrateurs") {
            self::$pageActuelleAdmin = "Détails de l'enseignant";
            self::afficherVue("Détails de l'enseignant", "Admin/vueDetailProf.php");
        } else {
            self::redirectionFlash("afficherAccueilAdmin", "danger", "Vous ne pouvez pas accéder à cette page");
        }
    }

    /**
     * @return void
     * Affiche la vue présentant les statistiques
     */
    public static function afficherVueStatistiques(): void
    {
        if (ConnexionUtilisateur::getTypeConnecte() == "Administrateurs") {
            self::$pageActuelleAdmin = "Statistiques";
            $stats = ServicePersonnel::recupererStats();
            self::afficherVue("Statistiques", "Admin/vueStatistiques.php", $stats);
        } else {
            self::redirectionFlash("afficherAccueilAdmin", "danger", "Vous ne pouvez pas accéder à cette page");
        }
    }

    /**
     * @return void
     * Affiche la vue présentant quelques historiques
     */
    public static function afficherVueHistorique(): void
    {
        if (ConnexionUtilisateur::getTypeConnecte() == "Administrateurs") {
            self::$pageActuelleAdmin = "Historique";
            $histo = ServicePersonnel::recupererHisto();
            self::afficherVue("Historique", "Admin/vueHistorique.php", $histo);
        } else {
            self::redirectionFlash("afficherAccueilAdmin", "danger", "Vous ne pouvez pas accéder à cette page");
        }
    }

    //APPEL AUX SERVICES -------------------------------------------------------------------------------------------------------------------------------------------------------

    public static function mettreAJour(): void
    {
        ServicePersonnel::mettreAJour();
    }

    public static function modifierOffre(): void
    {
        ServiceFormation::modifierOffre();
    }

    public static function modifierEtudiant(): void
    {
        ServiceEtudiant::modifierEtudiant();
    }

    public static function ajouterEtudiant(): void
    {
        ServiceEtudiant::ajouterEtudiant();
    }

    public static function rejeterFormation(): void
    {
        ServiceFormation::rejeterFormation();
    }

    public static function accepterFormation(): void
    {
        ServiceFormation::accepterFormation();
    }

    public static function supprimerFormation(): void
    {
        ServiceFormation::supprimerFormation();
    }

    public static function ajouterCSV(): void
    {
        ServiceFichier::ajouterCSV();
    }

    public static function refuserEntreprise(): void
    {
        ServiceEntreprise::refuserEntreprise();
    }

    public static function supprimerEntreprise(): void
    {
        ServiceEntreprise::supprimerEntreprise();
    }

    public static function validerEntreprise(): void
    {
        ServiceEntreprise::validerEntreprise();
    }

    public static function supprimerEtudiant(): void
    {
        ServiceEtudiant::supprimerEtudiant();
    }

    public static function promouvoirProf(): void
    {
        ServicePersonnel::promouvoirProf();
    }

    public static function retrograderProf(): void
    {
        ServicePersonnel::retrograderProf();
    }

    public static function modifierEntreprise(): void
    {
        ServiceEntreprise::modifierEntreprise();
    }

    public static function validerConvention(): void
    {
        ServiceConvention::validerConvention();
    }

    public static function rejeterConvention(): void
    {
        ServiceConvention::rejeterConvention();
    }

    public static function devenirTuteur(): void
    {
        ServicePersonnel::devenirTuteur();
    }

    public static function seProposerEnTuteurUM(): void
    {
        ServicePersonnel::seProposerEnTuteurUM();
    }

    public static function validerTuteurUM(): void
    {
        ServicePersonnel::validerTuteurUM();
    }

    public static function refuserTuteurUM(): void
    {
        ServicePersonnel::refuserTuteurUM();
    }

    public static function exporterCSV(): void
    {
        ServiceFichier::exporterCSV();
    }

    public static function ajouterAnnotation(): void
    {
        ServicePersonnel::ajouterAnnotation();
    }


    //FONCTIONS AUTRES ---------------------------------------------------------------------------------------------------------------------------------------------

    /**
     * @param string $action le nom de la fonction sur laquelle rediriger
     * @param string $type le type de message Flash
     * @param string $message le message à envoyer
     * @return void redirige en envoyant un messageFlash
     */
    public static function redirectionFlash(string $action, string $type, string $message): void
    {
        MessageFlash::ajouter($type, $message);
        self::$action();
    }

    /**
     * @return void met à jour l'image de profil d'un admin
     */
    public static function updateImage(): void
    {
        //si un fichier a été passé en paramètre
        if (!empty($_FILES['pdp']['name'])) {

            if (!in_array(strtolower(pathinfo($_FILES['pdp']['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'])) {
                ControleurAdminMain::redirectionFlash('afficherProfil', 'danger', 'Seuls les fichiers png,jpeg et jpg sont acceptés');
            } else {
                $admin = ((new ProfRepository())->getObjectParClePrimaire(ConnexionUtilisateur::getLoginUtilisateurConnecte()));
                $nom = "";
                $nomAdmin = $admin->getLoginProf();
                for ($i = 0; $i < strlen($admin->getLoginProf()); $i++) {
                    if ($nomAdmin[$i] == ' ') {
                        $nom .= "_";
                    } else {
                        $nom .= $nomAdmin[$i];
                    }
                }
                $nom .= "_logo";

                $ancienneImage = (new UploadsRepository())->imageParEtudiant(ConnexionUtilisateur::getLoginUtilisateurConnecte());

                $ai_id = TransfertImage::transfert();

                $adm = (new ProfRepository())->getObjectParClePrimaire(ConnexionUtilisateur::getLoginUtilisateurConnecte());
                $adm->setImg($ai_id);
                (new ProfRepository())->modifierObjet($adm);

                if ($ancienneImage["img_id"] != 1 && $ancienneImage["img_id"] != 0) (new UploadsRepository())->supprimer($ancienneImage["img_id"]);
            }
        }
    }
}
