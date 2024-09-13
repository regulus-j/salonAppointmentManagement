<?php

class Sales {
    private $conn;
    
    // Sales properties
    public $SaleID;
    public $AppointmentID;
    public $SaleDate;
    public $TotalAmount;
    public $PaymentMethod;

    // SaleItem properties
    public $SaleItems = []; // Array of sale items

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createSale() { /* ... */ }
    public function addSaleItem() { /* ... */ }
    public function updateSale() { /* ... */ }
    public function getSaleDetails() { /* ... */ }
}