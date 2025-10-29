CREATE DATABASE IF NOT EXISTS hms;
USE hms;

-- =========================
-- Users table (Admin, Doctor, Receptionist)
-- =========================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Hashed password
    role ENUM('admin', 'doctor', 'receptionist') NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- Doctors table (linked to users with role='doctor')
-- =========================
CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE, -- One-to-one link to users table
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    availability TEXT, -- Can store JSON or comma-separated slots
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- =========================
-- Patients table
-- =========================
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    contact VARCHAR(15) NOT NULL,
    address TEXT,
    medical_history TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- Appointments table
-- =========================
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NULL, -- allow NULL because of ON DELETE SET NULL
    appointment_date DATETIME NOT NULL,
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- =========================
-- Medical Records table
-- =========================
CREATE TABLE medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NULL, -- allow NULL because of ON DELETE SET NULL
    visit_date DATETIME NOT NULL,
    symptoms TEXT,
    diagnosis TEXT,
    prescription TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);


-- =========================
-- Sample Admin User
-- Replace the hash with a real one from password_hash()
-- =========================
INSERT INTO users (username, password, role, name) 
VALUES (
    'admin',
    '$2y$10$ReplaceThisWithRealBcryptHashFromPasswordHashFunction',
    'admin',
    'Admin User'
);
