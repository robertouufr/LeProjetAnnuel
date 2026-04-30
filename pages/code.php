<?php
session_start();
require '../includes/connexion.php';


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
            $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);
            $stmt_insert->bind_param('sssss', $nom, $email, $phone, $mdp_hash, $verify_token);
            $stmt_insert->execute();

            if ($stmt_insert->affected_rows > 0) {
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
    $query_check_email = "SELECT * FROM infos WHERE email = ? LIMIT 1";
    $stmt_query_check_email = $con->prepare($query_check_email);
    $stmt_query_check_email->bind_param('s', $email_login);
    $stmt_query_check_email->execute();
    $stmt_query_check_email_result = $stmt_query_check_email->get_result();
    if ($stmt_query_check_email_result->num_rows > 0) {
        $login_row_infos = $stmt_query_check_email_result->fetch_assoc();
        if (password_verify($password_login, $login_row_infos['mdp'])) {
            if ($login_row_infos['verifier_status'] == 1) {
                $_SESSION['id'] = $login_row_infos['id'];
                $_SESSION['nom'] = $login_row_infos['nom'];
                $_SESSION['status'] = "Connexion réussie !✅";
                header('location: dashboard.php');
                exit(0);
            } else {
                $_SESSION['status'] = 'Veuillez valider votre adresse e-mail avant de vous connecter !';
                header('location: login.php');
                exit(0);
            }

        } else {
            $_SESSION['status'] = 'Adresse e-mail ou mot de passe incorrect, veuillez réessayer !';
            header('location: login.php');
            exit(0);
        }

    } else {

        $_SESSION['status'] = 'Adresse e-mail ou mot de passe incorrect !';
        header('location: login.php');
        exit(0);
    }
}

    if (isset($_POST['revenu'])) {
        $id_user = $_SESSION['id'];
        $revenu = $_POST['revenu'];
        $query_revenu = "UPDATE infos SET revenus_mensuels = ? WHERE id = ?";
        $stmt_revenu = $con->prepare($query_revenu);
        $stmt_revenu->bind_param('di', $revenu, $id_user);

        if ($stmt_revenu->execute()) {
            echo json_encode(['status' => 'success',
            'revnueValue' => $revenu]);
        }
        else {
            echo json_encode(['status' => 'error']);

        }
        exit();
    }

//    --------------------------------------------------------------------------------------------------

    if (isset($_POST['submit_depense'])) {
        $id_user_depense = $_SESSION['id'];
        $categorie_id = $_POST['categorie_id'];
        $date = $_POST['date'];
        $description = $_POST['description'];
        $montant = $_POST['Montant'];

        $stmt_depense = 'INSERT INTO depenses(user_id, categorie_id, date_depense, description, montant) VALUES(?,?,?,?,?)';
        $stmt_insert_depense = $con->prepare($stmt_depense);
        $stmt_insert_depense->bind_param('iisss', $id_user_depense, $categorie_id, $date, $description, $montant);
        $stmt_insert_depense->execute();
        if ($stmt_insert_depense->affected_rows > 0) {
            header('location: dashboard.php?status=success');
        }
        else {
            header('location: dashboard.php?status=error');
        }
    }



    if(isset($_POST["catID"])) {
        $catID = $_POST["catID"];
        $montantLimit = $_POST["montantLimit"];
        $moisLimit = $_POST["moisLimite"];

        $queryLimit = "INSERT INTO budget_limites(user_id,categorie_id,montant_limite,mois) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE montant_limite = ?, mois = ?";
        $stmt_limit = $con->prepare($queryLimit);
        $stmt_limit->bind_param('iididi', $_SESSION['id'], $catID, $montantLimit, $moisLimit, $montantLimit, $moisLimit);
        $stmt_limit->execute();
        if ($stmt_limit->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'newlimit' => $montantLimit]);
        }
        else {
            echo json_encode(['status' => 'error', "message" => $con->error]);
        }
        exit;
    }


?>
