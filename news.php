<?php
$page_title = "Latest News";
$page_description = "Stay updated with the latest Real Madrid news, transfers, and match reviews";
$current_page = "news";

require_once 'includes/functions.php';

// Handle form submission for adding news
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {
    if (isset($_POST['add_news'])) {
        $title = sanitize_input($_POST['title']);
        $excerpt = sanitize_input($_POST['excerpt']);
        $content = sanitize_input($_POST['content']);
        $category = sanitize_input($_POST['category']);
        $author_id = $_SESSION['user_id'];
        $author = $_SESSION['user_name'];
        
        // Handle image upload
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = 'assets/images/news/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'news_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $filename;
            }
        }
        
        // Insert news into database
        $title_escaped = db_escape($title);
        $excerpt_escaped = db_escape($excerpt);
        $content_escaped = db_escape($content);
        $category_escaped = db_escape($category);
        $author_escaped = db_escape($author);
        $image_escaped = db_escape($image);
        
        $query = "INSERT INTO news (title, excerpt, content, image, category, author, author_id, date, created_at) 
                  VALUES ('$title_escaped', '$excerpt_escaped', '$content_escaped', '$image_escaped', '$category_escaped', '$author_escaped', $author_id, CURDATE(), NOW())";
        
        if (db_query($query)) {
            $_SESSION['message'] = "News article added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error adding news article.";
            $_SESSION['message_type'] = "error";
        }
        
        header("Location: news.php");
        exit;
    }
}

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = is_logged_in() ? 12 : 6; // Show fewer articles for non-logged users

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
    <?php display_message(); ?>
    
    <div class="news-header">
        <h1><i class="fas fa-newspaper"></i> <?php echo htmlspecialchars($page_title); ?></h1>
        <p>Stay updated with the latest Real Madrid news and updates</p>
    </div>

    <!-- Add News Button for Logged Users -->
    <?php if (is_logged_in()): ?>
        <div class="add-news-section">
            <button class="btn btn-primary" onclick="toggleAddNewsForm()">
                <i class="fas fa-plus"></i> Add News Article
            </button>
        </div>

        <!-- Add News Form (Hidden by default) -->
        <div id="addNewsForm" class="add-news-form" style="display: none;">
            <div class="form-container">
                <h3><i class="fas fa-edit"></i> Add New Article</h3>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Article Title *</label>
                            <input type="text" name="title" id="title" required maxlength="255">
                        </div>
                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select name="category" id="category" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="excerpt">Excerpt *</label>
                        <textarea name="excerpt" id="excerpt" rows="3" required placeholder="Brief summary of the article..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Content *</label>
                        <textarea name="content" id="content" rows="8" required placeholder="Full article content..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Featured Image</label>
                        <input type="file" name="image" id="image" accept="image/*">
                        <small>Recommended size: 800x400px</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="add_news" class="btn btn-primary">
                            <i class="fas fa-save"></i> Publish Article
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="toggleAddNewsForm()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

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
                        <?php 
                        // Determine image source based on database
                        $image_src = '/placeholder.svg?height=240&width=380';
                        if (!empty($article['image'])) {
                            // Check if it's a full path or just filename
                            if (strpos($article['image'], 'assets/') === 0) {
                                $image_src = $article['image'];
                            } else {
                                $image_src = 'assets/images/news/' . $article['image'];
                            }
                            
                            // Check if file exists
                            if (!file_exists($image_src)) {
                                $image_src = '/placeholder.svg?height=240&width=380';
                            }
                        }
                        ?>
                        <img src="<?php echo !empty($article['image']) ? 'assets/images/news/' . htmlspecialchars($article['image']) : 'assets/images/default-news.jpg'; ?>"
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

function toggleAddNewsForm() {
    const form = document.getElementById('addNewsForm');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth' });
    } else {
        form.style.display = 'none';
    }
}
</script>

<?php include 'includes/footer.php'; ?>
