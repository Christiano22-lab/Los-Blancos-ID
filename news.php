<?php
$page_title = "Latest News";
$page_description = "Stay updated with the latest Real Madrid news, transfers, and match reviews";
$current_page = "news";

require_once 'includes/functions.php';

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;

// Get news based on filters
if (!empty($category)) {
    $news = get_news_by_category($category, $per_page);
    $page_title = $category . " News";
} elseif (!empty($search)) {
    $news = search_news($search, $per_page);
    $page_title = "Search Results: " . $search;
} else {
    $news = get_latest_news($per_page);
}

// Get news categories for filter
$categories = ['Match Review', 'Transfers', 'Champions League', 'Team News', 'Analysis', 'Interviews'];

include 'includes/header.php';
?>

<div class="container">
    <div class="news-header">
        <h1><i class="fas fa-newspaper"></i> <?php echo htmlspecialchars($page_title); ?></h1>
        <p>Stay updated with the latest Real Madrid news and updates</p>
    </div>

    <!-- Search and Filter -->
    <div class="news-filters">
        <form method="GET" class="search-form">
            <div class="search-box">
                <input type="text" name="search" placeholder="Search news..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
        
        <div class="category-filters">
            <a href="news.php" class="filter-btn <?php echo empty($category) ? 'active' : ''; ?>">
                All News
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="news.php?category=<?php echo urlencode($cat); ?>" 
                   class="filter-btn <?php echo $category === $cat ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- News Grid -->
    <div class="news-grid">
        <?php if (!empty($news)): ?>
            <?php foreach ($news as $article): ?>
                <article class="news-card">
                    <div class="news-image">
                        <img src="<?php echo !empty($article['image']) ? 'assets/images/' . htmlspecialchars($article   ['image']) : 'assets/images/default-news.jpg'; ?>" 
                        alt="<?php echo htmlspecialchars($article['title']); ?>"
                        onerror="this.src='assets/images/default-news.jpg'"
                        loading="lazy">
                        <div class="news-overlay">
                            <span class="news-category"><?php echo htmlspecialchars($article['category']); ?></span>
                            <div class="news-stats">
                                <span><i class="fas fa-eye"></i> <?php echo number_format($article['views'] ?? 0); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="news-content">
                        <h3>
                            <a href="news-detail.php?id=<?php echo $article['id']; ?>">
                                <?php echo htmlspecialchars($article['title']); ?>
                            </a>
                        </h3>
                        <p class="news-excerpt"><?php echo htmlspecialchars($article['excerpt']); ?></p>
                        <div class="news-meta">
                            <div class="author-info">
                                <i class="fas fa-user-circle"></i>
                                <span><?php echo htmlspecialchars($article['author']); ?></span>
                            </div>
                            <div class="date-info">
                                <i class="fas fa-calendar-alt"></i>
                                <span><?php echo format_date($article['date']); ?></span>
                            </div>
                        </div>
                        <a href="news-detail.php?id=<?php echo $article['id']; ?>" class="read-more-btn">
                            Read Full Article <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-news">
                <i class="fas fa-newspaper"></i>
                <h3>No News Found</h3>
                <p>
                    <?php if (!empty($search)): ?>
                        No articles found for "<?php echo htmlspecialchars($search); ?>". Try different keywords.
                    <?php elseif (!empty($category)): ?>
                        No articles found in "<?php echo htmlspecialchars($category); ?>" category.
                    <?php else: ?>
                        No news articles available at the moment.
                    <?php endif; ?>
                </p>
                <a href="news.php" class="btn btn-primary">View All News</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<link rel="stylesheet" href="assets/css/news.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Highlight active filter button
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Add hover effect to news cards
    const newsCards = document.querySelectorAll('.news-card');
    newsCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
