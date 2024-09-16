<?php
session_start();

// Database connection parameters
$host = 'localhost'; // Replace with your database host
$dbname = 'salonManagement'; // Replace with your database name
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($username) || empty($password)) {
        $_SESSION['message'] = 'Please enter both username and password.';
        header('Location: index.php'); // Redirect back to login page
        exit;
    }

    // Prepare and execute query to check if user exists
    $stmt = $pdo->prepare('SELECT UserID, PasswordHash, UserType, IsActive FROM User WHERE Username = :username LIMIT 1');
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Check if the password is correct
        if (password_verify($password, $user['PasswordHash'])) {
            if ($user['IsActive']) {
                // Password is correct and user is active
                $_SESSION['user_id'] = $user['UserID'];
                $_SESSION['user_type'] = $user['UserType'];
                $_SESSION['message'] = 'Login successful.';
                header('Location: ../../public/index.php'); // Redirect to a dashboard or home page
                exit;
            } else {
                $_SESSION['message'] = 'Your account is not active.';
            }
        } else {
            $_SESSION['message'] = 'Invalid username or password.';
        }
    } else {
        $_SESSION['message'] = 'Invalid username or password.';
    }

    // Redirect back to login page with message
    header('Location: index.php');
    exit;
} else {
    // Not a POST request
    header('Location: index.php');
    exit;
}
?>