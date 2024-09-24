<?php

class Appointment
{
    private $conn;
    private $table_name = "Appointment";

    public $AppointmentID;
    public $CustomerID;
    public $StaffID;
    public $ServiceID;
    public $AppointmentDateTime;
    public $Status;
    public $Notes;
    public $CreatedAt;
    public $UpdatedAt;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function validateInput()
    {
        if (empty($this->CustomerID) || !is_numeric($this->CustomerID)) {
            throw new InvalidArgumentException("Invalid Customer ID");
        }
        if (empty($this->StaffID) || !is_numeric($this->StaffID)) {
            throw new InvalidArgumentException("Invalid Staff ID");
        }
        if (empty($this->ServiceID) || !is_numeric($this->ServiceID)) {
            throw new InvalidArgumentException("Invalid Service ID");
        }
        if (!strtotime($this->AppointmentDateTime)) {
            throw new InvalidArgumentException("Invalid Appointment Date/Time");
        }
        $validStatuses = ['Scheduled', 'Completed', 'Cancelled'];
        if (!in_array($this->Status, $validStatuses)) {
            throw new InvalidArgumentException("Invalid Status");
        }
    }

    public function add()
    {
        try {
            $this->validateInput();

            $query = "INSERT INTO " . $this->table_name . "
                      SET CustomerID=:customerid, StaffID=:staffid, ServiceID=:serviceid,
                          AppointmentDateTime=:appointmentdatetime, Status=:status, Notes=:notes";
            
            $stmt = $this->conn->prepare($query);
            
            // Sanitize inputs
            $this->CustomerID = htmlspecialchars(strip_tags($this->CustomerID));
            $this->StaffID = htmlspecialchars(strip_tags($this->StaffID));
            $this->ServiceID = htmlspecialchars(strip_tags($this->ServiceID));
            $this->AppointmentDateTime = htmlspecialchars(strip_tags($this->AppointmentDateTime));
            $this->Status = htmlspecialchars(strip_tags($this->Status));
            $this->Notes = htmlspecialchars(strip_tags($this->Notes));
            
            // Bind parameters
            $stmt->bindParam(":customerid", $this->CustomerID);
            $stmt->bindParam(":staffid", $this->StaffID);
            $stmt->bindParam(":serviceid", $this->ServiceID);
            $stmt->bindParam(":appointmentdatetime", $this->AppointmentDateTime);
            $stmt->bindParam(":status", $this->Status);
            $stmt->bindParam(":notes", $this->Notes);
            
            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (InvalidArgumentException $e) {
            // Log the error and return false or throw a custom exception
            error_log("Validation error in Appointment::add(): " . $e->getMessage());
            return false;
        } catch (PDOException $e) {
            // Log the database error and return false or throw a custom exception
            error_log("Database error in Appointment::add(): " . $e->getMessage());
            return false;
        }
    }

    public function update()
    {
        try {
            $this->validateInput();
    
            $query = "UPDATE " . $this->table_name . "
                      SET CustomerID=:customerid, StaffID=:staffid, ServiceID=:serviceid,
                          AppointmentDateTime=:appointmentdatetime, Status=:status, Notes=:notes
                      WHERE AppointmentID=:appointmentid";
            
            $stmt = $this->conn->prepare($query);
            
            // Sanitize inputs
            $this->CustomerID = htmlspecialchars(strip_tags($this->CustomerID));
            $this->StaffID = htmlspecialchars(strip_tags($this->StaffID));
            $this->ServiceID = htmlspecialchars(strip_tags($this->ServiceID));
            $this->AppointmentDateTime = htmlspecialchars(strip_tags($this->AppointmentDateTime));
            $this->Status = htmlspecialchars(strip_tags($this->Status));
            $this->Notes = htmlspecialchars(strip_tags($this->Notes));
            $this->AppointmentID = htmlspecialchars(strip_tags($this->AppointmentID));
            
            // Bind parameters
            $stmt->bindParam(":customerid", $this->CustomerID);
            $stmt->bindParam(":staffid", $this->StaffID);
            $stmt->bindParam(":serviceid", $this->ServiceID);
            $stmt->bindParam(":appointmentdatetime", $this->AppointmentDateTime);
            $stmt->bindParam(":status", $this->Status);
            $stmt->bindParam(":notes", $this->Notes);
            $stmt->bindParam(":appointmentid", $this->AppointmentID);
            
            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (InvalidArgumentException $e) {
            // Log the error and return false or throw a custom exception
            error_log("Validation error in Appointment::update(): " . $e->getMessage());
            return false;
        } catch (PDOException $e) {
            // Log the database error and return false or throw a custom exception
            error_log("Database error in Appointment::update(): " . $e->getMessage());
            return false;
        }
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE AppointmentID = ?";
        $stmt = $this->conn->prepare($query);
        $this->AppointmentID = htmlspecialchars(strip_tags($this->AppointmentID));
        $stmt->bindParam(1, $this->AppointmentID);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function fetch($id)
    {
        $query = "SELECT * FROM " . $this->table_name;
        if ($id) {
            $query .= " WHERE AppointmentID = :id";
        }
        $stmt = $this->conn->prepare($query);

        if ($id) {
            $stmt->bindParam("id", $id);
        }

        $stmt->execute();
        return $stmt;
    }

    public function fetchByUserId($userId) {
        $query = "SELECT 
                    a.AppointmentID, 
                    a.CustomerID, 
                    a.ServiceID, 
                    a.AppointmentDateTime, 
                    a.Status, 
                    a.Notes, 
                    s.StaffID, 
                    CONCAT(s.FirstName, ' ', s.LastName) as staffName, 
                    s.Phone, 
                    s.Role, 
                    sv.ServiceName
                  FROM 
                    " . $this->table_name . " a
                  JOIN 
                    staff s 
                  ON 
                    a.StaffID = s.StaffID
                  JOIN 
                    service sv 
                  ON 
                    a.ServiceID = sv.ServiceID
                  WHERE 
                    a.CustomerID = :customerid";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customerid", $userId);
        $stmt->execute();
        return $stmt;
    }       

    public function cancelAppointment($id)
    {
        $query = "UPDATE " . $this->table_name . " SET Status = 'Cancelled'";
    
        if ($id) {
            $query .= " WHERE AppointmentID = :id";
        }
    
        $stmt = $this->conn->prepare($query);
    
        if ($id) {
            $stmt->bindParam(":id", $id);
        }
    
        if ($stmt->execute()) {
            return true; // Successfully cancelled
        } else {
            return false; // Failed to cancel
        }
    }
}