<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "photography_website";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Include PHPMailer
require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = intval($_POST['id']);
  $booking_date = $_POST['booking_date'];
  $booking_time = $_POST['booking_time'];
  $description = $_POST['description'] ?? '';

  // Update booking with date, time, description, and set status to approved
  $stmt = $conn->prepare("UPDATE bookings SET booking_date = ?, booking_time = ?, description = ?, status = 'approved' WHERE id = ?");
  $stmt->bind_param("sssi", $booking_date, $booking_time, $description, $id);
  $stmt->execute();
  $stmt->close();

  // Fetch booking details to send email
  $stmt = $conn->prepare("SELECT customer_email, customer_name FROM bookings WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $booking = $result->fetch_assoc();
  $stmt->close();

  if ($booking) {
    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'ajibrilla294@gmail.com'; // your SMTP username
      $mail->Password = 'ihnf gfww xapq rulc'; // your SMTP password
      $mail->SMTPSecure = 'ssl';
      $mail->Port = 465;

      $mail->setFrom('ajibrilla294@gmail.com', 'IBJ Studios');
      $mail->addAddress($booking['customer_email'], $booking['customer_name']);
      $mail->Subject = "Booking Approved - IBJ Studios";
      $mail->Body = "Dear {$booking['customer_name']},\n\nYour booking has been approved!\n\n📅 Date: {$booking_date}\n⏰ Time: {$booking_time}\n\nAdditional info: {$description}\n\nPlease arrive 10 minutes early. We look forward to seeing you!\n\nBest regards,\nIBJ Studios";

      $mail->send();
    } catch (Exception $e) {
      error_log("Mailer Error: " . $mail->ErrorInfo);
    }
  }

  // Redirect back to booking management
  header("Location: booking_management.php");
  exit();
}

// Show form only if id is set via GET
if (!isset($_GET['id'])) {
  header("Location: booking_management.php");
  exit();
}

$id = intval($_GET['id']);

// Fetch booking data to prefill form (optional)
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
  echo "Booking not found.";
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Approve Booking</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background: #f9fafb;
    }
    form {
      background: white;
      padding: 20px;
      border-radius: 8px;
      max-width: 400px;
      margin: auto;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }
    input, textarea {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border-radius: 4px;
      border: 1px solid #ccc;
      font-size: 1em;
    }
    button {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #10b981;
      border: none;
      color: white;
      font-size: 1em;
      border-radius: 6px;
      cursor: pointer;
    }
    button:hover {
      background-color: #059669;
    }
  </style>
</head>
<body>
  <h1>Approve Booking for <?= htmlspecialchars($booking['customer_name']) ?></h1>
  <form method="POST" action="">
    <input type="hidden" name="id" value="<?= $id ?>" />
    <label for="booking_date">Booking Date</label>
    <input type="date" id="booking_date" name="booking_date" value="<?= htmlspecialchars($booking['booking_date']) ?>" required />

    <label for="booking_time">Booking Time</label>
    <input type="time" id="booking_time" name="booking_time" value="<?= htmlspecialchars($booking['booking_time']) ?>" required />

    <label for="description">Description (optional)</label>
    <textarea id="description" name="description" rows="4"><?= htmlspecialchars($booking['description'] ?? '') ?></textarea>

    <button type="submit">Approve Booking & Send Email</button>
  </form>
</body>
</html>
