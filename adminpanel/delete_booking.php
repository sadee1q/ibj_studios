<?php
require_once('config.php');

// Check if booking ID is provided
if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];

    // Delete the booking
    $stmt = $mysqli->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        echo "Booking deleted.";
        header("Location: view_bookings.php");
    } else {
        echo "Error deleting booking: " . $stmt->error;
    }
} else {
    echo "No booking ID specified.";
}
?>
