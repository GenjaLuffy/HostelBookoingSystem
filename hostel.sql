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

CREATE TABLE hostels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image VARCHAR(255),
    image2 VARCHAR(255),
    image3 VARCHAR(255),
    image4 VARCHAR(255),
    description TEXT,
    amenities TEXT,
    rules TEXT,
    fee DECIMAL(10,2) NOT NULL,
    gender ENUM('Boys Hostel', 'Girls Hostel', 'Other') NOT NULL,
    location VARCHAR(255),
    created_by INT NOT NULL,
    created_by_role ENUM('admin', 'superadmin') NOT NULL,
    status ENUM('Pending', 'Approved') NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_hostel_creator FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE CASCADE
);


/*Table for admin and super admin*/

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    dob DATE,
    phone VARCHAR(15),
    address TEXT,
    gender ENUM('male', 'female', 'other'),
    profile_picture VARCHAR(255),
    type ENUM('admin', 'superadmin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admins (name, username, email, password, dob, phone, address, gender, profile_picture, type) VALUES 
('Sudichchha Shretha','sudichha12','sudichchha12@gmail.com','$2y$10$HX9CsMyOKJoLlvlz/w5LnOR5I217tze.1iCmT1fWZdmkn507.av.G','2002-08-12','9876543210','Kathmandu','Female','','superadmin'),
('Binita Magar','binita12','binita12@gmail.com','$2y$10$z0sNZD.rwxqKne7ZH16JAeupl4w0EwSb3c1x6Yrm7dQ.ZfBzPZDvK','2002-12-26','9876542587','Kathmandu','Female','','superadmin');

/*admin login email and password

username = sudichha12
email = sudichchha12@gmail.com
password = sudichha

username = binita12
email = binita12@gmail.com
password = binita

*/




/*table for booking*/


CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seater INT,
    room_no VARCHAR(10),
    food_status ENUM('With Food', 'Without Food'),
    stay_from DATE,
    stay_duration INT,
    fee_per_month DECIMAL(10, 2),
    full_name VARCHAR(100),
    gender ENUM('Male', 'Female', 'Other'),
    contact_no VARCHAR(15),
    guardian_name VARCHAR(100),
    guardian_contact_no VARCHAR(15),
    image VARCHAR(255),
    corr_address TEXT,
    corr_city VARCHAR(100),
    corr_district VARCHAR(100),
    perm_address TEXT,
    perm_city VARCHAR(100),
    perm_district VARCHAR(100),
    user_id INT,
    hostel_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key constraints (optional but recommended)
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_hostel FOREIGN KEY (hostel_id) REFERENCES hostels(id)
);


/*Table for add rooms*/

CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_no VARCHAR(20) NOT NULL,
    seater INT NOT NULL,
    fee_per_student DECIMAL(10, 2) NOT NULL,
    admin_id INT NOT NULL,
    hostel_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_admin_room FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
    CONSTRAINT fk_hostel_room FOREIGN KEY (hostel_id) REFERENCES hostels(id) ON DELETE CASCADE
);
