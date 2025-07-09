<?php
$page_title = "Home";
$page_description = "The ultimate fan site for Real Madrid supporters worldwide";
$current_page = "home";

require_once 'includes/functions.php';
require_once 'includes/config.php';
require_once 'includes/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC LIMIT 5");
    $gallery_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $gallery_images = [];
}

// Pastikan semua data dummy ter-load
add_dummy_data();

// Get data for homepage
$latest_news = get_latest_news(4);
$upcoming_matches = get_upcoming_matches(3);
$recent_results = get_recent_results(2);

include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/index.css">
<link rel="stylesheet" href="assets/css/news-carousel.css">

<!-- Hero Section - True Full Screen -->
<section class="hero-section" style="background-image: linear-gradient(rgba(0, 38, 96, 0.8), rgba(0, 38, 96, 0.6)), url('assets/images/1.jpeg');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-badge">
            <i class="fas fa-crown"></i>
            <span>Los Blancos Indonesia</span>
        </div>
        <h1>Selamat Datang di Los Blancos ID </h1>
        <p>Tempat Berkumpulnya Madridista Sejati. Enjoy Aman dan Ketahui Informasi Terkini Seputar Real Madrid </p>
        <div class="hero-buttons">
            <a href="about.php" class="btn btn-hero-primary">
                <i class="fas fa-info-circle"></i> About Us
            </a>
            <a href="community.php" class="btn btn-hero-secondary">
                <i class="fas fa-users"></i> Join Community
            </a>
        </div>
    </div>
</section>

<!-- Latest News Section - White Background -->
<section class="section section-white">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fas fa-newspaper"></i>
                <span>Latest Updates</span>
            </div>
            <h2>Berita Terkini</h2>
            <p>Tetap ikuti berita Real Madrid terkini, laporan pertandingan, dan konten eksklusif dari komunitas kami.</p>
        </div>
        
        <div class="news-grid">
            <?php if (!empty($latest_news)): ?>
                <?php foreach ($latest_news as $index => $news): ?>
                    <article class="news-card <?php echo $index === 0 ? 'featured' : ''; ?>">
                        <div class="news-image">
                            <img src="<?php echo !empty($news['image']) 
                                ? 'assets/images/news/' . htmlspecialchars($news['image']) : '/placeholder.svg?height=300&width=400'; ?>" 
                                alt="<?php echo htmlspecialchars($news['title']); ?>"
                                onerror="this.src='/placeholder.svg?height=300&width=400'">
                            <div class="news-category-badge">
                                <?php echo htmlspecialchars($news['category']); ?>
                            </div>
                        </div>
                        <div class="news-content">
                            <div class="news-meta">
                                <span class="date">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo format_date($news['date']); ?>
                                </span>
                                <span class="views">
                                    <i class="fas fa-eye"></i>
                                    <?php echo number_format($news['views'] ?? 0); ?>
                                </span>
                            </div>
                            <h3>
                                <a href="news-detail.php?id=<?php echo $news['id']; ?>">
                                    <?php echo htmlspecialchars($news['title']); ?>
                                </a>
                            </h3>
                            <p><?php echo htmlspecialchars(substr($news['excerpt'], 0, 120)) . '...'; ?></p>
                            <a href="news-detail.php?id=<?php echo $news['id']; ?>" class="read-more">
                                Read More <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-content">
                    <i class="fas fa-newspaper"></i>
                    <h3>Tidak Tersedia Berita</h3>
                    <p>Periksa kembali nanti untuk mengetahui update Terbaru!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="section-footer">
            <a href="news.php" class="btn btn-outline">
                <i class="fas fa-newspaper"></i>
                View All News
            </a>
        </div>
    </div>
</section>

<!-- Matches Section - Blue Background -->
<section class="section section-blue">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fas fa-futbol"></i>
                <span>Match Center</span>
            </div>
            <h2>Upcoming & Recent Matches</h2>
            <p>Ikuti perjalanan Real Madrid melalui semua kompetisi dengan informasi dan hasil pertandingan terperinci.</p>
        </div>
        
        <div class="matches-tabs">
            <div class="tab-buttons">
                <button class="tab-btn active" data-tab="upcoming">
                    <i class="fas fa-clock"></i>
                    <span>Upcoming</span>
                </button>
                <button class="tab-btn" data-tab="results">
                    <i class="fas fa-check-circle"></i>
                    <span>Results</span>
                </button>
            </div>
            
            <!-- Upcoming Matches -->
            <div class="tab-content active" id="upcoming">
                <div class="matches-grid">
                    <?php if (!empty($upcoming_matches)): ?>
                        <?php foreach ($upcoming_matches as $match): ?>
                            <div class="match-card">
                                <div class="match-header">
                                    <div class="competition-badge">
                                        <?php echo htmlspecialchars($match['competition']); ?>
                                    </div>
                                    <div class="match-date">
                                        <?php echo format_date($match['match_date']); ?>
                                    </div>
                                </div>
                                <div class="match-teams">
                                    <div class="team">
                                        <div class="team-logo">
                                            <img src="<?php echo get_team_logo_path($match['home_team_logo']); ?>" 
                                                alt="<?php echo htmlspecialchars($match['home_team']); ?>"
                                                onerror="this.src='/placeholder.svg?height=60&width=60'">
                                        </div>
                                        <div class="team-name">
                                            <?php echo htmlspecialchars($match['home_team']); ?>
                                        </div>
                                    </div>
                                    <div class="match-vs">
                                        <div class="vs-text">VS</div>
                                        <div class="match-time">
                                            <i class="fas fa-clock"></i>
                                            <?php echo htmlspecialchars($match['match_time']); ?>
                                        </div>
                                    </div>
                                    <div class="team">
                                        <div class="team-logo">
                                            <img src="<?php echo get_team_logo_path($match['away_team_logo']); ?>" 
                                                alt="<?php echo htmlspecialchars($match['away_team']); ?>"
                                                onerror="this.src='/placeholder.svg?height=60&width=60'">
                                        </div>
                                        <div class="team-name">
                                            <?php echo htmlspecialchars($match['away_team']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="match-venue">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($match['stadium']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-content">
                            <i class="fas fa-calendar-times"></i>
                            <h3>No Upcoming Matches</h3>
                            <p>Check back later for match schedules!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Recent Results -->
            <div class="tab-content" id="results">
                <div class="matches-grid">
                    <?php if (!empty($recent_results)): ?>
                        <?php foreach ($recent_results as $match): ?>
                            <div class="match-card">
                                <div class="match-header">
                                    <div class="competition-badge">
                                        <?php echo htmlspecialchars($match['competition']); ?>
                                    </div>
                                    <div class="match-date">
                                        <?php echo format_date($match['match_date']); ?>
                                    </div>
                                </div>
                                <div class="match-teams">
                                    <div class="team">
                                        <div class="team-logo">
                                            <img src="<?php echo get_team_logo_path($match['home_team_logo']); ?>" 
                                                alt="<?php echo htmlspecialchars($match['home_team']); ?>"
                                                onerror="this.src='/placeholder.svg?height=60&width=60'">
                                        </div>
                                        <div class="team-name">
                                            <?php echo htmlspecialchars($match['home_team']); ?>
                                        </div>
                                    </div>
                                    <div class="match-score">
                                        <div class="score-display">
                                            <?php echo $match['home_score']; ?> - <?php echo $match['away_score']; ?>
                                        </div>
                                        <div class="match-status">Full Time</div>
                                    </div>
                                    <div class="team">
                                        <div class="team-logo">
                                            <img src="<?php echo get_team_logo_path($match['away_team_logo']); ?>" 
                                                alt="<?php echo htmlspecialchars($match['away_team']); ?>"
                                                onerror="this.src='/placeholder.svg?height=60&width=60'">
                                        </div>
                                        <div class="team-name">
                                            <?php echo htmlspecialchars($match['away_team']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="match-venue">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($match['stadium']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-content">
                            <i class="fas fa-calendar-times"></i>
                            <h3>No Recent Results</h3>
                            <p>Check back later for match results!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="section-footer">
            <a href="matches.php" class="btn btn-outline-white">
                <i class="fas fa-futbol"></i>
                View All Matches
            </a>
        </div>
    </div>
</section>

<!-- Gallery Section - White Background -->
<section class="section section-white">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fas fa-images"></i>
                <span>Photo Gallery</span>
            </div>
            <h2>Memorable Moments</h2>
            <p>Hidupkan kembali momen terhebat dalam sejarah Real Madrid melalui koleksi foto eksklusif kami.</p>
        </div>
        
        <div class="gallery-grid">
            <?php if (!empty($gallery_images)): ?>
                <?php foreach ($gallery_images as $index => $image): ?>
                    <div class="gallery-item <?php echo $index === 0 ? 'featured' : ''; ?>">
                        <div class="gallery-image">
                            <img src="<?php echo htmlspecialchars($image['thumbnail_url'] ?? $image['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($image['title']); ?>"
                                 onerror="this.src='/placeholder.svg?height=300&width=400'">
                            <div class="gallery-overlay">
                                <div class="gallery-content">
                                    <h4><?php echo htmlspecialchars($image['title']); ?></h4>
                                    <span class="gallery-category"><?php echo htmlspecialchars($image['category']); ?></span>
                                </div>
                                <div class="gallery-action">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-content">
                    <i class="fas fa-images"></i>
                    <h3>No Photos Available</h3>
                    <p>Check back later for photos!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="section-footer">
            <a href="media.php" class="btn btn-outline">
                <i class="fas fa-images"></i>
                View All Photos
            </a>
        </div>
    </div>
</section>

<!-- Login Prompt for Non-logged Users -->
<?php if (!is_logged_in()): ?>
        <div class="login-prompt-section">
            <div class="login-prompt">
                <div class="prompt-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <div class="prompt-content">
                    <h3>Want to Read More News?</h3>
                    <p>Join our community to access all news articles, add your own content, and stay updated with the latest Real Madrid news!</p>
                    <div class="prompt-actions">
                        <a href="login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </a>
                        <a href="register.php" class="btn btn-secondary">
                            <i class="fas fa-user-plus"></i> Create Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
<?php endif; ?>

<script src="assets/js/index.js"></script>
<script src="assets/js/news-carousel.js"></script>

<?php include 'includes/footer.php'; ?>
