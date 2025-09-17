
-- SQL Dump for photography_website users table
-- Generated on 2025-05-02 16:04:32

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Optional: Insert an initial admin user (change email and password later)
-- Password: admin123 (hashed)
INSERT INTO users (email, password, is_admin) VALUES (
    'admin@example.com',
    '$2y$10$e0NRzC.FbMv8LPpGRB0peuF6tAZ8s.H7PlB16Zkrdz2uvWXcTboCG',
    1
);
