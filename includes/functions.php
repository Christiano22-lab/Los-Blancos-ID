<?php
require_once 'db.php';

// Authentication functions
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        $_SESSION['message'] = "You must be logged in to access that page.";
        $_SESSION['message_type'] = "error";
        header("Location: login.php");
        exit;
    }
}

function is_admin() {
    return is_logged_in() && $_SESSION['user_role'] === 'admin';
}

function require_admin() {
    if (!is_admin()) {
        $_SESSION['message'] = "You don't have permission to access that page.";
        $_SESSION['message_type'] = "error";
        header("Location: index.php");
        exit;
    }
}

function login_user($email, $password) {
    $email = db_escape($email);
    
    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = db_query($query);
    
    if (db_num_rows($result) == 1) {
        $user = db_fetch_array($result);
        
        if (password_verify($password, $user['password'])) {
            // Password is correct, create session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            return true;
        }
    }
    
    return false;
}

function logout_user() {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
}

function register_user($name, $email, $password) {
    $name = db_escape($name);
    $email = db_escape($email);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if email already exists
    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = db_query($query);
    
    if (db_num_rows($result) > 0) {
        return false; // Email already exists
    }
    
    // Insert new user
    $query = "INSERT INTO users (name, email, password, role, created_at) 
              VALUES ('$name', '$email', '$hashed_password', 'user', NOW())";
    
    if (db_query($query)) {
        return db_insert_id();
    }
    
    return false;
}

// News functions
function get_latest_news($limit = 4) {
    $query = "SELECT * FROM news ORDER BY date DESC LIMIT $limit";
    $result = db_query($query);
    return db_fetch_all($result);
}

function get_news_by_category($category, $limit = 10) {
    $category = db_escape($category);
    $query = "SELECT * FROM news WHERE category = '$category' ORDER BY date DESC LIMIT $limit";
    $result = db_query($query);
    return db_fetch_all($result);
}

function get_news_by_id($id) {
    $id = (int)$id;
    $query = "SELECT * FROM news WHERE id = $id LIMIT 1";
    $result = db_query($query);
    
    if (db_num_rows($result) == 1) {
        return db_fetch_array($result);
    }
    
    return null;
}

function get_related_news($id, $limit = 3) {
    $id = (int)$id;
    $article = get_news_by_id($id);
    
    if (!$article) {
        return [];
    }
    
    $category = db_escape($article['category']);
    $query = "SELECT * FROM news WHERE id != $id AND category = '$category' ORDER BY date DESC LIMIT $limit";
    $result = db_query($query);
    return db_fetch_all($result);
}

// Match functions
function get_upcoming_matches($limit = 3) {
    $query = "SELECT * FROM matches WHERE match_date >= CURDATE() ORDER BY match_date ASC LIMIT $limit";
    $result = db_query($query);
    return db_fetch_all($result);
}

function get_recent_results($limit = 3) {
    $query = "SELECT * FROM matches WHERE match_date < CURDATE() AND home_score IS NOT NULL ORDER BY match_date DESC LIMIT $limit";
    $result = db_query($query);
    return db_fetch_all($result);
}

function get_match_by_id($id) {
    $id = (int)$id;
    $query = "SELECT * FROM matches WHERE id = $id LIMIT 1";
    $result = db_query($query);
    
    if (db_num_rows($result) == 1) {
        return db_fetch_array($result);
    }
    
    return null;
}

// Utility functions
function format_date($date_string) {
    $date = new DateTime($date_string);
    return $date->format('F j, Y');
}

function get_time_ago($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return "Just now";
    } elseif ($diff < 3600) {
        $mins = round($diff / 60);
        return $mins . " minute" . ($mins > 1 ? "s" : "") . " ago";
    } elseif ($diff < 86400) {
        $hours = round($diff / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } elseif ($diff < 604800) {
        $days = round($diff / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    } else {
        return format_date($datetime);
    }
}

function display_message() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'info';
        
        echo '<div class="alert alert-' . $type . '">' . $message . '</div>';
        
        // Clear the message
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

function redirect($location) {
    header("Location: $location");
    exit;
}

function clean_url($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

// Gallery functions
function get_gallery_images($limit = 5) {
    $query = "SELECT * FROM gallery ORDER BY id DESC LIMIT $limit";
    $result = db_query($query);
    return db_fetch_all($result);
}

// Community functions
function get_forum_categories() {
    $query = "SELECT * FROM forum_categories ORDER BY display_order";
    $result = db_query($query);
    return db_fetch_all($result);
}

function get_forum_topics($category_id, $limit = 10, $offset = 0) {
    $category_id = (int)$category_id;
    $limit = (int)$limit;
    $offset = (int)$offset;
    
    $query = "SELECT t.*, u.name as author_name, u.profile_image, 
              (SELECT COUNT(*) FROM forum_posts WHERE topic_id = t.id) as post_count,
              (SELECT MAX(created_at) FROM forum_posts WHERE topic_id = t.id) as last_post_date
              FROM forum_topics t
              JOIN users u ON t.user_id = u.id
              WHERE t.category_id = $category_id
              ORDER BY t.is_sticky DESC, t.last_post_at DESC
              LIMIT $offset, $limit";
    
    $result = db_query($query);
    return db_fetch_all($result);
}

function get_forum_topic($topic_id) {
    $topic_id = (int)$topic_id;
    
    $query = "SELECT t.*, u.name as author_name, u.profile_image, c.name as category_name
              FROM forum_topics t
              JOIN users u ON t.user_id = u.id
              JOIN forum_categories c ON t.category_id = c.id
              WHERE t.id = $topic_id";
    
    $result = db_query($query);
    
    if (db_num_rows($result) == 1) {
        return db_fetch_array($result);
    }
    
    return null;
}

function get_forum_posts($topic_id, $limit = 10, $offset = 0) {
    $topic_id = (int)$topic_id;
    $limit = (int)$limit;
    $offset = (int)$offset;
    
    $query = "SELECT p.*, u.name as author_name, u.profile_image, u.role as author_role
              FROM forum_posts p
              JOIN users u ON p.user_id = u.id
              WHERE p.topic_id = $topic_id
              ORDER BY p.created_at
              LIMIT $offset, $limit";
    
    $result = db_query($query);
    return db_fetch_all($result);
}

// Standings functions
function get_league_standings() {
    $query = "SELECT * FROM standings ORDER BY points DESC, goal_difference DESC, goals_for DESC";
    $result = db_query($query);
    return db_fetch_all($result);
}

// Player functions
function get_team_players() {
    $query = "SELECT * FROM players ORDER BY position, jersey_number";
    $result = db_query($query);
    return db_fetch_all($result);
}

function get_player_by_id($id) {
    $id = (int)$id;
    $query = "SELECT * FROM players WHERE id = $id LIMIT 1";
    $result = db_query($query);
    
    if (db_num_rows($result) == 1) {
        return db_fetch_array($result);
    }
    
    return null;
}

// Admin functions
function count_total_users() {
    $query = "SELECT COUNT(*) as total FROM users";
    $result = db_query($query);
    $data = db_fetch_array($result);
    return $data['total'];
}

function count_total_news() {
    $query = "SELECT COUNT(*) as total FROM news";
    $result = db_query($query);
    $data = db_fetch_array($result);
    return $data['total'];
}

function count_total_matches() {
    $query = "SELECT COUNT(*) as total FROM matches";
    $result = db_query($query);
    $data = db_fetch_array($result);
    return $data['total'];
}

function count_total_comments() {
    $query = "SELECT COUNT(*) as total FROM comments";
    $result = db_query($query);
    $data = db_fetch_array($result);
    return $data['total'];
}

// Add dummy data if tables are empty (for initial setup)
function add_dummy_data() {
    // Check if news table is empty
    $query = "SELECT COUNT(*) as count FROM news";
    $result = db_query($query);
    $data = db_fetch_array($result);
    
    if ($data['count'] == 0) {
        // Add dummy news
        $news = [
            [
                'title' => 'Bellingham Scores Hat-trick in El Clásico Victory',
                'excerpt' => 'Jude Bellingham\'s incredible performance leads Real Madrid to a dominant win over Barcelona.',
                'content' => '<p>In a stunning display of skill and determination, Jude Bellingham scored three goals to lead Real Madrid to a convincing victory over Barcelona in the latest edition of El Clásico. The English midfielder was unstoppable throughout the match, showcasing why he\'s become such a vital player for Los Blancos.</p><p>From the opening whistle, Bellingham\'s presence was felt as he controlled the midfield with precision passing and intelligent movement. His first goal came in the 23rd minute, a powerful strike from outside the box that left the goalkeeper with no chance.</p>',
                'category' => 'Match Review',
                'author' => 'Carlos Mendoza',
                'date' => date('Y-m-d', strtotime('-2 days')),
                'image' => 'assets/images/news1.jpg'
            ],
            [
                'title' => 'Real Madrid Advances to Champions League Semi-finals',
                'excerpt' => 'A dramatic late goal secures Real Madrid\'s place in the Champions League semi-finals.',
                'content' => '<p>Real Madrid has once again shown their Champions League pedigree with a dramatic victory that sends them through to the semi-finals. A last-minute goal from the captain sealed the deal in what was a tense and thrilling encounter at the Santiago Bernabéu.</p><p>The match was evenly balanced throughout, with both teams creating chances but failing to capitalize. As the game entered the final minutes, it seemed destined for extra time.</p>',
                'category' => 'Champions League',
                'author' => 'Maria Rodriguez',
                'date' => date('Y-m-d', strtotime('-4 days')),
                'image' => 'assets/images/news2.jpg'
            ],
            [
                'title' => 'Transfer Rumors: Madrid Eyes Premier League Star',
                'excerpt' => 'Reports suggest Real Madrid is preparing a summer bid for a Premier League midfielder.',
                'content' => '<p>According to multiple sources close to the club, Real Madrid is preparing a substantial offer for one of the Premier League\'s standout midfielders. The player, who has been in exceptional form this season, is seen as a perfect fit for Madrid\'s playing style.</p><p>The club\'s scouts have been monitoring the player for several months, and the feedback has been overwhelmingly positive. The technical staff believes he would complement the current squad perfectly.</p>',
                'category' => 'Transfers',
                'author' => 'James Wilson',
                'date' => date('Y-m-d', strtotime('-7 days')),
                'image' => 'assets/images/news3.jpg'
            ]
        ];
        
        foreach ($news as $item) {
            $title = db_escape($item['title']);
            $excerpt = db_escape($item['excerpt']);
            $content = db_escape($item['content']);
            $category = db_escape($item['category']);
            $author = db_escape($item['author']);
            $date = $item['date'];
            $image = db_escape($item['image']);
            
            $query = "INSERT INTO news (title, excerpt, content, image, category, author, date, created_at) 
                      VALUES ('$title', '$excerpt', '$content', '$image', '$category', '$author', '$date', NOW())";
            db_query($query);
        }
    }
    
    // Check if matches table is empty
    $query = "SELECT COUNT(*) as count FROM matches";
    $result = db_query($query);
    $data = db_fetch_array($result);
    
    if ($data['count'] == 0) {
        // Add dummy matches
        $matches = [
            [
                'competition' => 'La Liga',
                'home_team' => 'Real Madrid',
                'away_team' => 'Sevilla',
                'home_team_logo' => 'assets/images/real_madrid.png',
                'away_team_logo' => 'assets/images/sevilla.png',
                'match_date' => date('Y-m-d', strtotime('+5 days')),
                'match_time' => '20:00',
                'stadium' => 'Santiago Bernabéu',
                'status' => 'upcoming'
            ],
            [
                'competition' => 'Champions League',
                'home_team' => 'Bayern Munich',
                'away_team' => 'Real Madrid',
                'home_team_logo' => 'assets/images/bayern.png',
                'away_team_logo' => 'assets/images/real_madrid.png',
                'match_date' => date('Y-m-d', strtotime('+10 days')),
                'match_time' => '21:00',
                'stadium' => 'Allianz Arena',
                'status' => 'upcoming'
            ],
            [
                'competition' => 'La Liga',
                'home_team' => 'Real Madrid',
                'away_team' => 'Barcelona',
                'home_team_logo' => 'assets/images/real_madrid.png',
                'away_team_logo' => 'assets/images/barcelona.png',
                'match_date' => date('Y-m-d', strtotime('-3 days')),
                'match_time' => '20:00',
                'stadium' => 'Santiago Bernabéu',
                'home_score' => 3,
                'away_score' => 1,
                'status' => 'completed'
            ],
            [
                'competition' => 'La Liga',
                'home_team' => 'Atletico Madrid',
                'away_team' => 'Real Madrid',
                'home_team_logo' => 'assets/images/atletico.png',
                'away_team_logo' => 'assets/images/real_madrid.png',
                'match_date' => date('Y-m-d', strtotime('-10 days')),
                'match_time' => '19:00',
                'stadium' => 'Wanda Metropolitano',
                'home_score' => 0,
                'away_score' => 2,
                'status' => 'completed'
            ]
        ];
        
        foreach ($matches as $match) {
            $competition = db_escape($match['competition']);
            $home_team = db_escape($match['home_team']);
            $away_team = db_escape($match['away_team']);
            $home_team_logo = db_escape($match['home_team_logo']);
            $away_team_logo = db_escape($match['away_team_logo']);
            $match_date = $match['match_date'];
            $match_time = db_escape($match['match_time']);
            $stadium = db_escape($match['stadium']);
            $status = db_escape($match['status']);
            
            $home_score = isset($match['home_score']) ? $match['home_score'] : 'NULL';
            $away_score = isset($match['away_score']) ? $match['away_score'] : 'NULL';
            
            $query = "INSERT INTO matches (competition, home_team, away_team, home_team_logo, away_team_logo, match_date, match_time, stadium, home_score, away_score, status, created_at) 
                      VALUES ('$competition', '$home_team', '$away_team', '$home_team_logo', '$away_team_logo', '$match_date', '$match_time', '$stadium', $home_score, $away_score, '$status', NOW())";
            db_query($query);
        }
    }
    
    // Check if users table is empty (except for admin)
    $query = "SELECT COUNT(*) as count FROM users";
    $result = db_query($query);
    $data = db_fetch_array($result);
    
    if ($data['count'] <= 1) { // Only admin exists
        // Add dummy users
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'role' => 'user'
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => 'password123',
                'role' => 'user'
            ],
            [
                'name' => 'Robert Johnson',
                'email' => 'robert@example.com',
                'password' => 'password123',
                'role' => 'user'
            ]
        ];
        
        foreach ($users as $user) {
            $name = db_escape($user['name']);
            $email = db_escape($user['email']);
            $password = password_hash($user['password'], PASSWORD_DEFAULT);
            $role = db_escape($user['role']);
            
            $query = "INSERT INTO users (name, email, password, role, created_at) 
                      VALUES ('$name', '$email', '$password', '$role', NOW())";
            db_query($query);
        }
    }
}

// Tambahkan fungsi baru untuk mendapatkan forum topics terbaru
function get_recent_forum_topics($limit = 3) {
    $limit = (int)$limit;
    
    // Cek apakah tabel forum_topics ada
    $check_table = db_query("SHOW TABLES LIKE 'forum_topics'");
    if (db_num_rows($check_table) == 0) {
        // Tabel tidak ada, buat dummy data
        create_dummy_forum_data();
    }
    
    $query = "SELECT t.*, u.name as author_name, u.profile_image, c.name as category_name,
              (SELECT COUNT(*) FROM forum_posts WHERE topic_id = t.id) as post_count
              FROM forum_topics t
              JOIN users u ON t.user_id = u.id
              JOIN forum_categories c ON t.category_id = c.id
              ORDER BY t.created_at DESC
              LIMIT $limit";
    
    $result = db_query($query);
    return db_fetch_all($result);
}

// Fungsi untuk membuat dummy data forum jika belum ada
function create_dummy_forum_data() {
    // Cek apakah tabel forum_categories ada
    $check_categories = db_query("SHOW TABLES LIKE 'forum_categories'");
    if (db_num_rows($check_categories) == 0) {
        // Buat tabel forum_categories
        db_query("CREATE TABLE forum_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            display_order INT DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Tambahkan kategori dasar
        $categories = [
            ['name' => 'General Discussion', 'description' => 'General topics about Real Madrid'],
            ['name' => 'Match Discussion', 'description' => 'Discuss matches, tactics, and performances'],
            ['name' => 'Transfer Talk', 'description' => 'Rumors, news, and discussions about transfers'],
            ['name' => 'Fan Zone', 'description' => 'Share your experiences as a Madridista']
        ];
        
        foreach ($categories as $index => $category) {
            $name = db_escape($category['name']);
            $description = db_escape($category['description']);
            $order = $index + 1;
            
            db_query("INSERT INTO forum_categories (name, description, display_order) 
                     VALUES ('$name', '$description', $order)");
        }
    }
    
    // Cek apakah tabel forum_topics ada
    $check_topics = db_query("SHOW TABLES LIKE 'forum_topics'");
    if (db_num_rows($check_topics) == 0) {
        // Buat tabel forum_topics
        db_query("CREATE TABLE forum_topics (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category_id INT NOT NULL,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            is_sticky TINYINT(1) DEFAULT 0,
            is_locked TINYINT(1) DEFAULT 0,
            views INT DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            last_post_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES forum_categories(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )");
        
        // Buat tabel forum_posts
        db_query("CREATE TABLE forum_posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            topic_id INT NOT NULL,
            user_id INT NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (topic_id) REFERENCES forum_topics(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )");
        
        // Tambahkan beberapa topik dummy
        // Pertama, pastikan ada user
        $check_users = db_query("SELECT id FROM users LIMIT 1");
        if (db_num_rows($check_users) == 0) {
            // Buat user admin jika belum ada
            $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
            db_query("INSERT INTO users (name, email, password, role, created_at) 
                     VALUES ('Admin', 'admin@example.com', '$admin_password', 'admin', NOW())");
        }
        
        // Dapatkan user_id
        $user_result = db_query("SELECT id FROM users LIMIT 1");
        $user = db_fetch($user_result);
        $user_id = $user['id'];
        
        // Dapatkan category_ids
        $categories_result = db_query("SELECT id FROM forum_categories");
        $categories = db_fetch_all($categories_result);
        
        // Topik dummy
        $topics = [
            [
                'title' => 'Thoughts on our Champions League chances this season?',
                'content' => 'With the squad we have and our recent form, I think we have a good chance of going all the way in the Champions League this season. What do you all think?',
                'category_id' => $categories[1]['id'], // Match Discussion
                'views' => 245
            ],
            [
                'title' => 'Best signing of the summer transfer window',
                'content' => 'Who do you think was our best signing this summer? I personally think our new midfielder has been exceptional and exactly what we needed.',
                'category_id' => $categories[2]['id'], // Transfer Talk
                'views' => 189
            ],
            [
                'title' => 'My first visit to Santiago Bernabéu',
                'content' => 'I finally got to visit the Santiago Bernabéu last weekend and it was an incredible experience! The atmosphere was electric and seeing the team play live was a dream come true.',
                'category_id' => $categories[3]['id'], // Fan Zone
                'views' => 132
            ]
        ];
        
        foreach ($topics as $topic) {
            $title = db_escape($topic['title']);
            $content = db_escape($topic['content']);
            $category_id = (int)$topic['category_id'];
            $views = (int)$topic['views'];
            
            db_query("INSERT INTO forum_topics (category_id, user_id, title, content, views, created_at, last_post_at) 
                     VALUES ($category_id, $user_id, '$title', '$content', $views, NOW(), NOW())");
            
            // Tambahkan post pertama (sama dengan konten topik)
            $topic_id = db_insert_id();
            db_query("INSERT INTO forum_posts (topic_id, user_id, content, created_at) 
                     VALUES ($topic_id, $user_id, '$content', NOW())");
        }
    }
}



// Fungsi untuk membuat dummy data gallery jika belum ada
function create_dummy_gallery_data() {
    // Buat tabel gallery jika belum ada
    db_query("CREATE TABLE IF NOT EXISTS gallery (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        image_url VARCHAR(255) NOT NULL,
        thumbnail_url VARCHAR(255),
        category VARCHAR(50),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Tambahkan beberapa gambar dummy
    $images = [
        [
            'title' => 'Santiago Bernabéu Stadium',
            'description' => 'The iconic home of Real Madrid',
            'image_url' => 'assets/images/gallery/Santiago.jpeg',
            'category' => 'Stadium'
        ],
        [
            'title' => 'Champions League Celebration',
            'description' => 'Players celebrating our latest Champions League victory',
            'image_url' => 'assets/images/gallery/celebration.jpg',
            'category' => 'Celebrations'
        ],
        [
            'title' => 'Team Photo 2024/25',
            'description' => 'Official team photo for the 2024/25 season',
            'image_url' => 'assets/images/gallery/team-photo.jpg',
            'category' => 'Team'
        ],
        [
            'title' => 'El Clásico Action',
            'description' => 'Intense moment from the latest El Clásico',
            'image_url' => 'assets/images/gallery/clasico.jpg',
            'category' => 'Matches'
        ],
        [
            'title' => 'Training Session',
            'description' => 'Players during a training session at Valdebebas',
            'image_url' => 'assets/images/gallery/training.jpg',
            'category' => 'Training'
        ],
        [
            'title' => 'Fans at the Bernabéu',
            'description' => 'Amazing atmosphere created by our fans',
            'image_url' => 'assets/images/gallery/fans.jpg',
            'category' => 'Fans'
        ]
    ];
    
    foreach ($images as $image) {
        $title = db_escape($image['title']);
        $description = db_escape($image['description']);
        $image_url = db_escape($image['image_url']);
        $category = db_escape($image['category']);
        
        db_query("INSERT INTO gallery (title, description, image_url, category, created_at) 
                 VALUES ('$title', '$description', '$image_url', '$category', NOW())");
    }
}

// Tambahkan fungsi untuk membuat dummy data berita
function create_dummy_news_data() {
    // Cek apakah tabel news ada
    $check_table = db_query("SHOW TABLES LIKE 'news'");
    if (db_num_rows($check_table) == 0) {
        // Buat tabel news jika belum ada
        db_query("CREATE TABLE news (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            excerpt TEXT,
            content TEXT,
            category VARCHAR(100),
            author VARCHAR(100),
            image VARCHAR(255),
            views INT DEFAULT 0,
            date DATE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
    }
    
    // Cek apakah sudah ada data berita
    $check_data = db_query("SELECT COUNT(*) as count FROM news");
    $data = db_fetch($check_data);
    
    if ($data['count'] == 0) {
        // Tambahkan berita dummy
        $news_items = [
            [
                'title' => 'Bellingham Scores Hat-trick in El Clásico Victory',
                'excerpt' => 'Jude Bellingham\'s incredible performance leads Real Madrid to a dominant win over Barcelona in the latest El Clásico.',
                'content' => 'In a stunning display of skill and determination, Jude Bellingham scored three goals to lead Real Madrid to a convincing victory over Barcelona. The English midfielder was unstoppable throughout the match.',
                'category' => 'Match Review',
                'author' => 'Carlos Mendoza',
                'image' => 'assets/images/news/bellingham-clasico.jpg',
                'views' => 1250,
                'date' => date('Y-m-d', strtotime('-2 days'))
            ],
            [
                'title' => 'Real Madrid Advances to Champions League Semi-finals',
                'excerpt' => 'A dramatic late goal secures Real Madrid\'s place in the Champions League semi-finals after an intense battle.',
                'content' => 'Real Madrid has once again shown their Champions League pedigree with a dramatic victory that sends them through to the semi-finals. A last-minute goal sealed the deal.',
                'category' => 'Champions League',
                'author' => 'Maria Rodriguez',
                'image' => 'assets/images/news/champions-league.jpg',
                'views' => 980,
                'date' => date('Y-m-d', strtotime('-5 days'))
            ],
            [
                'title' => 'Transfer Rumors: Madrid Eyes Premier League Star',
                'excerpt' => 'Reports suggest Real Madrid is preparing a summer bid for a highly-rated Premier League midfielder.',
                'content' => 'According to multiple sources close to the club, Real Madrid is preparing a substantial offer for one of the Premier League\'s standout midfielders.',
                'category' => 'Transfers',
                'author' => 'James Wilson',
                'image' => 'assets/images/news/transfer-news.jpg',
                'views' => 756,
                'date' => date('Y-m-d', strtotime('-7 days'))
            ],
            [
                'title' => 'Injury Update: Key Defender Returns to Training',
                'excerpt' => 'Good news for Madridistas as a key defender returns to full training after a lengthy injury layoff.',
                'content' => 'In a boost to Real Madrid\'s defensive options, a key defender has returned to full training following a lengthy injury layoff.',
                'category' => 'Team News',
                'author' => 'Elena Sanchez',
                'image' => 'assets/images/news/training-return.jpg',
                'views' => 432,
                'date' => date('Y-m-d', strtotime('-10 days'))
            ],
            [
                'title' => 'Tactical Analysis: Ancelotti\'s New Formation',
                'excerpt' => 'A deep dive into Carlo Ancelotti\'s tactical innovations that have transformed Real Madrid\'s playing style.',
                'content' => 'Carlo Ancelotti has implemented a new tactical approach that has seen Real Madrid dominate possession and create more scoring opportunities.',
                'category' => 'Analysis',
                'author' => 'Thomas Mueller',
                'image' => 'assets/images/news/tactical-analysis.jpg',
                'views' => 623,
                'date' => date('Y-m-d', strtotime('-12 days'))
            ],
            [
                'title' => 'Santiago Bernabéu Renovation Update',
                'excerpt' => 'Latest updates on the ongoing renovation works at the iconic Santiago Bernabéu stadium.',
                'content' => 'The renovation of Santiago Bernabéu continues to progress with new technological features being installed.',
                'category' => 'Stadium News',
                'author' => 'Miguel Fernandez',
                'image' => 'assets/images/news/bernabeu-renovation.jpg',
                'views' => 891,
                'date' => date('Y-m-d', strtotime('-15 days'))
            ]
        ];
        
        foreach ($news_items as $news) {
            $title = db_escape($news['title']);
            $excerpt = db_escape($news['excerpt']);
            $content = db_escape($news['content']);
            $category = db_escape($news['category']);
            $author = db_escape($news['author']);
            $image = db_escape($news['image']);
            $views = (int)$news['views'];
            $date = $news['date'];
            
            db_query("INSERT INTO news (title, excerpt, content, category, author, image, views, date, created_at) 
                     VALUES ('$title', '$excerpt', '$content', '$category', '$author', '$image', $views, '$date', NOW())");
        }
    }
}
// Tambahkan fungsi untuk search berita
function search_news($search_term, $limit = 12) {
    $limit = (int)$limit;
    $search_term = db_escape($search_term);
    
    create_dummy_news_data();
    
    $query = "SELECT * FROM news 
              WHERE title LIKE '%$search_term%' 
              OR excerpt LIKE '%$search_term%' 
              OR content LIKE '%$search_term%'
              ORDER BY date DESC, created_at DESC LIMIT $limit";
    $result = db_query($query);
    return db_fetch_all($result);
}

// Fungsi untuk membuat dummy data matches
function create_dummy_matches_data() {
    // Cek apakah tabel matches ada
    $check_table = db_query("SHOW TABLES LIKE 'matches'");
    if (db_num_rows($check_table) == 0) {
        // Buat tabel matches
        db_query("CREATE TABLE matches (
            id INT AUTO_INCREMENT PRIMARY KEY,
            competition VARCHAR(100) NOT NULL,
            home_team VARCHAR(100) NOT NULL,
            away_team VARCHAR(100) NOT NULL,
            home_team_logo VARCHAR(255),
            away_team_logo VARCHAR(255),
            match_date DATE NOT NULL,
            match_time TIME,
            stadium VARCHAR(100),
            home_score INT NULL,
            away_score INT NULL,
            status VARCHAR(50) DEFAULT 'upcoming',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
    }
    
    // Cek apakah sudah ada data matches
    $check_data = db_query("SELECT COUNT(*) as count FROM matches");
    $data = db_fetch($check_data);
    
    if ($data['count'] == 0) {
        // Tambahkan matches dummy (kode yang sudah ada sebelumnya)
        $matches = [
            // Upcoming matches
            [
                'competition' => 'La Liga',
                'home_team' => 'Real Madrid',
                'away_team' => 'Sevilla',
                'home_team_logo' => 'assets/images/teams/real-madrid.png',
                'away_team_logo' => 'assets/images/teams/sevilla.png',
                'match_date' => date('Y-m-d', strtotime('+3 days')),
                'match_time' => '20:00',
                'stadium' => 'Santiago Bernabéu',
                'status' => 'upcoming'
            ],
            [
                'competition' => 'Champions League',
                'home_team' => 'Bayern Munich',
                'away_team' => 'Real Madrid',
                'home_team_logo' => 'assets/images/teams/bayern.png',
                'away_team_logo' => 'assets/images/teams/real-madrid.png',
                'match_date' => date('Y-m-d', strtotime('+8 days')),
                'match_time' => '21:00',
                'stadium' => 'Allianz Arena',
                'status' => 'upcoming'
            ],
            [
                'competition' => 'La Liga',
                'home_team' => 'Real Madrid',
                'away_team' => 'Valencia',
                'home_team_logo' => 'assets/images/teams/real-madrid.png',
                'away_team_logo' => 'assets/images/teams/valencia.png',
                'match_date' => date('Y-m-d', strtotime('+15 days')),
                'match_time' => '19:00',
                'stadium' => 'Santiago Bernabéu',
                'status' => 'upcoming'
            ],
            
            // Recent results
            [
                'competition' => 'La Liga',
                'home_team' => 'Real Madrid',
                'away_team' => 'Barcelona',
                'home_team_logo' => 'assets/images/teams/real-madrid.png',
                'away_team_logo' => 'assets/images/teams/barcelona.png',
                'match_date' => date('Y-m-d', strtotime('-3 days')),
                'match_time' => '20:00',
                'stadium' => 'Santiago Bernabéu',
                'home_score' => 3,
                'away_score' => 1,
                'status' => 'completed'
            ],
            [
                'competition' => 'Champions League',
                'home_team' => 'Real Madrid',
                'away_team' => 'Manchester City',
                'home_team_logo' => 'assets/images/teams/real-madrid.png',
                'away_team_logo' => 'assets/images/teams/mancity.png',
                'match_date' => date('Y-m-d', strtotime('-10 days')),
                'match_time' => '21:00',
                'stadium' => 'Santiago Bernabéu',
                'home_score' => 2,
                'away_score' => 1,
                'status' => 'completed'
            ]
        ];
        
        foreach ($matches as $match) {
            $competition = db_escape($match['competition']);
            $home_team = db_escape($match['home_team']);
            $away_team = db_escape($match['away_team']);
            $home_team_logo = db_escape($match['home_team_logo']);
            $away_team_logo = db_escape($match['away_team_logo']);
            $match_date = $match['match_date'];
            $match_time = db_escape($match['match_time']);
            $stadium = db_escape($match['stadium']);
            $status = db_escape($match['status']);
            
            $home_score = isset($match['home_score']) ? $match['home_score'] : 'NULL';
            $away_score = isset($match['away_score']) ? $match['away_score'] : 'NULL';
            
            $query = "INSERT INTO matches (competition, home_team, away_team, home_team_logo, away_team_logo, match_date, match_time, stadium, home_score, away_score, status, created_at) 
                      VALUES ('$competition', '$home_team', '$away_team', '$home_team_logo', '$away_team_logo', '$match_date', '$match_time', '$stadium', $home_score, $away_score, '$status', NOW())";
            db_query($query);
        }
    }
}

// Fungsi untuk membuat dummy users
function create_dummy_users_data() {
    // Cek apakah tabel users ada
    $check_table = db_query("SHOW TABLES LIKE 'users'");
    if (db_num_rows($check_table) == 0) {
        // Buat tabel users
        db_query("CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('user', 'admin') DEFAULT 'user',
            profile_image VARCHAR(255),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
    }
    
    // Cek apakah sudah ada user
    $check_data = db_query("SELECT COUNT(*) as count FROM users");
    $data = db_fetch($check_data);
    
    if ($data['count'] == 0) {
        // Buat user admin dan beberapa user dummy
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@realmadrid.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'profile_image' => 'assets/images/avatars/admin.png'
            ],
            [
                'name' => 'Carlos Mendoza',
                'email' => 'carlos@example.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'role' => 'user',
                'profile_image' => 'assets/images/avatars/carlos.png'
            ],
            [
                'name' => 'Maria Rodriguez',
                'email' => 'maria@example.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'role' => 'user',
                'profile_image' => 'assets/images/avatars/maria.png'
            ]
        ];
        
        foreach ($users as $user) {
            $name = db_escape($user['name']);
            $email = db_escape($user['email']);
            $password = $user['password'];
            $role = db_escape($user['role']);
            $profile_image = db_escape($user['profile_image']);
            
            db_query("INSERT INTO users (name, email, password, role, profile_image, created_at) 
                     VALUES ('$name', '$email', '$password', '$role', '$profile_image', NOW())");
        }
    }
}


?>


