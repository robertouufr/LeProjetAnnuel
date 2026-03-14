
<?php
$page = 'Connexion';
require ('../includes/connexion.php');
require ('../includes/header.php');
session_start();
?>

<form method="post" action="code.php">
    <?php if (isset($_SESSION['status'])) { ?>
        <p class="warning"><?php echo $_SESSION['status']; unset($_SESSION['status']); ?></p>
    <?php } ?>

    <hr>
    <label for="email"><b>Email</b></label>
    <input type="text" placeholder="Entrez Email" name="email" id="email" required>

    <label for="psw"><b>Mot de pass</b></label>
    <input type="password" placeholder="Entrez Mot de pass" name="psw" id="psw" required>
    <a href="passforget.php">Mot de passe oublié ?</a>

    <hr>

    <button type="submit" name="connexion_btn" class="registerbtn">Connecter</button>
    <div class="container_signin">
        <h2>Vous n'avez pas encore de compte ?</h2>
        <a href="register.php">Inscrivez-vous ici</a>

</form>

<?php
require ('../includes/footer.php');
?>




