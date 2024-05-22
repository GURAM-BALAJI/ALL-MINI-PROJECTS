<?php
// addtocart.php


// Check if JSON data is received
$json_data = file_get_contents('php://input');
if ($json_data !== false) {
    // Decode JSON data into associative array
    $data = json_decode($json_data, true);

    // Check if product ID, quantity, and user ID are set in the decoded data
    if (isset ($data['productId']) && isset ($data['quantity']) && isset ($data['userId'])) {
        // Retrieve product ID, quantity, and user ID from the decoded data
        $product_id = $data['productId'];
        $quantity = $data['quantity'];
        $user_id = $data['userId'];

        // Include database connection
        include 'db.php';


        // Prepare and execute the SQL query to insert cart data
        $stmt = $conn->prepare("INSERT INTO cart (product_id, user_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$product_id, $user_id, $quantity]);

        // Check if insertion was successful
        if ($stmt->rowCount() > 0) {
            // Cart item inserted successfully
            $response = array("success" => true, "message" => "Product added to cart successfully");
            echo json_encode($response);
        } else {
            // Error inserting into cart
            $response = array("success" => false, "message" => "Error adding product to cart");
            echo json_encode($response);
        }
    } else {
        // If product ID, quantity, or user ID is not set, send an error response
        $response = array("success" => false, "message" => "Product ID, quantity, or user ID is missing in JSON data");
        echo json_encode($response);
    }
} else {
    // If no JSON data is received, send an error response
    $response = array("success" => false, "message" => "No JSON data received");
    echo json_encode($response);
}
?>