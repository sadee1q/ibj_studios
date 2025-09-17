<?php
session_start();

$conn = new mysqli("localhost", "root", "", "photography_website");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if email already exists
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $insert_sql = "INSERT INTO users (name, email, password, is_admin, created_at) VALUES (?, ?, ?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param('sssi', $name, $email, $hashed_password, $is_admin);

            if ($insert_stmt->execute()) {
                $_SESSION['msg'] = "User added successfully.";
                $_SESSION['msg_type'] = "success";
                header("Location: user_management.php");
                exit;
            } else {
                $error = "Failed to add user. Please try again.";
            }

            $insert_stmt->close();
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add New User - IBJ Studio Admin</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f9fafb;
        padding: 30px;
        color: #333;
    }
    .container {
        max-width: 480px;
        margin: auto;
        background: #fff;
        padding: 30px 35px;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    h2 {
        text-align: center;
        margin-bottom: 30px;
        font-weight: 700;
        color: #222;
        letter-spacing: 1.1px;
    }
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #555;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 10px 12px;
        margin-bottom: 20px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 15px;
        transition: border-color 0.3s ease;
    }
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 5px #2563ebaa;
    }
    .checkbox-container {
        margin-bottom: 20px;
    }
    input[type="checkbox"] {
        transform: scale(1.2);
        margin-right: 8px;
        cursor: pointer;
    }
    .btn {
        background-color: #2563eb;
        color: white;
        padding: 12px 25px;
        font-weight: 700;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        width: 100%;
        transition: background-color 0.3s ease;
        user-select: none;
    }
    .btn:hover {
        background-color: #1e40af;
    }
    .error {
        color: #b91c1c;
        text-align: center;
        margin-bottom: 20px;
        font-weight: 600;
    }
    /* Back link styling */
    .back-link {
        display: inline-block;
        margin-bottom: 25px;
        color: #2563eb;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: color 0.3s ease;
    }
    .back-link:hover {
        text-decoration: underline;
        color: #1e40af;
    }
</style>
</head>
<body>
<div class="container">
    <a href="user_management.php" class="back-link">&larr; Back to User Management</a>
    <h2>Add New User</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" />

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required />

        <div class="checkbox-container">
            <label>
                <input type="checkbox" name="is_admin" <?= isset($_POST['is_admin']) ? 'checked' : '' ?> />
                Admin Role
            </label>
        </div>

        <button type="submit" class="btn">Add User</button>
    </form>
</div>
</body>
</html>

<?php
$conn->close();
?>
