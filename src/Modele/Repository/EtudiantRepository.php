<?php

namespace App\FormatIUT\Modele\Repository;

use App\FormatIUT\Modele\DataObject\AbstractDataObject;
use App\FormatIUT\Modele\DataObject\Etudiant;

class EtudiantRepository extends AbstractRepository
{

    protected function getNomTable(): string
    {
        return "Etudiants";
    }

    protected function getNomsColonnes(): array
    {
        return array("numEtudiant", "prenomEtudiant", "nomEtudiant", "loginEtudiant", "sexeEtu", "mailUniversitaire", "mailPerso", "telephone", "groupe", "parcours", "validationPedagogique", "presenceForumIUT", "codeEtape", "img_id");
    }

    protected function getClePrimaire(): string
    {
        return "numEtudiant";
    }

    public function construireDepuisTableau(array $dataObjectTableau): AbstractDataObject
    {
        $image = ((new ImageRepository()))->getImage($dataObjectTableau["img_id"]);
        return new Etudiant(
            $dataObjectTableau["numEtudiant"],
            $dataObjectTableau["prenomEtudiant"],
            $dataObjectTableau["nomEtudiant"],
            $dataObjectTableau["loginEtudiant"],
            $dataObjectTableau["sexeEtu"],
            $dataObjectTableau["mailUniversitaire"],
            $dataObjectTableau["mailPerso"],
            $dataObjectTableau["telephone"],
            $dataObjectTableau["groupe"],
            $dataObjectTableau["parcours"],
            $dataObjectTableau["validationPedagogique"],
            $dataObjectTableau["presenceForumIUT"],
            $dataObjectTableau["codeEtape"],
            $image["img_blob"]
        );
    }

    /**
     * @return void
     * rajoute dans la BD un étudiant qui postule à une offre
     */
    public function etudiantPostuler($numEtu, $numOffre): void
    {
        $sql = "INSERT INTO Postuler VALUES (:TagEtu,:TagOffre,'En Attente', NULL, NULL)";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array(
            "TagEtu" => $numEtu,
            "TagOffre" => $numOffre
        );
        $pdoStatement->execute($values);
    }

    /**
     * @param $numEtu
     * @param $idOffre
     * @return mixed
     * permet de savoir si un étudiant à postuler à cet Offre mais n'a pas changé d'état depuis
     */

    public function etudiantAPostule($numEtu, $idOffre): mixed
    {
        $sql = "SELECT * FROM Postuler WHERE numEtudiant=:TagEtu AND idOffre=:TagOffre AND Etat='En Attente'";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("TagEtu" => $numEtu, "TagOffre" => $idOffre);
        $pdoStatement->execute($values);
        return $pdoStatement->fetch();
    }


    /**
     * @param $idOffre
     * @return mixed
     * retourne le nombre de postulation faites au total pour cet offre
     */

    public function nbPostulations($idOffre): mixed
    {
        $sql = "SELECT COUNT(numEtudiant)as nb FROM Postuler WHERE idOffre=:Tag";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("Tag" => $idOffre);
        $pdoStatement->execute($values);
        return ($pdoStatement->fetch())["nb"];
    }

    /**
     * @param $idEtudiant
     * @return mixed
     * retourne si l'étudiant à déjà une formation
     */

    public function aUneFormation($idEtudiant): mixed
    {
        $sql = "SELECT * FROM Formations WHERE idEtudiant=:Tag";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("Tag" => $idEtudiant);
        $pdoStatement->execute($values);
        return $pdoStatement->fetch();
    }

    /**
     * @param $numEtudiant
     * @param $idOffre
     * @return mixed
     * retourne si l'étudiant à déjà postuler à cette offre
     */
    public function aPostule($numEtudiant, $idOffre): mixed
    {
        $sql = "SELECT * FROM Postuler WHERE numEtudiant=:TagEtu AND idOffre=:TagOffre";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("TagEtu" => $numEtudiant, "TagOffre" => $idOffre);
        $pdoStatement->execute($values);
        return $pdoStatement->fetch();
    }

    /**
     * @param $numEtudiant
     * @param $idImage
     * @return void
     * permet à un étudiant d'update son image de profil
     */

    public function updateImage($numEtudiant, $idImage): void
    {
        $sql = "UPDATE " . $this->getNomTable() . " SET img_id=:TagImage WHERE " . $this->getClePrimaire() . "=:Tag";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("TagImage" => $idImage, "Tag" => $numEtudiant);
        $pdoStatement->execute($values);
    }

    /**
     * @param $idOffre
     * @return array
     * retourne la liste des étudiant qui sont actuellement dans la table Postuler de cette offre
     */

    public function etudiantsEnAttente($idOffre): array
    {
        $sql = "SELECT numEtudiant FROM Postuler r WHERE idOffre=:Tag AND NOT EXISTS(SELECT * FROM Formations f WHERE r.numEtudiant=f.idEtudiant)";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("Tag" => $idOffre);
        $pdoStatement->execute($values);
        $listeEtu = array();
        foreach ($pdoStatement as $item) {
            $listeEtu[] = $this->getObjectParClePrimaire($item["numEtudiant"]);
        }
        return $listeEtu;
    }

    /**
     * @param $numEtudiant
     * @param $etat
     * @return mixed
     * retourne le nombre de fois où l'étudiant est dans un certain état
     */

    public function nbEnEtat($numEtudiant, $etat): mixed
    {
        $sql = "SELECT COUNT(idFormation) as nb FROM Postuler WHERE numEtudiant=:Tag AND Etat=:TagEtat";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("Tag" => $numEtudiant, "TagEtat" => $etat);
        $pdoStatement->execute($values);
        return $pdoStatement->fetch()["nb"];
    }

    public function estEtudiant(string $login): bool
    {
        $sql = "SELECT COUNT(*) FROM " . $this->getNomTable() . " WHERE loginEtudiant=:Tag";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("Tag" => $login);
        $pdoStatement->execute($values);
        $count = $pdoStatement->fetch();
        if ($count[0] == 1) return true;
        return false;
    }

    public function premiereConnexion(array $etudiant): void
    {
        $sql = "INSERT INTO " . $this->getNomTable() . " (numEtudiant,prenomEtudiant,nomEtudiant,loginEtudiant,mailUniversitaire) VALUES (:numTag,:prenomTag,:nomTag,:loginTag,:mailTag)";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array(
            "numTag" => $etudiant["numEtudiant"],
            "prenomTag" => $etudiant["prenomEtudiant"],
            "nomTag" => $etudiant["nomEtudiant"],
            "loginTag" => $etudiant["loginEtudiant"],
            "mailTag" => $etudiant["mailUniversitaire"]
        );
        $pdoStatement->execute($values);
    }

    public function getNumEtudiantParLogin(string $login): ?int
    {
        $sql = "SELECT numEtudiant FROM " . $this->getNomTable() . " WHERE loginEtudiant=:Tag";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("Tag" => $login);
        $pdoStatement->execute($values);

        $result = $pdoStatement->fetch();
        if (!$result) return null;
        else return $result[0];
    }

    public function modifierNumEtuSexe(Etudiant $etudiant, int $oldNumEtudiant): void
    {
        $sql = "UPDATE " . $this->getNomTable() . " SET numEtudiant=:TagNum,sexeEtu=:TagSexe WHERE numEtudiant=:tagOldNum";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array(
            "TagNum" => $etudiant->getNumEtudiant(),
            "TagSexe" => $etudiant->getSexeEtu(),
            "tagOldNum" => $oldNumEtudiant
        );
        $pdoStatement->execute($values);
    }

    public function modifierTelMailPerso(Etudiant $etudiant): void
    {
        $sql = "UPDATE " . $this->getNomTable() . " SET telephone=:tag1,mailPerso=:tag2 WHERE numEtudiant=:tagNum";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array(
            "tag1" => $etudiant->getTelephone(),
            "tag2" => $etudiant->getMailPerso(),
            "tagNum" => $etudiant->getNumEtudiant()
        );
        $pdoStatement->execute($values);
    }

    public function modifierGroupeParcours(Etudiant $etudiant): void
    {
        $sql = "UPDATE " . $this->getNomTable() . " SET groupe=:tag1,parcours=:tag2 WHERE numEtudiant=:tagNum";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array(
            "tag1" => $etudiant->getGroupe(),
            "tag2" => $etudiant->getParcours(),
            "tagNum" => $etudiant->getNumEtudiant()
        );
        $pdoStatement->execute($values);
    }

    public function mettreAJourInfos(string $adresseMail, string $telephone, string $numEtu): void
    {
        $sql = "UPDATE Etudiants SET mailPerso = :mailTag, telephone = :telTag WHERE numEtudiant = :numTag";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("mailTag" => $adresseMail, "telTag" => $telephone, "numTag" => $numEtu);
        $pdoStatement->execute($values);
    }

    public function etudiantsSansOffres(): array
    {
        $sql = "SELECT * FROM " . $this->getNomTable() . " etu WHERE NOT EXISTS( SELECT idEtudiant FROM Formations f WHERE f.idEtudiant=etu.numEtudiant ) ";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->query($sql);
        foreach ($pdoStatement as $etudiant) {
            $listeEtudiants[] = $this->construireDepuisTableau($etudiant);
        }
        return $listeEtudiants;
    }

    public function etudiantsEtats(): array
    {
        $sql = "SELECT numEtudiant,COUNT(idFormation) as AUneOffre
                FROM Etudiants etu 
                LEFT JOIN Formation f ON f.idEtudiant=etu.numEtudiant
                GROUP BY numEtudiant";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->query($sql);
        foreach ($pdoStatement as $item) {
            $nb = $item["AUneOffre"];
            unset($item["AUneOffre"]);
            $listeEtudiants[] = array("etudiant" => $this->getObjectParClePrimaire($item["numEtudiant"]), "aUneFormation" => $nb);

        }
        return $listeEtudiants;
    }

    public function etudiantsCandidats($idOffre): array
    {
        $sql = "SELECT numEtudiant FROM Postuler WHERE idOffre=:Tag";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("Tag" => $idOffre);
        $pdoStatement->execute($values);
        $listeEtudiants = array();
        foreach ($pdoStatement as $item) {
            $listeEtudiants[] = $this->getObjectParClePrimaire($item["numEtudiant"]);
        }
        return $listeEtudiants;
    }

    public function getAssociationPourOffre($idOffre, $numEtudiant): ?string
    {
        $sql = "SELECT * FROM Postuler WHERE idOffre=:TagOffre AND numEtudiant=:TagEtu";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("TagOffre" => $idOffre, "TagEtu" => $numEtudiant);
        $pdoStatement->execute($values);
        $resultat = $pdoStatement->fetch();
        if ($resultat) {
            if ($resultat["Etat"] == "En attente") {
                return "Candidat en attente";
            } else if ($resultat["Etat"] == "Validée") {
                return "Assigné";
            } else if ($resultat["Etat"] == "Refusée") {
                return "Refusé par l'entreprise";
            } else if ($resultat["Etat"] == "A Choisir") {
                return "Accepté par l'entreprise";
            } else {
                $sql = "SELECT * FROM Formations WHERE idEtudiant=:TagEtu AND idOffre=:TagOffre";
                $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
                $values = array("TagEtu" => $numEtudiant, "TagOffre" => $idOffre);
                $pdoStatement->execute($values);
                $resultat = $pdoStatement->fetch();
                if ($resultat) {
                    return "Assigné";
                }
            }
        } else {
            return "Non assigné";
        }
        return null;
    }

    public function getAnneeEtudiant(Etudiant $etudiant): int
    {
        return match (substr($etudiant->getGroupe(), 0, 1)) {
            "Q" => 2,
            "G" => 3,
            default => 2,
        };
    }


    public function getOffreValidee($numEtu, $typeOffre)
    {
        $sql = "Select * FROM Postuler r JOIN Offre o ON o.idOffre = r.idOffre WHERE typeOffre=:tagType AND numEtudiant = :tagEtu AND Etat = 'Validée'";
        $pdoStatement = ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("tagType" => $typeOffre, "tagEtu" => $numEtu);
        $pdoStatement->execute($values);
        return $pdoStatement->fetch();
    }

}
