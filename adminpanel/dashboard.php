<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <body>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f5f9;
            color: #333;
        }

        .admin-dashboard {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 250px;
            background-color: #1f1f1f;
            color: #fff;
            padding: 20px;
            flex-shrink: 0;
        }

        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 30px;
        }

        .nav {
            list-style: none;

        }

        .nav li {
            padding: 12px 15px;
            margin-bottom: 10px;
            color: #ccc;
            border-radius: 8px;
            transition: background 0.3s, color 0.3s;
            list-style: none;
        }

        .nav li:hover,
        .nav li.active {
            background-color: #333;
            color: #fff;
            cursor: pointer;

        }

        .main-content {
            flex-grow: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .cards-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
        }

        .card-link {
            color: inherit;
        }

        .card {
    background-color: #fff;
    padding: 55px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height:200px;
    margin-right:50px;
    box-sizing: border-box;
}

a {
  text-decoration: none;
  color: #fff;
}


        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .card h3 {
            color: #4CAF50;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .card p {
            font-size: 14px;
            color: #666;
        }

        @media (max-width: 768px) {
            .admin-dashboard {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                text-align: center;
            }

            .main-content {
                padding: 15px;
            }
        }
    </style>

    <div class="admin-dashboard">
        <aside class="sidebar">
            <h2>Admin Dashboard</h2>
            <ul class="nav">
              
                <li><a href="booking_management.php">Booking Management</a></li>
                <li><a href="contact_inquiries.php">Contact/Inquiries</a></li>
            </ul>
        </aside>

      



                <a href="booking_management.php" class="card-link">
                    <div class="card">
                        <h3>booking management</h3>
                        <p>Control user accounts, roles, and access levels.</p>
                    </div>
                </a>

                   
               

                <a href="contact_inquiries.php" class="card-link">
                    <div class="card">
                        <h3>contact inquiries</h3>
                        <p>Control user accounts, roles, and access levels.</p>
                    </div>
                </a>

           

               
              
            
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>
