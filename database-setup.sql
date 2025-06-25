-- Create database
CREATE DATABASE IF NOT EXISTS real_madrid_fansite;
USE real_madrid_fansite;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    profile_image VARCHAR(255),
    bio TEXT,
    favorite_player VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- News table
CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    excerpt TEXT NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL,
    author VARCHAR(100) NOT NULL,
    date DATE NOT NULL,
    views INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    news_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Matches table
CREATE TABLE IF NOT EXISTS matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    competition VARCHAR(100) NOT NULL,
    home_team VARCHAR(100) NOT NULL,
    away_team VARCHAR(100) NOT NULL,
    home_team_logo VARCHAR(255) NOT NULL,
    away_team_logo VARCHAR(255) NOT NULL,
    match_date DATE NOT NULL,
    match_time VARCHAR(20) NOT NULL,
    stadium VARCHAR(100) NOT NULL,
    home_score INT,
    away_score INT,
    status ENUM('upcoming', 'live', 'completed') DEFAULT 'upcoming',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Match details table
CREATE TABLE IF NOT EXISTS match_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    home_possession INT,
    away_possession INT,
    home_shots INT,
    away_shots INT,
    home_shots_on_target INT,
    away_shots_on_target INT,
    home_corners INT,
    away_corners INT,
    home_fouls INT,
    away_fouls INT,
    home_yellow_cards INT,
    away_yellow_cards INT,
    home_red_cards INT,
    away_red_cards INT,
    match_summary TEXT,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE
);

-- Gallery table
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    caption TEXT,
    category VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Forum categories table
CREATE TABLE IF NOT EXISTS forum_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Forum topics table
CREATE TABLE IF NOT EXISTS forum_topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    views INT DEFAULT 0,
    is_sticky TINYINT(1) DEFAULT 0,
    is_locked TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_post_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES forum_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Forum posts table
CREATE TABLE IF NOT EXISTS forum_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES forum_topics(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Standings table
CREATE TABLE IF NOT EXISTS standings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(100) NOT NULL,
    team_logo VARCHAR(255),
    played INT DEFAULT 0,
    won INT DEFAULT 0,
    drawn INT DEFAULT 0,
    lost INT DEFAULT 0,
    goals_for INT DEFAULT 0,
    goals_against INT DEFAULT 0,
    goal_difference INT DEFAULT 0,
    points INT DEFAULT 0,
    position INT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Players table
CREATE TABLE IF NOT EXISTS players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    jersey_number INT,
    position VARCHAR(50),
    nationality VARCHAR(100),
    birth_date DATE,
    height INT,
    weight INT,
    image VARCHAR(255),
    bio TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create admin user
INSERT INTO users (name, email, password, role, created_at) 
VALUES ('Admin', 'admin@realmadridfanclub.com', '$2y$10$8mnOY0hU9QzIKGgvYPUVn.zVPgxUURNPj4.QYFgT2Nc6tcgONq3Pu', 'admin', NOW());
-- Default password: admin123

-- Insert forum categories
INSERT INTO forum_categories (name, description, display_order) VALUES
('General Discussion', 'General discussion about Real Madrid', 1),
('Match Discussion', 'Discuss upcoming and past matches', 2),
('Transfer Talk', 'Rumors and news about transfers', 3),
('Player Discussion', 'Talk about current and former players', 4),
('Fan Zone', 'Share your experiences as a fan', 5);

-- Insert standings data
INSERT INTO standings (team_name, team_logo, played, won, drawn, lost, goals_for, goals_against, goal_difference, points, position) VALUES
('Real Madrid', 'assets/images/real_madrid.png', 10, 8, 1, 1, 25, 8, 17, 25, 1),
('Barcelona', 'assets/images/barcelona.png', 10, 7, 2, 1, 22, 10, 12, 23, 2),
('Atletico Madrid', 'assets/images/atletico.png', 10, 6, 2, 2, 18, 9, 9, 20, 3),
('Sevilla', 'assets/images/sevilla.png', 10, 5, 3, 2, 15, 10, 5, 18, 4),
('Real Sociedad', 'assets/images/sociedad.png', 10, 5, 2, 3, 14, 11, 3, 17, 5),
('Villarreal', 'assets/images/villarreal.png', 10, 4, 4, 2, 16, 13, 3, 16, 6),
('Athletic Bilbao', 'assets/images/athletic.png', 10, 4, 3, 3, 13, 11, 2, 15, 7),
('Real Betis', 'assets/images/betis.png', 10, 4, 3, 3, 12, 12, 0, 15, 8),
('Valencia', 'assets/images/valencia.png', 10, 3, 4, 3, 11, 12, -1, 13, 9),
('Osasuna', 'assets/images/osasuna.png', 10, 3, 3, 4, 10, 12, -2, 12, 10);

-- Insert players data
INSERT INTO players (name, jersey_number, position, nationality, birth_date, height, weight, image) VALUES
('Thibaut Courtois', 1, 'Goalkeeper', 'Belgium', '1992-05-11', 199, 96, 'assets/images/players/courtois.jpg'),
('Dani Carvajal', 2, 'Defender', 'Spain', '1992-01-11', 173, 73, 'assets/images/players/carvajal.jpg'),
('Éder Militão', 3, 'Defender', 'Brazil', '1998-01-18', 186, 78, 'assets/images/players/militao.jpg'),
('David Alaba', 4, 'Defender', 'Austria', '1992-06-24', 180, 78, 'assets/images/players/alaba.jpg'),
('Jude Bellingham', 5, 'Midfielder', 'England', '2003-06-29', 186, 75, 'assets/images/players/bellingham.jpg'),
('Luka Modrić', 10, 'Midfielder', 'Croatia', '1985-09-09', 172, 66, 'assets/images/players/modric.jpg'),
('Vinícius Júnior', 7, 'Forward', 'Brazil', '2000-07-12', 176, 73, 'assets/images/players/vinicius.jpg'),
('Kylian Mbappé', 9, 'Forward', 'France', '1998-12-20', 178, 73, 'assets/images/players/mbappe.jpg');