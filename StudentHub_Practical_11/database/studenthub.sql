CREATE DATABASE IF NOT EXISTS studenthub
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE studenthub;

CREATE TABLE IF NOT EXISTS students (
    student_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mobile VARCHAR(16) NOT NULL,
    course VARCHAR(100) NOT NULL,
    study_year TINYINT UNSIGNED NOT NULL,
    gender VARCHAR(30) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student','admin') NOT NULL DEFAULT 'student',
    remember_token_hash CHAR(64) DEFAULT NULL,
    remember_token_expires DATETIME DEFAULT NULL,
    last_login_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_students_year CHECK (study_year BETWEEN 1 AND 4)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS events (
    event_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(60) NOT NULL,
    event_date DATE NOT NULL,
    location VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    organizer VARCHAR(120) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS registrations (
    registration_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    registration_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_registrations_student
        FOREIGN KEY (student_id) REFERENCES students(student_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    UNIQUE KEY uq_student_registration (student_id)
) ENGINE=InnoDB;

INSERT INTO students (full_name, email, mobile, course, study_year, gender, password_hash)
VALUES
('Aarav Patel','aarav.patel@example.com','9876543210','Information Technology',2,'Male','$2y$10$7EqJtq98hPqEX7fNZaFWoO5hY4oY1P4t1n8VZ7Q5Q7Qf3f4v8kS9K'),
('Diya Shah','diya.shah@example.com','9876543211','Computer Science',1,'Female','$2y$10$7EqJtq98hPqEX7fNZaFWoO5hY4oY1P4t1n8VZ7Q5Q7Qf3f4v8kS9K'),
('Rohan Mehta','rohan.mehta@example.com','9876543212','Computer Engineering',3,'Male','$2y$10$7EqJtq98hPqEX7fNZaFWoO5hY4oY1P4t1n8VZ7Q5Q7Qf3f4v8kS9K')
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

INSERT INTO events (title, category, event_date, location, description, organizer)
SELECT * FROM (
    SELECT 'Web Development Workshop','Workshop','2026-11-05','Computer Lab 2','Practice building accessible web pages.','IT Department'
    UNION ALL SELECT 'Student Project Showcase','Exhibition','2026-11-18','Main Auditorium','Present student projects and ideas.','Innovation Cell'
    UNION ALL SELECT 'Python Coding Challenge','Competition','2026-11-09','Programming Lab','Solve programming problems with other students.','Coding Club'
) AS sample_events
WHERE NOT EXISTS (SELECT 1 FROM events LIMIT 1);

-- Practicals 11–14 additions.
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

-- Practical 11 requirement: at least 20 sample student records.
INSERT IGNORE INTO students (full_name,email,mobile,course,study_year,gender,password_hash,role) VALUES
('Kavya Desai','kavya.desai@example.com','9876543213','Information Technology',1,'Female','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Arjun Shah','arjun.shah@example.com','9876543214','Computer Science',2,'Male','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Mira Joshi','mira.joshi@example.com','9876543215','Computer Engineering',3,'Female','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Dev Patel','dev.patel@example.com','9876543216','Information Technology',4,'Male','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Anaya Mehta','anaya.mehta@example.com','9876543217','Computer Science',1,'Female','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Vivaan Trivedi','vivaan.trivedi@example.com','9876543218','Computer Engineering',2,'Male','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Isha Parmar','isha.parmar@example.com','9876543219','Information Technology',3,'Female','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Krish Dave','krish.dave@example.com','9876543220','Computer Science',4,'Male','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Riya Shah','riya.shah@example.com','9876543221','Computer Engineering',1,'Female','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Aditya Joshi','aditya.joshi@example.com','9876543222','Information Technology',2,'Male','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Sara Khan','sara.khan@example.com','9876543223','Computer Science',3,'Female','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Dhruv Patel','dhruv.patel@example.com','9876543224','Computer Engineering',4,'Male','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Nisha Rao','nisha.rao@example.com','9876543225','Information Technology',1,'Female','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Kabir Mehta','kabir.mehta@example.com','9876543226','Computer Science',2,'Male','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Pooja Desai','pooja.desai@example.com','9876543227','Computer Engineering',3,'Female','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Yash Shah','yash.shah@example.com','9876543228','Information Technology',4,'Male','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Tara Joshi','tara.joshi@example.com','9876543229','Computer Science',1,'Female','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Neil Patel','neil.patel@example.com','9876543230','Computer Engineering',2,'Male','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Aditi Mehta','aditi.mehta@example.com','9876543231','Information Technology',3,'Female','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student'),
('Reyansh Dave','reyansh.dave@example.com','9876543232','Computer Science',4,'Male','$2y$10$notARealLoginHashForSeededStudents000000000000000000000000000000','student');
