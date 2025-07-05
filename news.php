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
    
    // Handle edit news
    if (isset($_POST['edit_news']) && is_admin()) {
        $news_id = (int)$_POST['news_id'];
        $title = sanitize_input($_POST['title']);
        $excerpt = sanitize_input($_POST['excerpt']);
        $content = sanitize_input($_POST['content']);
        $category = sanitize_input($_POST['category']);
        
        // Handle image upload
        $image_update = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = 'assets/images/news/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'news_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_update = ", image = '" . db_escape($filename) . "'";
            }
        }
        
        // Update news in database
        $title_escaped = db_escape($title);
        $excerpt_escaped = db_escape($excerpt);
        $content_escaped = db_escape($content);
        $category_escaped = db_escape($category);
        
        $query = "UPDATE news SET 
                  title = '$title_escaped', 
                  excerpt = '$excerpt_escaped', 
                  content = '$content_escaped', 
                  category = '$category_escaped'
                  $image_update
                  WHERE id = $news_id";
        
        if (db_query($query)) {
            $_SESSION['message'] = "News article updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating news article.";
            $_SESSION['message_type'] = "error";
        }
        
        header("Location: news.php");
        exit;
    }
    
    // Handle delete news
    if (isset($_POST['delete_news']) && is_admin()) {
        $news_id = (int)$_POST['news_id'];
        
        // Get image filename to delete file
        $get_image_query = "SELECT image FROM news WHERE id = $news_id";
        $image_result = db_query($get_image_query);
        if ($image_data = db_fetch_array($image_result)) {
            if (!empty($image_data['image']) && file_exists('assets/images/news/' . $image_data['image'])) {
                unlink('assets/images/news/' . $image_data['image']);
            }
        }
        
        // Delete news from database
        $query = "DELETE FROM news WHERE id = $news_id";
        
        if (db_query($query)) {
            $_SESSION['message'] = "News article deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting news article.";
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

// Get specific news for editing if edit parameter is set
$edit_news = null;
if (isset($_GET['edit']) && is_admin()) {
    $edit_id = (int)$_GET['edit'];
    $edit_news = get_news_by_id($edit_id);
}

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

    <!-- Edit News Form for Admin -->
    <?php if ($edit_news && is_admin()): ?>
        <div id="editNewsForm" class="add-news-form" style="display: block;">
            <div class="form-container">
                <h3><i class="fas fa-edit"></i> Edit Article</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="news_id" value="<?php echo $edit_news['id']; ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_title">Article Title *</label>
                            <input type="text" name="title" id="edit_title" required maxlength="255" 
                                   value="<?php echo htmlspecialchars($edit_news['title']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="edit_category">Category *</label>
                            <select name="category" id="edit_category" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>" 
                                            <?php echo $edit_news['category'] === $cat ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_excerpt">Excerpt *</label>
                        <textarea name="excerpt" id="edit_excerpt" rows="3" required 
                                  placeholder="Brief summary of the article..."><?php echo htmlspecialchars($edit_news['excerpt']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_content">Content *</label>
                        <textarea name="content" id="edit_content" rows="8" required 
                                  placeholder="Full article content..."><?php echo htmlspecialchars($edit_news['content']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_image">Featured Image</label>
                        <?php if (!empty($edit_news['image'])): ?>
                            <div class="current-image">
                                <img src="assets/images/news/<?php echo htmlspecialchars($edit_news['image']); ?>" 
                                     alt="Current image" style="max-width: 200px; height: auto; margin-bottom: 10px;">
                                <p><small>Current image</small></p>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" id="edit_image" accept="image/*">
                        <small>Leave empty to keep current image. Recommended size: 800x400px</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="edit_news" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Article
                        </button>
                        <a href="news.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
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
                                // Already full path
                                $image_src = $article['image'];
                            } else {
                                // Just filename, add path
                                $image_src = 'assets/images/news/' . $article['image'];
                            }
                            
                            // Check if file exists
                            if (!file_exists($image_src)) {
                                $image_src = '/placeholder.svg?height=240&width=380';
                            }
                        }
                        ?>
                        <img src="<?php echo get_news_image_path($article['image']); ?>"
                        alt="<?php echo htmlspecialchars($article['title']); ?>"
                        onerror="this.src='/placeholder.svg?height=240&width=380'"
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
                        <div class="news-actions">
                            <a href="news-detail.php?id=<?php echo $article['id']; ?>" class="read-more-btn">
                                Read Full Article <i class="fas fa-chevron-right"></i>
                            </a>
                            
                            <!-- Admin Actions -->
                            <?php if (is_admin()): ?>
                                <div class="admin-actions">
                                    <a href="news.php?edit=<?php echo $article['id']; ?>" class="btn-admin-edit" title="Edit Article">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this article?');">
                                        <input type="hidden" name="news_id" value="<?php echo $article['id']; ?>">
                                        <button type="submit" name="delete_news" class="btn-admin-delete" title="Delete Article">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
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

<style>
/* Admin Actions Styles */
.news-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
}

.admin-actions {
    display: flex;
    gap: 8px;
}

.btn-admin-edit,
.btn-admin-delete {
    padding: 6px 8px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-admin-edit {
    background-color: #007bff;
    color: white;
}

.btn-admin-edit:hover {
    background-color: #0056b3;
    color: white;
    text-decoration: none;
}

.btn-admin-delete {
    background-color: #dc3545;
    color: white;
}

.btn-admin-delete:hover {
    background-color: #c82333;
}

.current-image img {
    border-radius: 4px;
    border: 1px solid #ddd;
}
</style>

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
