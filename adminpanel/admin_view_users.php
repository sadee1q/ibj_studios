<?php
require_once __DIR__ . '/../config.php';

// Fetch all users
$stmt = $mysqli->prepare("SELECT * FROM users ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Users - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2 class="mb-4">All Registered Users</h2>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Registered At</th>
                <th>Gallery Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <?php
                    $user_id = $user['id'];
                    $user_name = preg_replace('/\s+/', '_', $user['name']);
                    $folder_name = $user_id . '_' . $user_name;
                    $base_path = __DIR__ . '/../client_galleries/' . $folder_name;
                    $has_base = is_dir($base_path);

                    // Check if any subfolder exists inside base folder
                    $has_subfolder = false;
                    if ($has_base) {
                        $subfolders = glob($base_path . '/*', GLOB_ONLYDIR);
                        $has_subfolder = !empty($subfolders);
                    }
                ?>
                <tr>
                    <td><?= htmlspecialchars($user_id) ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                    <td>
                        <?php if (!$has_base): ?>
                            <a href="admin_create_gallery.php?user_id=<?= $user_id ?>" class="btn btn-sm btn-primary">
                                Create Gallery
                            </a>
                        <?php elseif (!$has_subfolder): ?>
                            <a href="admin_create_folder.php?user_id=<?= $user_id ?>" class="btn btn-sm btn-info">
                                Create Folder
                            </a>
                        <?php else: ?>
                            <!-- Redirects to login before viewing gallery -->
                            <a href="client_login.php?user_id=<?= $user_id ?>" class="btn btn-sm btn-success">
                                View Gallery
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-center">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
