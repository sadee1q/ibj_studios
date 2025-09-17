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

// Handle status updates and delete
if (isset($_GET['action'], $_GET['id'])) {
  $id = intval($_GET['id']);
  $action = $_GET['action'];

  $validActions = ['approve', 'reject', 'cancel', 'delete'];
  if (in_array($action, $validActions)) {
    if ($action === 'delete') {
      // Delete booking from database
      $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
      $stmt->bind_param("i", $id);
      $stmt->execute();
      $stmt->close();
    } else {
          // Update status for approve, reject, cancel
          $status = match ($action) {
            'approve' => 'approved',
            'reject' => 'rejected',
            'cancel' => 'cancelled',
          };

          $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
          $stmt->bind_param("si", $status, $id);
          $stmt->execute();
          $stmt->close();

          if ($action === 'approve') {
            // Fetch booking details for email
            $stmt = $conn->prepare("SELECT customer_email, customer_name, booking_date, booking_time FROM bookings WHERE id = ?");
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
                $mail->Username = 'ajibrilla294@gmail.com';
                $mail->Password = 'ihnf gfww xapq rulc';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                $mail->setFrom('ajibrilla294@@gmail.com', 'IBJ Studios');
                $mail->addAddress($booking['customer_email'], $booking['customer_name']);
                $mail->Subject = "Booking Approved - IBJ Studios";
                $mail->Body = "Dear {$booking['customer_name']},\n\nYour booking has been approved!\n\n📅
                 Date: {$booking['booking_date']}\n⏰ Time: {$booking['booking_time']}\n\nPlease arrive 10 minutes early.
                  We look forward to seeing you!\n\nBest regards,\nIBJ Studios";

                $mail->send();
              } catch (Exception $e) {
                error_log("Mailer Error: " . $mail->ErrorInfo);
              }
            }
          }
        }

        header("Location: booking_management.php");
        exit();
      }
    }

// Fetch bookings
$sql = "SELECT * FROM bookings ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Booking Management</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    /* (Your existing CSS here unchanged) */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }
    body {
      background-color: #f9fafb;
      padding: 20px;
    }
    .container {
      max-width: 1100px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    }
    h1 {
      font-size: 28px;
    }
    .top-buttons {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .actions {
      display: flex;
      flex-wrap: wrap;
    }
    .btn {
      padding: 6px 12px;
      text-decoration: none;
      color: white;
      font-size: 0.9em;
      border-radius: 6px;
      margin: 5px 5px 0 0;
      display: inline-block;
      transition: background-color 0.2s ease;
    }
    .btn.approve { background-color: #10b981; }
    .btn.approve:hover { background-color: #059669; }
    .btn.reject { background-color: #ef4444; }
    .btn.reject:hover { background-color: #dc2626; }
    .btn.cancel { background-color: #6b7280; }
    .btn.cancel:hover { background-color: #4b5563; }
    .btn.back { background-color: #3b82f6; }
    .btn.back:hover { background-color: #2563eb; }
    .btn.add-booking { background-color: #9333ea; }
    .btn.add-booking:hover { background-color: #7e22ce; }

    /* New Delete button style */
    .btn.delete {
      background-color: #ef4444;
      color: white;
      transition: background-color 0.2s ease;
    }
    .btn.delete:hover {
      background-color: #b91c1c;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      text-align: left;
      padding: 16px;
      border-bottom: 1px solid #e5e7eb;
    }
    th {
      background-color: #f3f4f6;
      font-weight: 600;
    }
    .status {
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 0.85em;
      display: inline-block;
    }
    .pending { background-color: #fef08a; color: #92400e; }
    .approved { background-color: #bbf7d0; color: #065f46; }
    .rejected { background-color: #fecaca; color: #7f1d1d; }
    .cancelled { background-color: #e5e7eb; color: #374151; }
    @media (max-width: 768px) {
      table, thead, tbody, th, td, tr {
        display: block;
      }
      th {
        position: absolute;
        left: -9999px;
      }
      tr {
        margin-bottom: 1rem;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
      }
      td {
        padding: 10px;
        border: none;
        display: flex;
        justify-content: space-between;
      }
      td::before {
        font-weight: bold;
        content: attr(data-label);
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="top-buttons">
      <h1>📸 Booking Management</h1>
      <div>
        <a href="add_booking.php" class="btn add-booking">➕ Add Booking</a>
        <a href="dashboard.php" class="btn back">⬅️ Back to Dashboard</a>
      </div>
    </div>
    <table>
      <thead>
        <tr>
          <th>Customer</th>
          <th>Session</th>
          <th>Date</th>
          <th>Time</th>
          <th>Status</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <?php $status = strtolower($row['status']); ?>
            <tr>
              <td data-label="Customer">
                <?= htmlspecialchars($row['customer_name']) ?><br>
                <small><?= htmlspecialchars($row['customer_email']) ?></small>
              </td>
              <td data-label="Session"><?= htmlspecialchars($row['session_type'] ?? '—') ?></td>
              <td data-label="Date"><?= $row['booking_date'] ?></td>
              <td data-label="Time"><?= $row['booking_time'] ?></td>
              <td data-label="Status">
                <span class="status <?= $status ?>">
                  <?= ucfirst($status) ?>
                </span>
              </td>
              <td data-label="Created"><?= date("Y-m-d H:i", strtotime($row['created_at'])) ?></td>
              <td data-label="Actions" class="actions">
                <?php if ($status === 'pending'): ?>
                  <a href="approve_booking.php?id=<?= $row['id'] ?>" class="btn approve">✅ Approve</a>
                  <a href="?action=reject&id=<?= $row['id'] ?>" class="btn reject">❌ Reject</a>
                  <a href="?action=cancel&id=<?= $row['id'] ?>" class="btn cancel">🕒 Cancel</a>
                <?php else: ?>
                  <span style="font-size: 0.85em; color: #6b7280;">No actions</span>
                <?php endif; ?>
                <!-- Delete button with spacing -->
                <a href="?action=delete&id=<?= $row['id'] ?>" class="btn delete"
                   style="margin-left: 10px;"
                   onclick="return confirm('Are you sure you want to delete this booking? This action cannot be undone.');">
                  🗑️ Delete
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7">No bookings found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
<?php $conn->close(); ?>
