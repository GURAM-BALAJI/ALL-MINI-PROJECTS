<?php
require_once '../db.php';


// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if form fields are set before accessing them
    $productId = isset($_POST["product_id"]) ? $_POST["product_id"] : "";
    // Check if file is uploaded
    if (isset($_FILES['image'])) {
        $file = $_FILES['image'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileError = $file['error'];
        // Check for upload errors
        if ($fileError === 0) {
            $fileDestination = 'uploads/' . $fileName;
            // Move the uploaded file to the destination directory
            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                // Update the product image path in the database
                $stmt = $conn->prepare("UPDATE products SET image = ? WHERE id = ?");
                $stmt->execute([$fileDestination, $productId]);
                echo "Image updated successfully";
            } else {
                echo "Error uploading image";
            }
        } else {
            echo "Error: " . $fileError;
        }
    } else {
        echo "No image uploaded";
    }
} else {
    // If the request method is not POST, return an error
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("error" => "Method not allowed"));
}
?>
