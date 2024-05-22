<?php
// delete_product.php

// Include db.php to establish database connection
require_once '../db.php';


// Check if product ID is provided in the request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["product_id"])) {
    $product_id = $_POST["product_id"];

    // Perform database operation to delete the product with the provided ID
    try {
        // Prepare SQL statement for deletion
        $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
        // Bind parameter
        $stmt->bindParam(':id', $product_id);
        // Execute the prepared statement
        $stmt->execute();
        echo "Product deleted successfully";
    } catch(PDOException $e) {
        echo $e->getMessage();
    }

    // Close the database connection
    $conn = null;
} else {
    // If the request method is not POST or product ID is not provided, return an error
    http_response_code(400); // Bad Request
    echo "Invalid request";
}
?>
