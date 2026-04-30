<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location:login.php');
    exit(0);
}


$page = 'dashboard';
require ('../includes/header.php');
require ('../includes/connexion.php');

$id_user = $_SESSION['id'];
$revenu_data = "SELECT revenus_mensuels from infos where id= ? LIMIT 1";
$stmt_revenu_data = $con->prepare($revenu_data);
$stmt_revenu_data->bind_param('i', $id_user);
$stmt_revenu_data->execute();
$result_revenu_data = $stmt_revenu_data->get_result();
$result_revenu = $result_revenu_data->fetch_assoc();
$revenu = $result_revenu['revenus_mensuels'];


$stmt_total_depense = "SELECT SUM(montant) as total_depenses FROM depenses where user_id= ? LIMIT 1";
$stmt_total_depense = $con->prepare($stmt_total_depense);
$stmt_total_depense->bind_param('i', $id_user);
$stmt_total_depense->execute();
$result_total_depense = $stmt_total_depense->get_result();
$result_total_depense = $result_total_depense->fetch_assoc();
$total_depense = $result_total_depense['total_depenses'] ?? 0;
$Solde_Restant = $revenu - $total_depense;


$query_historique = "SELECT depenses.* , categories.nom AS cat_nom, categories.icone
                    FROM depenses 
                    INNER JOIN categories ON depenses.categorie_id = categories.id
                    WHERE user_id = ?
                    ORDER BY date_depense DESC";
$stmt_historique = $con->prepare($query_historique);
$stmt_historique->bind_param('i', $id_user);
$stmt_historique->execute();
$result_historique = $stmt_historique->get_result();
$result_historique = $result_historique->fetch_all(MYSQLI_ASSOC);
$nombre_transition = count($result_historique);


$query_limits = "SELECT c.id, c.nom, c.icone, IFNULL(bl.montant_limite, 0) as limite,
                 (SELECT IFNULL(SUM(montant), 0) FROM depenses where categorie_id = c.id AND user_id = ? ) as spent
                 FROM categories c
                 LEFT JOIN budget_limites bl ON c.id = bl.categorie_id AND bl.user_id = ?";
$stmt_limits = $con->prepare($query_limits);
$stmt_limits->bind_param('ii', $id_user, $id_user);
$stmt_limits->execute();
$result_limits = $stmt_limits->get_result();
$all_categories = $result_limits->fetch_all(MYSQLI_ASSOC);




?>
<nav class="navbar">
    <h3 class="logo">LOGO (A changer)</h3>
    <div class="left-nav">
        <h1 class="user_name"><?php echo $_SESSION['nom']; ?></h1>
        <a class="logout_btn" href="logout.php">Déconnexion</a>
    </div>


</nav>

<section class="section1">
    <div class="revenu-container">
        <div class="revenus">
            <h1>Revenus Mensuels</h1>
            <h2 id="revenu-display"><?php echo isset($result_revenu['revenus_mensuels'] )? number_format($result_revenu['revenus_mensuels'], 2). ' €' : '0.00 €'; ?></h2>
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
            <h1><?php echo isset($total_depense) ? number_format($total_depense, 2). ' €' : '00.0 € '?></h1>
            <h3>Ce mois-ci</h3>

        </div>
    </div>

    <div class="SoldeRestant-container">
        <div class="SoldeRestant">
            <h2>Solde Restant</h2>
            <h1><?php echo isset($Solde_Restant) ? number_format($Solde_Restant,2). ' €' : '00.0 € '?></h1>
            <h3>Disponible</h3>
        </div>
    </div>

</section>

<section class="limits-section">
    <div class="limits-container">
        <div class="limits">
            <div class="titre-boutton">
                <h1>Limites par Catégorie</h1>
                <button class="limit_modif">Modifier</button>
            </div>
            <div class="barres">
                <?php foreach ($all_categories as $cat):
                    if ($cat['limite'] <= 0) {
                        $percentage = 0;
                        $color = '#e0e0e0';
                    } else {
                        $percentage = ($cat['spent'] * 100) / $cat['limite'];
                        if ($percentage > 100) $percentage = 100;

                        if ($percentage >= 100) {
                            $color = '#e74c3c';
                            $percentageColor = '#ffddd8';
                        } elseif ($percentage >= 80) {
                            $color = '#f39c12';
                            $percentageColor = '#fff3d9 ';
                        } else {
                            $color = '#2ecc71';
                            $percentageColor  = '#c7f6d1';
                        }
                    }
                    ?>
                    <div class="limit-card" id="limit-<?php echo $cat['id']; ?>"
                         data-spent="<?php echo $cat['spent']; ?>"
                         data-limit="<?php echo $cat['limite']; ?>">
                        <div class="limit-header">
                            <div class="cat-info">
                                <span class="icon"><?php echo $cat['icone']; ?></span>
                                <span class="icon"><?php echo $cat['nom']; ?></span>
                            </div>
                            <div class="values">
                                <span class="icon"><?php echo number_format($cat['spent'],2) . ' € '; ?></span>/
                                <span class="limite-val"><?php echo number_format($cat['limite'], 2). " €"; ?></span>
                            </div>
                        </div>
                        <div class="progress-container">
                            <div class="progress-fill" style="width: <?php echo $percentage?>%;background-color: <?php echo $color;?>;"></div>
                        </div>
                        <h3 class="percentage" style="background-color: <?php echo $percentageColor?>"><?php echo number_format($percentage, 0). ' %'?></h3>


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
                <input type="text" id="description"  name="description" placeholder="Ex:Courses du weekend">
                <label>Montant</label>
                <input type="number" id="Montant" name="Montant" placeholder="0.00">
                <button type="submit" name="submit_depense">+ Ajouter la dépense</button>
            </div>
        </div>
    </form>

<section class="historique-section">
    <div class="historique-container">
        <div class="historique">
            <div class="historique-header">
                <h1>Historique des Dépenses</h1>
                <span><?php echo $nombre_transition  ?> transactions</span>
            </div>
            <table class="hisotique_table">
                <thead>
                <th>Catégorie</th>
                <th>Description</th>
                <th>Montant</th>
                <th>Date</th>
                </thead>
                <tbody>
                <?php if(empty($result_historique)): ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Aucune dépense trouvée</td>
                </tr>

                <?php else: ?>
                <?php foreach ($result_historique as $historique) : ?>
                <tr>
                    <td><?php echo $historique['cat_nom'];?></td>
                    <td><?php echo $historique['description']; ?></td>
                    <td><?php echo "- " . $historique['montant']; ?></td>
                    <td><?php echo $historique['date_depense'];?></td>
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