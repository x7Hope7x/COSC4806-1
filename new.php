<?php
session_start();

// Redirect to index.php if the user is already authenticated
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] == true) {
    header('Location: index.php');
    exit();
}

require_once 'config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";port=" . DB_PORT;
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    );

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$registration_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username already exists
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        $registration_error = "Username already exists.";
    } else {
        // Check password security (minimum 8 characters, at least one uppercase letter, one lowercase letter, and one number)
        if (preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password)) {
            // Hash the password
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // Insert the new user into the database
            $sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$username, $password_hash])) {
                header('Location: login.php');
                echo "Account created successfully.";
                
                exit();
            } else {
                $registration_error = "Error: " . $stmt->errorInfo()[2];
            }
        } else {
            $registration_error = "Password does not meet the security requirements.";
        }
    }
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
            margin: 0;
        }
        .registration-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .registration-container h2 {
            margin-bottom: 20px;
        }
        .registration-container form {
            display: flex;
            flex-direction: column;
        }
        .registration-container input[type="text"],
        .registration-container input[type="password"] {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .registration-container input[type="submit"] {
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .registration-container input[type="submit"]:hover {
            background-color: #0056b3;
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
        <?php if (!empty($registration_error)): ?>
            <p class="error"><?php echo htmlspecialchars($registration_error); ?></p>
        <?php endif; ?>
        <form method="post" action="">
            Username <input type="text" name="username" required><br>
            Password <input type="password" name="password" required><br>
            <input type="submit" value="Register">
        </form>
        <p><a href="login.php">Already have an account? Login here</a></p>
    </div>
</body>
</html>
