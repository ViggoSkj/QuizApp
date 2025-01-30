<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
</head>

<body>

    <h2>Signup</h2>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error'];
        unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success'];
        unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <input type="text" name="name" placeholder="Name" required><br><br>
        <input type="text" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit">Signup</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>

</body>

</html>