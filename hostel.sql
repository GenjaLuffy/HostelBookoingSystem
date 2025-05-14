//To create DATABASE

CREATE DATABASE IF NOT EXISTS hostel_booking_system;


//Table for users

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    username VARCHAR(100) UNIQUE,
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    dob DATE,
    phone VARCHAR(15),
    address TEXT,
    gender ENUM('male', 'female', 'other'),
    type ENUM('student', 'business'),
    profile_picture VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
