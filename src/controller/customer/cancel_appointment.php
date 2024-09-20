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

// Check if appointment ID is provided
if (isset($_GET['id'])) {
    $appointment->AppointmentID = $_GET['id'];
    
    // Set the status to 'Cancelled'
    $appointment->Status = 'Cancelled';
    
    // Update the appointment status
    if ($appointment->update()) {
        header('Location: ..\..\view\customer\appointments.php?message=Appointment cancelled successfully.');
        exit();
    } else {
        header('Location: ..\..\view\customer\appointments.php?error=Failed to cancel appointment.');
        exit();
    }
} else {
    header('Location: appointments.php?error=Invalid appointment ID.');
    exit();
}