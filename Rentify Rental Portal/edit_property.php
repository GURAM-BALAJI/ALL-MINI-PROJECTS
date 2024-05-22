<?php
session_start(); // Start the session

// Check if the user is logged in and is a seller
if (!isset($_SESSION['user_seller_id']) || $_SESSION['user_type'] != 'seller') {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Include the database connection file
include 'db_connect.php';

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $property_id = sanitize_input($_POST["property_id"]);
    $title = sanitize_input($_POST["title"]);
    $description = sanitize_input($_POST["description"]);
    $price = sanitize_input($_POST["price"]);
    $location = sanitize_input($_POST["location"]);
    $area = sanitize_input($_POST["area"]);
    $bedrooms = sanitize_input($_POST["bedrooms"]);
    $bathrooms = sanitize_input($_POST["bathrooms"]);
    $nearby = sanitize_input($_POST["nearby"]);

    $stmt = $conn->prepare("UPDATE properties SET title = ?, description = ?, price = ?, location = ?, area = ?, bedrooms = ?, bathrooms = ?, nearby = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssdssissii", $title, $description, $price, $location, $area, $bedrooms, $bathrooms, $nearby, $property_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Property updated successfully.";
        header("Location: seller.php"); // Redirect to the seller's seller
        exit();
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    if (isset($_GET['id'])) {
        $property_id = sanitize_input($_GET['id']);

        $stmt = $conn->prepare("SELECT title, description, price, location, area, bedrooms, bathrooms, nearby FROM properties WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $property_id, $_SESSION['user_seller_id']);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($title, $description, $price, $location, $area, $bedrooms, $bathrooms, $nearby);
            $stmt->fetch();
        } else {
            $_SESSION['message'] = "Property not found or you do not have permission to edit this property.";
            header("Location: seller.php");
            exit();
        }

        $stmt->close();
    } else {
        $_SESSION['message'] = "Invalid request.";
        header("Location: seller.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Property</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Edit Property</h2>
        <form action="edit_property.php" method="POST">
            <input type="hidden" name="property_id" value="<?php echo htmlspecialchars($property_id); ?>">
            <input type="text" name="title" placeholder="Property Title" value="<?php echo htmlspecialchars($title); ?>" required>
            <textarea name="description" placeholder="Property Description" required><?php echo htmlspecialchars($description); ?></textarea>
            <input type="number" name="price" placeholder="Price" value="<?php echo htmlspecialchars($price); ?>" required>
            <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($location); ?>" required>
            <input type="text" name="area" placeholder="Area (sqft)" value="<?php echo htmlspecialchars($area); ?>" required>
            <input type="number" name="bedrooms" placeholder="Bedrooms" value="<?php echo htmlspecialchars($bedrooms); ?>" required>
            <input type="number" name="bathrooms" placeholder="Bathrooms" value="<?php echo htmlspecialchars($bathrooms); ?>" required>
            <textarea name="nearby" placeholder="Nearby"><?php echo htmlspecialchars($nearby); ?></textarea>
            <button type="submit">Update Property</button>
        </form>
        <p><?php if (isset($_SESSION['message'])) echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
    </div>
</body>
</html>
