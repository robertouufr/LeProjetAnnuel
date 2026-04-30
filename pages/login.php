<?php
$page = 'Connexion';
require ('../includes/connexion.php');
require ('../includes/header.php');
session_start();
?>

    <div class="auth-page">
        <div class="auth-container">

            <h1>Connexion</h1>
            <p class="auth-subtitle">Accédez à votre espace personnel</p>

            <?php if (isset($_SESSION['status'])) { ?>
                <p class="warning"><?php echo $_SESSION['status']; unset($_SESSION['status']); ?></p>
            <?php } ?>

            <form method="post" action="code.php">

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" placeholder="Entrez votre email" name="email" id="email" required>
                </div>

                <div class="form-group">
                    <label for="psw">Mot de passe</label>
                    <input type="password" placeholder="Entrez votre mot de passe" name="psw" id="psw" required>
                </div>

                <a href="passforget.php" class="forgot-link">Mot de passe oublié ?</a>

                <button type="submit" name="connexion_btn" class="registerbtn">Se connecter</button>

            </form>

            <hr class="auth-divider">

            <div class="container_signin">
                <p>Vous n'avez pas encore de compte ? <a href="register.php">Inscrivez-vous ici</a></p>
            </div>

        </div>
    </div>

<?php
require ('../includes/footer.php');
?>