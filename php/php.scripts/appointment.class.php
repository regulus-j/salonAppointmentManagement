<?php

class Appointment {
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

    public function __construct($db) {
        $this->conn = $db;
    }

    public function add() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET CustomerID=:customerid, StaffID=:staffid, ServiceID=:serviceid, 
                      AppointmentDateTime=:appointmentdatetime, Status=:status, Notes=:notes";
        
        $stmt = $this->conn->prepare($query);

        $this->CustomerID = htmlspecialchars(strip_tags($this->CustomerID));
        $this->StaffID = htmlspecialchars(strip_tags($this->StaffID));
        $this->ServiceID = htmlspecialchars(strip_tags($this->ServiceID));
        $this->AppointmentDateTime = htmlspecialchars(strip_tags($this->AppointmentDateTime));
        $this->Status = htmlspecialchars(strip_tags($this->Status));
        $this->Notes = htmlspecialchars(strip_tags($this->Notes));

        $stmt->bindParam(":customerid", $this->CustomerID);
        $stmt->bindParam(":staffid", $this->StaffID);
        $stmt->bindParam(":serviceid", $this->ServiceID);
        $stmt->bindParam(":appointmentdatetime", $this->AppointmentDateTime);
        $stmt->bindParam(":status", $this->Status);
        $stmt->bindParam(":notes", $this->Notes);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET CustomerID=:customerid, StaffID=:staffid, ServiceID=:serviceid, 
                      AppointmentDateTime=:appointmentdatetime, Status=:status, Notes=:notes
                  WHERE AppointmentID=:appointmentid";
        
        $stmt = $this->conn->prepare($query);

        $this->CustomerID = htmlspecialchars(strip_tags($this->CustomerID));
        $this->StaffID = htmlspecialchars(strip_tags($this->StaffID));
        $this->ServiceID = htmlspecialchars(strip_tags($this->ServiceID));
        $this->AppointmentDateTime = htmlspecialchars(strip_tags($this->AppointmentDateTime));
        $this->Status = htmlspecialchars(strip_tags($this->Status));
        $this->Notes = htmlspecialchars(strip_tags($this->Notes));
        $this->AppointmentID = htmlspecialchars(strip_tags($this->AppointmentID));

        $stmt->bindParam(":customerid", $this->CustomerID);
        $stmt->bindParam(":staffid", $this->StaffID);
        $stmt->bindParam(":serviceid", $this->ServiceID);
        $stmt->bindParam(":appointmentdatetime", $this->AppointmentDateTime);
        $stmt->bindParam(":status", $this->Status);
        $stmt->bindParam(":notes", $this->Notes);
        $stmt->bindParam(":appointmentid", $this->AppointmentID);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE AppointmentID = ?";
        $stmt = $this->conn->prepare($query);
        $this->AppointmentID = htmlspecialchars(strip_tags($this->AppointmentID));
        $stmt->bindParam(1, $this->AppointmentID);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function fetch($id = null) {
        $query = "SELECT * FROM " . $this->table_name;
        if($id) {
            $query .= " WHERE AppointmentID = ?";
        }
        $stmt = $this->conn->prepare($query);
        
        if($id) {
            $stmt->bindParam(1, $id);
        }

        $stmt->execute();
        return $stmt;
    }
}