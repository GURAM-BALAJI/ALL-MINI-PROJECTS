<?php
// add_product.php

// Include db.php to establish database connection
require_once '../db.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if form fields are set before accessing them
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    $price = isset($_POST["price"]) ? $_POST["price"] : "";
    $description = isset($_POST["description"]) ? $_POST["description"] : "";

    // Check if image file is uploaded
    if(isset($_FILES["image"])) {
        $image = $_FILES["image"];
        $image_name = $image["name"];
        $image_tmp_name = $image["tmp_name"];
        $image_size = $image["size"];
        $image_error = $image["error"];

        // Check if file is uploaded without errors
        if($image_error === UPLOAD_ERR_OK) {
            // Move uploaded file to desired location
            $image_destination = "uploads/" . $image_name; // Adjust the destination folder as per your requirements
            move_uploaded_file($image_tmp_name, $image_destination);

            // Perform database insertion with image file path
            try {
                // Prepare SQL statement for insertion
                $stmt = $conn->prepare("INSERT INTO products (name, price, description, image) VALUES (:name, :price, :description, :image)");
                // Bind parameters
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':image', $image_destination); // Store image file path in database

                // Execute the prepared statement
                $stmt->execute();

                // Return success message
                echo "Product added successfully";
            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        } else {
            echo "File upload failed";
        }
    } else {
        echo "Image file is required";
    }

    // Close the database connection
    $conn = null;
} else {
    // If the request method is not POST, return an error
    http_response_code(405); // Method Not Allowed
    echo "Method not allowed";
}
?>
