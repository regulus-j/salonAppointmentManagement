<?php

class Service {
    private $conn;
    private $table_name = "Service";

    public $ServiceID;
    public $ServiceName;
    public $Description;
    public $Duration;
    public $Price;
    public $CreatedAt;
    public $UpdatedAt;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function add() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET ServiceName=:servicename, Description=:description, 
                      Duration=:duration, Price=:price";
        
        $stmt = $this->conn->prepare($query);

        $this->ServiceName = htmlspecialchars(strip_tags($this->ServiceName));
        $this->Description = htmlspecialchars(strip_tags($this->Description));
        $this->Duration = htmlspecialchars(strip_tags($this->Duration));
        $this->Price = htmlspecialchars(strip_tags($this->Price));

        $stmt->bindParam(":servicename", $this->ServiceName);
        $stmt->bindParam(":description", $this->Description);
        $stmt->bindParam(":duration", $this->Duration);
        $stmt->bindParam(":price", $this->Price);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET ServiceName=:servicename, Description=:description, 
                      Duration=:duration, Price=:price
                  WHERE ServiceID=:serviceid";
        
        $stmt = $this->conn->prepare($query);

        $this->ServiceName = htmlspecialchars(strip_tags($this->ServiceName));
        $this->Description = htmlspecialchars(strip_tags($this->Description));
        $this->Duration = htmlspecialchars(strip_tags($this->Duration));
        $this->Price = htmlspecialchars(strip_tags($this->Price));
        $this->ServiceID = htmlspecialchars(strip_tags($this->ServiceID));

        $stmt->bindParam(":servicename", $this->ServiceName);
        $stmt->bindParam(":description", $this->Description);
        $stmt->bindParam(":duration", $this->Duration);
        $stmt->bindParam(":price", $this->Price);
        $stmt->bindParam(":serviceid", $this->ServiceID);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE ServiceID = ?";
        $stmt = $this->conn->prepare($query);
        $this->ServiceID = htmlspecialchars(strip_tags($this->ServiceID));
        $stmt->bindParam(1, $this->ServiceID);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function fetch($id = null) {
        $query = "SELECT * FROM " . $this->table_name;
        if($id) {
            $query .= " WHERE ServiceID = :id";
        }
        $stmt = $this->conn->prepare($query);
        
        if($id) {
            $stmt->bindParam(":id", $id);
        }

        $stmt->execute();
        return $stmt;
    }
}