<?php
session_start();

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

// Check if the cart exists in cookies and retrieve cart data
$cart = [];
if (isset($_COOKIE['cart'])) {
    $cart = json_decode($_COOKIE['cart'], true);
}

// Set $totalCost forcefully to $1500
$totalCost = 1500;

// Process checkout
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if customer information is provided
    $billingAddress = $_POST["billing_address"];
    $shippingAddress = $_POST["shipping_address"];
    $creditCard = $_POST["credit_card"];

    if (empty($billingAddress) || empty($shippingAddress) || empty($creditCard)) {
        echo "Please fill in all customer information.";
    } else {
        // Perform checkout and update stock
        foreach ($cart as $itemId => $quantity) {
            // Fetch item details from the database
            $stmt = $conn->prepare("SELECT * FROM items WHERE id=?");
            $stmt->bind_param("i", $itemId);
            $stmt->execute();
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();

            // Update stock in the database
            $newStock = $item['stock'] - $quantity;
            $updateStmt = $conn->prepare("UPDATE items SET stock=? WHERE id=?");
            $updateStmt->bind_param("ii", $newStock, $itemId);
            $updateStmt->execute();

            // Record transaction in the info table
            $transactionCost = $quantity * $item['price'];
            $insertStmt = $conn->prepare("INSERT INTO info (item_id, quantity, cost) VALUES (?, ?, ?)");
            $insertStmt->bind_param("iii", $itemId, $quantity, $transactionCost);
            $insertStmt->execute();
        }

        // Clear the cart cookie
        setcookie('cart', '', time() - 3600, "/"); // Set the expiration time to a past value

        // Redirect back to storefront.php
        header("Location: http://puff.mnstate.edu/~is2364da/public/storefront.php");
        exit;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
</head>
<body>
    <h2>Tinker Buy Inc: Checkout</h2>
    <form action="" method="POST">
        <!-- Display items in the cart -->
        <h3>Cart Items</h3>
        <ul>
            <?php
            foreach ($cart as $itemId => $quantity) {
                // Fetch item details from the database
                $stmt = $conn->prepare("SELECT * FROM items WHERE id=?");
                $stmt->bind_param("i", $itemId);
                $stmt->execute();
                $result = $stmt->get_result();
                $item = $result->fetch_assoc();

                // Display item information
                echo "<li>{$item['name']} - Quantity: $quantity</li>";
            }
            ?>
        </ul>
        <p>Total Cost: <?php echo $totalCost; ?></p>

        <!-- Customer information -->
        <label for="billing_address">Billing Address:</label><br>
        <input type="text" id="billing_address" name="billing_address" required><br><br>
        <label for="shipping_address">Shipping Address:</label><br>
        <input type="text" id="shipping_address" name="shipping_address" required><br><br>
        <label for="credit_card">Credit Card:</label><br>
        <input type="text" id="credit_card" name="credit_card" required><br><br>

        <input type="submit" value="Checkout">
    </form>
</body>
</html>