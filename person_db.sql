CREATE TABLE donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    mobile VARCHAR(15) NOT NULL,
    age INT NOT NULL,
    address TEXT NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blood_group VARCHAR(5) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
 ------------------------------------------------------------------------------------ or

CREATE DATABASE IF NOT EXISTS person_db;
USE person_db;

CREATE TABLE IF NOT EXISTS donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    mobile VARCHAR(15) NOT NULL UNIQUE,
    age INT NOT NULL CHECK (age BETWEEN 18 AND 65),
    email VARCHAR(255) NOT NULL UNIQUE,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-') NOT NULL,
    address TEXT NOT NULL,
    latitude DECIMAL(10, 7) NOT NULL,
    longitude DECIMAL(10, 7) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE donors DROP INDEX mobile;

ALTER TABLE donors DROP INDEX email;

ALTER TABLE donors ADD COLUMN landmark VARCHAR(255);
