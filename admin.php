<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: http://puff.mnstate.edu/~is2364da/public/login.html");
    exit;
}

// Database connection setup
$servername = "puff.mnstate.edu";
$dbusername = "SQLUsername";
$dbpassword = "SQLPassword";
$dbname = "alexander-botz_TinkerBuyInc";

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];

    // Prepare SQL statement to delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
}

// Export sales report as CSV
if (isset($_POST['export_report'])) {
    $filename = "sales_report.csv";

    // Fetch data from the info table
    $query = "SELECT * FROM info";
    $result = $conn->query($query);

    // Generate CSV content
    $csv_content = "Item ID,Quantity,Cost\n";
    while ($row = $result->fetch_assoc()) {
        $csv_content .= "{$row['item_id']},{$row['quantity']},{$row['cost']}\n";
    }

    // Output CSV file
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $csv_content;
    exit;
}

// Fetch all users from the users table
$query = "SELECT * FROM users";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body>
    <h2>Admin Panel</h2>

    <!-- Display all users -->
    <h3>All Users</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['firstname']; ?></td>
                <td><?php echo $row['lastname']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td>
                    <form action="" method="POST">
                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_user">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

    <!-- Print Sales Report button -->
    <form action="" method="POST">
        <button type="submit" name="export_report">Print Sales Report</button>
    </form>

</body>
</html>

<?php
$conn->close();
?>