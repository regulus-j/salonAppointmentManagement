<?php

class Inventory {
    private $conn;
    private $table_name = "ServiceInventory";

    public $ServiceInventoryID;
    public $ServiceID;
    public $InventoryID;
    public $QuantityRequired;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add a new ServiceInventory entry
    public function add() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET ServiceID = :serviceid, InventoryID = :inventoryid, QuantityRequired = :quantityrequired";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->ServiceID = htmlspecialchars(strip_tags($this->ServiceID));
        $this->InventoryID = htmlspecialchars(strip_tags($this->InventoryID));
        $this->QuantityRequired = htmlspecialchars(strip_tags($this->QuantityRequired));

        // Bind parameters
        $stmt->bindParam(":serviceid", $this->ServiceID);
        $stmt->bindParam(":inventoryid", $this->InventoryID);
        $stmt->bindParam(":quantityrequired", $this->QuantityRequired);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update an existing ServiceInventory entry
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET ServiceID = :serviceid, InventoryID = :inventoryid, QuantityRequired = :quantityrequired 
                  WHERE ServiceInventoryID = :serviceinventoryid";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->ServiceID = htmlspecialchars(strip_tags($this->ServiceID));
        $this->InventoryID = htmlspecialchars(strip_tags($this->InventoryID));
        $this->QuantityRequired = htmlspecialchars(strip_tags($this->QuantityRequired));
        $this->ServiceInventoryID = htmlspecialchars(strip_tags($this->ServiceInventoryID));

        // Bind parameters
        $stmt->bindParam(":serviceid", $this->ServiceID);
        $stmt->bindParam(":inventoryid", $this->InventoryID);
        $stmt->bindParam(":quantityrequired", $this->QuantityRequired);
        $stmt->bindParam(":serviceinventoryid", $this->ServiceInventoryID);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete a ServiceInventory entry
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE ServiceInventoryID = :serviceinventoryid";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->ServiceInventoryID = htmlspecialchars(strip_tags($this->ServiceInventoryID));

        // Bind parameters
        $stmt->bindParam(":serviceinventoryid", $this->ServiceInventoryID);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Fetch all ServiceInventory records or by ServiceInventoryID
    public function fetch($id = null) {
        $query = "SELECT * FROM " . $this->table_name;
        if ($id) {
            $query .= " WHERE ServiceInventoryID = :serviceinventoryid";
        }

        $stmt = $this->conn->prepare($query);

        if ($id) {
            // Bind parameters if ID is provided
            $stmt->bindParam(":serviceinventoryid", $id);
        }

        $stmt->execute();
        return $stmt;
    }
}
