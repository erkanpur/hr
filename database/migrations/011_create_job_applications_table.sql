CREATE TABLE IF NOT EXISTS job_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_posting_id INT NOT NULL,
    candidate_first_name VARCHAR(100) NOT NULL,
    candidate_last_name VARCHAR(100) NOT NULL,
    candidate_email VARCHAR(255) NOT NULL,
    candidate_phone VARCHAR(30) NULL,
    resume_path VARCHAR(255) NOT NULL,
    cover_letter_path VARCHAR(255) NULL,
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM(
        'received', 'under_review', 'shortlisted', 
        'interview_scheduled', 'interview_completed', 
        'assessment_sent', 'assessment_completed', 
        'reference_check', 'offer_extended', 'offer_accepted', 
        'offer_declined', 'hired', 'rejected', 'withdrawn_by_candidate'
    ) NOT NULL DEFAULT 'received',
    notes_by_recruiter TEXT NULL,
    rejection_reason TEXT NULL,
    expected_salary VARCHAR(100) NULL,
    source VARCHAR(100) NULL, -- e.g., 'Company Website', 'LinkedIn', 'Referral'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_posting_id) REFERENCES job_postings(id) ON DELETE CASCADE, 
    INDEX idx_ja_job_posting_id (job_posting_id),
    INDEX idx_ja_candidate_email (candidate_email),
    INDEX idx_ja_status (status),
    UNIQUE KEY job_candidate_email_unique (job_posting_id, candidate_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
