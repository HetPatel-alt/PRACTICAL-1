USE studenthub;

ALTER TABLE students
    ADD COLUMN role ENUM('student','admin') NOT NULL DEFAULT 'student',
    ADD COLUMN remember_token_hash CHAR(64) DEFAULT NULL,
    ADD COLUMN remember_token_expires DATETIME DEFAULT NULL,
    ADD COLUMN last_login_at DATETIME DEFAULT NULL;

-- After importing or running this migration, assign an existing account as admin:
-- UPDATE students SET role = 'admin' WHERE email = 'your-admin-email@example.com';
