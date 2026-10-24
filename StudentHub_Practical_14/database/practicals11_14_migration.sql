USE studenthub;

ALTER TABLE events ADD COLUMN poster_filename VARCHAR(255) DEFAULT NULL;

CREATE TABLE IF NOT EXISTS audit_logs (
    audit_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(80) NOT NULL,
    details VARCHAR(500) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_created (created_at),
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES students(student_id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;
