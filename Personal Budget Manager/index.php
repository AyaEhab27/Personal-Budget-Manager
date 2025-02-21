<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error in connection: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        handleLogin($conn);
    } elseif (isset($_POST['register'])) {
        handleRegistration($conn);
    }
}

$conn->close();

function handleLogin($conn) {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        echo "<script>alert('Please fill in all fields!');</script>";
        return;
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, balance FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['balance'] = $row['balance'];
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password!');</script>";
        }
    } else {
        echo "<script>alert('Username does not exist!');</script>";
    }
}

function handleRegistration($conn) {
    if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
        echo "<script>alert('Please fill in all fields!');</script>";
        return;
    }

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verify that the email is not in use
    $sql_check_email = "SELECT id FROM users WHERE email = ?";
    $stmt_check_email = $conn->prepare($sql_check_email);
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $result_check_email = $stmt_check_email->get_result();

    if ($result_check_email->num_rows > 0) {
        echo "<script>alert('Email already exists!');</script>";
        return;
    }

    // Verify that the username is not in use
    $sql_check_username = "SELECT id FROM users WHERE username = ?";
    $stmt_check_username = $conn->prepare($sql_check_username);
    $stmt_check_username->bind_param("s", $username);
    $stmt_check_username->execute();
    $result_check_username = $stmt_check_username->get_result();

    if ($result_check_username->num_rows > 0) {
        echo "<script>alert('Username already exists!');</script>";
        return;
    }

    // create account
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password, balance) VALUES (?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        $new_user_id = $stmt->insert_id;
        $_SESSION['user_id'] = $new_user_id;
        $_SESSION['username'] = $username;
        $_SESSION['balance'] = 0;
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Error creating account!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp and LogIn Page</title>
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
            height: 100vh;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(30, 30, 30, 0.9)), url('https://source.unsplash.com/1600x900/?technology');
            background-size: cover;
            background-position: center;
            backdrop-filter: blur(8px);
        }

        .container {
            width: 400px;
            padding: 30px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            text-align: center;
            transition: all 0.3s ease-in-out;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        h2 {
            color: #ffffff;
            font-size: 24px;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            outline: none;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        input:focus {
            background: rgba(255, 255, 255, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        button {
            background: rgba(0, 255, 0, 0.7);
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            cursor: pointer;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        button:hover {
            background: rgba(0, 255, 0, 1);
            transform: scale(1.05);
        }

        .switch {
            margin-top: 15px;
            cursor: pointer;
            font-size: 14px;
            transition: color 0.3s ease;
            color: white; 
        }

        .switch span {
            color: #00ff00;
            font-weight: bold;
            text-decoration: none;
        }

        .switch:hover span {
            color: #00cc00;
        }

        .hidden {
            display: none;
        }

        .container.scale-effect {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container" id="formContainer">
        <h2 id="formTitle">Login Page</h2>
        <form id="loginForm" action="index.php" method="POST">
            <input type="text" name="username" placeholder="UserName " required>
            <input type="password" name="password" placeholder="Password " required>
            <button type="submit" name="login">Enter</button>
        </form>
        <form id="registerForm" class="hidden" action="index.php" method="POST">
            <input type="text" name="username" placeholder="UserName " required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="register">Create Account </button>
        </form>
        <div class="switch" onclick="toggleForms()">
            <span id="switchText"><span style="color: white;">I don't have an account?</span> <span> Sign up</span></span>
        </div>
    </div>

    <script>
        function toggleForms() {
            let loginForm = document.getElementById('loginForm');
            let registerForm = document.getElementById('registerForm');
            let formTitle = document.getElementById('formTitle');
            let switchText = document.getElementById('switchText');
            let container = document.getElementById('formContainer');

            loginForm.classList.toggle('hidden');
            registerForm.classList.toggle('hidden');

            if (loginForm.classList.contains('hidden')) {
                formTitle.textContent = 'Create Account Page';
                switchText.innerHTML = '<span style="color: white;">I  have an account?</span> <span>  Login</span>';
            } else {
                formTitle.textContent = 'Login Page';
                switchText.innerHTML = '<span style="color: white;">I don\'t have an account?</span> <span> Sign UP</span>';
            }

            container.classList.add('scale-effect');
            setTimeout(() => {
                container.classList.remove('scale-effect');
            }, 200);
        }
    </script>
</body>
</html>