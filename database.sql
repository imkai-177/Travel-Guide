CREATE DATABASE IF NOT EXISTS travelGuide;
USE travelGuide;

DROP TABLE IF EXISTS trip_places;
DROP TABLE IF EXISTS trips;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS guide_requests;
DROP TABLE IF EXISTS guide_profiles;
DROP TABLE IF EXISTS cost_estimates;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS wishlist;
DROP TABLE IF EXISTS post_requests;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS users;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    is_verified TINYINT(1) NOT NULL DEFAULT 0,
    profile_picture VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    scout_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    short_history TEXT NOT NULL,
    country VARCHAR(100) NOT NULL,
    genre VARCHAR(50) NOT NULL,
    cost_level VARCHAR(20) NOT NULL,
    travel_medium_info VARCHAR(255) NOT NULL,
    images TEXT,
    status VARCHAR(20) NOT NULL DEFAULT 'approved',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (scout_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS post_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    scout_id INT NOT NULL,
    post_data LONGTEXT NOT NULL,
    original_post_id INT DEFAULT NULL,
    is_change_request TINYINT(1) NOT NULL DEFAULT 0,
    requested_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    FOREIGN KEY (scout_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wishlist (user_id,post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS cost_estimates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    base_cost DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'BDT',
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS trips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    trip_name VARCHAR(150) NOT NULL,
    start_date DATE,
    end_date DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS trip_places (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    post_id INT NOT NULL,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS guide_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    bio TEXT,
    location VARCHAR(150),
    phone VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS guide_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    traveler_id INT NOT NULL,
    guide_id INT NOT NULL,
    message TEXT,
    status VARCHAR(20) DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (traveler_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (guide_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_name VARCHAR(150) NOT NULL,
    service_type VARCHAR(100),
    description TEXT,
    price DECIMAL(10,2),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    booking_date DATE,
    status VARCHAR(20) DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT,
    post_id INT,
    rating INT,
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT IGNORE INTO users
(name,email,password,role,is_verified)
VALUES
('Admin','admin@travel.com','1234','admin',1);

INSERT IGNORE INTO users
(name,email,password,role,is_verified)
VALUES
('Kayes','kayesalmoon84@gmail.com','1234','user',1);

INSERT INTO posts
(scout_id,title,short_history,country,genre,cost_level,travel_medium_info,images,status)
SELECT id,'Coxs Bazar','A popular sea beach destination in Bangladesh. It is known for its long natural sandy beach and beautiful sunset.','Bangladesh','beach','medium','Bus, train or flight','coxs_bazar_1.jpg,coxs_bazar_2.jpg,coxs_bazar_3.jpg','approved'
FROM users WHERE email='admin@travel.com' LIMIT 1;

INSERT INTO posts
(scout_id,title,short_history,country,genre,cost_level,travel_medium_info,images,status)
SELECT id,'Bandarban','A beautiful hill destination in Bangladesh known for green mountains, winding roads and peaceful natural surroundings.','Bangladesh','mountain','low','Bus','bandarban_1.jpg,bandarban_2.jpg,bandarban_3.jpg','approved'
FROM users WHERE email='admin@travel.com' LIMIT 1;

INSERT INTO posts
(scout_id,title,short_history,country,genre,cost_level,travel_medium_info,images,status)
SELECT id,'Sundarban','The Sundarban is a unique mangrove forest in Bangladesh, famous for its rivers, wildlife and Royal Bengal Tigers.','Bangladesh','nature','medium','Bus and boat','sundarban_1.jpg,sundarban_2.jpg,sundarban_3.jpg','approved'
FROM users WHERE email='admin@travel.com' LIMIT 1;
