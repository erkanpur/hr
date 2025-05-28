CREATE TABLE IF NOT EXISTS leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    leave_type_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    days_requested DECIMAL(4,1) NOT NULL, -- This will need to be calculated by the application logic
    reason TEXT NULL,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    comments_by_approver TEXT NULL,
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    action_taken_by_user_id INT NULL, -- Can be an employee ID (manager/admin)
    action_taken_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Keep separate from requested_at for record creation time
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE, -- Or ON DELETE RESTRICT based on policy
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (action_taken_by_user_id) REFERENCES employees(id) ON DELETE SET NULL -- Approver account might be an employee
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
