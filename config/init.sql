-- --------------------------------------------------
-- Database: skillbridge
-- --------------------------------------------------
CREATE DATABASE IF NOT EXISTS skillbridge;
USE skillbridge;

-- --------------------------------------------------
-- Table: users
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('student','freelancer','admin') DEFAULT 'freelancer',
    points INT DEFAULT 0,
    profile_pic VARCHAR(255),
    bio TEXT
);

-- --------------------------------------------------
-- Table: skills
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS skills (
    skill_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    skill_name VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- --------------------------------------------------
-- Table: tasks
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255),
    description TEXT,
    skill_required VARCHAR(50),
    status ENUM('open','assigned','completed') DEFAULT 'open',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    deadline DATE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- --------------------------------------------------
-- Table: applications
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS applications (
    app_id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT,
    freelancer_id INT,
    message TEXT,
    status ENUM('pending','accepted','rejected') DEFAULT 'pending',
    applied_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id) ON DELETE CASCADE,
    FOREIGN KEY (freelancer_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- --------------------------------------------------
-- Table: messages
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    task_id INT DEFAULT NULL,
    message TEXT NOT NULL,
    status ENUM('unread','read') DEFAULT 'unread',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id) ON DELETE SET NULL
);

-- --------------------------------------------------
-- Table: notifications
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS notifications (
    notif_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    message TEXT,
    status ENUM('unread','read') DEFAULT 'unread',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- --------------------------------------------------
-- Table: points_log
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS points_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    change_amount INT,
    reason VARCHAR(255),
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- --------------------------------------------------
-- Table: reviews
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

