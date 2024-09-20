<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Home - Salon Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding: 20px;
        }
        header {
            background: #35424a;
            color: white;
            padding-top: 30px;
            min-height: 70px;
            border-bottom: #e8491d 3px solid;
        }
        header a {
            color: #ffffff;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 16px;
        }
        header ul {
            padding: 0;
            margin: 0;
            list-style: none;
            overflow: hidden;
        }
        header li {
            float: left;
            display: inline;
            padding: 0 20px 0 20px;
        }
        header #branding {
            float: left;
        }
        header #branding h1 {
            margin: 0;
        }
        header nav {
            float: right;
            margin-top: 10px;
        }
        .highlight, header .current a {
            color: #e8491d;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Customer') {
        header('Location: login.php');
        exit();
    }

    // Include the database connection class
    require_once '../../model/db.class.php';

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Fetch customer details
    $stmt = $db->prepare('SELECT FirstName, LastName FROM Customer WHERE UserID = :userId');
    $stmt->execute(['userId' => $_SESSION['user_id']]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>

    <header>
        <div class="container">
            <div id="branding">
                <h1><span class="highlight">Salon</span> Management System</h1>
            </div>
            <nav>
                <ul>
                    <li class="current"><a href="index.php">Home</a></li>
                    <li><a href="appointments.php">Appointments</a></li>
                    <li><a href="promos.php">Promos</a></li>
                    <li><a href="services.php">Services</a></li>
                    <li><a href="profile.php">Account</a></li>
                    <li><a href="../../controller/logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($customer['FirstName'] . ' ' . $customer['LastName']); ?>!</h2>
        <p>This is your customer dashboard. Here you can manage your appointments, view your profile, and more.</p>
        
        <h3>Quick Actions:</h3>
        <ul>
            <li><a href="book_appointment.php">Book a New Appointment</a></li>
            <li><a href="view_services.php">View Our Services</a></li>
            <li><a href="contact.php">Contact Us</a></li>
        </ul>

        <?php if (isset($_SESSION['message'])): ?>
            <p style="color: green;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>

        Uncomment this section for debugging purposes
        <h3>Session Information (Debug):</h3>
        <pre><?php var_dump($_SESSION); ?></pre>
       
    </div>
</body>
</html>
