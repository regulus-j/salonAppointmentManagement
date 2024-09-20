<?php
session_start();

// Include the database connection class
require_once __DIR__ . '/../model/db.class.php';

// Function to redirect based on user type
function redirectByUserType($userType) {
    switch ($userType) {
        case 'Customer':
            header('Location: ../view/customer/index.php');
            break;
        case 'Admin':
            header('Location: ../view/admin/index.php');
            break;
        case 'Staff':
            header('Location: ../view/staff/index.php');
            break;
        default:
            header('Location: ../../public/index.php');
    }
    exit;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $dob = trim($_POST['dob']);

    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) ||
        empty($first_name) || empty($last_name) || empty($phone) || empty($dob)) {
        $_SESSION['message'] = 'Please fill in all fields.';
        header('Location: ../view/customer/register.php');
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['message'] = 'Passwords do not match.';
        header('Location: ../view/customer/register.php');
        exit;
    }

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    try {
        // Start transaction
        $db->beginTransaction();

        // Check if username or email already exists
        $stmt = $db->prepare('SELECT UserID FROM User WHERE Username = :username OR Email = :email LIMIT 1');
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetch()) {
            throw new Exception('Username or email already exists.');
        }

        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $db->prepare('INSERT INTO User (Username, Email, PasswordHash, UserType, IsActive) 
                              VALUES (:username, :email, :password, :user_type, :is_active)');
        
        $user_type = 'Customer'; // Default user type
        $is_active = 1; // Set to active by default

        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $password_hash,
            'user_type' => $user_type,
            'is_active' => $is_active
        ]);

        $user_id = $db->lastInsertId();

        // Insert customer details
        $stmt = $db->prepare('INSERT INTO Customer (UserID, FirstName, LastName, Phone, DateOfBirth, JoinDate) 
                              VALUES (:user_id, :first_name, :last_name, :phone, :dob, CURDATE())');

        $stmt->execute([
            'user_id' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'phone' => $phone,
            'dob' => $dob
        ]);

        // Commit the transaction
        $db->commit();

        $_SESSION['message'] = 'Registration successful. Please log in.';
        header('Location: ../view/customer/login.php');
    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        $db->rollBack();
        $_SESSION['message'] = 'Registration failed: ' . $e->getMessage();
        header('Location: ../view/customer/register.php');
    }
    exit;
} else {
    // Not a POST request
    header('Location: ../view/customer/register.php');
    exit;
}
?>