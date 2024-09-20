<?php
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Customer') {
    header('Location: login.php');
    exit();
}

// Include necessary files
require_once '../../../model/db.class.php';
require_once '../../../model/appointment.class.php';
require_once '../../model/service.class.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Create appointment object
$appointment = new Appointment($db);

// Initialize variables
$appointment_details = null;
$services = null;
$action = 'view';
$error = null;
$message = null;

// Check if an appointment ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $error = "Invalid appointment ID.";
} else {
    $appointment_id = $_GET['id'];
    $appointment->AppointmentID = $appointment_id;

    // Fetch the appointment details
    $stmt = $appointment->fetch($appointment_id);
    $appointment_details = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appointment_details) {
        $error = "Appointment not found.";
    } elseif ($appointment_details['CustomerID'] != $_SESSION['user_id']) {
        $error = "You don't have permission to manage this appointment.";
    } else {
        // Handle different actions
        $action = isset($_GET['action']) ? $_GET['action'] : 'view';

        switch ($action) {
            case 'view':
                // Just display the appointment details
                break;

            case 'cancel':
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_cancel'])) {
                    $appointment->Status = 'Cancelled';
                    if ($appointment->update()) {
                        $message = "Appointment cancelled successfully.";
                        header('Location: appointments.php');
                        exit();
                    } else {
                        $error = "Failed to cancel the appointment.";
                    }
                }
                break;

            case 'edit':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Update appointment details
                    $appointment->ServiceID = $_POST['service_id'];
                    $appointment->AppointmentDateTime = $_POST['appointment_datetime'];
                    $appointment->Notes = $_POST['notes'];

                    if ($appointment->update()) {
                        $message = "Appointment updated successfully.";
                        header('Location: appointments.php');
                        exit();
                    } else {
                        $error = "Failed to update the appointment.";
                    }
                }
                // Fetch services for dropdown
                $service = new Service($db);
                $services = $service->fetch();
                break;

            default:
                $error = "Invalid action.";
        }
    }
}

// Include the view file
include '../../view/appointments.php';
?>