<?php
session_start(); // Start the session

// Check if the user is logged in and is a seller
if (!isset($_SESSION['user_seller_id']) || $_SESSION['user_type'] != 'seller') {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Include the database connection file
include 'db_connect.php';

if (isset($_GET['id'])) {
    $property_id = sanitize_input($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM properties WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $property_id, $_SESSION['user_seller_id']);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Property deleted successfully.";
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    $_SESSION['message'] = "Invalid request.";
}

header("Location: seller.php");
exit();

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>
