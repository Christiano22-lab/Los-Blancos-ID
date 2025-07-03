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
<<<<<<< HEAD
                        <img src="<?php echo !empty($article['image']) ? 'assets/images/' . htmlspecialchars($article   ['image']) : 'assets/images/default-news.jpg'; ?>" 
                        alt="<?php echo htmlspecialchars($article['title']); ?>"
                        onerror="this.src='assets/images/default-news.jpg'"
                        loading="lazy">
=======
                        <img src="<?php echo !empty($article['image']) ? htmlspecialchars($article['image']) : 'assets/images/default-news.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($article['title']); ?>"
                             onerror="this.src='assets/images/default-news.jpg'">
>>>>>>> 3f9dcde68acb56b96a6b5a0664e4b76626655a50
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

<<<<<<< HEAD
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
=======
<style>
.news-header {
    text-align: center;
    margin-bottom: 3rem;
    padding: 2rem 0;
    border-bottom: 3px solid #FFD700;
}

.news-header h1 {
    font-size: 3rem;
    color: #003DA5;
    margin-bottom: 1rem;
}

.news-header h1 i {
    color: #FFD700;
    margin-right: 0.5rem;
}

.news-header p {
    font-size: 1.2rem;
    color: #666;
}

.news-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 3rem;
    gap: 2rem;
    flex-wrap: wrap;
}

.search-form {
    flex: 1;
    max-width: 400px;
}

.search-box {
    display: flex;
    border: 2px solid #003DA5;
    border-radius: 25px;
    overflow: hidden;
}

.search-box input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: none;
    outline: none;
    font-size: 1rem;
}

.search-box button {
    background: #003DA5;
    color: white;
    border: none;
    padding: 0.75rem 1rem;
    cursor: pointer;
    transition: background 0.3s;
}

.search-box button:hover {
    background: #002a80;
}

.category-filters {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 0.5rem 1rem;
    border: 2px solid #003DA5;
    color: #003DA5;
    text-decoration: none;
    border-radius: 20px;
    font-weight: 600;
    transition: all 0.3s;
    font-size: 0.9rem;
}

.filter-btn:hover,
.filter-btn.active {
    background: #003DA5;
    color: white;
}

.news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
}

/* Reuse styles from index.php */
.news-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.news-card:hover {
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

.news-card:hover .news-image img {
    transform: scale(1.05);
}

.news-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.3), transparent, rgba(0,0,0,0.7));
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 1rem;
}

.news-category {
    background: #FFD700;
    color: #000;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.news-stats {
    color: white;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.news-content {
    padding: 1.5rem;
}

.news-content h3 {
    margin: 0 0 1rem 0;
    font-size: 1.25rem;
    line-height: 1.4;
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
    line-height: 1.6;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.news-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    font-size: 0.85rem;
    color: #888;
}

.author-info, .date-info {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.read-more-btn {
    background: transparent;
    color: #003DA5;
    border: 2px solid #003DA5;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.read-more-btn:hover {
    background: #003DA5;
    color: white;
    transform: translateX(5px);
}

.no-news {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    color: #666;
}

.no-news i {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 1rem;
}

.no-news h3 {
    font-size: 2rem;
    color: #003DA5;
    margin-bottom: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .news-filters {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-form {
        max-width: none;
    }
    
    .category-filters {
        justify-content: center;
    }
    
    .news-grid {
        grid-template-columns: 1fr;
    }
    
    .news-header h1 {
        font-size: 2rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
>>>>>>> 3f9dcde68acb56b96a6b5a0664e4b76626655a50
