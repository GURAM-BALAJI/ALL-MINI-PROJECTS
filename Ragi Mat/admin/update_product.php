<?php
require_once '../db.php';


// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if form fields are set before accessing them
    $id = isset($_POST["id"]) ? $_POST["id"] : "";
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    $price = isset($_POST["price"]) ? $_POST["price"] : "";
    $description = isset($_POST["description"]) ? $_POST["description"] : "";

    // Prepare and execute the update query
    $stmt = $conn->prepare("UPDATE products SET name = :name, price = :price, description = :description WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':description', $description);
    $stmt->execute();

    echo "Product updated successfully";
} else {
    // If the request method is not POST, return an error
    http_response_code(405); // Method Not Allowed
    echo "Method not allowed";
}
?>
