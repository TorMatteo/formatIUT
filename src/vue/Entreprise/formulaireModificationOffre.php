<html>
<head>
    <link rel="stylesheet" href="../ressources/css/styleFormulaireCreationOffre.css">
</head>
<body>
<div id="center">
    <div class="wrapDroite">
        <a href="?controleur=EntrMain&action=afficherFormulaireModificationOffre&idOffre=<?= $offre->getIdOffre() ?>">
            <button>Revenir sur les changements</button>
        </a>

        <form method="post" action="../web/controleurFrontal.php">
            <h1>MODIFIEZ VOTRE OFFRE ICI</h1>

            <label class="labelFormulaire" for="type_id">Type d'Offre </label>
            <div class="inputCentre">
                <select name="typeOffre">
                    <option value="Stage" name="typeOffre"
                            id="type_id" <?php if ($offre->getTypeOffre() == "Stage") echo 'selected' ?>>Stage
                    </option>
                    <option value="Alternance" name="typeOffre"
                            id="type_id" <?php if ($offre->getTypeOffre() == "Alternance") echo 'selected' ?>>Alternance
                    </option>
                </select>
            </div>

            <label class="labelFormulaire" for="nomOffre_id">Profession visée par l'offre</label>
            <div class="inputCentre">
                <input class="inputFormulaire" type="text" name="nomOffre" id="nomOffre_id" required
                       value="<?= $offre->getNomOffre() ?>" maxlength="24">
            </div>

            <label class="labelFormulaire" for="dateDebut_id">Date de début de l'offre</label>
            <div class="inputCentre">
                <input class="inputFormulaire" type="date" name="dateDebut" id="dateDebut_id"
                       value="<?= $offre->getDateDebut()->format("Y-m-d"); ?>" required>
            </div>

            <label class="labelFormulaire" for="dateFin_id">Date de fin de l'offre</label>
            <div class="inputCentre">
                <input class="inputFormulaire" type="date" name="dateFin" id="dateFin_id"
                       value="<?= $offre->getDateFin()->format("Y-m-d"); ?>" required>
            </div>

            <label class="labelFormulaire" for="sujet_id">Sujet bref de l'offre</label>
            <div class="inputCentre">
                <input class="inputFormulaire" type="text" name="sujet" id="sujet_id"
                       value="<?= $offre->getSujet() ?>" required maxlength="50">
            </div>

            <label class="labelFormulaire" for="detailProjet_id">Détails du projet</label>
            <br/>
            <div class="grandInputCentre">
                    <textarea class="inputFormulaire" name="detailProjet" id="detailProjet_id" required maxlength="255"
                    ><?= $offre->getDetailProjet() ?>
                    </textarea>
            </div>

            <label class="labelFormulaire" for="gratification_id">Rémunération de l'offre par mois</label>
            <div class="inputCentre">
                <input class="inputFormulaire" type="number" name="gratification" id="gratification_id"
                       value="<?= $offre->getGratification() ?>" required maxlength="4">
            </div>

            <label class="labelFormulaire" for="dureeHeures_id">Durée en heure</label>
            <div class="inputCentre">
                <input class="inputFormulaire" type="number" name="dureeHeures" id="dureeHeures_id"
                       value="<?= $offre->getDureeHeures() ?>" required maxlength="4">
            </div>

            <label class="labelFormulaire" for="jourParSemaine_id">Nombre de jours par semaine</label>
            <div class="inputCentre">
                <input class="inputFormulaire" type="number" name="joursParSemaine" id="jourParSemaine_id"
                       value="<?= $offre->getJoursParSemaine() ?>" required maxlength="1">
            </div>

            <label class="labelFormulaire" for="nbHeureHebdo_id">Nombre d'heures hebdomadaires</label>
            <div class="inputCentre">
                <input class="inputFormulaire" type="number" name="nbHeuresHebdo" id="nbHeureHebdo_id"
                       value="<?= $offre->getNbHeuresHebdo() ?>" required maxlength="2">
            </div>

            <div class="boutonsForm">
                <input type="submit" value="Envoyer" formaction="?action=modifierOffre&controleur=EntrMain">
            </div>
        </form>

    </div>
</div>
</body>
</html>
