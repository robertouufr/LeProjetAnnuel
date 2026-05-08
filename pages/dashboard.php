<?php
session_start();

// Vérification de la session : si l'utilisateur n'est pas connecté, retour à la case départ
if (!isset($_SESSION['id'])) {
    header('Location:login.php');
    exit(0);
}

$page = 'dashboard';
require ('../includes/header.php');
require ('../includes/connexion.php');

$id_user = $_SESSION['id'];

// --- 1. RÉCUPÉRATION DU REVENU ---
// Je récupère le revenu mensuel que l'utilisateur a enregistré dans la table 'infos'
$revenu_data = "SELECT revenus_mensuels from infos where id= ? LIMIT 1";
$stmt_revenu_data = $con->prepare($revenu_data);
$stmt_revenu_data->bind_param('i', $id_user);
$stmt_revenu_data->execute();
$result_revenu_data = $stmt_revenu_data->get_result();
$result_revenu = $result_revenu_data->fetch_assoc();
$revenu = $result_revenu['revenus_mensuels'] ?? 0;

// --- 2. CALCUL DES DÉPENSES TOTALES ---
// Ici, je fais la somme de tous les montants dépensés par l'utilisateur
$stmt_total_depense_query = "SELECT SUM(montant) as total_depenses FROM depenses where user_id= ? LIMIT 1";
$stmt_total_depense = $con->prepare($stmt_total_depense_query);
$stmt_total_depense->bind_param('i', $id_user);
$stmt_total_depense->execute();
$result_total_depense_data = $stmt_total_depense->get_result()->fetch_assoc();
$total_depense = $result_total_depense_data['total_depenses'] ?? 0;

// Calcul du solde restant (Revenu - Dépenses)
$Solde_Restant = $revenu - $total_depense;

// --- 3. HISTORIQUE DES TRANSACTIONS ---
// Je récupère toutes les dépenses avec le nom et l'icône de la catégorie correspondante
$query_historique = "SELECT depenses.* , categories.nom AS cat_nom, categories.icone
                    FROM depenses 
                    INNER JOIN categories ON depenses.categorie_id = categories.id
                    WHERE user_id = ?
                    ORDER BY date_depense DESC";
$stmt_historique = $con->prepare($query_historique);
$stmt_historique->bind_param('i', $id_user);
$stmt_historique->execute();
$result_historique = $stmt_historique->get_result()->fetch_all(MYSQLI_ASSOC);
$nombre_transition = count($result_historique);

// --- 4. LIMITES DE BUDGET PAR CATÉGORIE ---
// Je calcule combien a été dépensé par catégorie et je récupère la limite fixée
$query_limits = "SELECT c.id, c.nom, c.icone, IFNULL(bl.montant_limite, 0) as limite,
                 (SELECT IFNULL(SUM(montant), 0) FROM depenses where categorie_id = c.id AND user_id = ? ) as spent
                 FROM categories c
                 LEFT JOIN budget_limites bl ON c.id = bl.categorie_id AND bl.user_id = ?";
$stmt_limits = $con->prepare($query_limits);
$stmt_limits->bind_param('ii', $id_user, $id_user);
$stmt_limits->execute();
$all_categories = $stmt_limits->get_result()->fetch_all(MYSQLI_ASSOC);

// --- 5. GESTION DU DÉFI ÉPARGNE ---
// Récupération des infos du défi : titre, objectif, montant économisé et date de fin
$queryDefisInfos = "SELECT titre,montant_objectif,montant_economise,date_fin FROM defis_epargne where user_id = ?";
$stmtDefisInfos = $con->prepare($queryDefisInfos);
$stmtDefisInfos->bind_param('i', $id_user);
$stmtDefisInfos->execute();
$resultDefisInfos = $stmtDefisInfos->get_result()->fetch_assoc();

$objectif = $resultDefisInfos['montant_objectif'] ?? 0;
$montant_eco = $resultDefisInfos['montant_economise'] ?? 0;
$date_fin = $resultDefisInfos['date_fin'] ?? null;
$titre_defi = $resultDefisInfos['titre'] ?? 'Ajouter un défi !';

// Calcul de la barre de progression (sécurité : pas de division par zéro)
$progressDefi = ($objectif > 0) ? ($montant_eco * 100) / $objectif : 0;
if ($progressDefi > 100) { $progressDefi = 100; }

// Ce qu'il reste encore à économiser pour atteindre l'objectif
$encoreEconomise = ($objectif - $montant_eco > 0) ? $objectif - $montant_eco : 0;

// --- 6. CALCUL DU TEMPS RESTANT ET RECOMMANDATION ---
$jrs_restant = 0;
$mois_restant = 0;
$montant_recommende = $encoreEconomise;

if ($date_fin) {
    $date_fin_obj = new DateTime($date_fin);
    $date_now = new DateTime();

    // Si le défi n'est pas encore expiré, on calcule le temps restant
    if ($date_now < $date_fin_obj) {
        $interval = $date_now->diff($date_fin_obj);
        $jrs_restant = $interval->days;
        $mois_restant = ($interval->y * 12) + $interval->m;

        // Calcul du montant à mettre de côté par mois pour réussir le défi
        if ($mois_restant > 0) {
            $montant_recommende = $encoreEconomise / $mois_restant;
        }
    }
}
?>

    <nav class="navbar">
        <h3 class="logo">LOGO (A changer)</h3>
        <div class="left-nav">
            <h1 class="user_name"><?php echo htmlspecialchars($_SESSION['nom']); ?></h1>
            <a class="logout_btn" href="logout.php">Déconnexion</a>
        </div>
    </nav>

    <section class="section1">
        <div class="revenu-container">
            <div class="revenus">
                <h1>Revenus Mensuels</h1>
                <h2 id="revenu-display"><?php echo ($revenu > 0) ? number_format($revenu, 2). ' €' : '0.00 €'; ?></h2>
            </div>
            <div class="mois_modif">
                <h3>Ce mois-ci</h3>
                <button id="modifierBtn" class="edit-btn">Modifier</button>
            </div>
        </div>

        <div id="modalRevenus" class="modal" style="display: none">
            <div class="modal_contenu">
                <h3>Modifier le revenu</h3>
                <input type="number" id="revenuInput" placeholder="Entrez le montant (€)">
                <div class="buttons">
                    <button id="cancelBtn">Annuler</button>
                    <button id="confirmBtn">Valider</button>
                </div>
            </div>
        </div>

        <div class="totalDepense-container">
            <div class="totalDepense">
                <h2>Total Dépenses</h2>
                <h1><?php echo number_format($total_depense, 2). ' €'; ?></h1>
                <h3>Ce mois-ci</h3>
            </div>
        </div>

        <div class="SoldeRestant-container">
            <div class="SoldeRestant">
                <h2>Solde Restant</h2>
                <h1><?php echo number_format($Solde_Restant, 2). ' €'; ?></h1>
                <h3>Disponible</h3>
            </div>
        </div>
    </section>

    <section class="section2">
        <section class="limits-section">
            <div class="limits-container">
                <div class="limits">
                    <div class="titre-boutton">
                        <h1>Limites par Catégorie</h1>
                        <button class="limit_modif">Modifier</button>
                    </div>
                    <div class="barres">
                        <?php foreach ($all_categories as $cat):
                            // Gestion des couleurs des barres de progression selon le pourcentage
                            if ($cat['limite'] <= 0) {
                                $percentage = 0;
                                $color = '#e0e0e0';
                                $percentageColor = '#f0f0f0';
                            } else {
                                $percentage = ($cat['spent'] * 100) / $cat['limite'];
                                if ($percentage > 100) $percentage = 100;

                                if ($percentage >= 100) {
                                    $color = '#e74c3c'; $percentageColor = '#ffddd8';
                                } elseif ($percentage >= 80) {
                                    $color = '#f39c12'; $percentageColor = '#fff3d9 ';
                                } else {
                                    $color = '#2ecc71'; $percentageColor = '#c7f6d1';
                                }
                            }
                            ?>
                            <div class="limit-card" id="limit-<?php echo $cat['id']; ?>" data-spent="<?php echo $cat['spent']; ?>" data-limit="<?php echo $cat['limite']; ?>">
                                <div class="limit-header">
                                    <div class="cat-info">
                                        <span class="icon"><?php echo $cat['icone']; ?></span>
                                        <span class="icon"><?php echo $cat['nom']; ?></span>
                                    </div>
                                    <div class="values">
                                        <span><?php echo number_format($cat['spent'], 2); ?> € </span>/
                                        <span class="limite-val"><?php echo number_format($cat['limite'], 2); ?> €</span>
                                    </div>
                                </div>
                                <div class="progress-container">
                                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%; background-color: <?php echo $color; ?>;"></div>
                                </div>
                                <h3 class="percentage" style="background-color: <?php echo $percentageColor; ?>"><?php echo round($percentage); ?> %</h3>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="modifierLimitFenetre" style="display: none">
                        <div class="modifierLimitContainer">
                            <select class="cat-select_limits" name="categorie_limit_id">
                                <option value="1">Alimentation</option>
                                <option value="2">Transport</option>
                                <option value="3">Loisirs</option>
                                <option value="4">Logement</option>
                                <option value="5">Santé</option>
                                <option value="6">Shopping</option>
                            </select>
                            <input type="number" name="montant-limit" placeholder="Montant..." id="montant-limit">
                            <input type="text" name="Mois-limit" placeholder="Ex : 01" id="Mois">
                            <div class="buttons">
                                <button id="cancelBtn_limit">Annuler</button>
                                <button id="confirmBtn_limit">Valider</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="defi-section">
            <div class="defi-container">
                <div class="defi">
                    <div class="defiHeader">
                        <h1 class="defi-title">Défi Épargne - <span class="defi-title_entree"><?php echo ($resultDefisInfos) ? $resultDefisInfos['titre'] : " Ajouter un défi"; ?></span> </h1>
                        <button class="objectifBtn">Ajouter un objectif</button>
                    </div>
                    <div class="montants">
                        <div class="objMonatant">
                            <h1>Objectif</h1>
                            <h2 class="montantObj"><?php echo ($objectif > 0) ? number_format($objectif, 2). " €" : "Ajouter un objectif"; ?></h2>
                        </div>
                        <div class="economMontant">
                            <h1>Economisé</h1>
                            <h2><?php echo number_format($montant_eco, 2); ?> €</h2>
                        </div>
                    </div>
                    <div class="progress-defi-container">
                        <div class="progress-fill-defi" style="width: <?php echo $progressDefi; ?>%"></div>
                    </div>
                    <div class="defiCards">
                        <div class="tempsCard">
                            <h1>Temps restant</h1>
                            <?php if (isset($date_now) && isset($date_fin_obj) && $date_now <= $date_fin_obj) : ?>
                                <h2><?php echo $jrs_restant; ?> Jours</h2>
                                <h2><?php echo $mois_restant; ?> Mois</h2>
                            <?php else: ?>
                                <h2>Défi terminé !</h2>
                            <?php endif; ?>
                        </div>
                        <div class="recomCard">
                            <h1>Par mois</h1>
                            <h2><?php echo number_format($montant_recommende, 2); ?> €</h2>
                            <h2>recommandé</h2>
                        </div>
                    </div>
                    <div class="economiseCard">
                        <h1>Encore à économiser</h1>
                        <h2 style="color: white"><?php echo number_format($encoreEconomise, 2); ?> €</h2>
                    </div>
                    <button class="epagneBtn">Ajouter une epargne</button>
                </div>
            </div>

            <div class="addObjectifModal" style="display: none">
                <div class="addObjectifForm">
                    <label>titre</label>
                    <input type="text" name="titreEpargne" id="titreEpargne" placeholder="Titre">
                    <label>Objectif</label>
                    <input type="number" name="ObjectifEpargne" id="ObjectifEpargne" placeholder="Objectif">
                    <label>Date fin de défi</label>
                    <input type="date" name="DateEpargne" id="DateEpargne" placeholder="Date">
                </div>
                <div class="addObjectifBtn">
                    <button class="objConfirm">Ajouter</button>
                    <button class="objCancel">Annuler</button>
                </div>
            </div>

            <div class="addEpargneModal" style="display: none">
                <div class="addEpargnecard">
                    <div class="addEpargneform">
                        <label>Montant</label>
                        <input type="number" name="montantEpargne" id="montantEpargne" placeholder="montant d'epargne">
                    </div>
                    <div class="addEpargneBtn">
                        <button class="epargneConfirm">Ajouter</button>
                        <button class="epargneCancel">Annuler</button>
                    </div>
                </div>
            </div>
        </section>
    </section>

    <section class="section3">
        <form class="depense_form" method="post" action="code.php">
            <div class="depense-container">
                <div class="depenses">
                    <h1 class="depenses-title">Ajoute une Dépense</h1>
                    <input class="date" name="date" type="date" required>
                    <label>Catégorie</label>
                    <select name="categorie_id">
                        <option value="1">Alimentation</option>
                        <option value="2">Transport</option>
                        <option value="3">Loisirs</option>
                        <option value="4">Logement</option>
                        <option value="5">Santé</option>
                        <option value="6">Shopping</option>
                    </select>
                    <label for="description">Description</label>
                    <input type="text" id="description" name="description" placeholder="Ex:Courses du weekend">
                    <label>Montant</label>
                    <input type="number" step="0.01" id="Montant" name="Montant" placeholder="0.00">
                    <button type="submit" name="submit_depense">+ Ajouter la dépense</button>
                </div>
            </div>
        </form>

        <section class="historique-section">
            <div class="historique-container">
                <div class="historique">
                    <div class="historique-header">
                        <h1>Historique des Dépenses</h1>
                        <span><?php echo $nombre_transition; ?> transactions</span>
                    </div>
                    <table class="hisotique_table">
                        <thead>
                        <tr>
                            <th>Catégorie</th>
                            <th>Description</th>
                            <th>Montant</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(empty($result_historique)): ?>
                            <tr><td colspan="4" style="text-align: center;">Aucune dépense trouvée</td></tr>
                        <?php else: ?>
                            <?php foreach ($result_historique as $historique) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($historique['cat_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($historique['description']); ?></td>
                                    <td>- <?php echo number_format($historique['montant'], 2); ?> €</td>
                                    <td><?php echo $historique['date_depense']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </section>

    <script src="../assets/js/script.js"></script>
<?php require ('../includes/footer.php'); ?>