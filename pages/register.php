<?php
$page = 'inscreption';
require ('../includes/connexion.php');
require ('../includes/header.php');
session_start();
?>

<form method="post" action="code.php">

    <?php if (isset($_SESSION['status'])) { ?>
        <p class="register-warning"><?php echo $_SESSION['status']; unset($_SESSION['status']); ?></p>
    <?php } ?>

    <hr>

    <label for="name"><b>Nom</b></label>
    <input type="text" placeholder="Entrez votre nom" name="nom" id="nom" required>

    <label for="email"><b>Email</b></label>
    <input type="text" placeholder="Entrez Email" name="email" id="email" required>

    <label for="phone"><b>Number tel</b></label>
    <input type="text" placeholder="Entrez Phone" name="phone" id="phone" required>

    <label for="psw"><b>Mot de pass</b></label>
    <input type="password" placeholder="Entrez Mot de pass" name="psw" id="psw" required>

    <label for="psw-repeat"><b>Repetez le Mot de pass</b></label>
    <input type="password" placeholder="Repetez Mot de pass" name="psw_repeat" id="psw_repeat" required>
    <hr>


    <button type="submit" name="incrire_btn" class="registerbtn">s'inscrire</button>
    <div class="container signin">
        <h2>Vous avez déjà un compte ?<a href="login.php">Connexion</a>.</h2>

</form>

<?php
require ('../includes/footer.php');
?>

