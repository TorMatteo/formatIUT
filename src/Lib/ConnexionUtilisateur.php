<?php
namespace App\FormatIUT\Lib;

use App\FormatIUT\Modele\HTTP\Session;

class ConnexionUtilisateur
{
    // L'utilisateur connecté sera enregistré en session associé à la clé suivante
    private static string $cleConnexion = "_utilisateurConnecte";
    private static string $cleTypeConnexion ="_typeUtilisateurConnecte";

    public static function connecter(string $loginUtilisateur,string $typeUtilisateur): void
    {
        // À compléter
        $session=Session::getInstance();
        $session->enregistrer(self::$cleConnexion,$loginUtilisateur);
        $session->enregistrer(self::$cleTypeConnexion,$typeUtilisateur);
    }

    public static function estConnecte(): bool
    {
        // À compléter
        $session=Session::getInstance();
        return $session->contient(self::$cleConnexion);
    }

    public static function deconnecter(): void
    {
        // À compléter
        $session=Session::getInstance();
        $session->supprimer(self::$cleConnexion);
    }

    public static function getLoginUtilisateurConnecte(): ?string
    {
        // À compléter
        if (self::estConnecte()){
            $session=Session::getInstance();
            return $session->lire(self::$cleConnexion);
        }
        return null;
    }

    /**
     * @return string
     */
    public static function getCleTypeConnexion(): string
    {
        return self::$cleTypeConnexion;
    }
}