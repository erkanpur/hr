CREATE TABLE IF NOT EXISTS review_ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    performance_review_id INT NOT NULL,
    criteria_id INT NOT NULL,
    rating_score_manager INT NULL,
    manager_comments_on_criteria TEXT NULL,
    rating_score_employee INT NULL,
    employee_self_assessment_comments_on_criteria TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (performance_review_id) REFERENCES performance_reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (criteria_id) REFERENCES review_criteria(id) ON DELETE RESTRICT,
    UNIQUE KEY review_criterion_unique (performance_review_id, criteria_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
