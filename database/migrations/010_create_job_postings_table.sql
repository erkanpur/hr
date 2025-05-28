CREATE TABLE IF NOT EXISTS job_postings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    department_name VARCHAR(150) NULL, -- Consider FK to departments table later
    location VARCHAR(255) NULL,
    employment_type ENUM('full_time', 'part_time', 'contract', 'internship') NULL DEFAULT 'full_time',
    salary_range VARCHAR(100) NULL,
    status ENUM('draft', 'open', 'closed', 'archived') NOT NULL DEFAULT 'draft',
    posted_date DATE NULL,
    closing_date DATE NULL,
    created_by_employee_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by_employee_id) REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_jp_status (status),
    INDEX idx_jp_department_name (department_name),
    INDEX idx_jp_employment_type (employment_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
