<?php
session_start();
include '../includes/connexion.php';
include '../includes/header.php';

// --- ÉTAPE 1 : RÉCUPÉRATION DU TOKEN DEPUIS L'URL ---
// Si l'utilisateur clique sur le lien reçu par mail, on vérifie le token_reset
if (isset($_GET['token_reset'])) {
    $token = $_GET['token_reset'];

    // Je cherche dans la base de données si ce token existe bien
    $search_token = "SELECT * FROM infos WHERE token_psw = ?";
    $stmt = $con->prepare($search_token);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Si on trouve une ligne, ça veut dire que le lien est valide
    if ($result->num_rows == 1) {
        $clicked_token = $row["token_psw"];
        ?>

        <div class="auth-page">
            <div class="auth-container">

                <h1>Réinitialisation</h1>
                <p class="auth-subtitle">Bonjour <strong><?php echo htmlspecialchars($row['nom']); ?></strong>, veuillez remplir ce formulaire pour réinitialiser votre mot de passe.</p>

                <?php if (isset($_SESSION['status'])) { ?>
                    <p class="warning"><?php echo $_SESSION['status']; unset($_SESSION['status']); ?></p>
                <?php } ?>

                <form method="post" action="reset-password.php">

                    <input type="hidden" name="token_from_url" value="<?php echo htmlspecialchars($token); ?>">

                    <div class="form-group">
                        <label for="psw">Nouveau mot de passe</label>
                        <input type="password" placeholder="Entrez votre nouveau mot de passe" name="psw" id="psw" required>
                    </div>

                    <div class="form-group">
                        <label for="psw_confirm">Confirmer le mot de passe</label>
                        <input type="password" placeholder="Répétez votre mot de passe" name="psw_confirm" id="psw_confirm" required>
                    </div>

                    <button type="submit" name="reset_psw_confirm_btn" class="registerbtn">Confirmer</button>

                </form>

            </div>
        </div>

        <?php
    } else {
        // Si le token n'existe pas ou est expiré
        ?>
        <div class="auth-page">
            <div class="auth-container" style="text-align: center;">
                <h1>Lien invalide</h1>
                <p class="auth-subtitle">Ce lien de réinitialisation est invalide ou a expiré.</p>
                <a href="login.php" class="registerbtn" style="display:block; text-decoration:none; margin-top: 10px;">Retour à la connexion</a>
            </div>
        </div>
        <?php
    }
}

// --- ÉTAPE 2 : TRAITEMENT DU FORMULAIRE (POST) ---
// Quand l'utilisateur clique sur "Confirmer" pour changer son mot de passe
if (isset($_POST['reset_psw_confirm_btn'])) {
    $token_url = $_POST['token_from_url'];
    $psw = $_POST['psw'];
    $psw_confirm = $_POST['psw_confirm'];

    // On vérifie si les deux mots de passe saisis sont identiques
    if ($psw == $psw_confirm) {
        // Hachage du mot de passe pour la sécurité
        $hashed_password = password_hash($psw, PASSWORD_DEFAULT);

        // On met à jour le mot de passe et on supprime le token pour qu'il ne soit plus réutilisable
        $update_psw = "UPDATE infos SET mdp = ? , token_psw = NULL WHERE token_psw = ?";
        $stmt_psw_update = $con->prepare($update_psw);
        $stmt_psw_update->bind_param("ss", $hashed_password, $token_url);
        $stmt_psw_update->execute();

        if ($stmt_psw_update) {
            $_SESSION['status'] = "Votre mot de passe a été modifié ! Vous pouvez vous connecter maintenant.";
            header('location:login.php');
            exit(0);
        } else {
            $_SESSION['status'] = "Une erreur est survenue lors de l'enregistrement.";
            header('location:login.php');
            exit(0);
        }
    } else {
        // Si les mots de passe ne correspondent pas, on renvoie vers le formulaire avec une erreur
        $_SESSION['status'] = 'Le mot de passe et sa confirmation ne correspondent pas.';
        header("location:reset-password.php?token_reset=$token_url");
        exit(0);
    }
}
?>