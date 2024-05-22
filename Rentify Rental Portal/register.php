<?php
include 'db_connect.php';
session_start();
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = sanitize_input($_POST["full_name"]);
    $email = sanitize_input($_POST["email"]);
    $phone_number = sanitize_input($_POST["phone_number"]);
    $user_type = sanitize_input($_POST["user_type"]);
    $password = sanitize_input($_POST["password"]);
    $confirm_password = sanitize_input($_POST["confirm_password"]);

    $stmt1 = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt1->bind_param("s", $email);
    $stmt1->execute();
    $stmt1->store_result();

    if ($stmt1->num_rows > 0) {
        $_SESSION['error'] = "Email already registered.";
        $stmt1->close();
        $conn->close();
        header("Location: login.php"); // Redirect to sign-up page
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: login.php"); // Redirect to sign-up page
        exit();
    }
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone_number, user_type, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $full_name, $email, $phone_number, $user_type, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Account Created successfully.";
        header("Location: login.php"); // Redirect to a success page
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        header("Location: login.php"); // Redirect to sign-up page
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
