<?php
session_start();

require_once 'user.php';
$user = new User();
$user_list = $user->get_all_users();

// Redirect to login.php if the user is not authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit();
}

$current_date = date('l, F j, Y, g:i A');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .welcome-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .welcome-container h2 {
            margin-bottom: 20px;
        }
        .welcome-container a {
            color: #007BFF;
            text-decoration: none;
        }
        .welcome-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        <p>Today's date is: <?php echo $current_date; ?></p>
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
