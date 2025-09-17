<?php
session_start();
require_once('includes/config.php');

// Check admin login
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

$name = $price = $category = $description = "";
$name_err = $price_err = $category_err = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter a service name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate price (allow decimals)
    if (empty(trim($_POST["price"]))) {
        $price_err = "Please enter a price.";
    } elseif (!is_numeric($_POST["price"]) || $_POST["price"] < 0) {
        $price_err = "Please enter a valid positive number.";
    } else {
        $price = trim($_POST["price"]);
    }
    
    // Validate category
    if (empty(trim($_POST["category"]))) {
        $category_err = "Please select a category.";
    } else {
        $category = trim($_POST["category"]);
    }

    // Description is optional
    $description = trim($_POST["description"]);

    // If no errors, insert into DB
    if (empty($name_err) && empty($price_err) && empty($category_err)) {
        $stmt = $conn->prepare("INSERT INTO services (category, name, price, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $category, $name, $price, $description);
        if ($stmt->execute()) {
            $success_msg = "Service added successfully.";
            // Clear fields
            $name = $price = $category = $description = "";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Add Service</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f9fafb;
        padding: 30px 15px;
        display: flex;
        justify-content: center;
        min-height: 100vh;
        align-items: flex-start;
    }
    form {
        background: #fff;
        padding: 30px 35px;
        border-radius: 10px;
        max-width: 450px;
        width: 100%;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    h2 {
        margin-bottom: 25px;
        font-weight: 700;
        color: #333;
        text-align: center;
    }
    label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #444;
        margin-top: 18px;
    }
    input[type=text],
    input[type=number],
    select,
    textarea {
        width: 100%;
        padding: 12px 14px;
        font-size: 1rem;
        border: 1.8px solid #ccc;
        border-radius: 6px;
        transition: border-color 0.3s ease;
        resize: vertical;
        font-family: inherit;
    }
    input[type=text]:focus,
    input[type=number]:focus,
    select:focus,
    textarea:focus {
        border-color: #007BFF;
        outline: none;
        box-shadow: 0 0 5px rgba(0,123,255,0.4);
    }
    textarea {
        min-height: 80px;
    }
    .error {
        color: #d93025;
        font-size: 0.875rem;
        margin-top: 4px;
        font-weight: 600;
    }
    .success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
        padding: 12px 15px;
        border-radius: 7px;
        margin-bottom: 18px;
        font-weight: 600;
        text-align: center;
    }
    button {
        margin-top: 25px;
        width: 100%;
        padding: 14px 0;
        background-color: #007BFF;
        border: none;
        color: white;
        font-size: 1.1rem;
        font-weight: 700;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        user-select: none;
    }
    button:hover {
        background-color: #0056b3;
    }
    .back-btn {
        margin-top: 20px;
        display: inline-block;
        text-align: center;
        width: 100%;
        padding: 12px 0;
        border-radius: 8px;
        border: 2px solid #007BFF;
        color: #007BFF;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease, color 0.3s ease;
        user-select: none;
        text-decoration: none;
    }
    .back-btn:hover {
        background-color: #007BFF;
        color: white;
    }
</style>
</head>
<body>

<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" novalidate>
    <h2>Add New Service</h2>

    <?php if ($success_msg): ?>
        <div class="success"><?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>

    <label for="category">Category</label>
    <select name="category" id="category" required>
        <option value="" disabled <?= $category == "" ? "selected" : "" ?>>-- Select Category --</option>
        <option value="Wedding" <?= $category == "Wedding" ? "selected" : "" ?>>Wedding</option>
        <option value="Portrait" <?= $category == "Portrait" ? "selected" : "" ?>>Portrait</option>
        <option value="Event" <?= $category == "Event" ? "selected" : "" ?>>Event</option>
        <option value="Other" <?= $category == "Other" ? "selected" : "" ?>>Other</option>
    </select>
    <span class="error"><?= $category_err ?></span>

    <label for="name">Service Name</label>
    <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>" required />
    <span class="error"><?= $name_err ?></span>

    <label for="price">Price (e.g., 49.99)</label>
    <input type="number" step="0.01" name="price" id="price" value="<?= htmlspecialchars($price) ?>" required />
    <span class="error"><?= $price_err ?></span>

    <label for="description">Description (optional)</label>
    <textarea name="description" id="description"><?= htmlspecialchars($description) ?></textarea>

    <button type="submit">Add Service</button>
    <a href="services_pricing.php" class="back-btn">&larr; Back to Services</a>
</form>

</body>
</html>
