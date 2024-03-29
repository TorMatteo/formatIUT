<div id="center">

    <div class="presentation">

        <form method="post">
            <fieldset>
                <legend> Ma convention :</legend>
                <?php
                if($offre->getTypeOffre() == "Alternance"){
                    echo '<p><label for="offre_id">Votre alternance : </label>';
                }
                else{
                    echo '<p><label for="offre_id">Votre stage : </label>';
                } ?>
                <input type="text" value="<?=htmlspecialchars($offre->getNomOffre()) ?>" name="nomOffre" id="offre_id"  readonly required>
                </p>
                <p> Informations de l'étudiant :</p>
                <p><label for="num_id"> N° étudiant </label>
                    <input type="text" value="<?= $etudiant->getNumEtudiant(); ?>" name="numEtudiant" id="num_id"
                           readonly required>
                </p>
                <p><label for="nom_id"> Nom </label>
                    <input type="text" value="<?= htmlspecialchars($etudiant->getNomEtudiant()); ?>" name="nomEtudiant" id="nom_id"
                           readonly required>
                </p>
                <p><label for="prenom_id"> Nom </label>
                    <input type="text" value="<?= htmlspecialchars($etudiant->getPrenomEtudiant()); ?>" name="prenomEtudiant"
                           id="prenom_id" readonly required>
                </p>
                <p><label for="tel_id"> N° tel </label>
                    <input type="text" value="<?= htmlspecialchars($etudiant->getTelephone()) ?>" name="telephone" id="tel_id" readonly
                           required></p>
                <p><label for="mail_id">Mail</label>
                    <input type="text" value="<?= htmlspecialchars($etudiant->getMailPerso()); ?>" name="mailEtu" id="mail_id" readonly
                           required></p>
                <p><label for="assu_id">Assurance</label>
                    <input type="text" value="<?=htmlspecialchars($offre->getAssurance()); ?>" name="assurance" id="assu_id" readonly
                           required></p>
                <p>Informations de l'entreprise :</p>
                <p><label for="sir_id">Siret</label>
                    <input type="number" value="<?= $entreprise->getSiret(); ?>" name="siret" id="assu_id" readonly
                           required></p>
                <p><label for="nomEntr_id"> Nom entreprise </label>
                    <input type="text" value="<?= htmlspecialchars($entreprise->getNomEntreprise()); ?>" name="nomEntreprise" id="nomEntr_id" readonly
                           required>
                </p>
                <p><label for="adrEntr_id">Adresse Entreprise</label>
                    <input type="text"  value="<?= htmlspecialchars($entreprise->getAdresseEntreprise()); ?>" name="adresseEntr" id="adrEntr_id" readonly
                           required></p>
                <p><label for="villeEntr_id"> Ville </label>
                    <input type="text"  value="<?= htmlspecialchars($villeEntr->getNomVille()); ?>" name="villeEntr" id="villeEntr_id" readonly
                           required>
                <p><label for="cpEntr_id">Code postal </label>
                    <input type="text" value="<?= $villeEntr->getCodePostal(); ?>" name="codePostalEntr" id="cpEntr_id" readonly
                           required></p>
                <?php if($offre->getTypeOffre() == "Alternance"){
                    echo '<p><label for="debut_id"> Alternance : Date début </label>
                    <input type="date" value="'. $offre->getDateDebut() .'" name="dateDebut" id="debut_id" readonly
                           required>
                    <label for="fin_id"> Date fin </label>
                    <input type="date" value="'. $offre->getDateFin() .'" name="dateFin" id="fin_id" readonly
                           required></p>
                <p>
                    <label class="labelFormulaire" for="objStage_id">Programme de formation : </label>
                    <input class="inputFormulaire" value="'.htmlspecialchars($offre->getObjectifOffre()).'" name="objectifOffre" id="objStage_id"
                              readonly required>
                </p>';
                }
                else {
                    echo '<p><label for="debut_id"> Stage : Date début </label>
                    <input type="date" value="'. $offre->getDateDebut() .'" name="dateDebut" id="debut_id" required>
                    <label for="fin_id"> Date fin </label>
                    <input type="date" value="'. $offre->getDateFin() .'" name="dateFin" id="fin_id" required></p>
                <p>
                    <label class="labelFormulaire" for="objStage_id">Objectifs du stage : </label>
                    <input class="inputFormulaire" value="'.htmlspecialchars($offre->getObjectifOffre()).'" name="objectifOffre" id="objStage_id"
                              readonly required>
                </p>';
                }?>
                <input type="hidden" value="<?=date("d-m-Y");?>" name="dateTransmission">
                <input type="submit" value="Valider" formaction="?action=validerConvention&controleur=adminMain">
                <input type="submit" value="Rejeter" formaction="?action=rejeterConvention&controleur=adminMain">
            </fieldset>
        </form>
    </div>
</div>