/*To create DATABASE*/

CREATE DATABASE IF NOT EXISTS hostel;


/*Table for users*/

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

/*Table for hotels*/

CREATE TABLE `hostels` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `image4` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `rules` text DEFAULT NULL,
  `fee` decimal(10,2) NOT NULL,
  `gender` enum('Boys Hostel','Girls Hostel','Other') NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_by_role` enum('admin','superadmin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) 

/*Table for admin and super admin*/

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'superadmin') NOT NULL
);


INSERT INTO admins (username, password, role) VALUES
('admin1', 'admin', 'admin'),
('superadmin', 'superadmin', 'superadmin');