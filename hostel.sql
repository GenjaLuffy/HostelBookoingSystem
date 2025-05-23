

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
    user_id INT NOT NULL,
    hostel_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_user_room FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_hostel_room FOREIGN KEY (hostel_id) REFERENCES hostels(id)
);
