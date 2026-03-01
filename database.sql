-- ============================================================
-- POWER FITNESS GYM - Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS gym_system;
USE gym_system;

-- Users table (Admin login)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(20) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Members table
CREATE TABLE members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id_code VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    address VARCHAR(200),
    phone VARCHAR(20),
    gender VARCHAR(10),
    date_of_birth DATE,
    plan VARCHAR(50) NOT NULL,
    duration VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    date_enrolled DATE NOT NULL,
    date_expiry DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Attendance table
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT NOT NULL,
    check_in_date DATE NOT NULL,
    check_in_time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Payments table
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    quantity INT DEFAULT 1,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(20) NOT NULL,
    payment_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'Paid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Inventory table
CREATE TABLE inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    item_name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Announcements table
CREATE TABLE announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    date_from DATE NOT NULL,
    date_to DATE NOT NULL,
    priority VARCHAR(20) DEFAULT 'Normal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Feedback table
-- REMOVED - Feedback functionality not needed

-- Insert default admin user (password: admin123)
INSERT INTO users (email, password, full_name, role) 
VALUES ('admin@powergym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'admin');

-- Insert sample members
INSERT INTO members (member_id_code, first_name, last_name, address, phone, gender, date_of_birth, plan, duration, amount, date_enrolled, date_expiry, status) VALUES
('MEM-00001', 'James', 'Carter', 'Toril, Davao City', '09121234567', 'Male', '1995-03-15', 'Membership', '1 Year', 700.00, '2025-09-03', '2026-09-03', 'Active'),
('MEM-00002', 'Diva', 'Mucson', 'Baliok, Davao City', '09129876543', 'Female', '1998-07-22', 'Supplements', '3 Months', 708.00, '2025-09-01', '2025-12-01', 'Expired'),
('MEM-00003', 'Olivia', 'Santino', 'Maa, Davao City', '09123456789', 'Female', '1992-11-08', 'Membership', '1 Year', 700.00, '2025-03-10', '2026-03-10', 'Active');
