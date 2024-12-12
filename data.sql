-- CREATE DATABASE FindHire;

-- USE FindHire;

-- CREATE TABLE users (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     username VARCHAR(50) UNIQUE NOT NULL,
--     password VARCHAR(255) NOT NULL,
--     role ENUM('HR', 'Applicant') NOT NULL,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );

-- CREATE TABLE job_posts (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     title VARCHAR(100) NOT NULL,
--     description TEXT NOT NULL,
--     created_by INT NOT NULL,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (created_by) REFERENCES users(id)
-- );

-- CREATE TABLE applications (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     job_id INT NOT NULL,
--     applicant_id INT NOT NULL,
--     resume_path VARCHAR(255) NOT NULL,
--     status ENUM('Pending', 'Accepted', 'Rejected') DEFAULT 'Pending',
--     message TEXT,
--     applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (job_id) REFERENCES job_posts(id),
--     FOREIGN KEY (applicant_id) REFERENCES users(id)
-- );

-- CREATE TABLE messages (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     sender_id INT NOT NULL,
--     receiver_id INT NOT NULL,
--     content TEXT NOT NULL,
--     sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (sender_id) REFERENCES users(id),
--     FOREIGN KEY (receiver_id) REFERENCES users(id)
-- );
-- aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
-- -- Create the database
-- CREATE DATABASE IF NOT EXISTS findhiredb;
-- USE findhiredb;

-- -- Create the users table
-- CREATE TABLE IF NOT EXISTS users (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     username VARCHAR(50) NOT NULL UNIQUE,
--     password VARCHAR(255) NOT NULL,
--     role ENUM('Applicant', 'HR') NOT NULL,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );

-- -- Create the job_posts table
-- CREATE TABLE IF NOT EXISTS job_posts (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     title VARCHAR(255) NOT NULL,
--     description TEXT NOT NULL,
--     created_by INT NOT NULL,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
-- );

-- -- Create the applications table
-- CREATE TABLE IF NOT EXISTS applications (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     job_id INT NOT NULL,
--     applicant_id INT NOT NULL,
--     resume_path VARCHAR(255) NOT NULL,
--     status ENUM('Pending', 'Accepted', 'Rejected') DEFAULT 'Pending',
--     message TEXT DEFAULT NULL,
--     submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (job_id) REFERENCES job_posts(id) ON DELETE CASCADE,
--     FOREIGN KEY (applicant_id) REFERENCES users(id) ON DELETE CASCADE
-- );

-- -- Create the messages table
-- CREATE TABLE IF NOT EXISTS messages (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     sender_id INT NOT NULL,
--     receiver_id INT NOT NULL,
--     content TEXT NOT NULL,
--     sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
--     FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
-- );

-- -- Create initial HR and Applicant users for testing
-- INSERT INTO users (username, password, role) VALUES
-- ('hradmin', '$2y$10$examplehashedpasswordHR', 'HR'),
-- ('applicant1', '$2y$10$examplehashedpasswordApp', 'Applicant');

-- INSERT INTO job_posts (title, description, created_by) VALUES
-- ('Software Engineer', 'We are looking for a skilled Software Engineer.', 1),
-- ('Data Analyst', 'Join our team as a Data Analyst.', 1);

-- ALTER TABLE applications
-- ADD COLUMN application_status ENUM('Pending', 'Accepted', 'Rejected') DEFAULT 'Pending';

-- INSERT INTO job_posts (title, description, created_by) VALUES
-- ('Test Job Title', 'Test job description.', 1);