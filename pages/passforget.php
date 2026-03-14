<?php
session_start();
$page = 'Mot de passe oublie';
include '../includes/header.php';
include '../includes/connexion.php';

use PHPMailer\PHPMailer\PHPMailer;

require '../vendor/autoload.php';
function reset_password_mail($email, $token_password) {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sifoda2107@gmail.com';
    $mail->Password   = 'sdxtgodwjdeucijx';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // ✅ Fix SSL error sur XAMPP
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true
        )
    );

    $mail->setFrom('sifoda2107@gmail.com', 'Mon Pti Budjet');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Email Verification from Mon Pti Budjet to reset your password';

    $email_template = "
        <h2>You Want to reset your password in Mon Pti Budjet</h2>
        <h5>Click in the below given link to reset your password</h5>
        <br/><br/>
        <a href='http://localhost/LeProjetAnnuel/pages/reset-password.php?token_reset=$token_password'> Click Here to reset </a>
    ";

    $mail->Body = $email_template;

    if($mail->send()) {
        return true;
    } else {
        return false;
    }
}

if (isset($_POST['pswforget_btn'])) {
    $token_password = bin2hex(random_bytes(16));
    $email = $_POST['email'];
    $email_check = 'SELECT * FROM infos WHERE email = ? LIMIT 1';
    $stmt = $con->prepare($email_check);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt_result = $stmt->get_result();

    if ($stmt_result->num_rows == 1) {
        $stmt_update = 'UPDATE infos SET token_psw = ? WHERE email = ? LIMIT 1';
        $stmt_update_stmt = $con->prepare($stmt_update);
        $stmt_update_stmt->bind_param('ss', $token_password, $email);
        $stmt_update_stmt->execute();
        if ($stmt_update_stmt) {
            reset_password_mail($email, $token_password);
            $_SESSION['status'] = 'Un courriel a été envoyé à votre adresse électronique. Veuillez consulter votre boîte de réception pour réinitialiser votre mot de passe.';
            header('location: http://localhost/LeProjetAnnuel/pages/passforget.php');
            exit(0);
        }
        else {
            $_SESSION['status'] = "Une erreur s'est produite. Veuillez réessayer.";
            header('location: http://localhost/LeProjetAnnuel/pages/passforget.php');
            exit(0);
        }

    }
    else {
        $_SESSION['status'] = 'Cet email n\'existe pas.';
        header('location: http://localhost/LeProjetAnnuel/pages/passforget.php');
        exit(0);

    }
}
?>

<form method="post" action="passforget.php">
    <?php if (isset($_SESSION['status'])) { ?>
        <p class="warning"><?php echo $_SESSION['status']; unset($_SESSION['status']); ?></p>
    <?php } ?>

    <hr>
    <label for="email"><b>Email</b></label>
    <input type="text" placeholder="Entrez Email" name="email" id="email" required>

    <button type="submit" name="pswforget_btn" class="registerbtn">Réinitialiser le mot de passe</button>
</form>

<?php include '../includes/footer.php'; ?>