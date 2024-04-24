<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Replace these database credentials with your own
$servername = "puff.mnstate.edu";
$dbusername = "SQLUsername";
$dbpassword = "SQLPassword";
$dbname = "alexander-botz_TinkerBuyInc";

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
        // Set admin session variable
        $_SESSION["admin"] = true;
        // Redirect to admin.php for admin login
        header("Location: http://puff.mnstate.edu/~is2364da/public/admin.php");
        exit;
    }

    // Proceed with regular user login
    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
	
	// Check for errors
	if (!$stmt) {
		// Print the error message
		echo "Error: " . $conn->error;
		// You might also want to log the error for further investigation
		// Log the error to a file or database
		// Example: error_log("MySQL Error: " . $conn->error);
		// Redirect the user to an error page or display a user-friendly message
		exit; // Stop further execution of the script
	}

    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Valid username and password
        $_SESSION["username"] = $username;
        $_SESSION["password"] = $password; // Save password as well
        header("Location: http://puff.mnstate.edu/~is2364da/public/storefront.php"); // Redirect to storefront page on successful login
        exit;
    } else {
        // Invalid username or password
        echo "Invalid username or password!";
    }

    $stmt->close();
}

$conn->close();
?>