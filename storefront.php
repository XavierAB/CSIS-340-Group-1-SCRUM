<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

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

// Handle search query
$search = isset($_POST["search"]) ? $_POST["search"] : '';

// Prepare and execute SQL statement to fetch items based on search criteria
$stmt = $conn->prepare("SELECT * FROM items WHERE name LIKE ?");
$search_param = "%" . $search . "%";
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();

// Redirect to checkout.php if Save Cart & Checkout button is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_checkout'])) {
    header("Location: http://puff.mnstate.edu/~is2364da/public/checkout.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storefront</title>
</head>
<body>
    <h2>Storefront</h2>

    <!-- Search Bar -->
    <form action="" method="POST">
        <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Search items">
        <button type="submit">Search</button>
    </form>

    <!-- Display items -->
    <?php
    if ($result->num_rows > 0) {
        echo "<form action='' method='POST'>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>";
            echo "<input type='checkbox' name='item[]' value='" . $row['id'] . "'>";
            echo "<span>" . $row['name'] . "</span>";
            echo "<span>Stock: " . $row['stock'] . "</span>";
            echo "<span>Price: $" . $row['price'] . "</span>";
            echo "<input type='number' name='quantity[]' value='0' min='0'>";
            echo "</li>";
        }
        echo "</ul>";
        echo "<input type='hidden' name='cart' value='" . htmlspecialchars(json_encode($_SESSION['cart'])) . "'>";
        echo "<button type='submit' name='save_checkout'>Save Cart & Checkout</button>";
        echo "</form>";

        // Add a separate form for "Modify Account" button
        echo "<form action='modify.php' method='POST'>";
        echo "<button type='submit'>Modify Account</button>";
        echo "</form>";
    } else {
        echo "<p>No items found.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
</body>
</html>