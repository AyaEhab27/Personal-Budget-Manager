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

$sql_income = "SELECT SUM(amount) AS total_income FROM transactions WHERE user_id = ? AND type = 'income'";
$stmt_income = $conn->prepare($sql_income);
$stmt_income->bind_param("i", $user_id);
$stmt_income->execute();
$result_income = $stmt_income->get_result();
$total_income = $result_income->fetch_assoc()['total_income'] ?? 0;

$sql_expenses = "SELECT SUM(amount) AS total_expenses FROM transactions WHERE user_id = ? AND type = 'expense'";
$stmt_expenses = $conn->prepare($sql_expenses);
$stmt_expenses->bind_param("i", $user_id);
$stmt_expenses->execute();
$result_expenses = $stmt_expenses->get_result();
$total_expenses = $result_expenses->fetch_assoc()['total_expenses'] ?? 0;

$sql_categories = "SELECT category, SUM(amount) AS total FROM transactions WHERE user_id = ? AND type = 'expense' GROUP BY category";
$stmt_categories = $conn->prepare($sql_categories);
$stmt_categories->bind_param("i", $user_id);
$stmt_categories->execute();
$result_categories = $stmt_categories->get_result();
$categories = $result_categories->fetch_all(MYSQLI_ASSOC);

$stmt_income->close();
$stmt_expenses->close();
$stmt_categories->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .chart-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            max-width: 800px; 
            max-height: 500px; 
            margin: auto;
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            text-align: center; 
        }
        .chart-container h2 {
            margin-bottom: 15px;
        }
        
       
    </style>
</head>
<body>
    <div class="sidebar">
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="add_transaction.php">Add Transaction</a>
            <a href="#" class="active">Statistics</a>
            <a href="settings.php">Settings</a>
        </div>
        <div>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="chart-container">
            <h2>Income vs Expenses</h2>
            <canvas id="incomeExpensesChart" width="300" height="300"></canvas>
        </div>
        <div><br></div>
        <div class="chart-container">
            <h2>Expenses by Category</h2>
            <canvas id="expensesByCategoryChart" width="300" height="300"></canvas>
        </div>
    </div>

    <script>
        const incomeExpensesCtx = document.getElementById('incomeExpensesChart').getContext('2d');
        new Chart(incomeExpensesCtx, {
            type: 'bar',
            data: {
                labels: ['Income', 'Expenses'],
                datasets: [{
                    label: 'Amount',
                    data: [<?php echo $total_income; ?>, <?php echo $total_expenses; ?>],
                    backgroundColor: ['#00ff00', '#ff0000'],
                    borderColor: ['#00ff00', '#ff0000'],
                    borderWidth: 1
                }]
            },
            options: {
            responsive: true,
            maintainAspectRatio: false, 
            plugins: {
                legend: { display: false }
            }
}
        });

        const expensesByCategoryCtx = document.getElementById('expensesByCategoryChart').getContext('2d');
        new Chart(expensesByCategoryCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($categories, 'category')); ?>,
                datasets: [{
                    label: 'Amount',
                    data: <?php echo json_encode(array_column($categories, 'total')); ?>,
                    backgroundColor: ['#00ff00', '#ffa500', '#ff0000', '#00ccff'],
                    borderColor: ['#00ff00', '#ffa500', '#ff0000', '#00ccff'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                }
            }
        });
    </script>
</body>
</html>