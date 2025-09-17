<?php
session_start();
require_once('includes/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid service ID.";
    exit;
}

$id = (int)$_GET['id'];
$name = $price = $category = $description = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $category = trim($_POST["category"]);
    $name = trim($_POST["name"]);
    $price = trim($_POST["price"]);
    $description = trim($_POST["description"]);

    if ($category == "") $errors[] = "Category is required.";
    if ($name == "") $errors[] = "Name is required.";
    if (!is_numeric($price) || $price < 0) $errors[] = "Valid price required.";

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE services SET category=?, name=?, price=?, description=? WHERE id=?");
        $stmt->bind_param("ssdsi", $category, $name, $price, $description, $id);
        if ($stmt->execute()) {
            header("Location: services_pricing.php");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
    }
} else {
    // Load existing data
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $category = $row['category'];
        $name = $row['name'];
        $price = $row['price'];
        $description = $row['description'];
    } else {
        echo "Service not found.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Service</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        form { background: white; padding: 25px; border-radius: 8px; max-width: 450px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label { display: block; margin-top: 15px; }
        input, select, textarea { width: 100%; padding: 10px; margin-top: 5px; }
        button { margin-top: 20px; background: #007BFF; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: red; font-size: 0.9em; margin-top: 10px; }
        .back-btn { display: block; text-align: center; margin-top: 20px; color: #007BFF; text-decoration: none; }
    </style>
</head>
<body>

<h2 style="text-align:center;">Edit Service</h2>

<form method="post">
    <?php if ($errors): ?>
        <div class="error"><?= implode("<br>", $errors) ?></div>
    <?php endif; ?>

    <label>Category</label>
    <select name="category" required>
        <option value="">-- Select --</option>
        <option value="Wedding" <?= $category == "Wedding" ? "selected" : "" ?>>Wedding</option>
        <option value="Portrait" <?= $category == "Portrait" ? "selected" : "" ?>>Portrait</option>
        <option value="Event" <?= $category == "Event" ? "selected" : "" ?>>Event</option>
        <option value="Other" <?= $category == "Other" ? "selected" : "" ?>>Other</option>
    </select>

    <label>Service Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>

    <label>Price (₦)</label>
    <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($price) ?>" required>

    <label>Description</label>
    <textarea name="description"><?= htmlspecialchars($description) ?></textarea>

    <button type="submit">Update Service</button>
</form>

<a href="services_pricing.php" class="back-btn">← Back to Services</a>

</body>
</html>
