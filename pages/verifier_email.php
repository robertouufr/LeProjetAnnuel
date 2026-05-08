<?php
require '../includes/connexion.php';
session_start();

/**
 * LOGIQUE DE VÉRIFICATION D'EMAIL
 * Ce fichier permet de valider le compte utilisateur après avoir cliqué sur le lien reçu par mail.
 */

// 1. Vérification de la présence du token dans l'URL
if (isset($_GET['token'])) {
    $token_verifier = $_GET['token'];

    // Préparation de la requête pour trouver l'utilisateur avec ce jeton (token)
    $token_verifier_query = "SELECT id, verifier_status FROM infos WHERE token_verification = ? LIMIT 1";
    $stmt_email_verifier = $con->prepare($token_verifier_query);
    $stmt_email_verifier->bind_param('s', $token_verifier);
    $stmt_email_verifier->execute();
    $stmt_email_verifier_resultats = $stmt_email_verifier->get_result();

    // Si le jeton existe dans la base de données
    if ($stmt_email_verifier_resultats->num_rows > 0) {
        $row = $stmt_email_verifier_resultats->fetch_assoc();

        // 2. Vérification si le compte n'est pas déjà activé
        if ($row['verifier_status'] == 0) {

            // Mise à jour du statut de l'utilisateur : verifier_status passe à 1
            $change_veifier_status = "UPDATE infos SET verifier_status = 1 WHERE token_verification = ? LIMIT 1";
            $stmt_change_veifier_status = $con->prepare($change_veifier_status);
            $stmt_change_veifier_status->bind_param('s', $token_verifier);

            if ($stmt_change_veifier_status->execute()) {
                // Succès : Redirection vers login avec message de réussite
                $_SESSION['status'] = "Compte activé avec succès ✅ ! Vous pouvez maintenant vous connecter.";
                header('Location: login.php'); // Chemin relatif : plus sûr que localhost
                exit(0);
            } else {
                // Erreur technique lors de l'Update
                $_SESSION['status'] = "Erreur technique : Échec de l'activation du compte.";
                header('Location: register.php');
                exit(0);
            }
        } else {
            // Cas où l'email a déjà été vérifié auparavant
            $_SESSION['status'] = "Votre adresse e-mail est déjà vérifiée. Connectez-vous directement.";
            header('Location: login.php');
            exit(0);
        }

    } else {
        // Le jeton fourni dans l'URL ne correspond à aucun utilisateur
        $_SESSION['status'] = "Lien invalide : Ce jeton de vérification n'est plus valide ou est expiré.";
        header('Location: register.php');
        exit(0);
    }

} else {
    // Si l'utilisateur tente d'accéder à cette page directement sans jeton
    $_SESSION['status'] = "Accès refusé : Vous n'avez pas l'autorisation d'accéder à cette page.";
    header('Location: register.php');
    exit(0);
}
?>