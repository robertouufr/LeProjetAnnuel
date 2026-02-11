<?php include 'includes/header.php'; ?>

<h1>Budget Calculator</h1>

<form method="POST" action="pages/results.php">
    <label>Income:</label>
    <input type="number" name="income" required>

    <label>Expenses:</label>
    <input type="number" name="expenses" required>

    <button type="submit">Calculate</button>
</form>

<?php include 'includes/footer.php'; ?>
