
CREATE DATABASE IF NOT EXISTS hostel_management;
USE hostel_management;

-- ------------------------------------------------------------
-- 1. Admins Table
-- ------------------------------------------------------------
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- 2. Students Table
-- ------------------------------------------------------------
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matric_no VARCHAR(50) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    gender ENUM('male', 'female') DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- 3. Rooms Table
-- ------------------------------------------------------------
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(20) NOT NULL UNIQUE,
    capacity INT NOT NULL,
    occupied INT DEFAULT 0,
    status ENUM('available', 'full', 'under_maintenance') DEFAULT 'available'
);

-- ------------------------------------------------------------
-- 4. Allocations Table
-- ------------------------------------------------------------
CREATE TABLE allocations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    allocation_date DATE DEFAULT (CURRENT_DATE),
    status ENUM('pending', 'confirmed') DEFAULT 'pending',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- 5. Payments Table
-- ------------------------------------------------------------
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    allocation_id INT NOT NULL,
    receipt_number VARCHAR(50) NOT NULL UNIQUE,
    amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('unpaid', 'confirmed') DEFAULT 'unpaid',
    payment_date TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (allocation_id) REFERENCES allocations(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- 6. Maintenance Requests Table
-- ------------------------------------------------------------
CREATE TABLE maintenance_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    issue_description TEXT NOT NULL,
    status ENUM('pending', 'in_progress', 'resolved') DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- 7. Notices Table
-- ------------------------------------------------------------
CREATE TABLE notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    posted_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES admins(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- ✅ Optional Sample Admin Insert (for testing login)
-- ------------------------------------------------------------
INSERT INTO admins (full_name, email, password)
VALUES ('Hostel Admin', 'admin@hostel.com', '$2y$10$examplepasswordhash1234567890'); 
-- NOTE: Replace the password hash above with a real one from PHP's password_hash()

-- ------------------------------------------------------------
-- ✅ Notes
-- ------------------------------------------------------------
-- - Students log in with matric_no and password
-- - Admins manage rooms, payments, maintenance, and notices
-- - Foreign keys automatically delete related records when a user or room is deleted
-- ------------------------------------------------------------
