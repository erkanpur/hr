CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone_number VARCHAR(20) NULL,
    job_title VARCHAR(100) NOT NULL,
    department_id INT NULL, -- Will be a foreign key later
    hire_date DATE NOT NULL,
    salary DECIMAL(10, 2) NULL,
    address TEXT NULL,
    date_of_birth DATE NULL,
    emergency_contact_name VARCHAR(200) NULL,
    emergency_contact_phone VARCHAR(20) NULL,
    status ENUM('active', 'on_leave', 'terminated') NOT NULL DEFAULT 'active',
    profile_picture_path VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    -- CONSTRAINT fk_department FOREIGN KEY (department_id) REFERENCES departments(id) -- Add when departments table is created
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
