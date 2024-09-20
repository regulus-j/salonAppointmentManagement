<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include necessary files
require_once '../../model/db.class.php';
require_once '../../model/services.class.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Create service object
$service = new Service($db);

// Fetch all services
$stmt = $service->fetch();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Services - Salon Management System</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            color: #35424a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #35424a;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e2e2e2;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Available Services</h2>

        <?php if (empty($services)): ?>
            <p>No services available at the moment.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Service ID</th>
                        <th>Service Name</th>
                        <th>Description</th>
                        <th>Duration (min)</th>
                        <th>Price ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $srv): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($srv['ServiceID']); ?></td>
                            <td><?php echo htmlspecialchars($srv['ServiceName']); ?></td>
                            <td><?php echo htmlspecialchars($srv['Description']); ?></td>
                            <td><?php echo htmlspecialchars($srv['Duration']); ?></td>
                            <td><?php echo htmlspecialchars($srv['Price']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p><a href="appointments.php">Back to My Appointments</a></p>
    </div>
</body>
</html>