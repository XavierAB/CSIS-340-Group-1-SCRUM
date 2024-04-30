<?php
session_start();

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION["username"])) {
    header("Location: login.html");
    exit;
}

// Database connection setup
$servername = "puff.mnstate.edu";
$dbusername = "alexander-botz";
$dbpassword = "Pegman101";
$dbname = "alexander-botz_TinkerBuyInc";

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch current account information
$username = $_SESSION["username"];
$stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if username and password are set in the POST data
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        $newUsername = $_POST["username"];
        $newPassword = $_POST["password"];

        // Check if the new username already exists in the database (excluding the current user's username)
        $checkStmt = $conn->prepare("SELECT * FROM users WHERE username=? AND username<>?");
        $checkStmt->bind_param("ss", $newUsername, $username);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            echo "Username already exists. Please choose a different one.";
        } else {
            // Update user's account details
            $updateStmt = $conn->prepare("UPDATE users SET username=?, password=? WHERE username=?");
            $updateStmt->bind_param("sss", $newUsername, $newPassword, $username);
            if ($updateStmt->execute()) {
                // Update session username if the username was changed
                $_SESSION["username"] = $newUsername;
                header("Location: http://puff.mnstate.edu/~is2364da/public/login.html");
                exit;
            } else {
                echo "Error updating account details: " . $conn->error;
            }
        }

        $checkStmt->close();
        $updateStmt->close();
    } else {
        echo "Username or password not provided.";
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Account</title>
</head>
<body>
    <h2>Modify Account</h2>
    <form action="" method="POST">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" value="<?php echo isset($row['username']) ? htmlspecialchars($row['username']) : ''; ?>" required><br><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" value="<?php echo isset($row['password']) ? htmlspecialchars($row['password']) : ''; ?>" required><br><br>
        <input type="submit" value="Save Changes">
    </form>
</body>
</html>