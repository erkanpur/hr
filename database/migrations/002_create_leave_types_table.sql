CREATE TABLE IF NOT EXISTS leave_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    days_allocated_annually INT UNSIGNED NULL,
    is_paid BOOLEAN NOT NULL DEFAULT TRUE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional: Add some default leave types if desired for initial setup
-- INSERT INTO leave_types (name, description, days_allocated_annually, is_paid, is_active) VALUES
-- ('Annual Leave', 'Standard annual paid leave', 20, TRUE, TRUE),
-- ('Sick Leave', 'Leave taken due to illness', 10, TRUE, TRUE),
-- ('Unpaid Leave', 'Leave taken without pay', NULL, FALSE, TRUE);
