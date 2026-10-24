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

INSERT INTO students (full_name, email, mobile, course, study_year, gender)
VALUES
('Aarav Patel','aarav.patel@example.com','9876543210','Information Technology',2,'Male'),
('Diya Shah','diya.shah@example.com','9876543211','Computer Science',1,'Female'),
('Rohan Mehta','rohan.mehta@example.com','9876543212','Computer Engineering',3,'Male')
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

INSERT INTO events (title, category, event_date, location, description, organizer)
SELECT * FROM (
    SELECT 'Web Development Workshop','Workshop','2026-11-05','Computer Lab 2','Practice building accessible web pages.','IT Department'
    UNION ALL SELECT 'Student Project Showcase','Exhibition','2026-11-18','Main Auditorium','Present student projects and ideas.','Innovation Cell'
    UNION ALL SELECT 'Python Coding Challenge','Competition','2026-11-09','Programming Lab','Solve programming problems with other students.','Coding Club'
) AS sample_events
WHERE NOT EXISTS (SELECT 1 FROM events LIMIT 1);
