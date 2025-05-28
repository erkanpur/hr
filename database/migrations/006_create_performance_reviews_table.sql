CREATE TABLE IF NOT EXISTS performance_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    review_period_id INT NOT NULL,
    reviewer_id INT NULL, -- Can be NULL if self-review first or if assigned later
    review_date DATE NULL,
    overall_score DECIMAL(5,2) NULL,
    overall_manager_comments TEXT NULL,
    employee_self_assessment_comments TEXT NULL,
    strengths TEXT NULL,
    areas_for_improvement TEXT NULL,
    goals_for_next_period TEXT NULL,
    status ENUM('not_started', 'employee_input', 'manager_review', 'pending_acknowledgement', 'completed', 'archived') NOT NULL DEFAULT 'not_started',
    employee_acknowledged_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (review_period_id) REFERENCES performance_review_periods(id) ON DELETE RESTRICT,
    FOREIGN KEY (reviewer_id) REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_pr_employee_period (employee_id, review_period_id),
    INDEX idx_pr_status (status),
    INDEX idx_pr_reviewer (reviewer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
