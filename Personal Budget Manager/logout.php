<?php
session_start();

if (isset($_POST['confirm_logout'])) {
    session_unset();
    session_destroy(); 

    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(30, 30, 30, 0.9)), url('https://source.unsplash.com/1600x900/?finance,technology');
            background-size: cover;
            background-position: center;
            backdrop-filter: blur(8px);
            color: white;
        }
        .logout-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .logout-container h2 {
            margin-bottom: 20px;
        }
        .logout-container p {
            margin-bottom: 20px;
        }
        .logout-container button {
            background: #00ff00;
            color: black;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .logout-container button:hover {
            background: #00cc00;
        }
        .logout-container button.cancel {
            background: #ff0000;
            color: white;
        }
        .logout-container button.cancel:hover {
            background: #cc0000;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <h2>Are you sure you want to logout?</h2>
        <form method="POST">
            <button type="submit" name="confirm_logout">Yes</button>
            <button type="button" class="cancel" onclick="window.history.back();">No</button>
        </form>
    </div>
</body>
</html>