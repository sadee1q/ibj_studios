<?php
session_start();
require_once('includes/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

// Handle Delete Inquiry
if (isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM inquiries WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: contact_inquiries.php");
    exit;
}

// Fetch all inquiries
$query = "SELECT * FROM inquiries ORDER BY inquiry_date DESC";
$result = $conn->query($query);
$inquiries = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $inquiries[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Manage Inquiries</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f0f4f8;
        padding: 30px 20px;
        color: #2c3e50;
    }
    h1 {
        text-align: center;
        margin-bottom: 30px;
        font-weight: 700;
        font-size: 2rem;
        color: #34495e;
    }
    table {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto 40px auto;
        border-collapse: separate;
        border-spacing: 0 10px;
        background: white;
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        border-radius: 12px;
        overflow: hidden;
    }
    thead tr {
        background-color: #007bff;
        color: white;
        font-weight: 700;
        font-size: 1rem;
    }
    th, td {
        padding: 15px 20px;
        vertical-align: middle;
    }
    tbody tr {
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-radius: 8px;
        transition: background-color 0.2s ease;
    }
    tbody tr:hover {
        background-color: #f1f8ff;
    }
    td {
        color: #34495e;
        font-size: 0.95rem;
        line-height: 1.3;
        word-break: break-word;
    }
    /* Action Buttons Container */
    .action-btns {
        white-space: nowrap;
        width: 150px; /* Adjusted width since edit button removed */
        text-align: center;
    }
    /* Buttons base style */
    .action-btns a,
    .action-btns form button {
        display: inline-block;
        margin: 0 6px;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: background-color 0.25s ease, box-shadow 0.25s ease;
        text-decoration: none;
        color: white;
        box-shadow: 0 4px 8px rgba(0,0,0,0.12);
    }
    /* Specific button colors */
    .reply-btn {
        background-color: #17a2b8;
    }
    .reply-btn:hover {
        background-color: #138496;
        box-shadow: 0 6px 12px rgba(19,132,150,0.5);
    }
    .delete-btn {
        background-color: #dc3545;
        padding: 8px 16px;
    }
    .delete-btn:hover {
        background-color: #bd2130;
        box-shadow: 0 6px 12px rgba(189,33,48,0.5);
    }
    /* Add New Inquiry button */
    .add-btn {
        display: block;
        max-width: 220px;
        margin: 0 auto 35px;
        padding: 12px 20px;
        background-color: #007bff;
        color: white;
        font-weight: 700;
        text-align: center;
        border-radius: 10px;
        text-decoration: none;
        box-shadow: 0 6px 16px rgba(0,123,255,0.4);
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }
    .add-btn:hover {
        background-color: #0056b3;
        box-shadow: 0 8px 20px rgba(0,86,179,0.6);
    }
    /* No inquiries message */
    .no-inquiries {
        text-align: center;
        color: #7f8c8d;
        font-size: 1.1rem;
        margin-top: 60px;
    }
    /* Back to dashboard */
    .back-btn {
        display: block;
        max-width: 280px;
        margin: 0 auto;
        padding: 12px 0;
        background: #007bff;
        color: white;
        text-align: center;
        font-weight: 700;
        border-radius: 10px;
        text-decoration: none;
        box-shadow: 0 5px 14px rgba(0,123,255,0.5);
        transition: background-color 0.3s ease;
    }
    .back-btn:hover {
        background-color: #0056b3;
    }
</style>
</head>
<body>

<h1>Manage Inquiries</h1>

<a href="add_inquiry.php" class="add-btn">➕ Add New Inquiry</a>

<?php if (count($inquiries) === 0): ?>
    <p class="no-inquiries">No inquiries available.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Client Name</th>
            <th>Client Email</th>
            <th>Message</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($inquiries as $inq): ?>
        <tr>
            <td><?= htmlspecialchars($inq['id']) ?></td>
            <td><?= htmlspecialchars($inq['client_name']) ?></td>
            <td><?= htmlspecialchars($inq['client_email']) ?></td>
            <td><?= nl2br(htmlspecialchars($inq['message'])) ?></td>
            <td><?= htmlspecialchars(date("Y-m-d H:i", strtotime($inq['inquiry_date']))) ?></td>
            <td class="action-btns">
            <a href="reply_inquiry.php?id=<?= $inq['id'] ?>" class="reply-btn" title="Reply to Inquiry">✉️ Reply</a>
            <form method="post" action="contact_inquiries.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this inquiry?');">
                    <input type="hidden" name="delete_id" value="<?= $inq['id'] ?>">
                    <button type="submit" class="delete-btn" title="Delete Inquiry">🗑️ Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

</body>
</html>
