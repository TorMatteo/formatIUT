<?php

use App\FormatIUT\Configuration\Configuration;

$admin = (new \App\FormatIUT\Modele\Repository\ProfRepository())->getObjectParClePrimaire(\App\FormatIUT\Lib\ConnexionUtilisateur::getLoginUtilisateurConnecte());
?>

<div class="centreCompte">
    <script>window.onload = function () {
            afficherPageCompteAdmin("compte");
        };</script>
    <div class="menuAdmin">
        <div class="sousMenuAdmin compteM" onclick="afficherPageCompteAdmin('compte')">
            <img src="../ressources/images/profil.png" alt="profil">
            <div>
                <h3 class="titre">Mon Compte</h3>
            </div>
        </div>

        <div class="sousMenuAdmin notifsM" onclick="afficherPageCompteAdmin('notifs')">
            <img src="../ressources/images/notif.png" alt="profil">
            <div>
                <h3 class="titre">Notifications</h3>
            </div>
        </div>

        <?php
        if (\App\FormatIUT\Lib\ConnexionUtilisateur::getTypeConnecte()=="Administrateurs") {
            $prof = "profs";
            echo'
            <div class="sousMenuAdmin profsM" onclick="afficherPageCompteAdmin(\'profs\')" >
            <img src = "../ressources/images/professeur.png" alt = "profil" >
            <div >
                <h3 class="titre" > Permissions / Rôles</h3 >
            </div >
        </div >
        
        <div class="sousMenuAdmin etuM" onclick="afficherPageCompteAdmin(\'etu\')">
            <img src="../ressources/images/etudiants.png" alt="profil">
            <div>
                <h3 class="titre">Ajout d\'Étudiants</h3>
            </div>
        </div>


        ';
        }

        ?>


    </div>

    <div class="mainAdmins" id="compte">
        <h2 class="titre rouge">Modifier mon Profil</h2>
        <form method="POST" enctype="multipart/form-data">
            <h3 class="titre">Mon Avatar</h3>
            <div class="avatar">
                <?php
                echo "<img src='" . App\FormatIUT\Configuration\Configuration::getUploadPathFromId($admin->getImg()) . "' alt='admin'>";
                ?>
                <div>
                    <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
                    <input type="file" name="pdp" size="500">
                    <p>Glissez-déposez un fichier ou parcourez vos fichiers. JPEG et PNG uniquement</p>
                </div>
            </div>

            <h3 class="titre">Nom</h3>
            <div class="inputCentre">
                <label for="nom_id"></label><input type="text" value='<?= htmlspecialchars($admin->getNomProf()); ?>' name="nom" id="nom_id"
                                                   required maxlength="50">
            </div>

            <h3 class="titre">Prénom</h3>
            <div class="inputCentre">
                <label for="prenom_id"></label><input type="text" value='<?= htmlspecialchars($admin->getPrenomProf()); ?>' name="prenom"
                                                      id="prenom_id" required maxlength="50">
            </div>

            <div class="inputCentre">
                <input type="submit" value="Enregistrer" formaction="?action=mettreAJour&controleur=AdminMain">
            </div>


        </form>

    </div>

    <div class="mainAdmins" id="notifs">
        <h2 class="titre rouge">Gérer les paramètres de Notifications</h2>
    </div>

    <div class="mainAdmins" id="profs">
        <h2 class="titre rouge">Gérer les Permissions du Site</h2>

        <div class="wrapPerms">
            <h3 class="titre">Mes collègues</h3>
            <div>
                <?php
                $listeProfs = (new \App\FormatIUT\Modele\Repository\ProfRepository())->getListeObjet();

                if ($listeProfs != null) {
                    foreach ($listeProfs as $prof) {
                        if ($prof->getLoginProf() != $admin->getLoginProf()) {

                            echo '
                    <div class="prof">
                        <div class="permImg">
                            <img src="' . App\FormatIUT\Configuration\Configuration::getUploadPathFromId($prof->getImg()) . '" alt="admin">
                        </div>
                        <div class="perms">';
                                if ($prof->getPrenomProf() == null)
                                    echo '<h3 class="titre rouge">Prenom inconnu</h3>';
                                else
                                    echo '<h3 class="titre rouge">' . htmlspecialchars($prof->getPrenomProf()) . ' ' . htmlspecialchars($prof->getNomProf()) . '</h3>';
                                if ($prof->getLoginProf() == null || $prof->getLoginProf() == "")
                                    echo '<h3 class="titre rouge">Login inconnu</h3>';
                                else
                                    echo '<p>Login : ' . htmlspecialchars($prof->getLoginProf()) . '</p>
                            ';

                            if ($prof->isEstAdmin()) {
                                echo '
                        <p>Grade : Administrateur</p>
                        <div class="wrapBoutons">
                            <a href="?action=retrograderProf&controleur=AdminMain&loginProf=' . $prof->getLoginProf() . '">Rétrograder</a>
                            <a style="margin-left: 10px" href="?action=afficherVueProf&loginProf=' .$prof->getLoginProf(). '">Voir profil</a>
                        </div>
                        ';

                            } else {
                                echo '
                        <p>Grade : Enseignant</p>
                        <div class="wrapBoutons">
                            <a href="?action=promouvoirProf&controleur=AdminMain&loginProf=' . $prof->getLoginProf() . '">Promouvoir</a>
                            <a style="margin-left: 10px" href="?action=afficherVueProf&loginProf=' .$prof->getLoginProf(). '">Voir profil</a>
                        </div>
                        ';
                            }
                            echo '
                            
                        </div>
                    </div>';
                        }
                    }
                } else {
                    echo '
                    <div class="wrapErreur">
                    <img src="../ressources/images/erreur.png" alt="erreur">
                    <h3 class="titre">Aucun Enseignant ou Administrateur trouvé.</h3>
                    </div>

                ';
                }
                ?>
            </div>

        </div>

    </div>

    <div class="mainAdmins" id="etu">
        <h2 class="titre rouge">Ajouter un Étudiant sur le site</h2>

        <div class="ajoutEtu">
            <form method="POST">

                <label for="numEtudiant_id">Numéro étudiant</label> :
                <div class="inputCentre">
                    <input type="text" placeholder="22207651" name="numEtudiant"
                           id="numEtudiant_id" required>
                </div>

                <label for="nomEtudiant_id">Nom</label> :
                <div class="inputCentre">
                    <input type="text" placeholder="Smith" name="nomEtudiant"
                           id="nomEtudiant_id" required maxlength="32">
                </div>

                <label for="prenomEtudiant_id">Prénom</label> :
                <div class="inputCentre">
                    <input type="text" placeholder="John" name="prenomEtudiant"
                           id="prenomEtudiant_id" required maxlength="32">
                </div>

                <label for="loginEtudiant_id">Login</label> :
                <div class="inputCentre">
                    <input type="text" placeholder="smithj" name="loginEtudiant"
                           id="loginEtudiant_id" required maxlength="32">
                </div>

                <label for="mailUniversitaire_id">Mail universitaire</label> :
                <div class="inputCentre">
                    <input type="text" placeholder="john.smith@etu.umontpellier.fr" name="mailUniversitaire"
                           id="mailUniversitaire_id" required maxlength="50">
                </div>

                <label for="groupe_id">Groupe de classe</label> :
                <div class="inputCentre">
                    <input type="text" placeholder="Q1" name="groupe"
                           id="groupe_id" required maxlength="2">
                </div>

                <label for="parcours_id">Parcours du BUT</label> :
                <div class="inputCentre">
                    <input type="text" placeholder="RACDV" name="parcours"
                           id="parcours_id" required maxlength="5">
                </div>

                <div class="boutonsForm">
                    <input type="submit" value="Envoyer" formaction="?action=ajouterEtudiant&controleur=AdminMain">
                </div>
            </form>
        </div>

    </div>


</div>