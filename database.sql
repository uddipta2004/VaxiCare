-- Online Vaccine Registration System Database Schema
-- Database: vaccine_db

-- Create database
CREATE DATABASE IF NOT EXISTS vaccine_db;
USE vaccine_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL UNIQUE,
    age INT,
    gender ENUM('Male', 'Female', 'Other'),
    password VARCHAR(255) NOT NULL,
    photo VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admin Table
CREATE TABLE IF NOT EXISTS admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Districts Table
CREATE TABLE IF NOT EXISTS districts (
    district_id INT AUTO_INCREMENT PRIMARY KEY,
    district_name VARCHAR(100) NOT NULL UNIQUE
);

-- Vaccine Centers Table
CREATE TABLE IF NOT EXISTS centers (
    center_id INT AUTO_INCREMENT PRIMARY KEY,
    center_name VARCHAR(150) NOT NULL,
    district_id INT NOT NULL,
    address TEXT,
    contact VARCHAR(15),
    FOREIGN KEY (district_id) REFERENCES districts(district_id) ON DELETE CASCADE
);

-- Vaccine Stock Table (District-wise)
CREATE TABLE IF NOT EXISTS vaccine_stock (
    stock_id INT AUTO_INCREMENT PRIMARY KEY,
    district_id INT NOT NULL,
    vaccine_name VARCHAR(50) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (district_id) REFERENCES districts(district_id) ON DELETE CASCADE
);

-- Vaccine Slots Table
CREATE TABLE IF NOT EXISTS slots (
    slot_id INT AUTO_INCREMENT PRIMARY KEY,
    district_id INT NOT NULL,
    center_id INT NOT NULL,
    slot_date DATE NOT NULL,
    slot_time TIME NOT NULL,
    vaccine_name VARCHAR(50) NOT NULL,
    available_slots INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (district_id) REFERENCES districts(district_id) ON DELETE CASCADE,
    FOREIGN KEY (center_id) REFERENCES centers(center_id) ON DELETE CASCADE
);

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    slot_id INT NOT NULL,
    status ENUM('booked', 'completed', 'cancelled') DEFAULT 'booked',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (slot_id) REFERENCES slots(slot_id) ON DELETE CASCADE
);

-- Vaccination Certificates Table
CREATE TABLE IF NOT EXISTS certificates (
    certificate_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    booking_id INT NOT NULL,
    vaccine_name VARCHAR(50) NOT NULL,
    dose_number INT NOT NULL DEFAULT 1,
    vaccination_date DATE NOT NULL,
    certificate_number VARCHAR(50) NOT NULL UNIQUE,
    qr_code VARCHAR(255),
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
);

-- Daily Reports Table
CREATE TABLE IF NOT EXISTS daily_reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    report_date DATE NOT NULL,
    district_id INT,
    total_bookings INT DEFAULT 0,
    total_vaccinations INT DEFAULT 0,
    total_certificates INT DEFAULT 0,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (district_id) REFERENCES districts(district_id) ON DELETE SET NULL
);

-- OTP Verification Table
CREATE TABLE IF NOT EXISTS otp_verification (
    otp_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    otp_code VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    is_used BOOLEAN DEFAULT FALSE
);

-- Insert default admin
INSERT INTO admin (username, password, name) VALUES 
('admin', MD5('admin123'), 'System Administrator');

-- Insert sample districts
INSERT INTO districts (district_name) VALUES 
('Kamrup'),
('Dibrugarh'),
('Guwahati'),
('Nagaon'),
('Jorhat'),
('Silchar'),
('Tezpur'),
('Golaghat');

-- Insert sample centers
INSERT INTO centers (center_name, district_id, address, contact) VALUES 
('GMCH Hospital', 1, 'G.S. Road, Guwahati', '0361-123456'),
('Mahendra Mohan Choudhury Hospital', 1, 'Maligaon, Guwahati', '0361-654321'),
('Assam Medical College', 2, 'Barbari, Dibrugarh', '0373-123456'),
('Nagaon Civil Hospital', 4, 'Haibargaon, Nagaon', '03672-123456'),
('Jorhat Medical College', 5, 'Kushal Nagar, Jorhat', '0376-123456'),
('Silchar Medical College', 6, 'Meherpur, Silchar', '03842-123456'),
('Tezpur Medical College', 7, 'Bihaguri, Tezpur', '03712-123456'),
('Golaghat Civil Hospital', 8, 'Golaghat Town', '03774-123456');

-- Insert sample vaccine stock
INSERT INTO vaccine_stock (district_id, vaccine_name, quantity) VALUES 
(1, 'Covishield', 500),
(1, 'Covaxin', 300),
(2, 'Covishield', 400),
(2, 'Covaxin', 250),
(4, 'Covishield', 350),
(5, 'Covaxin', 200);

-- Insert sample slots
INSERT INTO slots (district_id, center_id, slot_date, slot_time, vaccine_name, available_slots) VALUES 
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', 'Covishield', 50),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00:00', 'Covishield', 50),
(1, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', 'Covaxin', 30),
(2, 3, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', 'Covishield', 40),
(4, 5, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '10:00:00', 'Covaxin', 25);
