<?php
// users/submit_inquiry.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../adminpanel/includes/config.php';

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../adminpanel/includes/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../adminpanel/includes/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../adminpanel/includes/PHPMailer/src/SMTP.php';

// Only handle POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.html');
    exit;
}

// Collect + sanitize
$client_name = trim($_POST['fullname'] ?? '');
$client_email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($client_name === '' || $client_email === '' || $message === '') {
    header('Location: contact.html?error=Please+fill+all+fields');
    exit;
}

if (!filter_var($client_email, FILTER_VALIDATE_EMAIL)) {
    header('Location: contact.html?error=Invalid+email+address');
    exit;
}

// Save to DB
$stmt = $conn->prepare("INSERT INTO inquiries (client_name, client_email, message) VALUES (?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("sss", $client_name, $client_email, $message);
    $stmt->execute();
    $stmt->close();
} else {
    die("Database error: " . $conn->error);
}

// Send email to admin
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; 
    $mail->SMTPAuth = true;
    $mail->Username = 'ajibrilla294@gmail.com'; 
    $mail->Password = 'dkosguovcajsuoyg'; 
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom($client_email, $client_name);
    $mail->addAddress('ajibrilla294@gmail.com', 'Admin'); 

    $mail->isHTML(true);
    $mail->Subject = "New Inquiry from $client_name";
    $mail->Body    = "<strong>Name:</strong> $client_name<br>
                      <strong>Email:</strong> $client_email<br>
                      <strong>Message:</strong><br>$message";

    $mail->send();

    header('Location: contact.html?success=Your+inquiry+was+sent');
    exit;
} catch (Exception $e) {
    die("Mailer Error: " . $mail->ErrorInfo);
}
