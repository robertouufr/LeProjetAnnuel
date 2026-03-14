<?php
session_start();
include '../includes/connexion.php';
include '../includes/header.php';


if (isset($_GET['token_reset'])) {
    $token = $_GET['token_reset'];
    $search_token = "SELECT * FROM infos WHERE token_psw = ?";
    $stmt = $con->prepare($search_token);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($result->num_rows == 1) {
        $clicked_token = $row["token_psw"];
        ?>
<form method="post" action="reset-password.php">
    <h1>Reset Your Password</h1>
    <p><b>hello <b><?php echo $row['nom']?> <b> Veuillez remplir ce formulaire pour réinitialiser votre mot de passe</p>
    <hr>
    <?php if (isset($_SESSION['status'])) { ?>
        <p class="warning"><?php echo $_SESSION['status']; unset($_SESSION['status']); ?></p>
    <?php } ?>

    <hr>
    <input type="hidden" name="token_from_url" value="<?php echo $token; ?>">
    <label for="psw"><b>Mot de pass</b></label>
    <input type="password" placeholder="Entrez Mot de pass" name="psw" id="psw" required>
    <label for="psw"><b>Confirmer le Mot de pass</b></label>
    <input type="password" placeholder="Entrez Mot de pass" name="psw_confirm" id="psw" required>
    <hr>

    <button type="submit" name="reset_psw_confirm_btn" class="registerbtn">Connecter</button>


</form>


    <?php }

    else {
    $_SESSION['status'] = "Invalid or Expired Token!";

    }
}

if (isset($_POST['reset_psw_confirm_btn'])) {
    $token_url = $_POST['token_from_url'];
    $psw = $_POST['psw'];
    $psw_confirm = $_POST['psw_confirm'];
    if ($psw == $psw_confirm) {
        $update_psw = "UPDATE infos SET mdp = ? , token_psw = NULL WHERE token_psw = ?";
        $stmt_psw_update = $con->prepare($update_psw);
        $stmt_psw_update->bind_param("ss", $psw, $token_url);
        $stmt_psw_update->execute();
        if ($stmt_psw_update) {
            $_SESSION['status'] = "Votre mot de passe a été modifié ! Vous pouvez vous connecter maintenant.";
            header('location:login.php');
            exit(0);
        }
        else {
            $_SESSION['status'] = "Une erreur est survenue lors de l'enregistrement";
            header('location:reset_password.php');
            exit(0);
        }
    }
    else {
        $_SESSION['status'] = 'Le mot de passe et sa confirmation ne correspondent pas.';
        header('location:reset_password.php');
        exit(0);
    }
}


    ?>







