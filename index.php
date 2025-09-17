<?php
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Home</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to Your Photo Space</h1>
        <p>Hello, User #<?php echo $_SESSION['user_id']; ?>!</p>

        <ul>
            <li><a href="dashboard.php">Go to Dashboard</a></li>
            <li><a href="gallery.php">View Gallery</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>
</body>
</html>
