<?php
require_once('config.php');
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];
    $status = 'pending';  // Default status
    $notes = trim($_POST['notes']);  // Optional field

    // Validate inputs
    if (empty($customer_name) || empty($customer_email) || empty($booking_date) || empty($booking_time)) {
        $error_message = "All fields are required.";
    } else {
        // Insert the booking into the database
        $stmt = $mysqli->prepare("INSERT INTO bookings (customer_name, customer_email, booking_date, booking_time, status, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $customer_name, $customer_email, $booking_date, $booking_time, $status, $notes);

        if ($stmt->execute()) {
            // Redirect to another page after successful booking
            header("Location: booking_management.php");
            exit;
        } else {
            $error_message = "Error adding booking: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input, textarea, button {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        input[type="text"],
        input[type="email"],
        input[type="date"],
        input[type="time"] {
            font-size: 16px;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
            font-size: 16px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        p.error-message {
            color: red;
            text-align: center;
        }


        .back-button {
            background-color: #f0f0f0;
            color: #333;
            border: 1px solid #ddd;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            margin-top: 20px;
            display: block;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }

        .back-button:hover {
            background-color: #e2e2e2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Booking</h2>
        <?php if (isset($error_message)) echo "<p class='error-message'>$error_message</p>"; ?>
        <form method="POST">
            <input type="text" name="customer_name" placeholder="Customer Name" required>
            <input type="email" name="customer_email" placeholder="Customer Email" required>
            <input type="date" name="booking_date" required>
            <input type="time" name="booking_time" required>
            <textarea name="notes" placeholder="Additional Notes (Optional)"></textarea>
            <button type="submit">Submit</button>
        </form>

        <!-- Back button -->
        <a href="javascript:history.back()" class="back-button">Back</a>
    </div>
</body>
</html>
