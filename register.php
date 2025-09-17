
<?php
require_once('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<p style='color:red;'>Email already registered.</p>";
    } else {
        // Insert new user
        $stmt = $mysqli->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $hashed_password);
        if ($stmt->execute()) {
            echo "<p style='color:green;'>Registration successful. <a href='login.php'>Login now</a>.</p>";
        } else {
            echo "<p style='color:red;'>Something went wrong. Try again.</p>";
        }
    }
}
?>

<h2>Register</h2>
<form method="POST">
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit">Register</button>
</form>
