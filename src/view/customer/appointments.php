<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include necessary files
require_once '../../model/db.class.php';
require_once '../../model/appointment.class.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Create appointment object
$appointment = new Appointment($db);

// Fetch appointments for the logged-in user
$stmt = $appointment->fetchByUserId($_SESSION['user_id']);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - Salon Management System</title>
    <style>
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
        .action-links a {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Appointments</h2>

        <?php if (empty($appointments)): ?>
            <p>You have no appointments scheduled.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Date & Time</th>
                        <th>Service</th>
                        <th>Staff</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appt): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appt['AppointmentID']); ?></td>
                            <td><?php echo htmlspecialchars($appt['AppointmentDateTime']); ?></td>
                            <td><?php echo htmlspecialchars($appt['ServiceID']); ?></td>
                            <td><?php echo htmlspecialchars($appt['StaffID']); ?></td>
                            <td><?php echo htmlspecialchars($appt['Status']); ?></td>
                            <td class="action-links">
                                <a href="edit_appointment.php?id=<?php echo $appt['AppointmentID']; ?>">Edit</a>
                                <a href="..\..\controller\customer\cancel_appointment.php?id=<?php echo $appt['AppointmentID']; ?>" onclick="return confirm('Are you sure you want to cancel this appointment?');">Cancel</a>                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p><a href="book_appointment.php">Book a New Appointment</a></p>
    </div>
</body>
</html>