<?php
session_start(); // Start the session
use PHPMailer\PHPMailer\PHPMailer;
// Include database connection
include 'db_connect.php';

// Include PHPMailer autoload file
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

// Check if a property ID is provided in the URL
if (isset($_GET['id'])) {
    $property_id = $_GET['id'];

    // Fetch the property details from the database based on the provided ID
    $stmt = $conn->prepare("SELECT * FROM properties WHERE id = ?");
    $stmt->bind_param("i", $property_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $property = $result->fetch_assoc();
        $buyer_id = $property['user_id']; // Assuming buyer_id is stored in properties table
        $stmt->close();
        
        // Fetch the buyer details from the database based on the buyer_id
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $buyer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $buyer = $result->fetch_assoc();
            $buyer_phone = $buyer['phone_number'];
            // Display the buyer's phone number and other details
            $buyer_name = $buyer['full_name'];
            $buyer_email = $buyer['email'];
        } else {
            $buyer_name = "Buyer details not found.";
            $buyer_phone = "";
            $buyer_email = "";
        }
        $stmt->close();
    } else {
        echo "<p>Property not found.</p>";
        exit(); // Exit if property not found
    }
} else {
    // If no property ID is provided, redirect back to the property listing page
    header("Location: property_listing.php");
    exit();
}

// Check if a property ID is provided in the URL
if (isset($_GET['id'])) {
    $property_id = $_GET['id'];

    // Fetch the property details from the database based on the provided ID
    $stmt = $conn->prepare("SELECT * FROM properties left join users on users.id=properties.user_id WHERE properties.id = ?");
    $stmt->bind_param("i", $property_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $property = $result->fetch_assoc();
        $seller_id = $property['user_id']; // Assuming seller_id is stored in properties table
        $stmt->close();
        
        // Fetch the seller details from the database based on the seller_id
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $seller_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $seller = $result->fetch_assoc();
            $seller_email = $seller['email'];
            // Fetch buyer details (assuming the buyer is logged in)
            $buyer_id = $_SESSION['user_buyer_id'];
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->bind_param("i", $buyer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $buyer = $result->fetch_assoc();
                $buyer_email = $buyer['email'];
                
                // Send emails to seller and buyer
                sendEmailToSeller($seller_email, $property);
                sendEmailToBuyer($buyer_email, $seller, $property);
                
            echo "<center><h1 style='color:green;'>Emails sent successfully!</h1></center>";
            } else {
                echo "Buyer details not found.";
            }
        } else {
            echo "Seller details not found.";
        }
        $stmt->close();
    } else {
        echo "Property not found.";
    }
} 

// Function to send email to seller
function sendEmailToSeller($seller_email, $property) {
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'gurambalaji2000@gmail.com';               // SMTP username
        $mail->Password   = 'XXXXXXXXXXXXXXXXXXXXXXX';                  // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;          // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->Port       = 587;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('gurambalaji2000@gmail.com', 'Rentify');
        $mail->addAddress($seller_email);                           // Add a recipient

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = 'Interested Buyer for Your Property';
        $mail->Body    = 'A buyer is interested in your property. Here are the details:<br>'
                        . 'Title: ' . $property['title'] . '<br>'
                        . 'Description: ' . $property['description'] . '<br>'
                        . 'Price: ' . $property['price'] . '<br>'
                        . 'Location: ' . $property['location'] . '<br>'
                        . 'Contact the buyer at: ' . $property['email'];

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Function to send email to buyer
function sendEmailToBuyer($buyer_email, $seller, $property) {
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'gurambalaji2000@gmail.com';               // SMTP username
        $mail->Password   = 'XXXXXXXXXXXXXXXXXXXXXXX';                  // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;          // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->Port       = 587;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('gurambalaji2000@gmail.com', 'Rentify');
        $mail->addAddress($buyer_email);                            // Add a recipient

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = 'Property Details from Seller';
        $mail->Body    = 'The seller has been notified of your interest. Here are the seller details:<br>'
                        . 'Seller Name: ' . $seller['full_name'] . '<br>'
                        . 'Seller Email: ' . $seller['email'] . '<br>'
                        . 'Contact the seller at: ' . $seller['phone_number'] . '<br><br>'
                        . 'Property Details:<br>'
                        . 'Title: ' . $property['title'] . '<br>'
                        . 'Description: ' . $property['description'] . '<br>'
                        . 'Price: ' . $property['price'] . '<br>'
                        . 'Location: ' . $property['location'];

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Details</title>
    <style>
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }

        h1 {
            text-align: center;
            margin-top: 0;
        }

        .buyer-info {
            margin-bottom: 20px;
        }

        .buyer-info p {
            margin: 10px 0;
        }

        .buyer-info p span {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Buyer Details</h1>
        <div class="buyer-info">
            <p><span>Name:</span> <?php echo htmlspecialchars($buyer_name); ?></p>
            <p><span>Email:</span> <?php echo htmlspecialchars($buyer_email); ?></p>
            <p><span>Phone Number:</span> <?php echo htmlspecialchars($buyer_phone); ?></p>
            <!-- Add other buyer details here if needed -->
        </div>
    </div>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
