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


<!-- Hero Section with Sign In/Sign Up -->
<section class="hero-section">
    <div class="hero-content">
        <h1>Welcome to Real Madrid Fan Club</h1>
        <p>The ultimate destination for Madridistas worldwide. Join our community of passionate fans celebrating the greatest club in football history.</p>
        <div class="hero-buttons">
            <?php if (!is_logged_in()): ?>
                <a href="login.php" class="btn btn-primary">Sign In</a>
                <a href="register.php" class="btn btn-secondary">Sign Up</a>
            <?php else: ?>
                <a href="profile.php" class="btn btn-primary">My Profile</a>
                <a href="community.php" class="btn btn-secondary">Community</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Latest News Carousel Section -->
<section class="news-carousel-section">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-newspaper"></i> Latest News</h2>
            <a href="news.php" class="view-all-btn">
                View All News <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="news-carousel-container">
            <div class="news-carousel" id="newsCarousel">
                <?php if (!empty($latest_news)): ?>
                    <?php foreach ($latest_news as $news): ?>
                        <div class="news-slide">
                            <div class="news-box">
                                <div class="news-image">
                                    <img src="<?php echo !empty($news['image']) 
                                        ? 'assets/images/' . htmlspecialchars($news['image']) : 'assets/images/default-news.jpg'; ?>" alt="<?php echo htmlspecialchars($news['title']); ?>"
                                        onerror="this.src='assets/images/default-news.jpg'">
                                    <div class="news-overlay">
                                        <span class="news-category"><?php echo htmlspecialchars($news['category']); ?></span>
                                    </div>
                                </div>
                                <div class="news-content">
                                    <h3>
                                        <a href="news-detail.php?id=<?php echo $news['id']; ?>">
                                            <?php echo htmlspecialchars($news['title']); ?>
                                        </a>
                                    </h3>
                                    <p class="news-excerpt"><?php echo htmlspecialchars(substr($news['excerpt'], 0, 100)) . '...'; ?></p>
                                    <div class="news-meta">
                                        <span class="date"><i class="fas fa-calendar"></i> <?php echo format_date($news['date']); ?></span>
                                        <span class="views"><i class="fas fa-eye"></i> <?php echo number_format($news['views'] ?? 0); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="news-slide">
                        <div class="no-news-box">
                            <i class="fas fa-newspaper"></i>
                            <h3>No News Available</h3>
                            <p>Check back later for updates!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Navigation buttons -->
            <button class="carousel-btn prev-btn" id="newsPrevBtn" onclick="slideNews(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="carousel-btn next-btn" id="newsNextBtn" onclick="slideNews(1)">
                <i class="fas fa-chevron-right"></i>
            </button>

            
            <!-- Dots indicator -->
            <div class="carousel-dots" id="newsDots"></div>
        </div>
    </div>
</section>

<!-- Matches Section -->
<section class="matches-section">
    <div class="container">
        <h2><i class="fas fa-calendar-check"></i> Match Center</h2>
        
        <div class="matches-tabs">
            <div class="tab-buttons">
                <button class="tab-btn active" data-tab="upcoming">
                    <i class="fas fa-clock"></i> Upcoming Matches
                </button>
                <button class="tab-btn" data-tab="results">
                    <i class="fas fa-check-circle"></i> Recent Results
                </button>
            </div>
            
            <!-- Upcoming Matches -->
            <div class="tab-content active" id="upcoming">
                <div class="matches-grid">
                    <?php if (!empty($upcoming_matches)): ?>
                        <?php foreach ($upcoming_matches as $match): ?>
                            <div class="match-card">
                                <div class="match-header">
                                    <span class="competition"><?php echo htmlspecialchars($match['competition']); ?></span>
                                    <span class="match-date"><?php echo format_date($match['match_date']); ?></span>
                                </div>
                                <div class="match-teams">
                                    <div class="team">
                                        <img src="<?php echo 'assets/images/' . htmlspecialchars($match['home_team_logo']); ?>" 
                                            alt="<?php echo htmlspecialchars($match['home_team']); ?>"
                                            onerror="this.src='assets/images/default-team.png'">
                                        <span><?php echo htmlspecialchars($match['home_team']); ?></span>
                                    </div>
                                    <div class="vs">
                                        <span>VS</span>
                                        <span class="time"><?php echo htmlspecialchars($match['match_time']); ?></span>
                                    </div>
                                    <div class="team">
                                        <img src="<?php echo 'assets/images/' . htmlspecialchars($match['away_team_logo']); ?>" 
                                            alt="<?php echo htmlspecialchars($match['away_team']); ?>"
                                            onerror="this.src='assets/images/default-team.png'">
                                        <span><?php echo htmlspecialchars($match['away_team']); ?></span>
                                    </div>
                                </div>
                                <div class="match-venue">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($match['stadium']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-matches">
                            <i class="fas fa-calendar-times"></i>
                            <p>No upcoming matches scheduled.</p>
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
                                    <span class="competition"><?php echo htmlspecialchars($match['competition']); ?></span>
                                    <span class="match-date"><?php echo format_date($match['match_date']); ?></span>
                                </div>
                                <div class="match-teams">
                                    <div class="team">
                                        <img src="<?php echo 'assets/images/' . htmlspecialchars($match['home_team_logo']); ?>" 
                                            alt="<?php echo htmlspecialchars($match['home_team']); ?>"
                                            onerror="this.src='assets/images/default-team.png'">
                                        <span><?php echo htmlspecialchars($match['home_team']); ?></span>
                                    </div>
                                    <div class="score">
                                        <span class="score-numbers">
                                            <?php echo $match['home_score']; ?> - <?php echo $match['away_score']; ?>
                                        </span>
                                        <span class="status">Full Time</span>
                                    </div>
                                    <div class="team">
                                        <img src="<?php echo 'assets/images/' . htmlspecialchars($match['away_team_logo']); ?>" 
                                            alt="<?php echo htmlspecialchars($match['away_team']); ?>"
                                            onerror="this.src='assets/images/default-team.png'">
                                        <span><?php echo htmlspecialchars($match['away_team']); ?></span>
                                    </div>
                                </div>
                                <div class="match-venue">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($match['stadium']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-matches">
                            <i class="fas fa-calendar-times"></i>
                            <p>No recent results available.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Gallery Carousel Section -->
<section class="gallery-carousel-section">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-images"></i> Photo Gallery</h2>
            <a href="media.php" class="view-all-btn">
                View All Photos <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="gallery-carousel-container">
            <div class="gallery-carousel" id="galleryCarousel">
                <?php if (!empty($gallery_images)): ?>
                    <?php foreach ($gallery_images as $image): ?>
                        <div class="gallery-slide">
                            <div class="gallery-box">
                                <a href="<?php echo htmlspecialchars($image['image_url']); ?>" class="gallery-link" data-caption="<?php echo htmlspecialchars($image['title']); ?>">
                                    <img src="<?php echo htmlspecialchars($image['thumbnail_url'] ?? $image['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($image['title']); ?>"
                                         onerror="this.src='assets/images/default-gallery.jpg'">
                                    <div class="gallery-overlay">
                                        <div class="gallery-info">
                                            <h4><?php echo htmlspecialchars($image['title']); ?></h4>
                                            <span class="gallery-category"><?php echo htmlspecialchars($image['category']); ?></span>
                                        </div>
                                        <i class="fas fa-search-plus"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="gallery-slide">
                        <div class="no-gallery-box">
                            <i class="fas fa-images"></i>
                            <h3>No Photos Available</h3>
                            <p>Check back later for photos!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Navigation buttons -->
            <button class="carousel-btn prev-btn" id="galleryPrevBtn" onclick="slideGallery(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="carousel-btn next-btn" id="galleryNextBtn" onclick="slideGallery(1)">
                <i class="fas fa-chevron-right"></i>
            </button>

            <!-- Dots indicator -->
            <div class="carousel-dots" id="galleryDots"></div>
        </div>
    </div>
</section>

<script src="assets/js/index.js"></script>
<script src="assets/js/news-carousel.js"></script>


<?php include 'includes/footer.php'; ?>
