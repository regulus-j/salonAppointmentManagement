<?php
// Start the session
session_start();

// Check if a user is logged in
if (isset($_SESSION['user_id'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session completely
    session_destroy();

    // Redirect to the login page with a success message
    header('Location: ../view/login.php?message=logout_success');
    exit();
} else {
    // If no user is logged in, redirect to the login page
    header('Location: ../view/login.php');
    exit();
}
