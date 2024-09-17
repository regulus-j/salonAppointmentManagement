<?php

class Customer {
    private $conn;
    private $table_name = "Customer";

    public $CustomerID;
    public $UserID;
    public $FirstName;
    public $LastName;
    public $Phone;
    public $DateOfBirth;
    public $JoinDate;
    public $CreatedAt;
    public $UpdatedAt;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function add() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET UserID=:userid, FirstName=:firstname, LastName=:lastname, 
                      Phone=:phone, DateOfBirth=:dateofbirth, JoinDate=:joindate";
        
        $stmt = $this->conn->prepare($query);

        $this->UserID = htmlspecialchars(strip_tags($this->UserID));
        $this->FirstName = htmlspecialchars(strip_tags($this->FirstName));
        $this->LastName = htmlspecialchars(strip_tags($this->LastName));
        $this->Phone = htmlspecialchars(strip_tags($this->Phone));
        $this->DateOfBirth = htmlspecialchars(strip_tags($this->DateOfBirth));
        $this->JoinDate = htmlspecialchars(strip_tags($this->JoinDate));

        $stmt->bindParam(":userid", $this->UserID);
        $stmt->bindParam(":firstname", $this->FirstName);
        $stmt->bindParam(":lastname", $this->LastName);
        $stmt->bindParam(":phone", $this->Phone);
        $stmt->bindParam(":dateofbirth", $this->DateOfBirth);
        $stmt->bindParam(":joindate", $this->JoinDate);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET FirstName=:firstname, LastName=:lastname, Phone=:phone, 
                      DateOfBirth=:dateofbirth
                  WHERE CustomerID=:customerid";
        
        $stmt = $this->conn->prepare($query);

        $this->FirstName = htmlspecialchars(strip_tags($this->FirstName));
        $this->LastName = htmlspecialchars(strip_tags($this->LastName));
        $this->Phone = htmlspecialchars(strip_tags($this->Phone));
        $this->DateOfBirth = htmlspecialchars(strip_tags($this->DateOfBirth));
        $this->CustomerID = htmlspecialchars(strip_tags($this->CustomerID));

        $stmt->bindParam(":firstname", $this->FirstName);
        $stmt->bindParam(":lastname", $this->LastName);
        $stmt->bindParam(":phone", $this->Phone);
        $stmt->bindParam(":dateofbirth", $this->DateOfBirth);
        $stmt->bindParam(":customerid", $this->CustomerID);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE CustomerID = ?";
        $stmt = $this->conn->prepare($query);
        $this->CustomerID = htmlspecialchars(strip_tags($this->CustomerID));
        $stmt->bindParam(1, $this->CustomerID);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function fetch($id = null) {
        $query = "SELECT * FROM " . $this->table_name;
        if($id) {
            $query .= " WHERE CustomerID = ?";
        }
        $stmt = $this->conn->prepare($query);
        
        if($id) {
            $stmt->bindParam(1, $id);
        }

        $stmt->execute();
        return $stmt;
    }
}