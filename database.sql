-- Salon Management System Database Schema (Updated)

-- User Table (for authentication)
CREATE TABLE User (
    UserID INT PRIMARY KEY,
    Username VARCHAR(50) UNIQUE,
    PasswordHash VARCHAR(255),
    Email VARCHAR(100) UNIQUE,
    UserType ENUM('Customer', 'Staff', 'Admin'),
    LastLogin DATETIME,
    IsActive BOOLEAN DEFAULT TRUE
);

-- Staff Table (modified)
CREATE TABLE Staff (
    StaffID INT PRIMARY KEY,
    UserID INT UNIQUE,
    FirstName VARCHAR(50),
    LastName VARCHAR(50),
    Phone VARCHAR(20),
    Role VARCHAR(50),
    HireDate DATE,
    FOREIGN KEY (UserID) REFERENCES User(UserID)
);

-- Service Table (unchanged)
CREATE TABLE Service (
    ServiceID INT PRIMARY KEY,
    ServiceName VARCHAR(100),
    Description TEXT,
    Duration INT, -- in minutes
    Price DECIMAL(10, 2)
);

-- Customer Table (modified)
CREATE TABLE Customer (
    CustomerID INT PRIMARY KEY,
    UserID INT UNIQUE,
    FirstName VARCHAR(50),
    LastName VARCHAR(50),
    Phone VARCHAR(20),
    DateOfBirth DATE,
    JoinDate DATE,
    FOREIGN KEY (UserID) REFERENCES User(UserID)
);

-- Appointment Table (unchanged)
CREATE TABLE Appointment (
    AppointmentID INT PRIMARY KEY,
    CustomerID INT,
    StaffID INT,
    ServiceID INT,
    AppointmentDateTime DATETIME,
    Status VARCHAR(20), -- e.g., Scheduled, Completed, Cancelled
    Notes TEXT,
    FOREIGN KEY (CustomerID) REFERENCES Customer(CustomerID),
    FOREIGN KEY (StaffID) REFERENCES Staff(StaffID),
    FOREIGN KEY (ServiceID) REFERENCES Service(ServiceID)
);

-- Schedule Table (unchanged)
CREATE TABLE Schedule (
    ScheduleID INT PRIMARY KEY,
    StaffID INT,
    WorkDate DATE,
    StartTime TIME,
    EndTime TIME,
    FOREIGN KEY (StaffID) REFERENCES Staff(StaffID)
);

-- Inventory Table (unchanged)
CREATE TABLE Inventory (
    InventoryID INT PRIMARY KEY,
    ProductName VARCHAR(100),
    Description TEXT,
    Quantity INT,
    ReorderLevel INT,
    UnitPrice DECIMAL(10, 2)
);

-- Sales Table (unchanged)
CREATE TABLE Sales (
    SaleID INT PRIMARY KEY,
    AppointmentID INT,
    SaleDate DATE,
    TotalAmount DECIMAL(10, 2),
    PaymentMethod VARCHAR(50),
    FOREIGN KEY (AppointmentID) REFERENCES Appointment(AppointmentID)
);

-- SaleItem Table (unchanged)
CREATE TABLE SaleItem (
    SaleItemID INT PRIMARY KEY,
    SaleID INT,
    ServiceID INT,
    InventoryID INT,
    Quantity INT,
    UnitPrice DECIMAL(10, 2),
    FOREIGN KEY (SaleID) REFERENCES Sales(SaleID),
    FOREIGN KEY (ServiceID) REFERENCES Service(ServiceID),
    FOREIGN KEY (InventoryID) REFERENCES Inventory(InventoryID)
);

-- Feedback Table (unchanged)
CREATE TABLE Feedback (
    FeedbackID INT PRIMARY KEY,
    AppointmentID INT,
    Rating INT,
    Comment TEXT,
    FeedbackDate DATE,
    FOREIGN KEY (AppointmentID) REFERENCES Appointment(AppointmentID)
);

-- New table for user permissions
CREATE TABLE UserPermission (
    PermissionID INT PRIMARY KEY,
    UserID INT,
    PermissionName VARCHAR(50),
    FOREIGN KEY (UserID) REFERENCES User(UserID)
);