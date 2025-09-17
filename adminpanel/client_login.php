<?php
session_start();
require_once __DIR__ . '/../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['client_id'] = $user['id'];
            $_SESSION['client_name'] = $user['name'];

            // Redirect to upload gallery
            header("Location: admin_upload_gallery.php?user_id=" . $user['id']);
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Client Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h3 class="mb-4">Client Login</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <button class="btn btn-primary">Login</button>
    </form>
</div>
</body>
</html>
