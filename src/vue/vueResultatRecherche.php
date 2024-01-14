<?php

use App\FormatIUT\Lib\ConnexionUtilisateur;
use App\FormatIUT\Lib\Recherche\AffichagesRecherche\EntrepriseRecherche;
use App\FormatIUT\Lib\Recherche\AffichagesRecherche\FormationRecherche;

if (!isset($_REQUEST['triPar'])) {
    $_REQUEST['triPar'] = "type";
}

/** @var array{ Entreprise: EntrepriseRecherche[], Formation: FormationRecherche[] } $liste */

?>

<div class="mainRecherche">

    <div class="bodyRecherche">

        <div class="controleRech">
            <h2 class="titre rouge">Effectuez une recherche sur Format'IUT</h2>
            <?php
            echo $codeRecherche;
            ?>

            <div class="trierPar">
                <h4 class="titre">Trier Par :</h4>

                <?php
                $url = $_REQUEST['recherche'];

                ?>

                <form method="get" id="formTrierPar">
                    <label>
                        <select name="triPar" onchange="this.form.submit()">
                            <option value="type">Type</option>
                            <option value="date">Date</option>
                            <option value="asc">Ordre Alphabétique</option>
                        </select>
                    </label>
                    <input type="hidden" name="controleur" value="AdminMain">
                    <input type="hidden" name="action" value="rechercher">
                    <input type="hidden" name="recherche" value="<?php echo $url ?>">
                </form>

                <a href="#filters" class="goFiltres">Filtres</a>
            </div>

        </div>

        <div class="resultatsRecherche">
            <?php
            if (!empty($liste)) {
                $i = 0;
                foreach ($liste as $type => $elements) {
                    foreach ($elements as $objet) {
                        $red = "";
                        $n = 2;
                        $row = intdiv($i, $n);
                        $col = $i % $n;
                        if (($row + $col) % 2 != 0) {
                            $red = "demi";
                        }
                        echo '<a class="element ' . $red . '" href="' . $objet->getLienAction() . '">
                            <img src="' . $objet->getImage() . '" alt="pp">

                            <div>
                                <h3 class="titre rouge">' . $objet->getTitreRouge() . '</h3>';
                        echo $objet->getTitres();
                        echo '</div></a>';
                        $i++;
                    }
                }
            }

            ?>
        </div>

    </div>

    <div id="filters" class="parametresRecherche">

        <div class="vitrine">
            <img src="../ressources/images/recherchez.png" alt="">
            <h3 class="titre rouge">Paramètres de Recherche</h3>
        </div>

        <div class="allOptions">
            <form method="get" id="options">


                <?php
                $privilege = ConnexionUtilisateur::getUtilisateurConnecte()->getFiltresRecherche();
                foreach ($privilege as $name => $filtres) {
                    $name2 = ucfirst($name) . "s";
                    echo '<div class="generique">
                    <h4 class="titre">' . $name2 . '</h4>
                    <span>
                        <label for="' . $name2 . '"></label><input class="switch" type="checkbox" name="' . $name2 . '"
                                                               id="' . $name2 . '" value="on" onchange="this.form.submit()"';
                    if (isset($_REQUEST[$name2])) {
                        echo 'checked';
                    }
                    echo '>
                    </span>
                </div>';
                }
                ?>

                <div class="filtresDetail">
                    <?php
                    $liste = ConnexionUtilisateur::getUtilisateurConnecte()->getFiltresRecherche();

                    foreach ($liste as $recherchables => $filtres) {
                        if (isset($_REQUEST[$recherchables . "s"])) {
                            ;
                            foreach ($filtres as $filtre) {
                                if (!in_array("obligatoire", $filtre)) {
                                    echo '
                                <span class="filtre">
                                    <label for="' . $filtre['value'] . '">' . ucfirst($filtre["label"]) . '</label>
                                    <input class="filter" type="checkbox" name="' . $filtre['value'] . '" id="' . $filtre['value'] . '" value="' . $filtre['value'] . '" onchange="this.form.submit()" ';
                                    if (isset($_REQUEST[$filtre["value"]])) {
                                        echo 'checked';
                                    }
                                    echo '>
                                </span>
                                ';
                                }
                            }
                        }
                    }

                    ?>
                </div>

                <input type="hidden" name="controleur" value="Main">
                <input type="hidden" name="action" value="rechercher">
                <input type="hidden" name="recherche" value="<?php echo $url ?>">
            </form>
        </div>
    </div>

</div>
