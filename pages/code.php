<?php
require '../includes/connexion.php';
session_start();

require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
function sendemail_verify($nom, $email, $verify_token) {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sifoda2107@gmail.com';
    $mail->Password   = 'sdxtgodwjdeucijx';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

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
    $mail->Subject = 'Email Verification from Mon Pti Budjet';

    $email_template = "
        <h2>You have Registered with Mon Pti Budjet</h2>
        <h5>Vérifiez votre adresse e-mail pour vous connecter via le lien ci-dessous.</h5>
        <br/><br/>
        <a href='http://localhost/LeProjetAnnuel/pages/verifier_email.php?token=$verify_token'> Cliquez ici </a>
    ";

    $mail->Body = $email_template;

    if($mail->send()) {
        return true;
    } else {
        return false;
    }
}

if (isset($_POST['incrire_btn'])) {
    $verify_token = md5(uniqid(rand(), true));
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $mdp = $_POST['psw'];
    $mdp_rep = $_POST['psw_repeat'];

    $query_check_email = "SELECT email FROM infos WHERE email = ? LIMIT 1";
    $stmt = $con->prepare($query_check_email);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt_result = $stmt->get_result();

    if ($stmt_result->num_rows > 0 ) {
        $_SESSION['status'] = 'Vous etes deja inscrit, connectez vous';
        header('location: register.php');
        exit(0);
    }
    else {
        if ($mdp == $mdp_rep) {
            $infos_insert = 'INSERT INTO infos(nom,email,phone,mdp,token_verification) VALUES(?,?,?,?,?)';
            $stmt_insert = $con->prepare($infos_insert);
            $stmt_insert->bind_param('sssss', $nom, $email, $phone, $mdp, $verify_token);
            $stmt_insert->execute();

            if ($stmt_insert) {
                sendemail_verify($nom, $email, $verify_token);
                $_SESSION['status'] = 'Inscription réussie ! Veuillez vérifier votre adresse e-mail.';
                header('location: register.php');
                exit(0);
            }
            else {
                $_SESSION['status'] = 'Erreur inscription ❌, essayez de nouveau';
                header('location: register.php');
                exit(0);
            }
        }
        else {
            $_SESSION['status'] = 'Le mot de passe et sa confirmation doivent correspondre !‼️';
            header('location: register.php');
            exit(0);
        }
    }

}

if (isset($_POST['connexion_btn'])) {
$email_login = $_POST['email'];
$password_login = $_POST['psw'];
$query_check_email = "SELECT * FROM infos WHERE email = ? AND mdp = ? LIMIT 1";
$stmt_query_check_email = $con->prepare($query_check_email);
$stmt_query_check_email->bind_param('ss', $email_login, $password_login);
$stmt_query_check_email->execute();
$stmt_query_check_email_result = $stmt_query_check_email->get_result();
if ($stmt_query_check_email_result->num_rows > 0) {
    $login_row = $stmt_query_check_email_result->fetch_assoc();
    if ($login_row['verifier_status'] == 1) {
        $_SESSION['status'] = "Connexion réussie !✅";
        header('location: dashboard.php');
        exit(0);
    }
    else {
        $_SESSION['status'] = 'Veuillez valider votre adresse e-mail avant de vous connecter !';
        header('location: login.php');
        exit(0);
     }
}
else {
    $_SESSION['status'] = 'Adresse e-mail ou mot de passe incorrect, veuillez réessayer !';
    header('location: login.php');
    exit(0);
}
}

?>
