<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <style>
        /* Common styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 15px auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .product {
            background-color: #cbcbcb;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: calc(25% - 20px);
            /* Adjust width as needed */
        }

        .product img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .product h2 {
            margin: 0;
        }

        .product p {
            margin-bottom: 4px;
            color: #666;
        }

        .add-to-cart {
            background-color: #35cf01;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            margin-top: 10px;
            font-size: 24px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .add-to-cart:hover {
            background-color: #66e75a;
        }

        /* Quantity Adjustment Buttons */
        .quantity-control {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .quantity-adjust {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            padding: 5px 10px;
            font-size: 20px;
        }

        .quantity-adjust:hover {
            background-color: #0056b3;
        }

        .quantity-input {
            font-size: 20px;
            width: 50px;
            margin: 0 10px;
            padding: 5px;
            border-radius: 4px;
            text-align: center;
            border: none;
            background-color: #cbcbcb;

        }

        /* Mobile view */
        @media screen and (max-width: 767px) {
            .product {
                width: 100%;
                margin-right: 0;
            }
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #e5e5e5;
            /* Adjust background color as needed */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .title-logo {
            display: flex;
            align-items: center;
        }

        .title-logo img {
            margin-left: 10px;
        }

        .cart {
            display: flex;
            align-items: center;
        }

        .cart img {
            width: 40px;
            /* Adjust size as needed */
            margin-right: -10px;

        }

        /* Styling for cart count */
        #cart-count {
            background-color: #007bff;
            color: #fff;
            font-size: 18px;
            padding: 5px 8px;
            border-radius: 50%;
            /* Make it circular */
            margin-right: 5px;
            margin-top: -15px;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="title-logo">

            <img src="images/logo.png" alt="Logo">

        </div>
        <div class="cart">
            <a href="cart.php">
                <img src="images/cart.png" alt="Cart">
                <span id="cart-count">0</span>
            </a>
        </div>
    </header>

    <div class="container">


        <?php
        // Include database connection
        include 'db.php';

        // Fetch products from the database
        $stmt = $conn->prepare("SELECT * FROM products");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Display each product
        foreach ($products as $product) {
            echo "<div class='product'>";
            echo "<img src='{$product['image']}' alt='{$product['name']}'>";
            echo "<h2>{$product['name']}</h2>";
            echo "<p>{$product['description']}</p>";
            echo "<p style='font-size:20px'>Price: <b>&#8377; {$product['price']}</b></p>";
            echo "<div class='quantity-controls'>
            <button class='quantity-adjust' data-adjust='decrease' data-product-id='{$product['id']}' onclick='adjustQuantity(this)'>-</button>
            <input type='number' class='quantity-input' id='quantity-input{$product['id']}' value='1' readonly>
            <button class='quantity-adjust' data-adjust='increase' data-product-id='{$product['id']}' onclick='adjustQuantity(this)'>+</button>            
    </div>";
            echo "<button class='add-to-cart' data-product-id='{$product['id']}' >Add to Cart</button>";
            echo "</div>";
        }
        ?>

    </div>

    <script>

        function updateCartCount(userId) {
            // Send AJAX request to fetch cart data
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetchCart.php?userId=' + userId, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // Parse the JSON response
                        var cartData = JSON.parse(xhr.responseText);
                        // Calculate total count of items in the cart
                        var totalCount = 0;
                        cartData.forEach(function (item) {
                            totalCount += item.quantity;
                        });
                        // Update the cart count in the UI
                        document.getElementById('cart-count').textContent = totalCount;
                    } else {
                        console.error('Error fetching cart data:', xhr.responseText);
                    }
                }
            };
            xhr.send();
        }

        function adjustQuantity(button) {
            // Get the product ID from the data-product-id attribute of the button
            var productId = button.getAttribute('data-product-id');

            // Construct the selector for the corresponding quantity input field
            var quantityInput = document.getElementById('quantity-input' + productId);

            // Get the current quantity value
            var currentQuantity = parseInt(quantityInput.value);

            // Adjust the quantity based on the button clicked
            var adjustment = button.getAttribute('data-adjust');
            var newQuantity = currentQuantity + (adjustment === 'increase' ? 1 : -1);

            // Update the quantity input field with the new value
            if (newQuantity >= 1) {
                quantityInput.value = newQuantity;
            }
        }


        // Add event listener to all Add to Cart buttons
        var addToCartButtons = document.querySelectorAll('.add-to-cart');
        addToCartButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var productId = button.getAttribute('data-product-id');
                var quantityInput = document.getElementById('quantity-input' + productId);
                var quantity = quantityInput ? parseInt(quantityInput.value) : 1;
                addToCart(productId, uuid, quantity);
            });
        });



        // Function to add a product to the cart
        function addToCart(productId, userId, quantity) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'addToCart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // Request was successful
                        updateCartCount(userId);
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            console.log('Product added to cart successfully');
                            // Here you can perform any actions needed after successful addition to cart
                        } else {
                            console.error('Error adding product to cart:', response.message);
                            // Handle error scenario
                        }
                    } else {
                        // Request failed (status code is not 200)
                        console.error('Error adding product to cart. Server returned status code:', xhr.status);
                        // Handle error scenario
                    }
                }
            };
            var data = JSON.stringify({ productId: productId, userId: userId, quantity: quantity });
            xhr.send(data);
        }


        // Function to generate a UUID
        function generateUUID() {
            var d = new Date().getTime();
            if (typeof performance !== 'undefined' && typeof performance.now === 'function') {
                d += performance.now(); // Use high-precision timer if available
            }
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                var r = (d + Math.random() * 16) % 16 | 0;
                d = Math.floor(d / 16);
                return (c == 'x' ? r : (r & 0x3 | 0x8)).toString(16);
            });
        }

        // Function to set a cookie
        function setCookie(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        // Check if the UUID cookie exists, if not, generate a new one and set it
        var uuid = getCookie('uuid');
        updateCartCount(uuid);

        if (!uuid) {
            uuid = generateUUID();
            setCookie('uuid', uuid, 365); // Set the cookie to last for 1 year
        }

        // Function to get a cookie by name
        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }
    </script>

</body>

</html>