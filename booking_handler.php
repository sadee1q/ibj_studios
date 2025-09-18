<?php
// users/booking_handler.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Correct config path
require_once __DIR__ . '/includes/config.php';

// ✅ PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ✅ Correct PHPMailer paths
require_once __DIR__ . '/adminpanel/phpmailer/src/Exception.php';
require_once __DIR__ . '/adminpanel/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/adminpanel/phpmailer/src/SMTP.php';

// Only handle POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: users/booking.html'); // Adjusted to match folder
    exit;
}

// Collect + sanitize
$customer_name  = trim($_POST['customer_name'] ?? '');
$customer_email = trim($_POST['customer_email'] ?? '');
$booking_date   = trim($_POST['booking_date'] ?? '');
$booking_time   = trim($_POST['booking_time'] ?? '');
$notes          = trim($_POST['notes'] ?? '');

if ($customer_name === '' || $customer_email === '' || $booking_date === '' || $booking_time === '') {
    header('Location: users/booking.html?error=Please+fill+all+required+fields');
    exit;
}

if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    header('Location: users/booking.html?error=Invalid+email+address');
    exit;
}

// Save to DB
$stmt = $conn->prepare("
    INSERT INTO bookings (customer_name, customer_email, booking_date, booking_time, notes) 
    VALUES (?, ?, ?, ?, ?)
");
if ($stmt) {
    $stmt->bind_param("sssss", $customer_name, $customer_email, $booking_date, $booking_time, $notes);
    $stmt->execute();
    $stmt->close();
} else {
    die("Database error: " . $conn->error);
}

// Send email
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ajibrilla294@gmail.com';  // your Gmail
    $mail->Password   = 'dkosguovcajsuoyg';        // Gmail App Password
    $mail->SMTPSecure = 'ssl'; 
    $mail->Port       = 465;

    // ✅ Always set your Gmail as sender
    $mail->setFrom('ajibrilla294@gmail.com', 'Photography Website');
    // ✅ Send to admin
    $mail->addAddress('ajibrilla294@gmail.com', 'Admin');
    // ✅ Customer goes in Reply-To
    $mail->addReplyTo($customer_email, $customer_name);

    $mail->isHTML(true);
    $mail->Subject = "New Booking from $customer_name";
    $mail->Body    = "
        <strong>Name:</strong> $customer_name<br>
        <strong>Email:</strong> $customer_email<br>
        <strong>Date:</strong> $booking_date<br>
        <strong>Time:</strong> $booking_time<br>
        <strong>Notes:</strong> $notes
    ";

    $mail->send();

    header('Location: index.html?success=Your+booking+was+submitted');
    exit;
} catch (Exception $e) {
    die("Mailer Error: " . $mail->ErrorInfo);
}
