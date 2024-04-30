<?php

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

// Handle search query
$search = isset($_POST["search"]) ? $_POST["search"] : '';

// Prepare and execute SQL statement to fetch items based on search criteria
$stmt = $conn->prepare("SELECT * FROM items WHERE name LIKE ?");
$search_param = "%" . $search . "%";
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();

// Initialize cart array
$cart = [];

// Check if cart cookie exists and retrieve cart data
if (isset($_COOKIE['cart'])) {
    $cart = json_decode($_COOKIE['cart'], true);
} else {
    $cart = [];
}

// Check if the cookie is being set
setcookie('cart', json_encode($cart), time() + (86400 * 30), "/");
echo "Cookie set: " . json_encode($cart) . "<br>";

// Check if the cookie is being retrieved
if (isset($_COOKIE['cart'])) {
    $cart = json_decode($_COOKIE['cart'], true);
    echo "Cookie retrieved: " . $_COOKIE['cart'] . "<br>";
} else {
    $cart = [];
    echo "Cookie not retrieved. <br>";
}


// Handle adding or removing items from the cart
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the form is submitted for adding/removing items from the cart
    if (isset($_POST['item']) && is_array($_POST['item'])) {
        foreach ($_POST['item'] as $item_id) {
            if (isset($_POST['quantity'][$item_id])) {
                $quantity = intval($_POST['quantity'][$item_id]);
                if ($quantity > 0) {
                    // Add or update item in the cart
                    $cart[$item_id] = $quantity;
                } else {
                    // Remove item from the cart if quantity is 0 or less
                    unset($cart[$item_id]);
                }
            }
        }
        // Update the cart cookie
        setcookie('cart', json_encode($cart), time() + (86400 * 30), "/");
    }
}

// Print out the contents of the cart cookie
echo "<pre>";
print_r($cart);
echo "</pre>";
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
        echo "<form action='checkout.php' method='POST'>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>";
            echo "<input type='checkbox' name='item[]' value='" . $row['id'] . "' " . (isset($cart[$row['id']]) ? 'checked' : '') . ">";
            echo "<span>" . $row['name'] . "</span>";
            echo "<span>Stock: " . $row['stock'] . "</span>";
            echo "<span>Price: $" . $row['price'] . "</span>";
            echo "<input type='number' name='quantity[" . $row['id'] . "]' value='" . (isset($cart[$row['id']]) ? $cart[$row['id']] : '0') . "' min='0'>";
            echo "</li>";
        }
        echo "</ul>";
        // Hidden input for cart
        echo "<input type='hidden' name='cart' value='" . htmlspecialchars(json_encode($cart)) . "'>";
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