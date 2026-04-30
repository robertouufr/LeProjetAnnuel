<?php
$page = 'Inscription';
require ('../includes/connexion.php');
require ('../includes/header.php');
session_start();
?>

    <div class="auth-page">
        <div class="auth-container">

            <h1>Inscription</h1>
            <p class="auth-subtitle">Créez votre compte gratuitement</p>

            <?php if (isset($_SESSION['status'])) { ?>
                <p class="register-warning"><?php echo $_SESSION['status']; unset($_SESSION['status']); ?></p>
            <?php } ?>

            <form method="post" action="code.php">

                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" placeholder="Entrez votre nom" name="nom" id="nom" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" placeholder="Entrez votre email" name="email" id="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Numéro de téléphone</label>
                    <input type="text" placeholder="Entrez votre numéro" name="phone" id="phone" required>
                </div>

                <div class="form-group">
                    <label for="psw">Mot de passe</label>
                    <input type="password" placeholder="Choisissez un mot de passe" name="psw" id="psw" required>
                </div>

                <div class="form-group">
                    <label for="psw_repeat">Répétez le mot de passe</label>
                    <input type="password" placeholder="Répétez votre mot de passe" name="psw_repeat" id="psw_repeat" required>
                </div>

                <button type="submit" name="incrire_btn" class="registerbtn">S'inscrire</button>

            </form>

            <hr class="auth-divider">

            <div class="container_signin">
                <p>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>
            </div>

        </div>
    </div>

<?php
require ('../includes/footer.php');
?>