<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(30, 30, 30, 0.9)), url('https://source.unsplash.com/1600x900/?finance,technology');
            background-size: cover;
            background-position: center;
            backdrop-filter: blur(8px);
            color: white;
        }
        .sidebar {
            width: 250px;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            padding: 20px;
            position: fixed;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            color: #00ff00;
        }
        .sidebar a.active {
            color: #00ff00;
            font-weight: bold;
        }
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 20px;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            margin: 0 auto;
        }
        .form-container input, .form-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            outline: none;
        }
        .form-container input[type="submit"] {
            background: #00ff00;
            color: black;
            font-weight: bold;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background: #00cc00;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="add_transaction.php">Add Transaction</a>
            <a href="statistics.php">Statistics</a>
            <a href="#" class="active">Settings</a>
        </div>
        <div>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="form-container">
            <h2>Settings</h2>
            <form action="update_settings.php" method="POST">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>

                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>

                <label for="budget">Monthly Budget</label>
                <input type="number" name="budget" id="budget" required>

                <input type="submit" value="Save">
            </form>
        </div>
    </div>
</body>
</html>