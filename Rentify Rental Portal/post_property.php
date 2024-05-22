<?php
session_start(); // Start the session

// Check if the user is logged in and is a seller
if (!isset($_SESSION['user_seller_id']) || $_SESSION['user_type'] != 'seller') {
    header("Location: login_page.php"); // Redirect to login page if not logged in
    exit();
}

// Include the database connection file
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_seller_id'];
    $title = sanitize_input($_POST["title"]);
    $description = sanitize_input($_POST["description"]);
    $price = sanitize_input($_POST["price"]);
    $location = sanitize_input($_POST["location"]);
    $area = sanitize_input($_POST["area"]);
    $bedrooms = sanitize_input($_POST["bedrooms"]);
    $bathrooms = sanitize_input($_POST["bathrooms"]);
    $nearby = sanitize_input($_POST["nearby"]);

    $stmt = $conn->prepare("INSERT INTO properties (user_id, title, description, price, location, area, bedrooms, bathrooms, nearby) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdssiss", $user_id, $title, $description, $price, $location, $area, $bedrooms, $bathrooms, $nearby);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Property posted successfully.";
        header("Location: seller.php"); // Redirect to the seller's dashboard
        exit();
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Property</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Post Property</h2>
        <form action="post_property.php" method="POST">
            <input type="text" name="title" placeholder="Property Title" required>
            <textarea name="description" placeholder="Property Description" required></textarea>
            <input type="number" name="price" placeholder="Price" required>
            <input type="text" name="location" placeholder="Location" required>
            <input type="text" name="area" placeholder="Area (sqft)" required>
            <input type="number" name="bedrooms" placeholder="Bedrooms" required>
            <input type="number" name="bathrooms" placeholder="Bathrooms" required>
            <textarea name="nearby" placeholder="Nearby"></textarea>
            <button type="submit">Post Property</button>
        </form>
        <p><?php if (isset($_SESSION['message'])) echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
    </div>
</body>
</html>
