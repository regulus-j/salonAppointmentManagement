<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .register-container {
            background-color: #fff;
            padding: 20px;
            margin-top: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            box-sizing: border-box;
        }

        h1 {
            margin-bottom: 20px;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .section-title {
            font-size: 1.2em;
            margin-bottom: 10px;
            text-decoration: underline;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .links {
            text-align: center;
            margin-top: 10px;
        }

        .links a {
            color: #4CAF50;
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h1>Register</h1>
        <form action="../../controller/register.php" method="post">
            <!-- Account Information Section -->
            <div class="section-title">Account Requirements</div>
            
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <!-- User Information Section -->
            <div class="section-title">User Details</div>
            
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" required pattern="[0-9]{4}-[0-9]{3}-[0-9]{4}" placeholder="0909-090-0909">

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>

            <!-- Submit Button -->
            <button type="submit">Register</button>

            <div class="links">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if there's an error message in the session
            <?php
            session_start();
            if (isset($_SESSION['message'])) {
                echo "alert('" . addslashes($_SESSION['message']) . "');";
                unset($_SESSION['message']); // Clear the message after displaying it
            }
            ?>
        });
    </script>
</body>

</html>
