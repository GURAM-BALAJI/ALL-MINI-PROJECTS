<?php
session_start(); // Start the session

// Include the database connection file
include 'db_connect.php';

// Function to sanitize user input
function sanitize_input($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize_input($_POST["email"]);
    $password = sanitize_input($_POST["password"]);

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, password,user_type FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password, $user_type);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            if ($user_type == 'buyer') {
                $_SESSION['user_buyer_id'] = $user_id;
                $_SESSION['user_type'] = "buyer";
                header("Location: index.php"); // Redirect to a welcome page
                exit();
            } else {
                $_SESSION['user_seller_id'] = $user_id;
                $_SESSION['user_type'] = "seller";
                header("Location: seller.php"); // Redirect to a welcome page
                exit();
            }
        } else {
            // Password is incorrect
            $_SESSION['login_message'] = "Invalid password.";
            header("Location: login.php"); // Redirect back to login page
            exit();
        }
    } else {
        // Email not found
        $_SESSION['login_message'] = "Email not found.";
        header("Location: login.php"); // Redirect back to login page
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>