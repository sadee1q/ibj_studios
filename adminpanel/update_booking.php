<?php
require_once('config.php');

// Check if booking ID is provided
if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];
    $new_status = $_GET['status'];  // Expected values: 'confirmed', 'cancelled'

    // Validate the new status
    if ($new_status != 'confirmed' && $new_status != 'cancelled') {
        echo "Invalid status.";
        exit;
    }

    // Update the status
    $stmt = $mysqli->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $booking_id);

    if ($stmt->execute()) {
        echo "Booking status updated to " . $new_status;
        header("Location: view_bookings.php");
    } else {
        echo "Error updating status: " . $stmt->error;
    }
} else {
    echo "No booking ID specified.";
}
?>
