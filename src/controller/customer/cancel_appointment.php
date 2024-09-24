<?php
session_start();

// Include necessary files
require_once '../../model/db.class.php';
require_once '../../model/appointment.class.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Create an instance of the Appointment class
$appointment = new Appointment($db);

// Check if the appointment ID is set
if (isset($_GET['id'])) {
    $appointmentID = $_GET['id'];

    // Call the cancelAppointment function
    if ($appointment->cancelAppointment($appointmentID)) {
        // Redirect with success message
        header("Location: ../../view/customer/appointments.php?msg=Appointment cancelled successfully");
    } else {
        // Redirect with error message
        header("Location: ../appointments.php?msg=Error cancelling appointment");
    }
} else {
    // Redirect with error message if ID is not set
    header("Location: ../../view/customer/appointments.php?msg=Invalid appointment ID");
}
exit();
