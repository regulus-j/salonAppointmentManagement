<?php
include_once '../model/db.class.php';
include_once '../model/user.class.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$message = '';

// Start session and generate CSRF token
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the form has been submitted with a CSRF token
    if (!isset($_POST['csrf_token'])) {
        $message = "<div style='color: red;'>Error: CSRF token missing. Please try again.</div>";
    } else {
        // Verify CSRF token
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $message = "<div style='color: red;'>Error: CSRF token validation failed. Please try again.</div>";
        } else {
            // CSRF validation passed, process the form
            if (($user->isUnique($_POST['username'])) === 0) {
                $user->Username = $_POST['username'];
            } else {
                $message = "Username is taken.";
                exit;
            }
            $user->PasswordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $user->Email = $_POST['email'];
            $user->UserType = $_POST['usertype'];
            $user->IsActive = $_POST['isactive'];

            if ($user->add()) {
                $message = "<div style='color: green;'>User created successfully.</div>";
            } else {
                $message = "<div style='color: red;'>Unable to create user.</div>";
            }
        }
    }
}

// Fetch existing users
$users = $user->fetch();
?>