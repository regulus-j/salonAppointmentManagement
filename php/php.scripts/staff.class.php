<?php
class Staff {
    private $conn;
    private $table_name = "Staff";

    public $StaffID;
    public $UserID;
    public $FirstName;
    public $LastName;
    public $Phone;
    public $Role;
    public $HireDate;
    public $CreatedAt;
    public $UpdatedAt;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function add() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET UserID=:userid, FirstName=:firstname, LastName=:lastname, 
                      Phone=:phone, Role=:role, HireDate=:hiredate";
        
        $stmt = $this->conn->prepare($query);

        $this->UserID = htmlspecialchars(strip_tags($this->UserID));
        $this->FirstName = htmlspecialchars(strip_tags($this->FirstName));
        $this->LastName = htmlspecialchars(strip_tags($this->LastName));
        $this->Phone = htmlspecialchars(strip_tags($this->Phone));
        $this->Role = htmlspecialchars(strip_tags($this->Role));
        $this->HireDate = htmlspecialchars(strip_tags($this->HireDate));

        $stmt->bindParam(":userid", $this->UserID);
        $stmt->bindParam(":firstname", $this->FirstName);
        $stmt->bindParam(":lastname", $this->LastName);
        $stmt->bindParam(":phone", $this->Phone);
        $stmt->bindParam(":role", $this->Role);
        $stmt->bindParam(":hiredate", $this->HireDate);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET FirstName=:firstname, LastName=:lastname, Phone=:phone, 
                      Role=:role, HireDate=:hiredate
                  WHERE StaffID=:staffid";
        
        $stmt = $this->conn->prepare($query);

        $this->FirstName = htmlspecialchars(strip_tags($this->FirstName));
        $this->LastName = htmlspecialchars(strip_tags($this->LastName));
        $this->Phone = htmlspecialchars(strip_tags($this->Phone));
        $this->Role = htmlspecialchars(strip_tags($this->Role));
        $this->HireDate = htmlspecialchars(strip_tags($this->HireDate));
        $this->StaffID = htmlspecialchars(strip_tags($this->StaffID));

        $stmt->bindParam(":firstname", $this->FirstName);
        $stmt->bindParam(":lastname", $this->LastName);
        $stmt->bindParam(":phone", $this->Phone);
        $stmt->bindParam(":role", $this->Role);
        $stmt->bindParam(":hiredate", $this->HireDate);
        $stmt->bindParam(":staffid", $this->StaffID);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE StaffID = ?";
        $stmt = $this->conn->prepare($query);
        $this->StaffID = htmlspecialchars(strip_tags($this->StaffID));
        $stmt->bindParam(1, $this->StaffID);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function fetch($id = null) {
        $query = "SELECT * FROM " . $this->table_name;
        if($id) {
            $query .= " WHERE StaffID = ?";
        }
        $stmt = $this->conn->prepare($query);
        
        if($id) {
            $stmt->bindParam(1, $id);
        }

        $stmt->execute();
        return $stmt;
    }
}