<?php
session_start(); // Start the session

// Check if the user is logged in and is a seller
if (!isset($_SESSION['user_seller_id']) || $_SESSION['user_type'] != 'seller') {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Include the database connection file
include 'db_connect.php';

$user_id = $_SESSION['user_seller_id'];

$stmt = $conn->prepare("SELECT id, title, description, price, location, area, bedrooms, bathrooms, nearby FROM properties WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$properties = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Properties</title>
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f2f2f2;
        }

        .actions {
            white-space: nowrap;
        }

        .actions a {
            display: inline-block;
            margin-right: 5px;
            padding: 5px 10px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 3px;
        }

        .actions a:hover {
            background-color: #0056b3;
        }

        .logout {
            float: right;
            background: gray;
            padding: 5px 10px;
            color: #fff;
            border-radius: 3px;
        }
        .add_new{
            background: green;
            padding: 5px 10px;
            color: #fff;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>My Properties</h2>
        <a href="post_property.php" class="actions add_new">Post New + </a>
        <a class="logout" href="logout.php">LOGOUT</a>
        <br>
        <br>

        <?php if (isset($_SESSION['message'])): ?>
            <p style="color:red;font-weight: bold;"><?php echo $_SESSION['message'];
            unset($_SESSION['message']); ?></p>
        <?php endif; ?>
        <br>
        <br>
        <table>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Price</th>
                <th>Location</th>
                <th>Area</th>
                <th>Bedrooms</th>
                <th>Bathrooms</th>
                <th>Nearby</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($properties as $property): ?>
                <tr>
                    <td><?php echo htmlspecialchars($property['title']); ?></td>
                    <td><?php echo htmlspecialchars($property['description']); ?></td>
                    <td><?php echo htmlspecialchars($property['price']); ?></td>
                    <td><?php echo htmlspecialchars($property['location']); ?></td>
                    <td><?php echo htmlspecialchars($property['area']); ?></td>
                    <td><?php echo htmlspecialchars($property['bedrooms']); ?></td>
                    <td><?php echo htmlspecialchars($property['bathrooms']); ?></td>
                    <td><?php echo htmlspecialchars($property['nearby']); ?></td>
                    <td class="actions">
                        <a href="edit_property.php?id=<?php echo $property['id']; ?>">Edit</a>
                        <a href="delete_property.php?id=<?php echo $property['id']; ?>"
                            onclick="return confirm('Are you sure you want to delete this property?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>

</html>