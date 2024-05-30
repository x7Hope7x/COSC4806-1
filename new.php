<?php
session_start();

$servername = "k89.h.filess.io";
$username_db = "ProjectWebDataManagement_singwomen";
$password_db = "856a4380f2bbbba960bd8de2471cf9195786782b";
$dbname = "ProjectWebDataManagement_singwomen";
$port = 3305;

// Create connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Redirect to index.php if the user is already authenticated
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] == true) {
    header('Location: index.php');
    exit();
}

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
    // Process form submission if $_POST is set
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve username and password from POST data
        $new_username = $_POST['username'];
        $new_password = $_POST['password'];

        // Validate input (example: check if username is not empty)
        if (empty($new_username) || empty($new_password)) {
            echo "Username and password are required.";
            // Handle validation errors as needed
        } else {
            // Check if username already exists in the database
            $sql = "SELECT id FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $new_username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo "Username already exists.";
                // Handle username already exists error
            } else {
                // Hash the password securely
                $password_hash = password_hash($new_password, PASSWORD_BCRYPT);

                // Insert new user into the database
                $insert_sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("ss", $new_username, $password_hash);

                if ($insert_stmt->execute()) {
                    echo "New user created successfully.";
                    // Redirect or show success message as needed
                } else {
                    echo "Error creating new user: " . $conn->error;
                    // Handle database insertion error
                }
            }
        }
        // Close statement and connection
            $stmt->close();
            $insert_stmt->close();
            $conn->close();
        }
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f0f0f0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .registration-container {
                background-color: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            .registration-container h2 {
                margin-bottom: 20px;
            }
            .registration-container a {
                color: #007BFF;
                text-decoration: none;
            }
            .registration-container a:hover {
                text-decoration: underline;
            }
            .error {
                color: red;
            }
        </style>
    </head>
    <body>
        <div class="registration-container">
            <h2>Create a New Account</h2>
            <!-- <?php if ($registration_error): ?>
                <p class="error"><?php echo htmlspecialchars($registration_error); ?></p>
            <?php endif; ?> -->
            <form method="post" action="">
                Username: <input type="text" name="username" required><br>
                Password: <input type="password" name="password" required><br>
                <input type="submit" value="Register">
            </form>
            <p><a href="login.php">Already have an account? Login here</a></p>
        </div>
    </body>
    </html>