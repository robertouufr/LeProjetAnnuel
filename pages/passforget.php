<?php
session_start();
$page = 'Mot de passe oublie';
include '../includes/header.php';
include '../includes/connexion.php';

// Utilisation de PHPMailer pour envoyer des emails via SMTP
use PHPMailer\PHPMailer\PHPMailer;
require '../vendor/autoload.php';

/**
 * Fonction pour envoyer l'email de réinitialisation
 * On utilise le serveur SMTP de Gmail ici
 */
function reset_password_mail($email, $token_password) {
    $mail = new PHPMailer(true);

    // Configuration du serveur de messagerie (SMTP)
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sifoda2107@gmail.com'; // Ton email Gmail
    $mail->Password   = 'sdxtgodwjdeucijx';    // Ton mot de passe d'application Google
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Options SSL pour éviter les blocages de certains serveurs locaux
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
    $mail->Subject = 'Réinitialisation de votre mot de passe - Mon Pti Budjet';

    // Construction dynamique du lien de réinitialisation
    $protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $reset_link = "$protocol://$host/LeProjetAnnuel/pages/reset-password.php?token_reset=$token_password";

    // Contenu du message
    $email_template = "
        <div style='font-family: Arial, sans-serif; line-height: 1.6;'>
            <h2>Vous avez demandé de réinitialiser votre mot de passe</h2>
            <p>Cliquez sur le bouton ci-dessous pour changer votre mot de passe :</p>
            <br/>
            <a href='$reset_link' style='background: #4f39f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Réinitialiser mon mot de passe</a>
            <br/><br/>
            <p>Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.</p>
        </div>
    ";

    $mail->Body = $email_template;

    return $mail->send();
}

// --- LOGIQUE DE TRAITEMENT DU FORMULAIRE ---
if (isset($_POST['pswforget_btn'])) {
    // Génération d'un Token unique de 32 caractères
    $token_password = bin2hex(random_bytes(16));
    $email = $_POST['email'];

    // Vérifier si l'email existe dans la base de données
    $email_check = 'SELECT * FROM infos WHERE email = ? LIMIT 1';
    $stmt = $con->prepare($email_check);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt_result = $stmt->get_result();

    if ($stmt_result->num_rows == 1) {
        // Mise à jour de l'utilisateur avec le nouveau Token
        $stmt_update = 'UPDATE infos SET token_psw = ? WHERE email = ? LIMIT 1';
        $stmt_update_stmt = $con->prepare($stmt_update);
        $stmt_update_stmt->bind_param('ss', $token_password, $email);
        $stmt_update_stmt->execute();

        if ($stmt_update_stmt) {
            // Envoi effectif du mail
            if(reset_password_mail($email, $token_password)) {
                $_SESSION['status'] = 'Un courriel a été envoyé ! Consultez votre boîte de réception pour continuer.';
            } else {
                $_SESSION['status'] = "Erreur lors de l'envoi du mail. Réessayez plus tard.";
            }
        } else {
            $_SESSION['status'] = "Une erreur technique s'est produite.";
        }
    } else {
        $_SESSION['status'] = 'Cet email n\'existe pas dans notre système.';
    }

    header('location: passforget.php');
    exit(0);
}
?>

    <div class="formulaire-connexion">
        <a href="login.php" class="retour"> ← retour à la connexion</a>
        <h2>Mot de passe oublié ?</h2>
        <p>Entrez votre email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>

        <?php if (isset($_SESSION['status'])) { ?>
            <p class="warning"><?php echo $_SESSION['status']; unset($_SESSION['status']); ?></p>
        <?php } ?>

        <form method="post" action="passforget.php">
            <div class="formulaire">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="✉ vousexemple@gmail.com" required>
            </div>
            <button type="submit" name="pswforget_btn">Envoyer le lien de réinitialisation</button>
        </form>
    </div>

<?php include '../includes/footer.php'; ?>