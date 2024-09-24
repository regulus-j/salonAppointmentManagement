<?php

// Include necessary files
require_once '../../model/db.class.php';
require_once '../../model/appointment.class.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Create an instance of the Appointment class
$appointment = new Appointment($db);

// Start or resume the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: Please log in to book an appointment.");
}

// Fetch customer_id from user_id
$stmt = $db->prepare("SELECT CustomerID FROM customer WHERE UserID = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $customer_id = $row['CustomerID'];
} else {
    die("Error: No customer profile found for this user. Please complete your profile before booking an appointment.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book an Appointment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
            font-weight: bold;
        }
        input, select, textarea {
            margin-bottom: 15px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            font-size: 0.9em;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
<div class="container" id="datetime-input">
        <h1>Select Appointment Date and Time</h1>
        <form id="datetimeForm" onsubmit="return showFullForm(event)">
            <label for="appointment_date">Select Date:</label>
            <input type="date" id="appointment_date" name="appointment_date" required>
            
            <label for="appointment_start_time">Select Time:</label>
            <input type="time" id="appointment_start_time" name="appointment_start_time" required>
            <br>
            
            <button type="submit">Next</button>
        </form>
    </div>


    <?php
    // Handle form submission for date and time
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Store the date and time in the session
        $_SESSION['appointment_date'] = $_POST['appointment_date'];
        $_SESSION['appointment_start_time'] = $_POST['appointment_start_time'];

        // Redirect to the same page to show the full form
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    ?>

<?php
// Check if the date and time are set in the session
if (isset($_SESSION['appointment_date']) && isset($_SESSION['appointment_start_time'])) {
    $appointment_date = $_SESSION['appointment_date'];
    $appointment_start_time = $_SESSION['appointment_start_time'];
    
    // Now show the full form with available stylists and services
    ?>
    <div class="container hidden" id="full-form">
        <h1>Book an Appointment</h1>
        <form id="appointmentForm" method="post">
            <!-- Stylist Selection -->
            <label for="staffID">Select a stylist:</label>
            <select id="staffID" name="staffID" required>
                <option value="">Select a stylist</option>
                <?php
                // Fetch available stylists based on date and time
                $stmt = $db->prepare("
                SELECT DISTINCT s.StaffID, CONCAT(s.FirstName, ' ', s.LastName) AS FullName
                FROM staff s
                LEFT JOIN appointment a ON s.StaffID = a.StaffID
                    AND DATE(a.AppointmentDateTime) = :appointment_date
                    AND TIME(a.AppointmentDateTime) = :appointment_start_time
                WHERE a.AppointmentID IS NULL
                  OR a.Status = 'Cancelled'
            ");
            
            $stmt->bindParam(':appointment_date', $appointment_date);
            $stmt->bindParam(':appointment_start_time', $appointment_start_time);
            
            $stmt->execute();

            $appointment_start_datetime = "$appointment_date $appointment_start_time";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row['StaffID'] . "'>" . htmlspecialchars($row['FullName']) . "</option>";
                }
                ?>
            </select>

            <!-- Service Selection -->
            <label for="serviceID">Select a service:</label>
            <select id="serviceID" name="serviceID" required>
                <option value="">Select a service</option>
                <?php
                // Fetch available services
                $stmt = $db->prepare("SELECT s.ServiceID, s.ServiceName
                                        FROM service s
                                        INNER JOIN ServiceInventory si
                                            ON s.ServiceID = si.ServiceID
                                        INNER JOIN inventory i
                                            ON si.InventoryID = i.InventoryID
                                        GROUP BY s.ServiceID, s.ServiceName
                                        HAVING MIN(i.Quantity >= si.QuantityRequired);");

                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row['ServiceID'] . "'>" . htmlspecialchars($row['ServiceName']) . "</option>";
                }
                ?>
            </select>

            <!-- Appointment Date and Time (read-only from the session) -->
            <label for="appointmentDateTime">Appointment Date and Time:</label>
            <input type="datetime-local" id="appointmentDateTime" name="appointmentDateTime" value="<?= $appointment_start_datetime ?>" readonly required>

            <!-- Status -->
            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="Scheduled">Scheduled</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>

            <!-- Notes -->
            <label for="notes">Notes:</label>
            <textarea id="notes" name="notes" rows="4"></textarea>

            <!-- Submit Button -->
            <button type="submit">Book Appointment</button>
        </form>
    </div>
    <?php
}
?>


    <?php
    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Set appointment properties
        $appointment->CustomerID = $customer_id; // Use the fetched customer_id
        $appointment->StaffID = $_POST['staffID'];
        $appointment->ServiceID = $_POST['serviceID'];
        $appointment->AppointmentDateTime = $_POST['appointmentDateTime'];
        $appointment->Status = $_POST['status'];
        $appointment->Notes = $_POST['notes'];

        // Create the appointment
        if($appointment->add()) {
            echo "<script>alert('Appointment booked successfully!');</script>";
            header('location: appointments.php');
        } else {
            echo "<script>alert('Unable to book appointment. Please try again.');</script>";
        }
    }
    ?>

<script>
        // Set min date to prevent past dates and restrict to 5 hours from now
        window.onload = function() {
            const appointmentInput = document.getElementById('appointmentDateTime');
            const now = new Date();
            
            // Add 5 hours to the current time
            now.setHours(now.getHours() + 5);
            
            // Format the date and time for the input's min attribute (YYYY-MM-DDTHH:MM)
            const year = now.getFullYear();
            const month = ('0' + (now.getMonth() + 1)).slice(-2); // Months are zero-based
            const day = ('0' + now.getDate()).slice(-2);
            const hours = ('0' + now.getHours()).slice(-2);
            const minutes = ('0' + now.getMinutes()).slice(-2);
            const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            
            // Set the min attribute to restrict past dates and times within the next 5 hours
            appointmentInput.min = minDateTime;
        };

        // Form validation
        document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            let isValid = true;

            // Reset error messages
            document.querySelectorAll('.error').forEach(el => el.textContent = '');

            const staffID = document.getElementById('staffID').value;
            if (!staffID) {
                document.getElementById('staffIDError').textContent = "Please select a stylist";
                isValid = false;
            }

            const serviceID = document.getElementById('serviceID').value;
            if (!serviceID) {
                document.getElementById('serviceIDError').textContent = "Please select a service";
                isValid = false;
            }

            const appointmentDateTime = document.getElementById('appointmentDateTime').value;
            if (!appointmentDateTime) {
                document.getElementById('appointmentDateTimeError').textContent = "Invalid Appointment Date/Time";
                isValid = false;
            }

            const status = document.getElementById('status').value;
            const validStatuses = ['Scheduled', 'Completed', 'Cancelled'];
            if (!validStatuses.includes(status)) {
                document.getElementById('statusError').textContent = "Invalid Status";
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault(); // Prevent form submission if validation fails
            }
        });

        function showFullForm(event) {
            event.preventDefault();
            
            // Get selected date and time
            const date = document.getElementById('appointment_date').value;
            const time = document.getElementById('appointment_start_time').value;
            
            // Combine date and time
            const dateTime = `${date}T${time}`;
            
            // Set the combined date and time to the hidden input
            document.getElementById('appointmentDateTime').value = dateTime;
            
            // Hide datetime input and show full form
            document.getElementById('datetime-input').classList.add('hidden');
            document.getElementById('full-form').classList.remove('hidden');
            
            return false;
        }
    </script>
</body>
</html>