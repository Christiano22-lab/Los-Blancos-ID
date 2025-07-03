<<<<<<< HEAD
-- Database setup untuk Los Blancos ID
CREATE DATABASE IF NOT EXISTS los_blancos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE los_blancos_db;

-- Tabel users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100),
    role ENUM('admin', 'user') DEFAULT 'user',
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel news
CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    excerpt TEXT,
    content TEXT NOT NULL,
    image VARCHAR(255),
    category VARCHAR(100),
    author VARCHAR(100),
    author_id INT,
    views INT DEFAULT 0,
    date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabel matches (dengan FK ke users dan teams)
=======
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
>>>>>>> 3f9dcde68acb56b96a6b5a0664e4b76626655a50
CREATE TABLE IF NOT EXISTS matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    competition VARCHAR(100) NOT NULL,
    home_team VARCHAR(100) NOT NULL,
    away_team VARCHAR(100) NOT NULL,
<<<<<<< HEAD
    home_team_logo VARCHAR(255),
    away_team_logo VARCHAR(255),
    match_date DATETIME NOT NULL,
    match_time TIME,
    stadium VARCHAR(100),
    venue VARCHAR(100),
    home_score INT DEFAULT NULL,
    away_score INT DEFAULT NULL,
    status ENUM('scheduled', 'live', 'finished') DEFAULT 'scheduled',
    created_by INT,
    home_team_id INT,
    away_team_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (home_team_id) REFERENCES teams(id) ON DELETE RESTRICT,
    FOREIGN KEY (away_team_id) REFERENCES teams(id) ON DELETE RESTRICT
);

-- Tabel comments
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    news_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- Tabel gallery (dengan FK ke users)
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(255) NOT NULL,
    thumbnail_url VARCHAR(255),
    category VARCHAR(50),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert admin user default
INSERT IGNORE INTO users (username, email, password, name, role) 
VALUES ('admin', 'admin@losblancos.com', MD5('admin123'), 'Administrator', 'admin');

-- Insert sample user: Jane Smith
INSERT INTO users (username, email, password, name, role, created_at)
VALUES (
  'janesmith','jane@example.com','$2y$10$f.8DNECiMtN9Qn479he2I.dsSoGSK58blxrpkYZlRdnNN4Hc2nirm','Jane Smith','user',NOW());

-- Insert sample news
INSERT IGNORE INTO news (title, excerpt, content, category, author, date, created_at) VALUES 
('Bellingham Cetak Hat-trick di El Clásico', 
 'Performa luar biasa Jude Bellingham membawa Real Madrid meraih kemenangan telak atas Barcelona.',
 'Dalam pertunjukan yang memukau, Jude Bellingham mencetak tiga gol untuk membawa Real Madrid meraih kemenangan meyakinkan atas Barcelona di El Clásico terbaru. Gelandang asal Inggris ini tak terbendung sepanjang pertandingan.',
 'Match Review', 'Carlos Mendoza', CURDATE() - INTERVAL 2 DAY, NOW()),

('Real Madrid Lolos ke Semifinal Liga Champions',
 'Gol dramatis di menit akhir mengamankan tempat Real Madrid di semifinal Liga Champions.',
 'Real Madrid sekali lagi menunjukkan mentalitas Liga Champions mereka dengan kemenangan dramatis yang membawa mereka ke semifinal. Gol di menit-menit akhir menjadi penentu kemenangan.',
 'Champions League', 'Maria Rodriguez', CURDATE() - INTERVAL 5 DAY, NOW()),

('Rumor Transfer: Madrid Incar Bintang Premier League',
 'Laporan menyebutkan Real Madrid sedang mempersiapkan tawaran untuk gelandang Premier League.',
 'Menurut berbagai sumber yang dekat dengan klub, Real Madrid sedang mempersiapkan tawaran substansial untuk salah satu gelandang terbaik Premier League yang dipandang cocok dengan gaya bermain Madrid.',
 'Transfers', 'James Wilson', CURDATE() - INTERVAL 7 DAY, NOW());

-- Insert sample matches
INSERT IGNORE INTO matches (competition, home_team, away_team, match_date, venue, status) VALUES 
('La Liga', 'Real Madrid', 'Sevilla', DATE_ADD(CURDATE(), INTERVAL 3 DAY) + INTERVAL 20 HOUR, 'Santiago Bernabéu', 'scheduled'),
('Champions League', 'Bayern Munich', 'Real Madrid', DATE_ADD(CURDATE(), INTERVAL 8 DAY) + INTERVAL 21 HOUR, 'Allianz Arena', 'scheduled'),
('La Liga', 'Real Madrid', 'Barcelona', DATE_SUB(CURDATE(), INTERVAL 3 DAY) + INTERVAL 20 HOUR, 'Santiago Bernabéu', 'finished');

-- Update finished match with scores
UPDATE matches SET home_score = 3, away_score = 1 WHERE home_team = 'Real Madrid' AND away_team = 'Barcelona' AND status = 'finished';

-- Create indexes for better performance
CREATE INDEX idx_news_created_at ON news(created_at);
CREATE INDEX idx_news_category ON news(category);
CREATE INDEX idx_matches_date ON matches(match_date);
CREATE INDEX idx_matches_status ON matches(status);
CREATE INDEX idx_comments_news_id ON comments(news_id);

-- Tabel Communities
CREATE TABLE IF NOT EXISTS communities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    platform ENUM('instagram', 'whatsapp', 'facebook', 'telegram', 'discord') NOT NULL,
    link VARCHAR(500) NOT NULL,
    image VARCHAR(255),
    member_count INT DEFAULT 0,
    is_official TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL

);

-- Insert sample gallery data
INSERT INTO gallery (title, description, image_url, category) VALUES 
('Santiago Bernabéu Stadium', 'Stadion ikonik Real Madrid', 'assets/images/gallery/santiago-bernabeu.jpg', 'Stadium'),
('Champions League Celebration', 'Perayaan kemenangan Liga Champions', 'assets/images/gallery/champions-celebration.jpg', 'Celebrations'),
('El Clásico Victory', 'Kemenangan atas Barcelona', 'assets/images/gallery/clasico-victory.jpg', 'Matches'),
('Team Training', 'Sesi latihan tim', 'assets/images/gallery/training-session.jpg', 'Training'),
('Madridista Fans', 'Para penggemar setia', 'assets/images/gallery/madridista-fans.jpg', 'Fans');

-- Insert sample communities data
INSERT INTO communities (name, description, platform, link, member_count, is_official) VALUES 
('Los Blancos ID Official', 'Komunitas resmi Los Blancos Indonesia di Instagram', 'instagram', 'https://instagram.com/losblancosid_official', 15420, 1),
('Madridista Indonesia', 'Grup WhatsApp diskusi Real Madrid Indonesia', 'whatsapp', 'https://chat.whatsapp.com/invite/madridista-indonesia', 2847, 1),
('Real Madrid Fans Indonesia', 'Komunitas Facebook penggemar Real Madrid Indonesia', 'facebook', 'https://facebook.com/groups/realmadridfansindonesia', 8932, 1),
('Madridista Jakarta', 'Komunitas Madridista Jakarta', 'whatsapp', 'https://chat.whatsapp.com/invite/madridista-jakarta', 1256, 0),
('Hala Madrid Indonesia', 'Grup Telegram Real Madrid Indonesia', 'telegram', 'https://t.me/halamadridindonesia', 3421, 0);

-- Tabel media_videos (dengan FK ke users)
CREATE TABLE IF NOT EXISTS media_videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    youtube_id VARCHAR(50) NOT NULL,
    embed_url VARCHAR(500) NOT NULL,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert sample videos
INSERT INTO media_videos (title, description, youtube_id, embed_url, created_at) VALUES 
('Real Madrid vs Barcelona - El Clasico Highlights', 'Highlight pertandingan El Clasico terbaru dengan gol-gol spektakuler', 'dQw4w9WgXcQ', 'https://www.youtube.com/embed/dQw4w9WgXcQ', NOW()),
('Bellingham Best Goals 2024', 'Kompilasi gol-gol terbaik Jude Bellingham musim ini', 'dQw4w9WgXcQ', 'https://www.youtube.com/embed/dQw4w9WgXcQ', NOW()),
('Real Madrid Training Session', 'Sesi latihan tim di Valdebebas sebelum pertandingan besar', 'dQw4w9WgXcQ', 'https://www.youtube.com/embed/dQw4w9WgXcQ', NOW()),
('Vinicius Jr Skills & Goals', 'Skill dan gol-gol menakjubkan dari Vinicius Junior', 'dQw4w9WgXcQ', 'https://www.youtube.com/embed/dQw4w9WgXcQ', NOW()),
('Champions League Final Highlights', 'Momen-momen terbaik dari final Liga Champions', 'dQw4w9WgXcQ', 'https://www.youtube.com/embed/dQw4w9WgXcQ', NOW()),
('Ancelotti Tactical Analysis', 'Analisis taktik dari pelatih Carlo Ancelotti', 'dQw4w9WgXcQ', 'https://www.youtube.com/embed/dQw4w9WgXcQ', NOW());

-- Membuat Tabel Team
CREATE TABLE IF NOT EXISTS teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    logo_url VARCHAR(255),
    stadium VARCHAR(100),
    city VARCHAR(100),
    country VARCHAR(100),
    founded YEAR
);
=======
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
>>>>>>> 3f9dcde68acb56b96a6b5a0664e4b76626655a50
