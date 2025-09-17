<?php
require_once ('config.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT id, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id, $hashed_password, $is_admin);

    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['is_admin'] = $is_admin;

        if ($is_admin == 1) {
            header("Location: adminpanel/dashboard.php");
        } else {
            header("Location: users/dashboard.php");
        }
        exit;
    } else {
        echo "<p style='color:red;'>Invalid email or password.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login page</title>
<body>
    <title>Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 26px;
            color: #333;
            font-weight: 600;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s;
        }

        input[type="email"]:focus, input[type="password"]:focus {
            border-color: #4CAF50;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            margin-top: 15px;
            font-size: 14px;
            display: block;
            text-align: center; 
        }


        @media (max-width: 480px) {
            .login-container {
                width: 90%;
                padding: 20px;
            }

            h2 {
                font-size: 22px;
            }

            input[type="email"], input[type="password"], button {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Login</h2>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= $error_message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
    </div>
</body>

</body>
</html>