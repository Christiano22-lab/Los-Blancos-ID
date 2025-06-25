<?php
$page_title = "Home";
$page_description = "The ultimate fan site for Real Madrid supporters worldwide";
$current_page = "home";

require_once 'includes/functions.php';

// Pastikan semua data dummy ter-load
add_dummy_data();

// Get data for homepage
$latest_news = get_latest_news(4);
$upcoming_matches = get_upcoming_matches(3);
$recent_results = get_recent_results(2);
$gallery_images = get_gallery_images(6);
$forum_topics = get_recent_forum_topics(3);

include 'includes/header.php';
?>

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
                                    <img src="<?php echo !empty($news['image']) ? htmlspecialchars($news['image']) : 'assets/images/default-news.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($news['title']); ?>"
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
            <button class="carousel-btn prev-btn" onclick="slideNews(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="carousel-btn next-btn" onclick="slideNews(1)">
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
                                        <img src="<?php echo htmlspecialchars($match['home_team_logo']); ?>" 
                                             alt="<?php echo htmlspecialchars($match['home_team']); ?>"
                                             onerror="this.src='assets/images/default-team.png'">
                                        <span><?php echo htmlspecialchars($match['home_team']); ?></span>
                                    </div>
                                    <div class="vs">
                                        <span>VS</span>
                                        <span class="time"><?php echo htmlspecialchars($match['match_time']); ?></span>
                                    </div>
                                    <div class="team">
                                        <img src="<?php echo htmlspecialchars($match['away_team_logo']); ?>" 
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
                                        <img src="<?php echo htmlspecialchars($match['home_team_logo']); ?>" 
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
                                        <img src="<?php echo htmlspecialchars($match['away_team_logo']); ?>" 
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
            <a href="gallery.php" class="view-all-btn">
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
            <button class="carousel-btn prev-btn" onclick="slideGallery(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="carousel-btn next-btn" onclick="slideGallery(1)">
                <i class="fas fa-chevron-right"></i>
            </button>
            
            <!-- Dots indicator -->
            <div class="carousel-dots" id="galleryDots"></div>
        </div>
    </div>
</section>

<!-- Forum Discussion Section -->
<section class="forum-section">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-comments"></i> Discussion Forum</h2>
            <a href="forum.php" class="view-all-btn">
                Join Discussion <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="forum-preview">
            <?php if (!empty($forum_topics)): ?>
                <div class="forum-topics">
                    <?php foreach ($forum_topics as $topic): ?>
                        <div class="forum-topic-card">
                            <div class="topic-header">
                                <div class="topic-category">
                                    <span class="category-badge"><?php echo htmlspecialchars($topic['category_name']); ?></span>
                                </div>
                                <div class="topic-stats">
                                    <span><i class="fas fa-comments"></i> <?php echo number_format($topic['post_count']); ?></span>
                                    <span><i class="fas fa-eye"></i> <?php echo number_format($topic['views']); ?></span>
                                </div>
                            </div>
                            <h3 class="topic-title">
                                <a href="forum-topic.php?id=<?php echo $topic['id']; ?>">
                                    <?php echo htmlspecialchars($topic['title']); ?>
                                </a>
                            </h3>
                            <div class="topic-meta">
                                <div class="author">
                                    <img src="<?php echo !empty($topic['profile_image']) ? htmlspecialchars($topic['profile_image']) : 'assets/images/default-avatar.png'; ?>" 
                                         alt="<?php echo htmlspecialchars($topic['author_name']); ?>"
                                         class="author-avatar">
                                    <span><?php echo htmlspecialchars($topic['author_name']); ?></span>
                                </div>
                                <div class="topic-date">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo get_time_ago($topic['created_at']); ?></span>
                                </div>
                            </div>
                            <div class="topic-preview">
                                <?php echo htmlspecialchars(substr(strip_tags($topic['content']), 0, 150)) . '...'; ?>
                            </div>
                            <a href="forum-topic.php?id=<?php echo $topic['id']; ?>" class="read-more-btn">
                                Read Discussion <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="forum-cta">
                    <div class="forum-cta-content">
                        <h3>Join the Conversation</h3>
                        <p>Connect with fellow Madridistas, share your thoughts, and stay updated with the latest discussions.</p>
                        <?php if (!is_logged_in()): ?>
                            <div class="cta-buttons">
                                <a href="login.php" class="btn btn-primary">Sign In to Participate</a>
                                <a href="register.php" class="btn btn-secondary">Create Account</a>
                            </div>
                        <?php else: ?>
                            <div class="cta-buttons">
                                <a href="forum-new-topic.php" class="btn btn-primary">Start New Topic</a>
                                <a href="forum.php" class="btn btn-secondary">Browse All Forums</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-forum">
                    <i class="fas fa-comments"></i>
                    <h3>No Forum Topics</h3>
                    <p>Be the first to start a discussion!</p>
                    <?php if (is_logged_in()): ?>
                        <a href="forum-new-topic.php" class="btn btn-primary">Start New Topic</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary">Sign In to Participate</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* Hero Section Styles */
.hero-section {
    text-align: center;
    padding: 6rem 2rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    margin: 0;
    position: relative;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(rgba(0, 51, 173, 0.6), rgba(0, 0, 0, 0.7));
    z-index: -1;
}

.hero-section h1 {
    font-size: 3.5rem;
    color: white;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    margin-bottom: 1.5rem;
}

.hero-section p {
    font-size: 1.3rem;
    color: white;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    margin-bottom: 2.5rem;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.hero-buttons {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 0.75rem 2rem;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 1rem;
    min-width: 150px;
}

.btn-primary {
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: #000;
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #FFA500, #FFD700);
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(255, 215, 0, 0.3);
}

.btn-secondary {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(255, 255, 255, 0.2);
}

/* News Carousel Styles */
.news-carousel-section {
    padding: 4rem 0;
    background: rgba(255, 255, 255, 0.95);
}

.news-carousel-container {
    position: relative;
    overflow: hidden;
    border-radius: 15px;
}

.news-carousel {
    display: flex;
    transition: transform 0.5s ease;
}

.news-slide {
    min-width: 300px;
    margin-right: 2rem;
    flex-shrink: 0;
}

.news-box {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s;
    height: 400px;
    display: flex;
    flex-direction: column;
}

.news-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.news-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.news-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.news-box:hover .news-image img {
    transform: scale(1.05);
}

.news-overlay {
    position: absolute;
    top: 1rem;
    right: 1rem;
}

.news-category {
    background: #FFD700;
    color: #000;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.news-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.news-content h3 {
    margin: 0 0 1rem 0;
    font-size: 1.1rem;
    line-height: 1.4;
    flex: 1;
}

.news-content h3 a {
    color: #003DA5;
    text-decoration: none;
    transition: color 0.3s;
}

.news-content h3 a:hover {
    color: #FFD700;
}

.news-excerpt {
    color: #666;
    line-height: 1.5;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.news-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: #888;
    margin-top: auto;
}

.no-news-box {
    background: white;
    border-radius: 15px;
    padding: 3rem 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    height: 400px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.no-news-box i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 1rem;
}

/* Matches Section */
.matches-section {
    padding: 4rem 0;
    background: rgba(255, 255, 255, 0.9);
}

.matches-tabs {
    margin-top: 2rem;
}

.tab-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.tab-btn {
    background: transparent;
    border: 2px solid #003DA5;
    color: #003DA5;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tab-btn.active,
.tab-btn:hover {
    background: #003DA5;
    color: white;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.matches-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.match-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.match-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.match-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #eee;
}

.competition {
    background: #FFD700;
    color: #000;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.match-date {
    color: #666;
    font-size: 0.9rem;
}

.match-teams {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.team {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    flex: 1;
}

.team img {
    width: 50px;
    height: 50px;
    object-fit: contain;
}

.team span {
    font-weight: 600;
    text-align: center;
    font-size: 0.9rem;
}

.vs, .score {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    flex: 0 0 auto;
    margin: 0 1rem;
}

.vs span:first-child,
.score-numbers {
    font-size: 1.5rem;
    font-weight: bold;
    color: #003DA5;
}

.time, .status {
    font-size: 0.8rem;
    color: #666;
}

.match-venue {
    text-align: center;
    color: #666;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
}

.no-matches {
    grid-column: 1 / -1;
    text-align: center;
    padding: 2rem;
    color: #666;
}

.no-matches i {
    font-size: 2rem;
    color: #ddd;
    margin-bottom: 0.5rem;
}

/* Gallery Carousel Styles */
.gallery-carousel-section {
    padding: 4rem 0;
    background: rgba(0, 51, 173, 0.05);
}

.gallery-carousel-container {
    position: relative;
    overflow: hidden;
    border-radius: 15px;
}

.gallery-carousel {
    display: flex;
    transition: transform 0.5s ease;
}

.gallery-slide {
    min-width: 250px;
    margin-right: 1.5rem;
    flex-shrink: 0;
}

.gallery-box {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s;
    aspect-ratio: 1 / 1;
}

.gallery-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.gallery-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.gallery-box:hover img {
    transform: scale(1.1);
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    opacity: 0;
    transition: opacity 0.3s;
}

.gallery-box:hover .gallery-overlay {
    opacity: 1;
}

.gallery-info {
    text-align: center;
    color: white;
    margin-top: auto;
}

.gallery-info h4 {
    font-size: 1rem;
    margin-bottom: 0.5rem;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
}

.gallery-category {
    background: #FFD700;
    color: #000;
    padding: 0.25rem 0.5rem;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
}

.gallery-overlay i {
    color: white;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.no-gallery-box {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    aspect-ratio: 1 / 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.no-gallery-box i {
    font-size: 2.5rem;
    color: #ddd;
    margin-bottom: 1rem;
}

/* Forum Section */
.forum-section {
    padding: 4rem 0;
    background: rgba(255, 255, 255, 0.95);
}

.forum-preview {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}

.forum-topics {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.forum-topic-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.forum-topic-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.topic-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.category-badge {
    background: #003DA5;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.topic-stats {
    display: flex;
    gap: 1rem;
    color: #666;
    font-size: 0.8rem;
}

.topic-title {
    margin: 0 0 1rem 0;
    font-size: 1.25rem;
    line-height: 1.4;
}

.topic-title a {
    color: #003DA5;
    text-decoration: none;
    transition: color 0.3s;
}

.topic-title a:hover {
    color: #FFD700;
}

.topic-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    font-size: 0.85rem;
    color: #888;
}

.author {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.author-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
}

.topic-date {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.topic-preview {
    color: #666;
    line-height: 1.6;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.forum-cta {
    background: linear-gradient(135deg, #003DA5, #002470);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
}

.forum-cta-content {
    padding: 2rem;
    text-align: center;
    color: white;
}

.forum-cta h3 {
    font-size: 1.75rem;
    margin-bottom: 1rem;
}

.forum-cta p {
    margin-bottom: 2rem;
    line-height: 1.6;
}

.cta-buttons {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.no-forum {
    grid-column: 1 / -1;
    text-align: center;
    padding: 3rem;
    color: #666;
}

.no-forum i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 1rem;
}

/* Carousel Navigation */
.carousel-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 51, 173, 0.8);
    color: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.carousel-btn:hover {
    background: #003DA5;
    transform: translateY(-50%) scale(1.1);
}

.prev-btn {
    left: -25px;
}

.next-btn {
    right: -25px;
}

.carousel-dots {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 2rem;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #ccc;
    cursor: pointer;
    transition: all 0.3s;
}

.dot.active {
    background: #003DA5;
    transform: scale(1.2);
}

/* Responsive Carousel */
@media (max-width: 768px) {
    .news-slide {
        min-width: 280px;
        margin-right: 1rem;
    }
    
    .gallery-slide {
        min-width: 200px;
        margin-right: 1rem;
    }
    
    .carousel-btn {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .prev-btn {
        left: -20px;
    }
    
    .next-btn {
        right: -20px;
    }
}

@media (max-width: 576px) {
    .news-slide {
        min-width: 250px;
    }
    
    .gallery-slide {
        min-width: 180px;
    }
}
</style>

<script>
// Carousel functionality
let newsCurrentSlide = 0;
let galleryCurrentSlide = 0;
let newsSlides = [];
let gallerySlides = [];

document.addEventListener('DOMContentLoaded', function() {
    // Initialize carousels
    initNewsCarousel();
    initGalleryCarousel();
    
    // Auto-slide every 5 seconds
    setInterval(() => {
        slideNews(1);
        slideGallery(1);
    }, 5000);
});

function initNewsCarousel() {
    const carousel = document.getElementById('newsCarousel');
    if (!carousel) return;
    
    newsSlides = carousel.querySelectorAll('.news-slide');
    if (newsSlides.length === 0) return;
    
    // Create dots
    const dotsContainer = document.getElementById('newsDots');
    dotsContainer.innerHTML = '';
    
    const visibleSlides = getVisibleSlides();
    const totalDots = Math.ceil(newsSlides.length / visibleSlides);
    
    for (let i = 0; i < totalDots; i++) {
        const dot = document.createElement('span');
        dot.className = 'dot';
        if (i === 0) dot.classList.add('active');
        dot.onclick = () => goToNewsSlide(i);
        dotsContainer.appendChild(dot);
    }
    
    updateNewsCarousel();
}

function initGalleryCarousel() {
    const carousel = document.getElementById('galleryCarousel');
    if (!carousel) return;
    
    gallerySlides = carousel.querySelectorAll('.gallery-slide');
    if (gallerySlides.length === 0) return;
    
    // Create dots
    const dotsContainer = document.getElementById('galleryDots');
    dotsContainer.innerHTML = '';
    
    const visibleSlides = getVisibleGallerySlides();
    const totalDots = Math.ceil(gallerySlides.length / visibleSlides);
    
    for (let i = 0; i < totalDots; i++) {
        const dot = document.createElement('span');
        dot.className = 'dot';
        if (i === 0) dot.classList.add('active');
        dot.onclick = () => goToGallerySlide(i);
        dotsContainer.appendChild(dot);
    }
    
    updateGalleryCarousel();
}

function getVisibleSlides() {
    const width = window.innerWidth;
    if (width >= 1200) return 4;
    if (width >= 768) return 3;
    if (width >= 576) return 2;
    return 1;
}

function getVisibleGallerySlides() {
    const width = window.innerWidth;
    if (width >= 1200) return 5;
    if (width >= 768) return 4;
    if (width >= 576) return 3;
    return 2;
}

function slideNews(direction) {
    if (newsSlides.length === 0) return;
    
    const visibleSlides = getVisibleSlides();
    const maxSlide = Math.ceil(newsSlides.length / visibleSlides) - 1;
    
    newsCurrentSlide += direction;
    
    if (newsCurrentSlide > maxSlide) {
        newsCurrentSlide = 0;
    } else if (newsCurrentSlide < 0) {
        newsCurrentSlide = maxSlide;
    }
    
    updateNewsCarousel();
}

function slideGallery(direction) {
    if (gallerySlides.length === 0) return;
    
    const visibleSlides = getVisibleGallerySlides();
    const maxSlide = Math.ceil(gallerySlides.length / visibleSlides) - 1;
    
    galleryCurrentSlide += direction;
    
    if (galleryCurrentSlide > maxSlide) {
        galleryCurrentSlide = 0;
    } else if (galleryCurrentSlide < 0) {
        galleryCurrentSlide = maxSlide;
    }
    
    updateGalleryCarousel();
}

function goToNewsSlide(slideIndex) {
    newsCurrentSlide = slideIndex;
    updateNewsCarousel();
}

function goToGallerySlide(slideIndex) {
    galleryCurrentSlide = slideIndex;
    updateGalleryCarousel();
}

function updateNewsCarousel() {
    const carousel = document.getElementById('newsCarousel');
    if (!carousel) return;
    
    const visibleSlides = getVisibleSlides();
    const slideWidth = 300 + 32; // slide width + margin
    const offset = newsCurrentSlide * slideWidth * visibleSlides;
    
    carousel.style.transform = `translateX(-${offset}px)`;
    
    // Update dots
    const dots = document.querySelectorAll('#newsDots .dot');
    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === newsCurrentSlide);
    });
}

function updateGalleryCarousel() {
    const carousel = document.getElementById('galleryCarousel');
    if (!carousel) return;
    
    const visibleSlides = getVisibleGallerySlides();
    const slideWidth = 250 + 24; // slide width + margin
    const offset = galleryCurrentSlide * slideWidth * visibleSlides;
    
    carousel.style.transform = `translateX(-${offset}px)`;
    
    // Update dots
    const dots = document.querySelectorAll('#galleryDots .dot');
    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === galleryCurrentSlide);
    });
}

// Handle window resize
window.addEventListener('resize', () => {
    updateNewsCarousel();
    updateGalleryCarousel();
});

// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.getAttribute('data-tab');
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            button.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
    
    // Initialize lightbox for gallery if available
    if (typeof SimpleLightbox !== 'undefined') {
        new SimpleLightbox('.gallery-link', {
            captionsData: 'caption',
            captionDelay: 250
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
