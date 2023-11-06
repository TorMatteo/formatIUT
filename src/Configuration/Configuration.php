<?php
/*
//On configure ici l'accès à la base de données
define('DB_HOST', 'localhost'); //Adresse de la base de données
define('DB_USER', 'loyet'); //Nom d'utilisateur de la base de données
define('DB_PASS', '26032004'); //Mot de passe de la base de données
define('DB_NAME', 'loyet'); //Nom de la base de données4
*/

namespace App\FormatIUT\Configuration;

class Configuration
{

    static private array $configLocal = array(
        
        'hostname' => 'localhost',
        'database' => 'loyet',
        'port' => '3306',
        'login' => 'root',
        'password'=>'root'
    );
    static private array $configWebInfo = array(

        'hostname' => 'localhost',
        'database' => 'loyet',
        'port' => '3306',
        'login' => 'loyet',
        'password' => 'gfsGnT!!hSSfE88.'
    );
    
    static private function getConfig():array{
        if (strpbrk($_SERVER["HTTP_HOST"],"localhost")) {
            return self::$configLocal;
        }
        else return self::$configWebInfo;
    }

    public static function getHostname(): string
    {
        return self::getConfig()['hostname'];
    }

    public static function getDatabase(): string
    {
        return self::getConfig()['database'];
    }

    public static function getLogin(): string
    {
        return self::getConfig()['login'];
    }

    public static function getPassword(): string
    {
        return self::getConfig()['password'];
    }

    public static function getPort(): string
    {
        return self::getConfig()['port'];
    }

    public static function getAbsoluteURL()
    {
        if (strpbrk($_SERVER["HTTP_HOST"],"localhost")) {
            return "http://localhost/SAE_DEV/web/controleurFrontal.php";
        }
        return "https://webinfo.iutmontp.univ-montp2.fr/~loyet/2S5t5RAd2frMP6/web/controleurFrontal.php";
    }


    private static string $controleur;

    public static function getControleur(): string
    {
        return self::$controleur;
    }
    public static function controleurIs(string $contr): bool {
        return self::$controleur == $contr;
    }

    public static function setControleur(string $controleur): void
    {
        self::$controleur = $controleur;
    }

    public static function getCheminControleur(): string {
        return "App\FormatIUT\Controleur\Controleur" . self::$controleur;
    }
}