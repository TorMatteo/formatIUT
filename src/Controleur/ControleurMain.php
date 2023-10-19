<?php

namespace App\FormatIUT\Controleur;

use App\FormatIUT\Controleur\ControleurEntrMain;
use App\FormatIUT\Lib\ConnexionUtilisateur;
use App\FormatIUT\Lib\MessageFlash;
use App\FormatIUT\Lib\MotDePasse;
use App\FormatIUT\Lib\TransfertImage;
use App\FormatIUT\Lib\VerificationEmail;
use App\FormatIUT\Modele\DataObject\Entreprise;
use App\FormatIUT\Modele\HTTP\Session;
use App\FormatIUT\Modele\Repository\EntrepriseRepository;
use App\FormatIUT\Modele\Repository\OffreRepository;

class ControleurMain
{

    /***
     * Affiche la page d'acceuil du site sans qu'aucune connexion n'est été faite
     */
    public static function afficherIndex(){
        self::afficherVue('vueGenerale.php',["menu"=>self::getMenu(),"chemin"=>"vueIndex.php","titrePage"=>"Accueil"]);
    }

    /***
     * Affiche la page de présentations aux entreprises n'ayant pas de compte
     */
    public static function afficherVuePresentation() {
        self::afficherVue('vueGenerale.php',["menu"=>self::getMenu(),"chemin"=>"Entreprise/vuePresentationEntreprise.php","titrePage"=>"Accueil Entreprise"]);
    }

    /***
     * Affiche la page de detail d'une offre qui varie selon le client
     */
    public static function afficherVueDetailOffre(){
        $menu = "App\Formatiut\Controleur\Controleur" . $_REQUEST['controleur'];
        $liste=(new OffreRepository())->getListeIdOffres();
        if (isset($_REQUEST["idOffre"])) {
            if (in_array($_REQUEST["idOffre"], $liste)) {
                $offre = (new OffreRepository())->getObjectParClePrimaire($_REQUEST['idOffre']);
                $entreprise = (new EntrepriseRepository())->getObjectParClePrimaire($offre->getSiret());
                if ($_REQUEST["controleur"] == "EntrMain") $client = "Entreprise";
                else $client = "Etudiant";
                $chemin = ucfirst($client) . "/vueDetail" . ucfirst($client) . ".php";
                self::afficherVue('vueGenerale.php', ["menu" => $menu::getMenu(), "chemin" => $chemin, "titrePage" => "Detail de l'offre", "offre" => $offre, "entreprise" => $entreprise]);
            } else {
                $menu::afficherErreur("L'offre n'existe pas");
            }
        }else {
            $menu::afficherErreur("L'offre n'est pas renseigné");
        }
    }

    public static function afficherVue(string $cheminVue, array $parametres = []): void
    {
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . "/../vue/$cheminVue"; // Charge la vue
    }

    public static function getMenu() :array{
        return array(
            array("image"=>"../ressources/images/accueil.png","label"=>"Accueil","lien"=>"?controleur=Main&action=afficherIndex"),
            array("image"=>"../ressources/images/profil.png","label"=>"(prov étudiants)","lien"=>"?controleur=EtuMain&action=afficherAccueilEtu"),
            array("image"=>"../ressources/images/profil.png","label"=>"Se Connecter","lien"=>"?controleur=Main&action=afficherPageConnexion"),
            array("image"=>"../ressources/images/entreprise.png","label"=>"Accueil Entreprise","lien"=>"?controleur=Main&action=afficherVuePresentation")
        );
    }

    /***
     * @param array $liste
     * @return array|null
     * retourne les 3 éléments avec la valeur les plus hautes
     */
    protected static function getTroisMax(array $liste) : ?array{
        $list=array();
        if (!empty($liste)) {
            $min=min(3, sizeof($liste));
            for ($i = 0; $i < $min; $i++) {
                $id = max($liste);
                foreach ($liste as $item => $value) {
                    if ($value == $id) $key = $item;
                }
                unset($liste[$key]);
                $list[] = $id;
            }
        }
        return $list;
    }

    public static function afficherErreur(string $error): void
    {
        $menu="App\Formatiut\Controleur\Controleur".$_REQUEST['controleur'];
        self::afficherVueDansCorps("Erreur", 'vueErreur.php', $menu::getMenu(), [
            'erreurStr' => $error
        ]);
    }

    protected static function afficherVueDansCorps(string $titrePage, string $cheminVue, array $menu, array $parametres = []): void
    {
        self::afficherVue("vueGenerale.php", array_merge(
            [
                'titrePage' => $titrePage,
                'chemin' => $cheminVue,
                'menu' => $menu
            ],
            $parametres
        ));
    }

    public static function insertImage($nom){
        return TransfertImage::transfert($nom);
    }

    protected static function autoIncrement($listeId, $get): int
    {
        $id = 1;
        while (!isset($_REQUEST[$get])) {
            if (in_array($id, $listeId)) {
                $id++;
            } else {
                $_REQUEST[$get] = $id;
            }
        }
        return $id;
    }
    protected static function autoIncrementF($listeId, $get): int
    {
        $id = 1;
        while (!isset($_REQUEST[$get])) {
            if (in_array("F".$id, $listeId)) {
                $id++;
            } else {
                $_REQUEST[$get] = $id;
            }
        }
        return $id;
    }
    public static function afficherPageConnexion(){
        self::afficherVue("vueGenerale.php",["titrePage"=>"Se Connecter","menu"=>self::getMenu(),"chemin"=>"vueFormulaireConnexion.php"]);
    }

    public static function seConnecter(){
        if(isset($_REQUEST["login"],$_REQUEST["mdp"])){
            $user=((new EntrepriseRepository())->getObjectParClePrimaire($_REQUEST["login"]));
            if (!is_null($user)){
                if ( MotDePasse::verifier($_REQUEST["mdp"],$user->getMdpHache())){
                    ConnexionUtilisateur::connecter($_REQUEST["login"]);
                    MessageFlash::ajouter("success", "Connexion Réussie");
                    header("Location: controleurFrontal.php?action=afficherAccueilEntr&controleur=EntrMain");
                    exit();
                }
            }
        }
        header("Location: controleurFrontal.php?controleur=Main&action=afficherPageConnexion&erreur=1");
    }
    public static function seDeconnecter(){
        ConnexionUtilisateur::deconnecter();
        Session::getInstance()->detruire();
        header("Location: controleurFrontal.php");
    }

    public static function validerEmail(){
        VerificationEmail::traiterEmailValidation($_REQUEST["login"],$_REQUEST["nonce"]);
        header("Location : controleurFrontal.php?action=afficherPageConnexion&controleur=Main");
    }

    public static function redirectionFlash(string $action,string $type,string $message){
        MessageFlash::ajouter($type,$message);
        self::$action();

}
    public static function creerCompteEntreprise(){
        if ($_REQUEST["mdp"]==$_REQUEST["mdpConf"]) {
            $entreprise = Entreprise::construireDepuisFormulaire($_REQUEST);
            (new EntrepriseRepository())->creerObjet($entreprise);
            VerificationEmail::envoiEmailValidation($entreprise);
            header("Location: controleurFrontal.php");
        }else {
            self::redirectionFlash("afficherVuePresentation","warning","Les mots de passes doivent coincider");
        }
    }
}