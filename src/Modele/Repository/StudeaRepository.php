<?php

namespace App\FormatIUT\Modele\Repository;

use App\FormatIUT\Modele\DataObject\studea;

class StudeaRepository extends AbstractRepository
{
    public function getNomTable(): string
    {
        return "InsererDonnees";
    }

    public function construireDepuisTableau(array $DataObjectTableau): studea {
        $maitreApprentissage = (bool)$DataObjectTableau[113];
        $formationMaitreApprentissage = (bool)$DataObjectTableau[114];
        $maitreApprentissage2 = (bool)$DataObjectTableau[122];
        $formationMaitreApprentissage2 = (bool)$DataObjectTableau[123];

        return new studea(
            $DataObjectTableau[0],
            $DataObjectTableau[1],
            $DataObjectTableau[2],
            $DataObjectTableau[3],
            $DataObjectTableau[4],
            $DataObjectTableau[5],
            $DataObjectTableau[6],
            $DataObjectTableau[7],
            $DataObjectTableau[8],
            $DataObjectTableau[9],
            $DataObjectTableau[10],
            $DataObjectTableau[11],
            $DataObjectTableau[12],
            $DataObjectTableau[13],
            $DataObjectTableau[14],
            $DataObjectTableau[15],
            $DataObjectTableau[16],
            $DataObjectTableau[17],
            $DataObjectTableau[18],
            $DataObjectTableau[19],
            $DataObjectTableau[20],
            $DataObjectTableau[21],
            $DataObjectTableau[22],
            $DataObjectTableau[23],
            $DataObjectTableau[24],
            $DataObjectTableau[25],
            $DataObjectTableau[26],
            $DataObjectTableau[27],
            $DataObjectTableau[28],
            $DataObjectTableau[29],
            $DataObjectTableau[30],
            $DataObjectTableau[31],
            $DataObjectTableau[32],
            $DataObjectTableau[33],
            $DataObjectTableau[34],
            $DataObjectTableau[35],
            $DataObjectTableau[36],
            $DataObjectTableau[37],
            $DataObjectTableau[38],
            $DataObjectTableau[39],
            $DataObjectTableau[40],
            $DataObjectTableau[41],
            $DataObjectTableau[42],
            $DataObjectTableau[43],
            $DataObjectTableau[44],
            $DataObjectTableau[45],
            $DataObjectTableau[46],
            $DataObjectTableau[47],
            $DataObjectTableau[48],
            $DataObjectTableau[49],
            $DataObjectTableau[50],
            $DataObjectTableau[51],
            $DataObjectTableau[52],
            $DataObjectTableau[53],
            $DataObjectTableau[54],
            $DataObjectTableau[55],
            $DataObjectTableau[56],
            $DataObjectTableau[57],
            $DataObjectTableau[58],
            $DataObjectTableau[59],
            $DataObjectTableau[60],
            $DataObjectTableau[61],
            $DataObjectTableau[62],
            $DataObjectTableau[63],
            $DataObjectTableau[64],
            $DataObjectTableau[65],
            $DataObjectTableau[66],
            $DataObjectTableau[67],
            $DataObjectTableau[68],
            $DataObjectTableau[69],
            $DataObjectTableau[70],
            $DataObjectTableau[71],
            $DataObjectTableau[72],
            $DataObjectTableau[73],
            $DataObjectTableau[74],
            $DataObjectTableau[75],
            $DataObjectTableau[76],
            $DataObjectTableau[77],
            $DataObjectTableau[78],
            $DataObjectTableau[79],
            $DataObjectTableau[80],
            $DataObjectTableau[81],
            $DataObjectTableau[82],
            $DataObjectTableau[83],
            $DataObjectTableau[84],
            $DataObjectTableau[85],
            $DataObjectTableau[86],
            $DataObjectTableau[87],
            $DataObjectTableau[88],
            $DataObjectTableau[89],
            $DataObjectTableau[90],
            $DataObjectTableau[91],
            $DataObjectTableau[92],
            $DataObjectTableau[93],
            $DataObjectTableau[94],
            $DataObjectTableau[95],
            $DataObjectTableau[96],
            $DataObjectTableau[97],
            $DataObjectTableau[98],
            $DataObjectTableau[99],
            $DataObjectTableau[100],
            $DataObjectTableau[101],
            $DataObjectTableau[102],
            $DataObjectTableau[103],
            $DataObjectTableau[104],
            $DataObjectTableau[105],
            $DataObjectTableau[106],
            $DataObjectTableau[107],
            $DataObjectTableau[108],
            $DataObjectTableau[109],
            $DataObjectTableau[110],
            $DataObjectTableau[111],
            $DataObjectTableau[112],
            $maitreApprentissage,
            $formationMaitreApprentissage,
            $DataObjectTableau[115],
            $DataObjectTableau[116],
            $DataObjectTableau[117],
            $DataObjectTableau[118],
            $DataObjectTableau[119],
            $DataObjectTableau[120],
            $DataObjectTableau[121],
            $maitreApprentissage2,
            $formationMaitreApprentissage2,
            $DataObjectTableau[124],
            $DataObjectTableau[125],
            $DataObjectTableau[126],
            $DataObjectTableau[127],
            $DataObjectTableau[128],
            $DataObjectTableau[129],
            $DataObjectTableau[130],
            $DataObjectTableau[131],
            $DataObjectTableau[132],
            $DataObjectTableau[133],
            $DataObjectTableau[134],
            $DataObjectTableau[135],
            $DataObjectTableau[136],
            $DataObjectTableau[137],
            $DataObjectTableau[138],
            $DataObjectTableau[139],
            $DataObjectTableau[140],
            $DataObjectTableau[141],
            $DataObjectTableau[142],
        );
    }

    public function callProcedure(studea $studea) : bool{
        $sql='CALL insertionStudea(:numEtudiant, :prenomEtudiant, :nomEtudiant, :loginEtudiant);';
        $pdoStatement=ConnexionBaseDeDonnee::getPdo()->prepare($sql);
        $values = array("numEtudiant" => $studea->getId(), "nomEtudiant" => $studea->getNomAlternant(), "prenomEtudiant"=>$studea->getPrenomAlternant(), "loginEtudiant"=>"loginRandom");
        $pdoStatement->execute($values);
        if($pdoStatement->fetch()){
            return true;
        } else{
            return false;
        }
    }

    protected function getNomsColonnes(): array
    {
        return array();
    }

    protected function getClePrimaire(): string
    {
        return "codeUFR";
    }
}
