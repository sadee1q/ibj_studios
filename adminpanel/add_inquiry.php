<?php
session_start();
require_once('includes/config.php');

// Ensure only admin access
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

$client_name = $client_email = $message = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_name = trim($_POST['client_name'] ?? '');
    $client_email = trim($_POST['client_email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validate inputs
    if ($client_name === '') {
        $errors['client_name'] = "Client name is required.";
    }
    if ($client_email === '') {
        $errors['client_email'] = "Client email is required.";
    } elseif (!filter_var($client_email, FILTER_VALIDATE_EMAIL)) {
        $errors['client_email'] = "Invalid email format.";
    }
    if ($message === '') {
        $errors['message'] = "Message is required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO inquiries (client_name, client_email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $client_name, $client_email, $message);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: contact_inquiries.php?msg=Inquiry+added+successfully");
            exit;
        } else {
            $errors['general'] = "Database error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Add Inquiry</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f4f4;
        padding: 20px;
    }
    form {
        max-width: 500px;
        margin: 40px auto;
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    label {
        display: block;
        margin-top: 25px;
        font-weight: 600;
    }
    input[type=text], input[type=email], textarea {
        width: 100%;
        padding: 12px;
        margin-top: 8px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 1em;
        resize: vertical;
        box-sizing: border-box;
    }
    textarea {
        height: 140px;
    }
    .error {
        color: red;
        font-size: 0.9em;
        margin-top: 4px;
    }
    button {
        margin-top: 30px;
        background: #007BFF;
        color: white;
        border: none;
        padding: 14px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1.1em;
        display: block;
        width: 100%;
        box-shadow: 0 4px 8px rgba(0,123,255,0.3);
        transition: background-color 0.3s ease;
    }
    button:hover {
        background: #0056b3;
        box-shadow: 0 6px 12px rgba(0,86,179,0.5);
    }
    .back-btn {
        display: block;
        max-width: 300px;
        margin: 40px auto 0;
        text-align: center;
        background: #6c757d;
        color: white;
        padding: 12px 18px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        box-shadow: 0 3px 6px rgba(108,117,125,0.5);
        transition: background-color 0.3s ease;
    }
    .back-btn:hover {
        background: #565e64;
    }
</style>
</head>
<body>

<h2 style="text-align:center;">Add New Inquiry</h2>

<form method="post" action="">
    <?php if (!empty($errors['general'])): ?>
        <p class="error"><?= htmlspecialchars($errors['general']) ?></p>
    <?php endif; ?>

    <label for="client_name">Client Name</label>
    <input type="text" name="client_name" id="client_name" value="<?= htmlspecialchars($client_name) ?>" required />
    <?php if (!empty($errors['client_name'])): ?>
        <p class="error"><?= htmlspecialchars($errors['client_name']) ?></p>
    <?php endif; ?>

    <label for="client_email">Client Email</label>
    <input type="email" name="client_email" id="client_email" value="<?= htmlspecialchars($client_email) ?>" required />
    <?php if (!empty($errors['client_email'])): ?>
        <p class="error"><?= htmlspecialchars($errors['client_email']) ?></p>
    <?php endif; ?>

    <label for="message">Message</label>
    <textarea name="message" id="message" required><?= htmlspecialchars($message) ?></textarea>
    <?php if (!empty($errors['message'])): ?>
        <p class="error"><?= htmlspecialchars($errors['message']) ?></p>
    <?php endif; ?>

    <button type="submit">Add Inquiry</button>
</form>

<a href="contact_inquiries.php" class="back-btn">← Back to Inquiries</a>

</body>
</html>
