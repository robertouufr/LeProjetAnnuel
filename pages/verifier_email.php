<?php
require '../includes/connexion.php';
session_start();

if (isset($_GET['token'])) {
    $token_verifier = $_GET['token'];

    $token_verifier_query = 'select * from infos where token_verification = ? LIMIT 1';
    $stmt_email_verifier = $con->prepare($token_verifier_query);
    $stmt_email_verifier->bind_param('s', $token_verifier);
    $stmt_email_verifier->execute();
    $stmt_email_verifier_resultats = $stmt_email_verifier->get_result();

    if ($stmt_email_verifier_resultats->num_rows > 0) {
        $row = $stmt_email_verifier_resultats->fetch_assoc();
        if ($row['verifier_status'] == 0) {
            $change_veifier_status = 'UPDATE infos SET verifier_status = 1 WHERE token_verification = ? LIMIT 1';
            $stmt_change_veifier_status = $con->prepare($change_veifier_status);
            $stmt_change_veifier_status->bind_param('s', $token_verifier);
            $stmt_change_veifier_status->execute();
            if ($stmt_change_veifier_status) {
                $_SESSION['status'] = 'Compte vérifié avec succès ✅ ! Vous pouvez vous connecter maintenant.';
                header('location: http://localhost/LeProjetAnnuel/pages/login.php');
                exit(0);
            }
            else {
                $_SESSION['status'] = 'Échec de la vérification ! ❌';
                header('location: http://localhost/LeProjetAnnuel/pages/register.php');
                exit(0);
            }
        }
        else {
            $_SESSION['status'] = 'Adresse e-mail déjà vérifiée. Veuillez vous connecter.';
            header('location: http://localhost/LeProjetAnnuel/pages/login.php');
            exit(0);
        }

    }
    else {
        $_SESSION['status'] = 'Votre adresse e-mail ne peut être vérifiée, votre jeton nest pas valide.';
        header('location: http://localhost/LeProjetAnnuel/pages/register.php');
        exit(0);
    }

}

else {
    $_SESSION['status'] = 'Vous navez pas encore accès à cette page !';
    header('location: http://localhost/LeProjetAnnuel/pages/register.php');
    exit(0);
}








?>