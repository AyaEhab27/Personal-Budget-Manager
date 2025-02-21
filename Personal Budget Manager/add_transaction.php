<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "users_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'];
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO transactions (user_id, type, category, amount, date) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $user_id, $type, $category, $amount, $date);

    if ($stmt->execute()) {
        $sql_update_balance = "UPDATE users SET balance = balance ";
        if ($type == 'income') {
            $sql_update_balance .= "+ ?";
        } else {
            $sql_update_balance .= "- ?";
        }
        $sql_update_balance .= " WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update_balance);
        $stmt_update->bind_param("di", $amount, $user_id);
        $stmt_update->execute();
        $stmt_update->close();

        echo "<script>alert('Transaction added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Transaction</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .form-container h2 {
            margin-bottom: 20px;
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
        .form-container input[type="number"] {
            width: calc(100% - 80px);
            display: inline-block;
        }
        .form-container button {
            width: 40px;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background: #00ff00;
            color: black;
            font-weight: bold;
            cursor: pointer;
        }
        .form-container button:hover {
            background: #00cc00;
        }
        .form-container input[type="submit"] {
            width: 100%;
            background: #00ff00;
            color: black;
            font-weight: bold;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background: #00cc00;
        }
        .amount-container {
    display: flex;
    flex-direction: row; 
    align-items: center; 
    gap: 10px; 
}

.buttons {
    display: flex;
    flex-direction: row; 
    gap: 5px; 
}

.buttons button {
    width: 40px;
    height: 40px; 
    padding: 10px;
    border: none;
    border-radius: 5px;
    background: #00ff00;
    color: black;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.buttons button:hover {
    background: #00cc00;
}

input[type="number"] {
    height: 40px; 
    padding: 10px;
    border: none;
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    outline: none;
}
    
    </style>
</head>
<body>
    <div class="sidebar">
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="#" class="active">Add Transaction</a>
            <a href="statistics.php">Statistics</a>
            <a href="settings.php">Settings</a>
        </div>
        <div>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="main-content">
        <div class="form-container">
            <h2>Add Transaction</h2>
            <form action="add_transaction.php" method="POST">
                <label for="type">Type</label>
                <select name="type" id="type" onchange="changeColor(this)">
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>

                <label for="category">Category</label>
                <select name="category" id="category">
                    <option value="salary">Salary</option>
                    <option value="food">Food</option>
                    <option value="rent">Rent</option>
                    <option value="entertainment">Entertainment</option>
                    <option value="other">Other</option>
                </select>

                <label for="amount">Amount</label>
                <div class="amount-container">
                    <input type="number" name="amount" id="amount" value="0" min="0" step="10">
                    <div class="buttons" >
                        <button type="button" onclick="increaseAmount()">+</button>
                        <button type="button" onclick="decreaseAmount()">-</button>
                    </div>
                </div>

                <label for="date">Date</label>
                <input type="date" name="date" id="date" required>

                <input type="submit" value="Save">
            </form>
        </div>
    </div>

    <script>
        function changeColor(select) {
            const form = select.closest('form');
            if (select.value === 'income') {
                form.style.border = '2px solid #00ff00';
            } else {
                form.style.border = '2px solid #ff0000';
            }
        }

        function increaseAmount() {
            const amountInput = document.getElementById('amount');
            let amount = parseInt(amountInput.value);
            amount += 10;
            amountInput.value = amount;
        }

        function decreaseAmount() {
            const amountInput = document.getElementById('amount');
            let amount = parseInt(amountInput.value);
            if (amount >= 10) {
                amount -= 10;
                amountInput.value = amount;
            }
        }
    </script>
</body>
</html>