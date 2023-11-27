<?php

namespace App\FormatIUT\Modele\Repository;

use App\FormatIUT\Configuration\Configuration;
use App\FormatIUT\Controleur\ControleurEtuMain;
use App\FormatIUT\Modele\DataObject\AbstractDataObject;

abstract class AbstractRepository
{
    protected abstract function getNomTable(): string;

    protected abstract function getNomsColonnes(): array;

    protected abstract function getClePrimaire(): string;

    public abstract function construireDepuisTableau(array $dataObjectTableau): AbstractDataObject;

    /***
     * @return array|null
     * retourne une liste de l'ensemble des object d'une même classe
     * renvoie null si aucun objet n'est créer
     */
    public function getListeObjet(): ?array
    {
        $sql = 'SELECT * FROM ' . $this->getNomTable();
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->query($sql);
        foreach ($pdoStatement as $item) {
            $listeObjet[] = $this->construireDepuisTableau($item);
        }
        return $listeObjet;
    }

    public function getListeID()
    {
        $sql = 'SELECT * FROM ' . $this->getNomTable();
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->query($sql);
        foreach ($pdoStatement as $item) {
            $listeObjet[] = $item[$this->getClePrimaire()];
        }
        return $listeObjet;
    }

    /***
     * @param $clePrimaire
     * @return AbstractDataObject|null
     * retourne un objet correspondant à la clé primaire donnée en paramètre
     * si l'objet n'existe pas, renvoie null
     */

    public function getObjectParClePrimaire($clePrimaire): ?AbstractDataObject
    {
        $sql = "SELECT * FROM " . $this->getNomTable() . " WHERE " . $this->getClePrimaire() . "=:Tag ";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("Tag" => $clePrimaire);
        $pdoStatement->execute($values);
        $objet = $pdoStatement->fetch();
        if (!$objet) {
            return null;
        }
        return $this->construireDepuisTableau($objet);
    }


    /*public function getObjectParClesPrimaires($clesPrimaires):?AbstractDataObject{
        $placeholders = implode(',', array_fill(0, count($clesPrimaires), '?'));
        $sql = "SELECT * FROM " . $this->getNomTable() . " WHERE " . $this->getClePrimaire() . " IN ({$placeholders})";
        $pdoStatement=ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        foreach ($clesPrimaires as $index => $valeur) {
            $pdoStatement->bindValue($index + 1, $valeur, PDO::PARAM_INT);
        }
        $pdoStatement->execute();
        $objets = $pdoStatement->fetchAll();
        if (!$objets){
            return null;
        }
        $result = array();
        foreach ($objets as $objet) {
            $result[] = $this->construireDepuisTableau($objet);
        }
        return $this->construireDepuisTableau($result);
    }*/

    // ----- CRUD -----

    /***
     * @param AbstractDataObject $object
     * @return void
     * créer un object dans la Base de Donnée avec les informations de l'objet donné en paramètre
     */
    public function creerObjet(AbstractDataObject $object): void
    {
        $fields = "";
        $values = "";
        $tags = array();
        foreach ($this->getNomsColonnes() as $nomColonne) {
            if ($nomColonne != $this->getNomsColonnes()[0]) {
                $fields .= ", ";
                $values .= ", ";
            }
            $fields .= $nomColonne;
            $values .= ":" . $nomColonne . "Tag";
            $tags[$nomColonne . "Tag"] = $object->formatTableau()[$nomColonne];
        }
        $sql = "INSERT IGNORE INTO " . $this->getNomTable() . " ($fields) VALUES ($values);";

        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $pdoStatement->execute($tags);
    }


    public function modifierObjet(AbstractDataObject $objet): void
    {
        $sql = "UPDATE " . $this->getNomTable() . " SET ";
        foreach ($this->getNomsColonnes() as $nomColonne) {
            if ($nomColonne != $this->getNomsColonnes()[0])
                $sql .= ",";
            $sql .= "$nomColonne = :$nomColonne" . "Tag";
            $values[$nomColonne . "Tag"] = $objet->formatTableau()[$nomColonne];
        }
        $where = " WHERE ";
        $clePrimaire = $this->getClePrimaire();
        $clePrimaire = trim($clePrimaire, "()");
        $colonnesClePrimaire = explode(', ', $clePrimaire);
        foreach ($colonnesClePrimaire as $colonne) {
            $where .= "$colonne = :$colonne" . "Tag AND ";
            $values[$colonne . "Tag"] = $objet->formatTableau()[$colonne];
        }
        $where = rtrim($where, " AND ");
        $sql .= $where;
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $pdoStatement->execute($values);
    }

    /***
     * @param $clePrimaire
     * @return void
     * supprime dans la Base de donnée l'objet donc la clé primaire est en paramètre
     */

    public function supprimer($clePrimaire): void
    {
        $sql = "DELETE FROM " . $this->getNomTable() . " WHERE " . $this->getClePrimaire() . "=:Tag ";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("Tag" => $clePrimaire);
        $pdoStatement->execute($values);
    }

    //-------------AUTRES------------

    public static function getResultatRechercheTrie($motsclefs): ?array
    {
        if (Configuration::controleurIs("EtuMain"))
            $anneeEtu = (new EtudiantRepository())->getAnneeEtudiant((new EtudiantRepository())->getObjectParClePrimaire(ControleurEtuMain::getCleEtudiant()));

        $pdo = ConnexionBaseDeDonnee::getPdo();
        $res = [
            'offres' => array(),
            'entreprises' => array(),
        ];

        $sql = "";
        $tags = [];
        for ($i = 0; $i < count($motsclefs); $i++) {
            $mot = $motsclefs[$i];
            $sql .= "
            SELECT *
            FROM Offre
            WHERE (LOWER(sujet) LIKE LOWER(:tag$i)
                OR LOWER(nomOffre) LIKE LOWER(:tag$i)
                OR LOWER(typeOffre) LIKE LOWER(:tag$i)
                OR LOWER(detailProjet) LIKE LOWER(:tag$i))";

            if (Configuration::controleurIs("EtuMain"))
                $sql .= "AND  $anneeEtu >= anneeMin
                         AND  $anneeEtu <= anneeMax";

            $sql .= "INTERSECT";
            $tags["tag$i"] = "%$mot%";
        }

        $sql = substr($sql, 0, -9);

        try {
            $pdoStatement = $pdo->prepare($sql);
            $pdoStatement->execute($tags);
        } catch (\PDOException $e) {
            return null;
        }

        foreach ($pdoStatement as $row)
            $res['offres'][] = (new FormationRepository())->construireDepuisTableau($row);

        $sql = "";
        $tags = [];
        for ($i = 0; $i < count($motsclefs); $i++) {
            $mot = $motsclefs[$i];
            $sql .= "
            SELECT *
            FROM Entreprise
            WHERE LOWER(nomEntreprise) LIKE LOWER(:tag$i)
            INTERSECT";
            $tags["tag$i"] = "%$mot%";
        }

        $sql = substr($sql, 0, -9);

        //try {
            $pdoStatement = $pdo->prepare($sql);
            $pdoStatement->execute($tags);
        //} catch (\PDOException $e) {
        //    return null;
        //}
        foreach ($pdoStatement as $row)
            $res['entreprises'][] = (new EntrepriseRepository())->construireDepuisTableau($row);

        return $res;
    }

    /***
     * @param $clePrimaire
     * @return true si l'objet existe dans la base de donnée, false sinon
     */
    public function estExistant($clePrimaire): bool
    {
        $sql = "SELECT * FROM " . $this->getNomTable() . " WHERE " . $this->getClePrimaire() . "=:Tag ";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("Tag" => $clePrimaire);
        $pdoStatement->execute($values);
        $objet = $pdoStatement->fetch();
        if (!$objet) {
            return false;
        }
        return true;
    }
}
