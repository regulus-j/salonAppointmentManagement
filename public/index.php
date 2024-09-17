<?php
session_start(); // Start the session at the beginning of the script
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Navigate Site</h1>
    <a href="../src/view/user/sign-up.php">Sign up Users</a>
    <h1>
        <!-- Check if 'userid' is set before trying to access it -->
        <?= isset($_SESSION['userid']) ? htmlspecialchars($_SESSION['userid']) : 'No user ID set' ?>
        <br>
        <!-- For debugging, check the contents of the session array -->
        <pre>
            <?= isset($_SESSION) ? var_export($_SESSION, true) : 'Session is not started' ?>
        </pre>
    </h1>
</body>

</html>