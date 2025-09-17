<?php
session_start();

$conn = new mysqli("localhost", "root", "", "photography_website");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['msg'] = "Invalid user ID.";
    $_SESSION['msg_type'] = "error";
    header("Location: user_management.php");
    exit;
}

$user_id = (int)$_GET['id'];

// Fetch existing user data
$sql = "SELECT id, name, email, is_admin FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['msg'] = "User not found.";
    $_SESSION['msg_type'] = "error";
    header("Location: user_management.php");
    exit;
}

$user = $result->fetch_assoc();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if (empty($name) || empty($email)) {
        $error = "Name and Email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email already used by another user
        $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param('si', $email, $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "Email already in use by another user.";
        } else {
            // Update user info
            $update_sql = "UPDATE users SET name = ?, email = ?, is_admin = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param('ssii', $name, $email, $is_admin, $user_id);

            if ($update_stmt->execute()) {
                $_SESSION['msg'] = "User updated successfully.";
                $_SESSION['msg_type'] = "success";
                header("Location: user_management.php");
                exit;
            } else {
                $error = "Failed to update user. Please try again.";
            }
        }
        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit User - IBJ Studio Admin</title>
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
    input[type="email"] {
        width: 100%;
        padding: 10px 12px;
        margin-bottom: 20px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 15px;
        transition: border-color 0.3s ease;
    }
    input[type="text"]:focus,
    input[type="email"]:focus {
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
    .message {
        text-align: center;
        margin-bottom: 20px;
        font-weight: 600;
        color: #16a34a;
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
    <h2>Edit User</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($user['name']) ?>" />

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>" />

        <div class="checkbox-container">
            <label>
                <input type="checkbox" name="is_admin" <?= $user['is_admin'] == 1 ? 'checked' : '' ?> />
                Admin Role
            </label>
        </div>

        <button type="submit" class="btn">Update User</button>
    </form>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
