CREATE DATABASE IF NOT EXISTS `orchidcircle` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `orchidcircle`;
CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS profiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  image VARCHAR(255),
  rate_per_hour DECIMAL(8,2) DEFAULT 0,
  status ENUM('Available','Unavailable') DEFAULT 'Available'
);

CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  profile_id INT NOT NULL,
  customer_name VARCHAR(150),
  booking_date DATE,
  hours INT,
  message TEXT,
  status ENUM('Pending','Approved','Rejected','Completed') DEFAULT 'Pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_type ENUM('user','admin') NOT NULL,
  sender_name VARCHAR(150),
  profile_id INT DEFAULT NULL,
  message TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vip_codes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(100) NOT NULL UNIQUE,
  status ENUM('active','inactive') DEFAULT 'active'
);

CREATE TABLE IF NOT EXISTS logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  action TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
