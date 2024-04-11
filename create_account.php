<?php
session_start();

// Replace these database credentials with your own
$servername = "localhost";
$dbusername = "your_dbusername";
$dbpassword = "your_dbpassword";
$dbname = "your_database";

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];

    // Check if the username already exists
    $check_username_query = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($check_username_query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Username already exists. Please choose a different one.";
    } else {
        // Prepare SQL statement to insert new user data
        $insert_user_query = "INSERT INTO users (firstname, lastname, username, password, email) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_user_query);
        $stmt->bind_param("sssss", $firstname, $lastname, $username, $password, $email);

        if ($stmt->execute() === TRUE) {
            // User created successfully
            header("Location: login.html"); // Redirect to login page after successful account creation
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
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
    <title>Tinker Buy Inc: Create Account</title>
</head>
<body>
    <h2>Create Account</h2>
    <form action="" method="POST">
        <label for="firstname">First Name:</label><br>
        <input type="text" id="firstname" name="firstname" required><br><br>
        <label for="lastname">Last Name:</label><br>
        <input type="text" id="lastname" name="lastname" required><br><br>
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        <input type="submit" value="Create Account">
    </form>
</body>
</html>