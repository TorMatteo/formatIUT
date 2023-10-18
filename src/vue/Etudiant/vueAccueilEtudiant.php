<html>
<head>
    <link rel="stylesheet" href="../ressources/css/styleVueAccueilEtudiant.css">
</head>
<body>
<div class="conteneurPrincipal">
    <div class="conteneurBienvenue">
        <div class="texteBienvenue">
            <h3>Bonjour, <?php
                $etu=((new \App\FormatIUT\Modele\Repository\EtudiantRepository())->getObjectParClePrimaire(\App\FormatIUT\Controleur\ControleurEtuMain::getCleEtudiant()));
                echo $etu->getPrenomEtudiant();
                ?></h3>

            <p>Voici les dernières nouveautés en offres de stage et d'alternance :</p>
        </div>

        <div class="imageBienvenue">
            <img src="../ressources/images/bienvenueRemoved.png" alt="image de bienvenue" class="imageMoyenne">
        </div>
    </div>

    <div class="notifications">
        <h4>Vos Notifications :</h4>
        <!-- affichage d'un erreur pour dire qu'il n'y a pas de notifications -->
        <div class="wrapErreur">
            <img src="../ressources/images/erreur.png" alt="image d'erreur" class="imageErreur">
            <h4>Aucune notification pour le moment.</h4>
        </div>
    </div>

    <div class="nouveautesWrap">
        <div class="nouveautesStages">
            <h4>Nouveautés Stages de la semaine :</h4>
            <div class="conteneurAnnonces">
                <?php /**
                 * @param $listeStage
                 * @return array
                 */
                function extracted($listeStage): array
                {
                    for ($i = 0; $i < sizeof($listeStage); $i++) {
                        $entreprise = (new \App\FormatIUT\Modele\Repository\EntrepriseRepository())->getObjectParClePrimaire($listeStage[$i]->getSiret());
                        $ville=(new \App\FormatIUT\Modele\Repository\VilleRepository())->getObjectParClePrimaire($entreprise->getVille());
                        $lien = "?controleur=EtuMain&action=afficherVueDetailOffre&idOffre=" . $listeStage[$i]->getIdOffre();
                        echo '<a href =' . $lien . ' >
                    <div class="imagesAnnonce" >';
                        echo '<img src="data:image/jpeg;base64,'.base64_encode( $entreprise->getImg()).'"/>
                    </div >
                    <div class="texteAnnonce" >
                        <h4 >';
                        echo $entreprise->getNomEntreprise();
                        echo ' </h4 >
                        <div class="detailsAnnonce" >
                            <div class="lieuRemun" >
                                <div class="lieuAnnonce" >
                                    <img src = "../ressources/images/emplacement.png" alt = "image" class="imagesPuces" >
                                    <p class="petitTexte" >';
                        echo $ville->getNomVille();
                        echo ' </p >
                                </div >
                                <div class="remunAnnonce" >
                                    <img src = "../ressources/images/euros.png" alt = "image" class="imagesPuces" >
                                    <p class="petitTexte" >';
                        echo $listeStage[$i]->getGratification();
                        echo ' €/ mois</p >
                                </div >
                            </div >
                            <div class="dureeLibelle" >
                                <div class="dureeAnnonce" >
                                    <img src = "../ressources/images/histoire.png" alt = "image" class="imagesPuces" >
                                    <p class="petitTexte" >';
                        echo ($listeStage[$i]->getDateDebut()->diff($listeStage[$i]->getDateFin()))->format('%m mois');

                        echo '</p >
                                </div >
                                <div class="libelleAnnonce" >
                                    <img src = "../ressources/images/emploi.png" alt = "image" class="imagesPuces" >
                                    <p class="petitTexte" >';
                        $nomHTML=htmlspecialchars($listeStage[$i]->getNomOffre());
                        echo $nomHTML;
                        echo '</p >
                                </div >
                            </div >
                        </div >
                    </div >
                </a >';
                    }
                    return array($i, $entreprise, $lien);
                }
                if(empty($listeStage)){
                    echo "Vide";
                }
                else list($i, $entreprise, $lien) = extracted($listeStage);
                ?>

            </div>
        </div>
        <div class="nouveautesAltern">
            <h4>Nouveautés Alternances de la semaine :</h4>
            <div class="conteneurAnnonces">
                <?php if (empty($listeAlternance)){
                    echo "Vide";
                }
                else list($i, $entreprise, $lien) = extracted($listeAlternance);
                ?>
        </div>
    </div>

</div>
</body>
</html>