-- ==============================================================
-- StudentHub Portal Database Schema & Seed Data
-- Subject: Web Development Frameworks (ITUE203) - Semester 3
-- Practical 8, 9, 10, 11, 12, 14
-- Compatible with XAMPP MySQL / phpMyAdmin
-- ==============================================================

CREATE DATABASE IF NOT EXISTS studenthub;
USE studenthub;

-- 1. Table: users (Practical 8, 9, 10)
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Table: students (Practical 8, 11 - Minimum 20 Seeded Records)
DROP TABLE IF EXISTS students;
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    course VARCHAR(50) NOT NULL,
    year VARCHAR(20) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Table: events (Practical 8, 12, 13)
DROP TABLE IF EXISTS events;
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(50) NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(100) NOT NULL,
    poster VARCHAR(255) DEFAULT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Table: registrations (Practical 8, 14)
DROP TABLE IF EXISTS registrations;
CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    student_email VARCHAR(100) NOT NULL,
    event_id INT NOT NULL,
    event_title VARCHAR(100) NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Table: audit_logs (Practical 14)
DROP TABLE IF EXISTS audit_logs;
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(150) NOT NULL,
    username VARCHAR(50) NOT NULL,
    role VARCHAR(20) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================
-- SEED DATA
-- ==============================================================

-- Seed Users (Passwords: admin123, student123, manali123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@studenthub.com', '\\.y8/I5MQ8CA3HKF0j6aes7Pccon2lG.ZxAjZPlc5JfHfVk6kFzm', 'admin'),
('student', 'student@studenthub.com', '\\.HXqukjbVMO25EHrrB.2gVCzm2qdvQFE/qerdY5vT2', 'student'),
('manali', 'manali@studenthub.com', '\\.HXqukjbVMO25EHrrB.2gVCzm2qdvQFE/qerdY5vT2', 'student'),
('rohit', 'rohit.sharma@studenthub.com', '\\.HXqukjbVMO25EHrrB.2gVCzm2qdvQFE/qerdY5vT2', 'student'),
('priya', 'priya.patel@studenthub.com', '\\.HXqukjbVMO25EHrrB.2gVCzm2qdvQFE/qerdY5vT2', 'student');

-- Seed Students: Minimum 20 records (Practical 11 requirement)
INSERT INTO students (student_id, name, email, mobile, course, year, gender) VALUES
('ST202601', 'Manali Patel', 'manali@studenthub.com', '9876543201', 'Computer Engineering', '2nd Year', 'Female'),
('ST202602', 'Aarav Sharma', 'aarav.sharma@studenthub.com', '9876543202', 'IT Engineering', '2nd Year', 'Male'),
('ST202603', 'Diya Mehta', 'diya.mehta@studenthub.com', '9876543203', 'AI & ML', '1st Year', 'Female'),
('ST202604', 'Rohan Verma', 'rohan.verma@studenthub.com', '9876543204', 'Computer Engineering', '3rd Year', 'Male'),
('ST202605', 'Ananya Shah', 'ananya.shah@studenthub.com', '9876543205', 'IT Engineering', '2nd Year', 'Female'),
('ST202606', 'Kabir Joshi', 'kabir.joshi@studenthub.com', '9876543206', 'Mechanical Engineering', '4th Year', 'Male'),
('ST202607', 'Isha Trivedi', 'isha.trivedi@studenthub.com', '9876543207', 'Civil Engineering', '1st Year', 'Female'),
('ST202608', 'Devansh Desai', 'devansh.desai@studenthub.com', '9876543208', 'Computer Engineering', '2nd Year', 'Male'),
('ST202609', 'Khushi Dave', 'khushi.dave@studenthub.com', '9876543209', 'AI & ML', '3rd Year', 'Female'),
('ST202610', 'Yash Parmar', 'yash.parmar@studenthub.com', '9876543210', 'IT Engineering', '4th Year', 'Male'),
('ST202611', 'Pooja Bhatt', 'pooja.bhatt@studenthub.com', '9876543211', 'Computer Engineering', '1st Year', 'Female'),
('ST202612', 'Siddharth Rao', 'siddharth.rao@studenthub.com', '9876543212', 'Mechanical Engineering', '2nd Year', 'Male'),
('ST202613', 'Neha Gupta', 'neha.gupta@studenthub.com', '9876543213', 'IT Engineering', '3rd Year', 'Female'),
('ST202614', 'Aryan Solanki', 'aryan.solanki@studenthub.com', '9876543214', 'Civil Engineering', '2nd Year', 'Male'),
('ST202615', 'Tanvi Choksi', 'tanvi.choksi@studenthub.com', '9876543215', 'AI & ML', '2nd Year', 'Female'),
('ST202616', 'Harsh Vora', 'harsh.vora@studenthub.com', '9876543216', 'Computer Engineering', '4th Year', 'Male'),
('ST202617', 'Riddhi Prajapati', 'riddhi.prajapati@studenthub.com', '9876543217', 'IT Engineering', '1st Year', 'Female'),
('ST202618', 'Vivek Soni', 'vivek.soni@studenthub.com', '9876543218', 'Computer Engineering', '3rd Year', 'Male'),
('ST202619', 'Mitali Rathod', 'mitali.rathod@studenthub.com', '9876543219', 'AI & ML', '1st Year', 'Female'),
('ST202620', 'Karan Makwana', 'karan.makwana@studenthub.com', '9876543220', 'Mechanical Engineering', '3rd Year', 'Male'),
('ST202621', 'Jiya Panchal', 'jiya.panchal@studenthub.com', '9876543221', 'Civil Engineering', '4th Year', 'Female');

-- Seed Events (Practical 12, 13)
INSERT INTO events (title, description, category, event_date, venue, poster, status) VALUES
('Web Development Workshop', 'Hands-on practical session covering HTML5, CSS Grid, Flexbox, and PHP backend integration.', 'Workshop', '2026-10-25', 'Auditorium Hall A', 'event1.jpg', 'Upcoming'),
('Student Innovation Expo', 'Annual exhibition showcasing student tech projects, software models, and research posters.', 'Exhibition', '2026-11-05', 'Campus Grounds', 'event2.jpg', 'Upcoming'),
('AI & Machine Learning Seminar', 'Expert talk by industry leaders on the role of machine learning in modern engineering.', 'Seminar', '2026-11-12', 'Seminar Hall 2', 'event3.jpg', 'Upcoming'),
('CodeSprint Hackathon', '24-hour inter-college coding competition with exciting prizes and mentorship.', 'Competition', '2026-11-20', 'Computer Lab 3', 'event4.jpg', 'Upcoming'),
('Career Guidance Seminar', 'Session on internship opportunities, resume building, and placement preparation.', 'Seminar', '2026-12-02', 'Main Auditorium', 'event5.jpg', 'Upcoming'),
('Sports Meet 2026', 'Inter-departmental cricket, volleyball, badminton, and athletics tournaments.', 'Sports', '2026-12-15', 'Sports Complex', 'event6.jpg', 'Upcoming');

-- Seed Registrations (Practical 8, 14)
INSERT INTO registrations (student_name, student_email, event_id, event_title) VALUES
('Manali Patel', 'manali@studenthub.com', 1, 'Web Development Workshop'),
('Aarav Sharma', 'aarav.sharma@studenthub.com', 1, 'Web Development Workshop'),
('Diya Mehta', 'diya.mehta@studenthub.com', 2, 'Student Innovation Expo'),
('Rohan Verma', 'rohan.verma@studenthub.com', 3, 'AI & Machine Learning Seminar'),
('Ananya Shah', 'ananya.shah@studenthub.com', 4, 'CodeSprint Hackathon');

-- Seed Audit Logs (Practical 14)
INSERT INTO audit_logs (action, username, role) VALUES
('Admin logged in', 'admin', 'admin'),
('Added new event: Web Development Workshop', 'admin', 'admin'),
('Added student: Manali Patel', 'admin', 'admin'),
('Exported student records', 'admin', 'admin'),
('System database initialized', 'admin', 'admin');
