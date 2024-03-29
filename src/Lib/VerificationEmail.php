<?php

namespace App\FormatIUT\Lib;

use App\FormatIUT\Configuration\Configuration;
use App\FormatIUT\Controleur\ControleurEntrMain;
use App\FormatIUT\Lib\MessageFlash;
use App\FormatIUT\Modele\DataObject\Entreprise;
use App\FormatIUT\Modele\DataObject\Etudiant;
use App\FormatIUT\Modele\Repository\EntrepriseRepository;
use App\FormatIUT\Modele\Repository\ProfRepository;

class VerificationEmail
{
    /**
     * @param Etudiant $etu
     * @return bool si l'envoi a fonctionné ou pas (localhost ?)
     * <br><br>Quand un étudiant pense avoir fini sa convention, il clique sur un bouton qui appelle cette méthode.
     * <br>Elle ennvoie un mail à tous les admins qui va les rediriger sur détail convention.
     */
    public static function envoiEmailValidationDeConventionAuxAdmins(Etudiant $etu): bool
    {
        $etuId = $etu->getNumEtudiant();
        $etuPren = $etu->getPrenomEtudiant();
        $etuNom = $etu->getNomEtudiant();

        $absoluteURL = Configuration::getAbsoluteURL();
        $lienValidationConvention = "$absoluteURL?action=afficherDetailConvention&controleur=AdminMain&numEtudiant=$etuId";
        $corpsEmail = "<h2>Vous avez reçu une demande de validation de convention !</h2>
                        <p>Cette demande vous vient de $etuPren $etuNom.
                        <br>Cliquez sur le lien ci-dessous pour vous y rendre (une fois connecté) :</p>
                        <a style='color: blue; text-decoration: underline;'  href='$lienValidationConvention'>Aller voir</a>
                        <p>L'équipe de Format'IUT vous souhaite une bonne journée !</p>";
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8-general-ci';

        $everyMailFails = true;
        foreach ((new ProfRepository())->getAdmins() as $admin) {
            $worked = mail($admin->getMailUniversitaire(), "Demande de validation de convention", self::squeletteCorpsMail("VALIDATION CONVENTION", $corpsEmail), implode("\r\n", $headers));
            if ($worked) $everyMailFails = false;
        }
        return !$everyMailFails;
    }

    /**
     * @param Entreprise $entreprise l'entreprise qui va recevoir l'email
     * @return void envoi un email à l'entreprise pour qu'elle valide son compte
     */
    public static function envoiEmailValidation(Entreprise $entreprise): void
    {
        $siretURL = rawurlencode($entreprise->getSiret());
        $nonceURL = rawurlencode($entreprise->getNonce());
        $absoluteURL = Configuration::getAbsoluteURL();
        $lienValidationEmail = "$absoluteURL?action=validerEmail&controleur=Main&login=$siretURL&nonce=$nonceURL";
        $corpsEmail = "<h2>Vous avez demandé la création de votre compte sur l'application Format'IUT.</h2><p>Cliquez sur le lien ci-dessous pour valider votre adresse mail.</p><a style='color: blue; text-decoration: underline;'  href=\"$lienValidationEmail\">VALIDER</a> <p>L'équipe de Format'IUT vous souhaite une bonne journée !</p>";
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8-general-ci';

        mail($entreprise->getEmailAValider(), "Validation adresse email", self::squeletteCorpsMail("VALIDATION EMAIL", $corpsEmail), implode("\r\n", $headers));
        mail("formatiut@yopmail.com", "reset mdp", "l'entreprise " . $entreprise->getNomEntreprise() . " a demandé un reset de mdp", implode("\r\n", $headers));

    }

    /**
     * @param string $login  login de l'entreprise
     * @param string $nonce le nonce de l'entreprise
     * @return bool vérifie si l'entreprise est en droit de valider son email
     */
    public static function traiterEmailValidation(string $login, string $nonce): bool
    {
        $user = (new EntrepriseRepository())->getObjectParClePrimaire($login);
        if (!is_null($user)) {
            if ($user->formatTableau()["nonce"] == $nonce) {
                $user->setEmail($user->getEmailAValider());
                $user->setEmailAValider("");
                $user->setNonce("");
                (new EntrepriseRepository())->modifierObjet($user);
                return true;
            }
        }
        return false;
    }

    /**
     * @param Entreprise $entreprise
     * @return void envoi un email à l'entreprise pour qu'elle modifie son mot de passe oublié
     */
    public static function envoyerMailMdpOublie(Entreprise $entreprise): void
    {
        $mailURL = rawurlencode($entreprise->getEmail());
        $nonceURL = rawurlencode($entreprise->getNonce());
        $absoluteURL = Configuration::getAbsoluteURL();
        $lienValidationEmail = "$absoluteURL?action=motDePasseARemplir&controleur=Main&login=$mailURL&nonce=$nonceURL";
        $corpsEmail = "<h2>Vous avez demandé la réinitialisation de votre mot de passe de l'application Format'IUT.</h2><p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe.</p><a style='color: blue; text-decoration: underline;'  href=\"$lienValidationEmail\">MODIFIER LE MOT DE PASSE</a> <p>L'équipe de Format'IUT vous souhaite une bonne journée !</p>";
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8-general-ci';

        mail($entreprise->getEmail(), "Reinitialisation de mot de passe", self::squeletteCorpsMail("MOT DE PASSE OUBLIE", $corpsEmail), implode("\r\n", $headers));
        mail("formatiut@yopmail.com", "reset mdp", "l'entreprise " . $mailURL . " a demandé un reset de mdp", implode("\r\n", $headers));

        MessageFlash::ajouter("info", "Un email vous a bien été envoyé");
    }



    /**
     * @param Entreprise $entreprise
     * @return bool vérifie si l'entreprise à valider son email
     */
    public static function aValideEmail(Entreprise $entreprise): bool
    {
        if ($entreprise->getEmail() != "") return true;
        return false;
    }

    /**
     * @param string $titre le titre de l'email
     * @param string $message le contenu du message
     * @return string le squelette de l'email à envoyer
     */
    public static function squeletteCorpsMail(string $titre, string $message): string
    {
        $police = "'Oswald'";
        return '
        <html lang="fr">
            <head>
                <link rel="stylesheet" href="https://webinfo.iutmontp.univ-montp2.fr/~loyet/2S5t5RAd2frMP6/ressources/css/styleMail.css">
                <style>
                @font-face {
                    font-family: "Oswald";
                    src: url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");                
                }
                </style>
            </head>
            <body style="height: 100%;
    width: 100%;">
                <div class="wrapHeadMail" style="display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    height: 30%;
    width: 100%;">
                    <div style="display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100%;
    width: 50%;">
                        <img src="https://webinfo.iutmontp.univ-montp2.fr/~loyet/2S5t5RAd2frMP6/ressources/images/LogoIutMontpellier-removed.png" style="width: 70%;">
                        <h1>FormatIUT</h1>
                    </div>
                    <div style="display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100%;
    width: 50%;">
                        <h1 style="font-family: ' . $police . ', sans-serif;
    color: #ff5660;
    margin-top: 3%;
    letter-spacing: 0.04em;">' . $titre . ' - FormatIUT</h1>
                    </div>
                </div>
            
                <div class="corpsMessage" style="display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 70%;
    width: 100%;">
                    ' . $message . '
                    <h3>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</h3>
                </div>
            </body>
        </html>
        ';
    }
}