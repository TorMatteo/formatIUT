<?php

namespace App\FormatIUT\Modele\Repository;

use App\FormatIUT\Lib\ConnexionUtilisateur;
use App\FormatIUT\Lib\TransfertImage;
use App\FormatIUT\Modele\DataObject\AbstractDataObject;
use App\FormatIUT\Modele\DataObject\Ville;

class UploadsRepository extends AbstractRepository
{

    protected function getNomTable(): string
    {
        return "Uploads";
    }

    protected function getNomsColonnes(): array
    {
        return ["idUpload", "fileName"];
    }

    protected function getClePrimaire(): string
    {
        return "idUpload";
    }

    /**
     * @param array $dataObjectTableau
     * @return AbstractDataObject permet de construire une ville depuis un tableau
     */
    public function construireDepuisTableau(array $dataObjectTableau): AbstractDataObject
    {
        return new Ville("", "", "");
    }


    /**
     * @param $id
     * @return string|null
     * retourne le nom du fichier depuis son id
     */
    public function getFileNameFromId($id): ?string
    {
        $sql = "SELECT fileName FROM " . $this->getNomTable() . " WHERE " . $this->getClePrimaire() . "=:Tag ";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("Tag" => $id);
        $pdoStatement->execute($values);
        $objet = $pdoStatement->fetch();
        if (!$objet) {
            return null;
        }
        return $objet['fileName'];
    }

    /**
     * @param $img_if
     * @return mixed permet de récupérer une image depuis son id
     */
    public function getImage($img_if): mixed
    {
        $sql = "SELECT * FROM " . $this->getNomTable() . " WHERE " . $this->getClePrimaire() . "=:Tag ";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("Tag" => $img_if);
        $pdoStatement->execute($values);
        $objet = $pdoStatement->fetch();
        if (!$objet) {
            return null;
        }
        return $objet;
    }

    /**
     * @param string $fileName
     * @return int l'auto increment
     * crée un fichier dans la base de donnée
     */
    public function insert(string $fileName): int
    {
        $req = "INSERT INTO Uploads (fileName) VALUES (:fileNameTag);";
        $pdo = ConnexionBaseDeDonnee::getPdo();
        $pdo->prepare($req)->execute(['fileNameTag' => $fileName]);
        return $pdo->lastInsertId();
    }


    /**
     * @param $Siret
     * @return mixed
     * retourne l'image de photo de profil pour une entreprise
     */

    public function imageParEntreprise($Siret): mixed
    {
        $sql = "SELECT img_id FROM Entreprises WHERE numSiret=:Tag";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("Tag" => $Siret);
        $pdoStatement->execute($values);
        return $pdoStatement->fetch();
    }

    /**
     * @param $numEtudiant
     * @return mixed
     * retourne l'image de photo de profil pour un étudiant
     */
    public function imageParEtudiant($numEtudiant): mixed
    {
        $sql = "SELECT img_id FROM Etudiants WHERE numEtudiant=:Tag";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("Tag" => $numEtudiant);
        $pdoStatement->execute($values);
        return $pdoStatement->fetch();
    }
}
