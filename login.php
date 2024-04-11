<?php
session_start();

// Replace these database credentials with your own
$servername = "localhost";
$dbusername = "your_dbusername";
$dbpassword = "your_dbpassword";
$dbname = "your_database";

// Static admin credentials
$adminUsername = "admin";
$adminPassword = "admin123";

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if the login attempt is from admin
    if ($username === $adminUsername && $password === $adminPassword) {
        // Redirect to admin.php for admin login
        header("Location: admin.php");
        exit;
    }

    // Proceed with regular user login
    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Valid username and password
        $_SESSION["username"] = $username;
        $_SESSION["password"] = $password; // Save password as well
        header("Location: storefront.php"); // Redirect to storefront page on successful login
        exit;
    } else {
        echo "Invalid username or password!";
    }

    $stmt->close();
}

$conn->close();
?>