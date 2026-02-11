<?php
$income = $_POST['income'];
$expenses = $_POST['expenses'];

$balance = $income - $expenses;
?>

<h2>Result</h2>
<p>Your balance is: <?php echo $balance; ?></p>
<a href="../index.php">Go Back</a>
