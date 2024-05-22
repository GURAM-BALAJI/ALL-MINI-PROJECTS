<?php
// Include database connection
include 'db_connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Listing</title>
    <!-- Add your CSS styling here -->
    <style>
        /* Example CSS styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        .filter-form {
            margin-bottom: 20px;
        }

        .filter-form label {
            display: block;
            margin-bottom: 5px;
        }

        .filter-form input[type="number"],
        .filter-form input[type="text"] {
            width: calc(50% - 10px);
            margin-right: 10px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }

        .filter-form button {
            padding: 8px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .property {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 5px;
        }

        .property h2 {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .property p {
            margin-bottom: 5px;
        }

        .property a {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
        }

        .logout {
            float: right;
            background: gray;
            padding: 5px 10px;
            color: #fff;
            border-radius: 3px;
            text-decoration: none;
        }

        .like-button {
            display: inline-block;
            background-color: #28a745;
            color: #fff;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Available Rental Properties</h1>
        <!-- Filter options -->
        <a class="logout"
            href="<?php echo isset($_SESSION['user_buyer_id']) ? 'logout.php' : 'login.php'; ?>"><?php echo isset($_SESSION['user_buyer_id']) ? 'LOGOUT' : 'Login'; ?></a>
        <form action="" method="GET" class="filter-form">
            <table style="width: 100%;">
                <tr>
                    <td><label for="price">Min Price:</label>
                        <input style="width: 100%;" type="number" name="min_price" placeholder="Min Price">
                        <label for="price">Max Price:</label>
                        <input style="width: 100%;" type="number" name="max_price" placeholder="Max Price">
                        <br>
                    </td>
                    <td>
                        <label for="bedrooms">Number of Bedrooms:</label>
                        <input style="width: 100%;" type="number" name="bedrooms" placeholder="Number of Bedrooms">
                        <br>
                        <label for="location">Location:</label>
                        <input style="width: 100%;" type="text" name="location" placeholder="Location">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <!-- Add more filtering options as needed -->
                        <button style="width: 100%;" type="submit">Apply Filters</button>
                    </td>
                </tr>

            </table>
        </form>

        <!-- Display all posted rental properties -->
        <?php
        // Build SQL query based on filter criteria
        $sql = "SELECT * FROM properties WHERE 1=1"; // 1=1 to append AND conditions easily
        
        // Handle price filter
        if (isset($_GET['min_price']) && isset($_GET['max_price']) && !empty($_GET['min_price']) && !empty($_GET['max_price'])) {
            $min_price = $_GET['min_price'];
            $max_price = $_GET['max_price'];
            $sql .= " AND price BETWEEN $min_price AND $max_price";
        }

        // Handle number of bedrooms filter
        if (isset($_GET['bedrooms']) && !empty($_GET['bedrooms'])) {
            $bedrooms = $_GET['bedrooms'];
            $sql .= " AND bedrooms = $bedrooms";
        }

        // Handle location filter
        if (isset($_GET['location']) && !empty($_GET['location'])) {
            $location = $_GET['location'];
            $sql .= " AND location LIKE '%$location%'";
        }

        $result = mysqli_query($conn, $sql);

        if ($result) {
            while ($property = mysqli_fetch_assoc($result)):
                ?>
                <div class="property">
                    <h2><?php echo htmlspecialchars($property['title']); ?></h2>
                    <p>Description: <?php echo htmlspecialchars($property['description']); ?></p>
                    <p>Price: <?php echo htmlspecialchars($property['price']); ?></p>
                    <p>Location: <?php echo htmlspecialchars($property['location']); ?></p>
                    <!-- "I am Interested" button -->
                    <a
                        href="<?php echo isset($_SESSION['user_buyer_id']) ? 'seller_details.php?id=' . $property['id'] : 'login.php'; ?>">I
                        am Interested</a>
                    <!-- Like button -->
                    <button class="like-button" data-property-id="<?php echo $property['id']; ?>">Like</button>
                    <span class="like-count">Likes: <?php echo htmlspecialchars($property['likes']); ?></span>
                </div>
                <?php
            endwhile;
        } else {
            echo "<p>No properties found matching the criteria.</p>";
        }

        // Close database connection
        mysqli_close($conn);
        ?>
    </div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Get all like buttons
        var likeButtons = document.querySelectorAll('.like-button');

        // Attach click event listener to each like button
        likeButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var propertyId = this.getAttribute('data-property-id');
                var likeCountElement = this.nextElementSibling; // Next sibling element is the like count span

                // Send AJAX request
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'like_property.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 400) {
                        // Update like count on success
                        var response = xhr.responseText;
                        if (response.startsWith('Success:')) {
                            var likeCount = parseInt(response.split(':')[1]);
                            likeCountElement.textContent = 'Likes: ' + likeCount;
                        } else {
                            console.error(response);
                        }
                    } else {
                        console.error('Request failed with status:', xhr.status);
                    }
                };
                xhr.onerror = function () {
                    console.error('Request failed');
                };
                xhr.send('property_id=' + propertyId);
            });
        });
    });
</script>

<script type="module">
    // Import the functions you need from the SDKs you need
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
    import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js";

    const firebaseConfig = {
        apiKey: "AIzaSyCtWN8zSSw_qMW4Udko2Am1BY03j7G1Jes",
        authDomain: "test-e0c2a.firebaseapp.com",
        projectId: "test-e0c2a",
        storageBucket: "test-e0c2a.appspot.com",
        messagingSenderId: "836381633860",
        appId: "1:836381633860:web:2f62cfe56904115a11bb11",
        measurementId: "G-7CD93QMLB3"
    };

    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    navigator.serviceWorker.register("sw.js").then(registration => {
        getToken(messaging, {
            serviceWorkerRegistration: registration,
            vapidKey: 'BGTGebtKndUxFrqcn9LkCYQY7SqkETj4I2_01T_A7xXZYy9ILWupW40od9FcVR1DGcqscZAt8--LQX9aLx_fPg4'
        }).then((currentToken) => {
            if (currentToken) {
                console.log("Token is: " + currentToken);
            } else {
                console.log('No registration token available. Request permission to generate one.');
            }
        }).catch((err) => {
            console.log('An error occurred while retrieving token. ', err);
        });
    });



</script>

</html>