ALTER TABLE employees
ADD COLUMN manager_id INT NULL DEFAULT NULL AFTER job_title,
ADD CONSTRAINT fk_employee_manager 
    FOREIGN KEY (manager_id) 
    REFERENCES employees(id) 
    ON DELETE SET NULL 
    ON UPDATE CASCADE;

-- Add an index for manager_id for better performance on lookups
CREATE INDEX idx_employee_manager_id ON employees(manager_id);
