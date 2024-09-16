<?php

class User
{
    private $conn;
    private $table_name = "User";

    public $UserID;
    public $Username;
    public $PasswordHash;
    public $Email;
    public $UserType;
    public $LastLogin;
    public $IsActive;
    public $CreatedAt;
    public $UpdatedAt;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function add()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET Username=:username, PasswordHash=:passwordhash, Email=:email, 
                      UserType=:usertype, IsActive=:isactive";

        $stmt = $this->conn->prepare($query);

        $this->Username = htmlspecialchars(strip_tags($this->Username));
        $this->PasswordHash = htmlspecialchars(strip_tags($this->PasswordHash));
        $this->Email = htmlspecialchars(strip_tags($this->Email));
        $this->UserType = htmlspecialchars(strip_tags($this->UserType));
        $this->IsActive = $this->IsActive ? 1 : 0;

        $stmt->bindParam(":username", $this->Username);
        $stmt->bindParam(":passwordhash", $this->PasswordHash);
        $stmt->bindParam(":email", $this->Email);
        $stmt->bindParam(":usertype", $this->UserType);
        $stmt->bindParam(":isactive", $this->IsActive);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                  SET Username=:username, Email=:email, UserType=:usertype, 
                      IsActive=:isactive, LastLogin=:lastlogin
                  WHERE UserID=:userid";

        $stmt = $this->conn->prepare($query);

        $this->Username = htmlspecialchars(strip_tags($this->Username));
        $this->Email = htmlspecialchars(strip_tags($this->Email));
        $this->UserType = htmlspecialchars(strip_tags($this->UserType));
        $this->IsActive = $this->IsActive ? 1 : 0;
        $this->LastLogin = htmlspecialchars(strip_tags($this->LastLogin));
        $this->UserID = htmlspecialchars(strip_tags($this->UserID));

        $stmt->bindParam(":username", $this->Username);
        $stmt->bindParam(":email", $this->Email);
        $stmt->bindParam(":usertype", $this->UserType);
        $stmt->bindParam(":isactive", $this->IsActive);
        $stmt->bindParam(":lastlogin", $this->LastLogin);
        $stmt->bindParam(":userid", $this->UserID);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE UserID = ?";
        $stmt = $this->conn->prepare($query);
        $this->UserID = htmlspecialchars(strip_tags($this->UserID));
        $stmt->bindParam(1, $this->UserID);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function fetch($id = null)
    {
        $query = "SELECT * FROM " . $this->table_name;
        if ($id) {
            $query .= " WHERE UserID = ?";
        }
        $stmt = $this->conn->prepare($query);

        if ($id) {
            $stmt->bindParam(1, $id);
        }

        $stmt->execute();
        return $stmt;
    }

    public function fetchByUsername($username)
    {
        $query = "SELECT UserID, Username, PasswordHash, Email, UserType FROM " . $this->table_name . " WHERE Username = :username";

        $stmt = $this->conn->prepare($query);
        $username = htmlspecialchars(strip_tags($username));
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt;
    }

    public function isUnique($username)
    {
        $query = "SELECT COUNT(*)FROM " . $this->table_name . " WHERE  Username = :username;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $query;
    }
}