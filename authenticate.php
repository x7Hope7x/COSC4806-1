<?php

require_once 'config.php';

$servername = "k89.h.filess.io";
$username = "ProjectWebDataManagement_singwomen";
$password = "856a4380f2bbbba960bd8de2471cf9195786782b";
$dbname = "ProjectWebDataManagement_singwomen";
$port = 3305;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname,$port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username already exists
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Username already exists.";
    } else {
        // Check password security (minimum 8 characters, at least one uppercase letter, one lowercase letter, and one number)
        if (preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password)) {
            // Hash the password
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // Insert the new user into the database
            $sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $password_hash);

            if ($stmt->execute()) {
                echo "Account created successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Password does not meet the security requirements.";
        }
    }

    $stmt->close();
}

$conn->close();
?>
