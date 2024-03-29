<?php

namespace App\FormatIUT\Modele\DataObject;

use App\FormatIUT\Configuration\Configuration;
use App\FormatIUT\Controleur\ControleurMain;
use App\FormatIUT\Lib\ConnexionUtilisateur;
use App\FormatIUT\Lib\MotDePasse;
use App\FormatIUT\Modele\Repository\AbstractRepository;
use App\FormatIUT\Modele\Repository\TuteurProRepository;
use App\FormatIUT\Modele\Repository\UploadsRepository;
use App\FormatIUT\Modele\Repository\VilleRepository;
use DateTime;

class Entreprise extends AbstractDataObject
{
    private int $siret;
    private string $nomEntreprise;
    private ?string $statutJuridique;
    private ?int $effectif;
    private ?string $codeNAF;
    private ?string $tel;
    private string $adresseEntreprise;
    private string $idVille;
    private ?string $img;
    private string $mdpHache;
    private string $email;
    private ?string $emailAValider;
    private ?string $nonce;
    private bool $estValide;
    private ?string $dateCreationCompte;

    /**
     * @param int $siret
     * @param string $nomEntreprise
     * @param string|null $statutJuridique
     * @param int|null $effectif
     * @param string|null $codeNAF
     * @param string|null $tel
     * @param string $AdresseEntreprise
     * @param string $idVille
     * @param string|null $img
     * @param string $mdpHache
     * @param string $email
     * @param string|null $emailAValider
     * @param string|null $nonce
     * @param bool $estValide
     * @param string|null $dateCreationCompte
     */
    public function __construct(int $siret, string $nomEntreprise, ?string $statutJuridique, ?int $effectif, ?string $codeNAF, ?string $tel, string $AdresseEntreprise, string $idVille, ?string $img, string $mdpHache, string $email, ?string $emailAValider, ?string $nonce, bool $estValide, ?string $dateCreationCompte)
    {
        $this->siret = $siret;
        $this->nomEntreprise = $nomEntreprise;
        $this->statutJuridique = $statutJuridique;
        $this->effectif = $effectif;
        $this->codeNAF = $codeNAF;
        $this->tel = $tel;
        $this->adresseEntreprise = $AdresseEntreprise;
        $this->idVille = $idVille;
        $this->img = $img;
        $this->mdpHache = $mdpHache;
        $this->email = $email;
        $this->emailAValider = $emailAValider;
        $this->nonce = $nonce;
        $this->estValide = $estValide;
        $this->dateCreationCompte = $dateCreationCompte;
    }


    public function formatTableau(): array
    {
        $valide = 0;
        if ($this->estValide) $valide = 1;
        return ['numSiret' => $this->siret,
            'nomEntreprise' => $this->nomEntreprise,
            'statutJuridique' => $this->statutJuridique,
            'effectif' => $this->effectif,
            'codeNAF' => $this->codeNAF,
            'tel' => $this->tel,
            "adresseEntreprise" => $this->adresseEntreprise,
            "idVille" => $this->idVille,
            "img_id" => $this->img,
            "mdpHache" => $this->mdpHache,
            "email" => $this->email,
            "emailAValider" => $this->emailAValider,
            "nonce" => $this->nonce,
            "estValide" => $valide,
            "dateCreationCompte" => $this->dateCreationCompte
        ];
    }


    public static function construireDepuisFormulaire(array $EntrepriseEnFormulaire): Entreprise
    {
        $ville = (new VilleRepository())->getVilleParNom($EntrepriseEnFormulaire["ville"]);
        if (!$ville) {
            $newVille = new Ville(null, $EntrepriseEnFormulaire["ville"], $EntrepriseEnFormulaire["codePostal"]);
            $ville = (new VilleRepository())->creerObjet($newVille);
        }

        return new Entreprise(
            $EntrepriseEnFormulaire["siret"],
            $EntrepriseEnFormulaire["nomEntreprise"],
            $EntrepriseEnFormulaire["statutJuridique"],
            $EntrepriseEnFormulaire["effectif"],
            $EntrepriseEnFormulaire["codeNAF"],
            $EntrepriseEnFormulaire["tel"],
            $EntrepriseEnFormulaire["adresseEntreprise"],
            $ville,
            0,
            MotDePasse::hacher($EntrepriseEnFormulaire["mdp"]),
            "",
            $EntrepriseEnFormulaire["email"],
            MotDePasse::genererChaineAleatoire(),
            false,
            (new DateTime())->format('d-m-Y')
        );
    }

    protected static function autoIncrementVille($listeId, $get): string
    {
        $id = 1;
        while (!isset($_REQUEST[$get])) {
            if (in_array("V" . $id, $listeId)) {
                $id++;
            } else {
                $_REQUEST[$get] = $id;
            }
        }
        return "V" . $id;
    }

    public function getSiret(): int
    {
        return $this->siret;
    }

    public function setSiret(int $siret): void
    {
        $this->siret = $siret;
    }

    public function getNomEntreprise(): string
    {
        return $this->nomEntreprise;
    }

    public function setNomEntreprise(string $nomEntreprise): void
    {
        $this->nomEntreprise = $nomEntreprise;
    }

    public function getStatutJuridique(): ?string
    {
        return $this->statutJuridique;
    }

    public function setStatutJuridique(?string $statutJuridique): void
    {
        $this->statutJuridique = $statutJuridique;
    }

    public function getEffectif(): ?int
    {
        return $this->effectif;
    }

    public function setEffectif(?int $effectif): void
    {
        $this->effectif = $effectif;
    }

    public function getCodeNAF(): ?string
    {
        return $this->codeNAF;
    }

    public function setCodeNAF(?string $codeNAF): void
    {
        $this->codeNAF = $codeNAF;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): void
    {
        $this->tel = $tel;
    }

    public function getAdresseEntreprise(): string
    {
        return $this->adresseEntreprise;
    }

    public function setAdresseEntreprise(string $adresseEntreprise): void
    {
        $this->adresseEntreprise = $adresseEntreprise;
    }

    public function getIdVille(): string
    {
        return $this->idVille;
    }

    public function setIdVille(string $idVille): void
    {
        $this->idVille = $idVille;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): void
    {
        $this->img = $img;
    }

    public function getMdpHache(): string
    {
        return $this->mdpHache;
    }

    public function setMdpHache(string $mdpHache): void
    {
        $this->mdpHache = $mdpHache;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmailAValider(): ?string
    {
        return $this->emailAValider;
    }

    public function setEmailAValider(?string $emailAValider): void
    {
        $this->emailAValider = $emailAValider;
    }

    public function getNonce(): ?string
    {
        return $this->nonce;
    }

    public function setNonce(?string $nonce): void
    {
        $this->nonce = $nonce;
    }

    public function isEstValide(): bool
    {
        return $this->estValide;
    }

    public function setEstValide(bool $estValide): void
    {
        $this->estValide = $estValide;
    }

    public function getDateCreationCompte(): ?string
    {
        return $this->dateCreationCompte;
    }

    public function setDateCreationCompte(string $dateCreationCompte): void
    {
        $this->dateCreationCompte = $dateCreationCompte;
    }

    public static function ajouterTuteur($nom, $prenom, $email, $tel, $fonction): void
    {
        $idEntreprise = ConnexionUtilisateur::getNumEntrepriseConnectee();
        $nb = (new TuteurProRepository())->getNewIdTuteurPro();
        (new TuteurProRepository())->creerObjet(new TuteurPro($nb, $email, $tel, $fonction,$nom, $prenom, $idEntreprise));
    }

    public static function creerEntreprise(array $entreprise): Entreprise
    {
        return new Entreprise(
            $entreprise["siret"],
            $entreprise["nomEntreprise"],
            $entreprise["statutJuridique"],
            $entreprise["effectif"],
            $entreprise['codeNAF'],
            $entreprise["tel"],
            $entreprise["adresseEntreprise"],
            $entreprise["idVille"],
            $entreprise["img"],
            $entreprise["mdpHache"],
            $entreprise["email"],
            $entreprise["emailAValider"],
            $entreprise["nonce"],
            1,
            $entreprise['dateCreationCompte']
        );
    }

}
