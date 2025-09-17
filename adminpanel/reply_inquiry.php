<?php
session_start();
require_once('includes/config.php');

// PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: contact_inquiries.php");
    exit;
}

$inquiry_id = intval($_GET['id']);

// Fetch inquiry info
$stmt = $conn->prepare("SELECT client_name, client_email, message FROM inquiries WHERE id = ?");
$stmt->bind_param("i", $inquiry_id);
$stmt->execute();
$result = $stmt->get_result();
$inquiry = $result->fetch_assoc();
$stmt->close();

if (!$inquiry) {
    echo "Inquiry not found.";
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = filter_var($_POST['to'], FILTER_VALIDATE_EMAIL);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (!$to) $errors[] = "Invalid email address.";
    if (empty($subject)) $errors[] = "Subject is required.";
    if (empty($message)) $errors[] = "Message cannot be empty.";

    if (empty($errors)) {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ajibrilla294@gmail.com';      // Your Gmail
            $mail->Password = 'dkosguovcajsuoyg';         // 16-char App Password
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;


            // Sender and recipient
            $mail->setFrom('ajibrilla294@gmail.com', 'ibj studios');
            $mail->addAddress($to);

            // Email content
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
            $success = "✅ Email sent successfully!";
        } catch (Exception $e) {
            $errors[] = "Mailer Error: " . $mail->ErrorInfo;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reply to Inquiry</title>
    <style>
        .back-btn {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 16px;
    background-color: #6c757d;
    color: white;
    text-decoration: none;
    font-weight: bold;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}
.back-btn:hover {
    background-color: #5a6268;
}

        body { font-family: sans-serif; background: #f5f5f5; padding: 40px; }
        .container { max-width: 700px; background: #fff; padding: 20px; margin: auto; border-radius: 10px; }
        input, textarea, button {
            width: 100%; padding: 10px; margin: 10px 0; font-size: 1rem;
        }
        button { background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
<div class="container">
    <h2>Reply to: <?= htmlspecialchars($inquiry['client_email']) ?></h2>

    <?php if (!empty($errors)): ?>
        <div class="error"><ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Email:</label>
        <input type="email" name="to" value="<?= htmlspecialchars($inquiry['client_email']) ?>" readonly />

        <label>Subject:</label>
        <input type="text" name="subject" placeholder="Enter subject..." />

        <label>Message:</label>
        <textarea name="message" rows="8" placeholder="Write your reply here..."></textarea>

        <button type="submit">Send Reply</button>
    </form>
    <a href="contact_inquiries.php" class="back-btn">← Back to Inquiries</a>

</div>
</body>
</html>
