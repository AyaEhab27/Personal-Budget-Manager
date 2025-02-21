<?php
session_start();
include 'db_connection.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $budget = $_POST['budget'];

    $sql = "UPDATE users SET username=?, email=?, budget=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $username, $email, $budget, $user_id);

    if ($stmt->execute()) {
        echo "Settings updated successfully!";
    } else {
        echo "Error updating settings.";
    }

    $stmt->close();
    $conn->close();
}
?>
