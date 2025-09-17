
-- Create database
CREATE DATABASE IF NOT EXISTS photography_website;
USE photography_website;

-- Drop tables if they already exist
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS gallery;
DROP TABLE IF EXISTS inquiries;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(255),
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100),
    session_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create gallery table
CREATE TABLE gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    photo_title VARCHAR(100),
    photo_url VARCHAR(255),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create inquiries table
CREATE TABLE inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_name VARCHAR(100),
    message TEXT,
    received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data into users
INSERT INTO users (name, email, password, is_admin) VALUES
('Admin User', 'admin@example.com', 'admin123', 1),
('John Doe', 'john@example.com', 'john123', 0),
('Jane Smith', 'jane@example.com', 'jane123', 0);

-- Insert sample data into bookings
INSERT INTO bookings (client_name, session_date) VALUES
('Jane Smith', '2025-05-12'),
('John Doe', '2025-05-14');

-- Insert sample data into gallery
INSERT INTO gallery (photo_title, photo_url) VALUES
('Sunset Portrait', '/uploads/sunset.jpg'),
('Wedding Shot', '/uploads/wedding.jpg');

-- Insert sample data into inquiries
INSERT INTO inquiries (sender_name, message) VALUES
('Alex', 'Hi, I’d like to know more about wedding sessions.'),
('Sarah', 'Is there a discount for group shoots?');
