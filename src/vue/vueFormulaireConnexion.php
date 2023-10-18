<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styleIndex.css">
    <title>Format'IUT</title>
    <link rel="icon" type="image/png" href="Data/UM.png"/>
</head>
<body>
<p></p>
<div id="center">


    <div id="desc">
        <div id="zoneLogo">
            <img src="Data/LogoIutMontpellier-removed.png" alt="logo" id="logo">
        </div>

        <h1>BIENVENUE SUR FORMAT'IUT</h1>
        <div id="texteCentre">
            <p id="premiereLigne"> LA PLATEFORME DE GESTION ET DE CONSULATION DES STAGES ET DES ALTERNANCES DE L'IUT
                MONTPELLIER-SETE</p>
            <!-- <p>Vous êtes une entreprise et voulez faire une proposition de stage ou d'alternance ?</p> -->
        </div>
        <div id="boutonCentre">
            <a href="proposition.html"><input id="boutonredirect" type="button" value="CONSULTER"></a>
        </div>
    </div>


    <div id="formulaire">
        <div id="form">
            <h2>CONNEXION</h2>
            <div id="erreur">
                <?php
                if (isset($_GET['erreur'])) {
                    $err = $_GET['erreur'];
                    if ($err > 0)
                        echo "<div id='imageErreur'>";
                    echo "<img src='Data/attention.png' alt='erreur' id='attention'>";
                    echo "</div>";
                    echo "<p style='color:red'>Utilisateur ou mot de passe incorrect</p>";
                }
                ?>
            </div>
            <form action="connexion.php" method="post">
                <div id="wrapInput">
                    <div id="Userlogin">
                        <img src="Data/utilisateur.png" alt="user" id="user">
                        <input type="text" name="login" id="login" required>
                    </div>
                    <div id="Usermdp">
                        <img src="Data/cadenas.png" alt="cadenas" id="cadenas">
                        <input type="password" name="mdp" id="mdp" required>
                    </div>
                </div>
                <input id="bouton" type="submit" value="CONNEXION">
            </form>
        </div>
    </div>

</div>

</body>
</html>
