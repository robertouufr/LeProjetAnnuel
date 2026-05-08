<?php
$page = 'Inscription';
require ('../includes/connexion.php');
require ('../includes/header.php');
session_start();
?>

    <div class="formulaire-connexion">
        <h2>Inscription</h2>

        <?php if (isset($_SESSION['status'])) { ?>
            <p class="register-warning"><?php echo $_SESSION['status']; unset($_SESSION['status']); ?></p>
        <?php } ?>

        <form method="post" action="code.php">

            <div class="formulaire">
                <label for="nom">Nom Complet</label>
                <input type="text" placeholder="Ex : Enzo Druere" name="nom" id="nom" required>
            </div>

            <div class="formulaire">
                <label for="email">Email</label>
                <input type="email" placeholder="✉ vousexemple@gmail.com" name="email" id="email" required>
            </div>

            <div class="formulaire">
                <label for="phone">Téléphone</label>
                <input type="text" placeholder="Ex : 06 54 47 85 45" name="phone" id="phone" required>
            </div>

            <div class="formulaire">
                <label for="psw">Mot de passe</label>
                <input type="password" placeholder="⌦ ••••••••••" name="psw" id="psw" required>
            </div>

            <div class="formulaire">
                <label for="psw_repeat">Confirmer mot de passe</label>
                <input type="password" placeholder="⌦ ••••••••••" name="psw_repeat" id="psw_repeat" required>
            </div>

            <button type="submit" name="incrire_btn">Créer mon compte</button>

            <p class="inscription">
                Déjà un compte ? <a href="login.php">Se connecter</a>
            </p>

        </form>
    </div>

<?php
require ('../includes/footer.php');
?>