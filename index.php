<?php
include_once 'php/php.scripts/db.class.php';
include_once 'php/php.scripts/user.class.php';
//include_once 'php/php.scripts/staff.class.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Add a new user
$user->Username = "newuser";
$user->PasswordHash = password_hash("password123", PASSWORD_DEFAULT);
$user->Email = "newuser@example.com";
$user->UserType = "Customer";
$user->IsActive = true;

if($user->add()) {
    echo "User created successfully.";
} else {
    echo "Unable to create user.";
}

// Fetch all users
$stmt = $user->fetch();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    echo "User ID: " . $UserID . ", Username: " . $Username . ", Email: " . $Email . "\n";
}

// Similar usage for other classes