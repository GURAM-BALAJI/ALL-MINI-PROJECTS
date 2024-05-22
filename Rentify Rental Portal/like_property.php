<?php
include 'db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_buyer_id'])) {
    // User is not logged in, return error message
    echo 'Error: User is not logged in';
    exit();
}

// Check if property_id is provided
if (!isset($_POST['property_id'])) {
    // property_id is not provided, return error message
    echo 'Error: Property ID is missing';
    exit();
}

// Sanitize the property_id
$propertyId = mysqli_real_escape_string($conn, $_POST['property_id']);

// Update the like count in the database
$sql = "UPDATE properties SET likes = likes + 1 WHERE id = '$propertyId'";
if (mysqli_query($conn, $sql)) {
    // Fetch updated like count
    $result = mysqli_query($conn, "SELECT likes FROM properties WHERE id = '$propertyId'");
    $property = mysqli_fetch_assoc($result);
    $likeCount = $property['likes'];
    echo 'Success:' . $likeCount;
} else {
    echo 'Error: Unable to update like count';
}

// Close database connection
mysqli_close($conn);
?>
