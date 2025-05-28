ALTER TABLE performance_reviews
ADD COLUMN employee_final_comments TEXT NULL DEFAULT NULL COMMENT 'Employee final comments upon acknowledgement' AFTER employee_acknowledged_at;
