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
CREATE TABLE IF NOT EXISTS matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    competition VARCHAR(100) NOT NULL,
    home_team VARCHAR(100) NOT NULL,
    away_team VARCHAR(100) NOT NULL,
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