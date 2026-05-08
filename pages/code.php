<?php
session_start();
require '../includes/connexion.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendemail_verify($nom, $email, $verify_token) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sifoda2107@gmail.com';
        $mail->Password   = 'sdxtgodwjdeucijx';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom('sifoda2107@gmail.com', 'Mon Pti Budget');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Activez votre compte - Mon Pti Budget';

        //  (Local ou Host)
        $host = $_SERVER['HTTP_HOST'];

// Si je suis en local et que le port 8888 n'est pas inclus, je l'ajoute manuellement
        if ($host == "localhost" && strpos($host, ':8888') === false) {
            $host = "localhost:8888";
        }

        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $link = $protocol . $host . "/LeProjetAnnuel/pages/verifier_email.php?token=$verify_token";

        $mail->Body = "
            <div style='max-width: 600px; margin: auto; padding: 20px; font-family: Arial, sans-serif; border: 1px solid #e0e0e0; border-radius: 10px; color: #333;'>
                <div style='text-align: center; margin-bottom: 20px;'>
                    <h1 style='color: #4f39f6;'>Mon Pti Budget</h1>
                </div>
                <h2 style='color: #1a1a1a;'>Bienvenue parmi nous, $nom ! 👋</h2>
                <p style='font-size: 16px; line-height: 1.6;'>
                    Merci d'avoir choisi <b>Mon Pti Budget</b> pour gérer vos finances. 
                    Il ne vous reste qu'une dernière étape : confirmer votre adresse e-mail pour activer votre compte.
                </p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='$link' style='background-color: #4f39f6; color: #ffffff; padding: 15px 30px; text-decoration: none; font-weight: bold; border-radius: 5px; display: inline-block;'>
                        Vérifier mon compte
                    </a>
                </div>
                <p style='font-size: 13px; color: #777;'>
                    Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur : <br>
                    <a href='$link' style='color: #4f39f6;'>$link</a>
                </p>
                <hr style='border: 0; border-top: 1px solid #eee; margin-top: 30px;'>
                <p style='font-size: 12px; color: #aaa; text-align: center;'>
                    Ceci est un message automatique, merci de ne pas y répondre. <br>
                    © 2026 Mon Pti Budget. Tous droits réservés.
                </p>
            </div>";

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}

//   INSCRIPTION
if (isset($_POST['incrire_btn'])) {
    $verify_token = bin2hex(random_bytes(16)); // Token sécurisé pour l'email
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $mdp = $_POST['psw'];
    $mdp_rep = $_POST['psw_repeat'];

    // On vérifie d'abord si l'utilisateur existe déjà
    $stmt = $con->prepare("SELECT email FROM infos WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['status'] = 'Cet e-mail est déjà pris, désolé !';
        header('location: register.php');
        exit;
    }

    // Est-ce que les mots de passe sont identiques ?
    if ($mdp !== $mdp_rep) {
        $_SESSION['status'] = 'Les mots de passe ne sont pas les mêmes...';
        header('location: register.php');
        exit;
    }

    // On hash le mot de passe pour la sécurité
    $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);
    $stmt = $con->prepare("INSERT INTO infos (nom, email, phone, mdp, token_verification) VALUES (?,?,?,?,?)");
    $stmt->bind_param('sssss', $nom, $email, $phone, $mdp_hash, $verify_token);

    if ($stmt->execute()) {
        sendemail_verify($nom, $email, $verify_token);
        $_SESSION['status'] = "C'est fait ! Regarde tes mails pour valider ton compte.";
    } else {
        $_SESSION['status'] = "Petit souci lors de l'inscription, réessaie encore.";
    }
    header('location: register.php');
    exit;
}

//      CONNEXION
if (isset($_POST['connexion_btn'])) {
    $email = trim($_POST['email']);
    $mdp = $_POST['psw'];

    $stmt = $con->prepare("SELECT * FROM infos WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // On check le mot de passe hashé
        if (password_verify($mdp, $row['mdp'])) {
            // Est-ce qu'il a cliqué sur le lien de l'email ?
            if ($row['verifier_status'] == 1) {
                $_SESSION['id'] = $row['id'];
                $_SESSION['nom'] = $row['nom'];
                $_SESSION['status'] = "Content de te revoir ! 👋";
                header('location: dashboard.php');
            } else {
                $_SESSION['status'] = "Il faut d'abord valider ton e-mail !";
                header('location: login.php');
            }
        } else {
            $_SESSION['status'] = "Mot de passe incorrect, essaie encore.";
            header('location: login.php');
        }
    } else {
        $_SESSION['status'] = "Aucun compte trouvé avec cet e-mail.";
        header('location: login.php');
    }
    exit;
}

//     MISE À JOUR DU REVENU (Via JS)
if (isset($_POST['revenu'])) {
    $id_user = $_SESSION['id'];
    $revenu = floatval($_POST['revenu']); // On s'assure que c'est un nombre

    $stmt = $con->prepare("UPDATE infos SET revenus_mensuels = ? WHERE id = ?");
    $stmt->bind_param('di', $revenu, $id_user);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'revnueValue' => $revenu]);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}

// --- 4. AJOUTER UNE DÉPENSE ---
if (isset($_POST['submit_depense'])) {
    $id_user = $_SESSION['id'];
    $cat_id = intval($_POST['categorie_id']);
    $date = $_POST['date'];
    $desc = trim($_POST['description']);
    $montant = floatval($_POST['Montant']);

    $stmt = $con->prepare("INSERT INTO depenses (user_id, categorie_id, date_depense, description, montant) VALUES (?,?,?,?,?)");
    $stmt->bind_param('iisss', $id_user, $cat_id, $date, $desc, $montant);

    $status = ($stmt->execute()) ? 'success' : 'error';
    header("location: dashboard.php?status=$status");
    exit;
}

// --- 5. LIMITES DE BUDGET (Via AJAX) ---
if (isset($_POST["catID"])) {
    $catID = intval($_POST["catID"]);
    $limit = floatval($_POST["montantLimit"]);
    $mois = intval($_POST["moisLimite"]);

    // Si la limite existe on l'update, sinon on l'insère
    $query = "INSERT INTO budget_limites (user_id, categorie_id, montant_limite, mois) VALUES (?,?,?,?) 
              ON DUPLICATE KEY UPDATE montant_limite = ?, mois = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('iididi', $_SESSION['id'], $catID, $limit, $mois, $limit, $mois);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'newlimit' => $limit]);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}

//     NOUVEAU DÉFI ÉPARGNE (Via JS)
if (isset($_POST["titre"])) {
    $user_id = $_SESSION['id'];
    $titre = trim($_POST["titre"]);
    $obj = floatval($_POST["objectif"]);
    $date = $_POST["date"];

    // On supprime l'ancien défi pour repartir à zéro
    $stmt_del = $con->prepare("DELETE FROM defis_epargne WHERE user_id = ?");
    $stmt_del->bind_param('i', $user_id);
    $stmt_del->execute();

    $stmt = $con->prepare("INSERT INTO defis_epargne (user_id, titre, montant_objectif, date_fin) VALUES (?,?,?,?)");
    $stmt->bind_param('isds', $user_id, $titre, $obj, $date);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}

//     AJOUTER DE L'ÉPARGNE AU DÉFI (Via JS)
if (isset($_POST["montantEpargne"])) {
    $user_id = $_SESSION['id'];
    $montant = floatval($_POST["montantEpargne"]);
    $cat_epargne = 7; // L'ID de ma catégorie épargne dans la BDD
    $date_now = date("Y-m-d");
    $desc = "Épargne pour défi";

    // Update du montant déjà économisé
    $stmt1 = $con->prepare("UPDATE defis_epargne SET montant_economise = montant_economise + ? WHERE user_id = ? LIMIT 1");
    $stmt1->bind_param('di', $montant, $user_id);
    $stmt1->execute();

    // On ajoute aussi une ligne dans la table dépenses pour garder une trace
    $stmt2 = $con->prepare("INSERT INTO depenses (user_id, categorie_id, date_depense, description, montant) VALUES (?,?,?,?,?)");
    $stmt2->bind_param('iissd', $user_id, $cat_epargne, $date_now, $desc, $montant);

    if ($stmt2->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}
?>