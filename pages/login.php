<?php
// On définit le nom de la page pour le header
$page = 'Connexion';

// Connexion à la base de données et inclusion du header
require ('../includes/connexion.php');
require ('../includes/header.php');

// On démarre la session pour gérer les messages d'erreur et la connexion
session_start();
?>

    <section class="page_connexion">

        <div class="header">
            <div class="logo">
                <img src="../assets/images/portefeuille%20(1).png" alt="logo">
            </div>
            <h1>Mon Pti Budget</h1>
            <p>Gérer vos finances simplement</p>
        </div>

        <div class="formulaire-connexion">
            <h2>Connexion</h2>

            <?php if (isset($_SESSION['status'])) { ?>
                <p class="warning">
                    <?php
                    echo $_SESSION['status'];
                    unset($_SESSION['status']); // On efface le message après l'affichage
                    ?>
                </p>
            <?php } ?>

            <form method="post" action="code.php">

                <div class="formulaire">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" placeholder=" ✉ vousexemple@gmail.com" required>
                </div>

                <div class="formulaire">
                    <label for="motdepasse">Mot de passe</label>
                    <input type="password" id="motdepasse" name="psw" placeholder=" ⌦ ••••••••••" required>
                </div>

                <a href="passforget.php" class="forgot-link">Mot de passe oublié ?</a>

                <button type="submit" name="connexion_btn">Se connecter</button>

                <p class="inscription">
                    Pas encore de compte ? <a href="register.php">Créer un compte</a>
                </p>

            </form>
        </div>

    </section>

<?php
// Inclusion du footer pour fermer les balises correctement
require ('../includes/footer.php');
?>