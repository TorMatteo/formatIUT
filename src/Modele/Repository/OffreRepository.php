<?php

namespace App\FormatIUT\Modele\Repository;

use App\FormatIUT\Controleur\ControleurEntrMain;
use App\FormatIUT\Modele\DataObject\AbstractDataObject;
use App\FormatIUT\Modele\DataObject\Offre;
use Cassandra\Bigint;

class OffreRepository extends AbstractRepository
{


    public function getNomsColonnes() : array{
        return ["idOffre","nomOffre","dateDebut","dateFin","sujet","detailProjet","gratification","dureeHeures","joursParSemaine","nbHeuresHebdo","idEntreprise","typeOffre"];
    }

    public function getNomTable():string{
        return "Offre";
    }

    public function ListeParEntreprise($idEntreprise):array{
        $sql="SELECT * FROM ".$this->getNomTable()." WHERE idEntreprise=:Tag";
        $pdoStatement=ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values=array("Tag"=>$idEntreprise);
        $pdoStatement->execute($values);
        $listeOffre=array();
        foreach ($pdoStatement as $item) {
            $listeOffre[]=$this->construireDepuisTableau($item);
        }
        return $listeOffre;
    }
    public function OffresParEntrepriseDispo($idEntreprise){
        $sql="SELECT * 
                FROM ".$this->getNomTable()." o
                WHERE idEntreprise=:Tag 
                AND NOT EXISTS (
                SELECT idOffre FROM Formation f
                WHERE f.idOffre=o.idOffre)";
        $pdoStatement=ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values=array("Tag"=>$idEntreprise);
        $pdoStatement->execute($values);
        $listeOffre=array();
        foreach ($pdoStatement as $item) {
            $listeOffre[]=$this->construireDepuisTableau($item);
        }
        return $listeOffre;
    }

    public function getListeOffreParEntreprise($idEntreprise,$type,$etat): array
    {
        $sql="SELECT * FROM ". $this->getNomTable() ." o WHERE idEntreprise=:Tag";
        if ($type=="Stage" || $type=="Alternance"){
            $sql.=" AND typeOffre=:TypeTag";
            $values["TypeTag"]=$type;
        }
        if ($etat=="Dispo") {
            $sql .= " AND NOT EXISTS (SELECT idOffre FROM regarder r WHERE Etat!='Annulé' AND o.idOffre=r.idOffre)";
        }else if ($etat=="Assigné"){
            $sql.= " AND EXISTS (SELECT idOffre FROM Formation f WHERE f.idOffre=o.idOffre)";
        }
        $pdoStatement=ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values["Tag"]= $idEntreprise;
        $pdoStatement->execute($values);
        $listeOffre=array();
        foreach ($pdoStatement as $offre){
            $listeOffre[]=$this->construireDepuisTableau($offre);
        }
        return $listeOffre;
    }
    public function listOffreEtu($numEtudiant){
        $sql ="Select o.idOffre, nomOffre, dateDebut, dateFin, sujet, detailProjet, gratification, 	
        dureeHeures, joursParSemaine, nbHeuresHebdo, idEntreprise, typeOffre 
        FROM Offre o JOIN regarder r ON o.idOffre = r.idOffre WHERE numEtudiant= :TagEtu";
        $pdoStatement=ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values=array(
            "TagEtu"=>$numEtudiant
        );
        $pdoStatement->execute($values);
        $listOffre=array();
        foreach ($pdoStatement as $offre){
            $listOffre[]=$this->construireDepuisTableau($offre);
        }
        return $listOffre;

    }

    public function getListeIdOffres():array{
        $sql="SELECT idOffre FROM Offre";
        $pdoStatement=ConnexionBaseDeDonnee::getPdo()->query($sql);
        $listeId=array();
        foreach ($pdoStatement as $item=>$value) {
            $listeId[]=$value["idOffre"];
        }
        return $listeId;
    }
    public function ListeIdTypeOffre(string $type):array{
        $sql="SELECT idOffre FROM Offre WHERE typeOffre=:Tag";
        $pdoStatement=ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values=array("Tag"=>$type);
        $pdoStatement->execute($values);
        $listeID=array();
        foreach ($pdoStatement as $item=>$value) {
            $listeID[]=$value["idOffre"];
        }
        return $listeID;
    }

    public function ListeIdOffreEntreprise($idEntreprise):array{
        $sql="SELECT idOffre FROM Offre WHERE idEntreprise=:Tag";
        $pdoStatement=ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values=array("Tag"=>$idEntreprise);
        $pdoStatement->execute($values);
        $listeID=array();
        foreach ($pdoStatement as $item=>$value) {
            $listeID[]=$value["idOffre"];
        }
        return $listeID;
    }

    public function construireDepuisTableau(array $offre): Offre
    {
        $dateDebut= new \DateTime($offre['dateDebut']);
        $dateFin= new \DateTime($offre['dateFin']);
        //echo $idEntreprise;
        return new Offre($offre['idOffre'], $offre['nomOffre'], $dateDebut, $dateFin, $offre['sujet'], $offre['detailProjet'], $offre['gratification'], $offre['dureeHeures'], $offre['joursParSemaine'], $offre['nbHeuresHebdo'],$offre["idEntreprise"],$offre['typeOffre']);
    }

    protected function getClePrimaire(): string
    {
        return "idOffre";
    }




}