<?php
session_start();

$conn = new mysqli("localhost", "root", "", "users_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$sql_budget = "SELECT budget FROM users WHERE id = ?";
$stmt_budget = $conn->prepare($sql_budget);
$stmt_budget->bind_param("i", $user_id);
$stmt_budget->execute();
$result_budget = $stmt_budget->get_result();
$budget = $result_budget->fetch_assoc()['budget'] ?? 0;
$stmt_budget->close();

$sql_income = "SELECT SUM(amount) AS total_income FROM transactions WHERE user_id = ? AND type = 'income'";
$stmt_income = $conn->prepare($sql_income);
$stmt_income->bind_param("i", $user_id);
$stmt_income->execute();
$result_income = $stmt_income->get_result();
$income = $result_income->fetch_assoc()['total_income'] ?? 0;

$sql_expenses = "SELECT SUM(amount) AS total_expenses FROM transactions WHERE user_id = ? AND type = 'expense'";
$stmt_expenses = $conn->prepare($sql_expenses);
$stmt_expenses->bind_param("i", $user_id);
$stmt_expenses->execute();
$result_expenses = $stmt_expenses->get_result();
$expenses = $result_expenses->fetch_assoc()['total_expenses'] ?? 0;

$balance = $income - $expenses;

$sql_transactions = "SELECT type, category, amount, date FROM transactions WHERE user_id = ? ORDER BY date DESC LIMIT 5";
$stmt_transactions = $conn->prepare($sql_transactions);
$stmt_transactions->bind_param("i", $user_id);
$stmt_transactions->execute();
$result_transactions = $stmt_transactions->get_result();
$transactions = $result_transactions->fetch_all(MYSQLI_ASSOC);

$stmt_income->close();
$stmt_expenses->close();
$stmt_transactions->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
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
            width: 250px; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(10px);
            padding: 20px; position: fixed; height: 100vh; display: flex; flex-direction: column; justify-content: space-between;
        }
        .sidebar a { color: white; text-decoration: none; padding: 10px; display: block; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { color: #00ff00; font-weight: bold; }
        .main-content { 
            margin-left: 250px; flex: 1; padding: 20px; 
            display: flex; flex-direction: column; justify-content: center; align-items: center;
        }
        .balance-container, .budget-container, .summary-container, .transactions-container {
            background: rgba(255, 255, 255, 0.1); padding: 30px; border-radius: 15px; margin-bottom: 25px; text-align: center;
            width: 90%; max-width: 900px; /* زيادة حجم البطاقات قليلاً */
        }
        .summary-container {
            display: flex; justify-content: space-around; gap: 20px; flex-wrap: wrap;
        }
        .summary-item {
            flex: 1; padding: 20px; border-radius: 10px; max-width: 300px; background: rgba(255, 255, 255, 0.1);
        }
        .income { color: #00ff00; font-size: 24px; font-weight: bold; } 
        .expenses { color: #ff0000; font-size: 24px; font-weight: bold; } 
        .budget-color { color:rgb(255, 0, 128); font-size: 24px; font-weight: bold; } 
        .orange { color: #ffa500; }
        .summary-item p { color: white; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; border: 1px solid white; text-align: center; }
        th { background: rgba(255, 255, 255, 0.2); }
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 25px;
            width: 90%;
            max-width: 900px; 
            text-align: center;
        }
        .budget-summary {
            display: flex;
            justify-content: space-around;
            gap: 20px;
            flex-wrap: wrap;
        }
        .budget-item {
            flex: 1;
            padding: 20px;
            border-radius: 10px;
            max-width: 300px;
            background: rgba(255, 255, 255, 0.1);
        }
        .transactions-color { color: #ffff00; font-size: 24px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div>
            <a href="#" class="active">Dashboard</a>
            <a href="add_transaction.php">Add Transaction</a>
            <a href="statistics.php">Statistics</a>
            <a href="settings.php">Settings</a>
        </div>
        <div>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="main-content">
        <div class="balance-container glass-card">
            <h2>Your Balance</h2>
            <p class="balance <?php
                if ($balance > 500) {
                    echo 'income';
                } elseif ($balance > 200) {
                    echo 'orange';
                } else {
                    echo 'expenses';
                }
            ?>">
                $<?php echo number_format($balance, 2); ?>
            </p>
        </div>

        <div class="budget-container glass-card">
            <h2 >Monthly Budget</h2>
            <br><br>
            <div class="budget-summary">
                <div class="budget-item">
                    <h3 class="budget-color">Budget</h3>
                    <p >$<?php echo number_format($budget, 2); ?></p>
                </div>
                <div class="budget-item">
                    <h3 class="budget-color">Remaining</h3>
                    <p >
                        $<?php echo number_format($budget - $expenses, 2); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="summary-container glass-card">
            <div class="summary-item">
                <h3 class="income">Total Income</h3>
                <p >$<?php echo number_format($income, 2); ?></p>
            </div>
            <div class="summary-item">
                <h3 class="expenses">Total Expenses</h3>
                <p >$<?php echo number_format($expenses, 2); ?></p>
            </div>
        </div>

        <div class="transactions-container glass-card">
            <h2 >Recent Transactions</h2>
            <table>
                <tr class="transactions-color">
                    <th>Type</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo ucfirst($transaction['type']); ?></td>
                        <td><?php echo $transaction['category']; ?></td>
                        <td>$<?php echo number_format($transaction['amount'], 2); ?></td>
                        <td><?php echo $transaction['date']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>