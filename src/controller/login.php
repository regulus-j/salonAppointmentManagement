<?php
session_start();

// Include the database connection class
require_once __DIR__ . '/../model/db.class.php';

// Function to redirect based on user type
function redirectByUserType($userType) {
    switch ($userType) {
        case 'Customer':
            header('Location: ../view/customer/home.php');
            break;
        case 'Admin':
            header('Location: ../view/admin/index.php');
            break;
        case 'Staff':
            header('Location: ../view/staff/index.php');
            break;
        default:
            // If an unknown user type is encountered, redirect to a default page
            header('Location: ../../public/index.php');
    }
    exit;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($username) || empty($password)) {
        $_SESSION['message'] = 'Please enter both username and password.';
        header('Location: ../view/customer/login.php'); // Redirect back to login page
        exit;
    }

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Prepare and execute query to check if user exists
    $stmt = $db->prepare('SELECT UserID, PasswordHash, UserType, IsActive FROM User WHERE Username = :username LIMIT 1');
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

                // Update LastLogin
                $updateStmt = $db->prepare('UPDATE User SET LastLogin = NOW() WHERE UserID = :userId');
                $updateStmt->execute(['userId' => $user['UserID']]);

                $_SESSION['message'] = 'Login successful.';
               
                // Redirect based on user type
                redirectByUserType($user['UserType']);
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
    header('Location: ../view/customer/login.php');
    exit;
} else {
    // Not a POST request
    header('Location: ../view/customer/login.php');
    exit;
}
?>