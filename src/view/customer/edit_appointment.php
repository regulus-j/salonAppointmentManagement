<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if appointment ID is passed
if (!isset($_GET['id'])) {
    header('Location: appointments.php');
    exit();
}

// Include necessary files
require_once '../../model/db.class.php';
require_once '../../model/services.class.php'; // Include the Service class
require_once '../../model/appointment.class.php'; // Include Appointment class

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Fetch the services for the dropdown
$service = new Service($db);
$stmt = $service->fetch();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create appointment object
$appointment = new Appointment($db);

// Fetch the appointment details
$appointment->AppointmentID = $_GET['id'];
$stmt = $appointment->fetch($appointment->AppointmentID);
$apptDetails = $stmt->fetch(PDO::FETCH_ASSOC);

// If the appointment does not exist, redirect to appointments page
if (!$apptDetails) {
    header('Location: appointments.php');
    exit();
}

// Validation function for POST inputs
function validateInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Error messages container
$errors = [];

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize the inputs
    $appointment->CustomerID = $_SESSION['user_id'];

    // Validate staff ID (required and must be numeric)
    if (!isset($_POST['staff_id']) || !is_numeric($_POST['staff_id'])) {
        $errors[] = "Invalid or missing Staff ID.";
    } else {
        $appointment->StaffID = validateInput($_POST['staff_id']);
    }

    // Validate service ID (required and must be numeric)
    if (!isset($_POST['service_id']) || !is_numeric($_POST['service_id'])) {
        $errors[] = "Invalid or missing Service ID.";
    } else {
        $appointment->ServiceID = validateInput($_POST['service_id']);
    }

    // Validate appointment date and time (required and must be a valid datetime)
    if (!isset($_POST['appointment_datetime']) || !strtotime($_POST['appointment_datetime'])) {
        $errors[] = "Invalid Appointment Date/Time.";
    } else {
        $appointment->AppointmentDateTime = validateInput($_POST['appointment_datetime']);
    }

    // Validate status (required and must be one of the predefined values)
    $validStatuses = ['Scheduled', 'Completed', 'Cancelled'];
    if (!isset($_POST['status']) || !in_array($_POST['status'], $validStatuses)) {
        $errors[] = "Invalid Status.";
    } else {
        $appointment->Status = validateInput($_POST['status']);
    }

    // Sanitize notes (optional)
    $appointment->Notes = validateInput($_POST['notes']);

    // If there are no errors, proceed with updating the appointment
    if (empty($errors)) {
        if ($appointment->update()) {
            $_SESSION['message'] = 'Appointment updated successfully!';
            header('Location: appointments.php');
            exit();
        } else {
            $error_message = "Failed to update appointment. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment - Salon Management System</title>
</head>
<body>
    <h2>Edit Appointment</h2>

    <!-- Display any errors -->
    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Appointment Edit Form -->
    <form method="post">
        <label for="service_id">Service:</label>
        <select name="service_id" id="service_id">
            <?php foreach ($services as $service): ?>
                <option value="<?php echo $service['ServiceID']; ?>" <?php if ($apptDetails['ServiceID'] == $service['ServiceID']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($service['ServiceName']); ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="staff_id">Staff ID:</label>
        <input type="text" name="staff_id" id="staff_id" value="<?php echo htmlspecialchars($apptDetails['StaffID']); ?>"><br>

        <label for="appointment_datetime">Appointment Date/Time:</label>
        <input type="datetime-local" name="appointment_datetime" id="appointment_datetime" value="<?php echo htmlspecialchars($apptDetails['AppointmentDateTime']); ?>"><br>

        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="Scheduled" <?php if ($apptDetails['Status'] == 'Scheduled') echo 'selected'; ?>>Scheduled</option>
            <option value="Completed" <?php if ($apptDetails['Status'] == 'Completed') echo 'selected'; ?>>Completed</option>
            <option value="Cancelled" <?php if ($apptDetails['Status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
        </select><br>

        <label for="notes">Notes:</label>
        <textarea name="notes" id="notes"><?php echo htmlspecialchars($apptDetails['Notes']); ?></textarea><br>

        <button type="submit">Update Appointment</button>
    </form>

</body>
</html>
