<?php
// Include the config.php for database or session if needed
// require_once('config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photography Website</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="../index.php"><img src="../assets/images/logo.png" alt="Logo"></a>
            </div>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="../users/dashboard.php">Dashboard</a></li>
                <li><a href="../users/gallery.php">Gallery</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="../logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="../login.php">Login</a></li>
                    <li><a href="../register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
