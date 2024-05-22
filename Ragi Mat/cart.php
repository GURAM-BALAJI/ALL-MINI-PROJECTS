<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
</head>
<style>
    body {
        font-family: sans-serif;
        margin: 0;
        padding: 0;
    }

    header {
        background-color: #f2f2f2;
        padding: 10px;
        display: flex;
        align-items: center;
        margin: 15px;
    }

    header h1 {

        font-size: 20px;
        margin-left: 20px;
    }

    header p {
        font-size: 15px;
        color: rgb(83, 83, 83);
        margin-left: 5px;
    }

    main {
        padding: 10px;
    }

    .cart-item {
        display: flex;
        border-bottom: 1px solid #ddd;
        margin: 7px;
        background-color: rgba(176, 176, 176, 0.24);
        padding: 10px;
        border-radius: 7px;
    }


    .item-image img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        margin-right: 15px;
        border-radius: 10px;
    }

    .item-details {
        flex: 1;
    }

    .item-sub-total h2 {
        margin: 10px;
        margin-top: 65%;
        color: rgba(5, 5, 5, 0.803);
        font-size: 18px;

    }

    .item-details h2 {
        font-size: 18px;
        margin-bottom: -15px;
    }

    .item-details p {
        margin-bottom: 10px;
        color: #696969;
    }

    .quantity {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quantity button {
        padding: 5px 10px;
        border: none;
        cursor: pointer;
        border-radius: 7px;
        background-color: #66666654;
    }

    .quantity button:hover {
        background-color: #f2f2f2;
    }

    .quantity span {
        margin: 0 10px;
    }

    footer {
        background-color: #f2f2f2;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    footer p {
        margin: 5px 0;
    }

    footer button {
        background-color: #388e3c;
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
    }

    footer button:hover {
        background-color: #2e7d32;
    }
</style>

<body>
    <header>
        <a href="index.php">
            <img src="images/close.png" />
        </a>
        <h1>My Cart </h1>
        <p> (4)</p>

    </header>
    <main>
        <?php
        include 'db.php';
        if (isset ($_COOKIE['uuid'])) {
            $user_id = $_COOKIE['uuid'];
            $cartItemsQuery = "SELECT * FROM cart WHERE user_id=:user_id";
            $stmt = $conn->prepare($cartItemsQuery);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute(); // Execute the prepared statement
            $cartItemsResult = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows as an associative array
        
            foreach ($cartItemsResult as $cartItem) {
                // Fetch product details for the current cart item
                $productId = $cartItem['product_id'];
                $productQuery = "SELECT * FROM products WHERE id = $productId";
                $productResult = $conn->query($productQuery);
                if ($productResult->rowCount() > 0) {
                    $product = $productResult->fetch(PDO::FETCH_ASSOC);
                    // Output cart item HTML
                    echo "<section class='cart-item'>";
                    echo "<div class='item-image'>";
                    echo "<img src='{$product['image']}' alt='{$product['name']}'>";
                    echo "</div>";
                    echo "<div class='item-details'>";
                    echo "<h2>{$product['name']}</h2>";
                    echo "<p>&#8377;{$product['price']}</p>";
                    echo "<div class='quantity'>";
                    echo "<button class='decrease' data-id='{$product['id']}'>-</button>";
                    echo "<span>{$cartItem['quantity']}</span>";
                    echo "<button class='increase' data-id='{$product['id']}'>+</button>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='item-sub-total'>";
                    $subtotal = $product['price'] * $cartItem['quantity'];
                    echo "<h2>&#8377;{$subtotal}</h2>";
                    echo "</div>";
                    echo "</section>";
                }
            }
        } else {
            // Redirect to index.html if the cookie is not set
            header("Location: index.php");
            exit; // Stop further execution
        }
        $conn = null;
        ?>
    </main>
    <script>

    </script>
</body>

</html>