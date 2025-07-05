<?php
$current_page = "news";

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Get article ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['message'] = "Invalid article ID.";
    $_SESSION['message_type'] = "error";
    header("Location: news.php");
    exit;
}

// Get article
$article = get_news_by_id($id);

if (!$article) {
    $_SESSION['message'] = "Article not found.";
    $_SESSION['message_type'] = "error";
    header("Location: news.php");
    exit;
}

$page_title = $article['title'];
$page_description = $article['excerpt'] ?? substr(strip_tags($article['content']), 0, 160);

$article_image_path = get_news_image_path($article['image']);

// Create comments table if not exists
create_comments_table();

// Create news views table if not exists
create_news_views_table();

// Handle view tracking - increment views setiap kali artikel dibuka
if (is_logged_in()) {
    $user_id = $_SESSION['user_id'];
    
    // Langsung record view dan increment tanpa cek hari ini
    record_news_view($id, $user_id);
    increment_news_views($id);
} else {
    // Untuk user yang tidak login, tetap increment views
    increment_news_views($id);
}

// Get related articles
$related_articles = get_related_news($id, 3);

// Get comments with username instead of full name
$comments = get_news_comments_with_username($id);

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {
    if (!empty($_POST['comment'])) {
        $comment_content = sanitize_input($_POST['comment']);
        $user_id = $_SESSION['user_id'];
        
        if (add_comment($id, $user_id, $comment_content)) {
            $_SESSION['message'] = "Comment added successfully.";
            $_SESSION['message_type'] = "success";
            header("Location: news-detail.php?id=$id");
            exit;
        } else {
            $_SESSION['message'] = "Error adding comment.";
            $_SESSION['message_type'] = "error";
        }
    }
}

// Function to get comments with username
function get_news_comments_with_username($news_id) {
    $news_id = (int)$news_id;
    
    $query = "SELECT c.*, 
                     COALESCE(u.username, u.name) as display_name,
                     u.profile_image 
              FROM comments c 
              LEFT JOIN users u ON c.user_id = u.id 
              WHERE c.news_id = $news_id 
              ORDER BY c.created_at DESC";
    
    $result = db_query($query);
    return db_fetch_all($result);
}

include 'includes/header.php';
?>

<div class="news-detail-page">
    <div class="container">
        <!-- Navigation -->
        <div class="article-navigation">
            <a href="news.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to News
            </a>
        </div>

        <!-- Article -->
        <article class="article">
            <div class="article-header">
                <div class="article-category">
                    <?php echo htmlspecialchars($article['category'] ?? 'News'); ?>
                </div>
                <h1 class="article-title">
                    <?php echo htmlspecialchars($article['title']); ?>
                </h1>
                
                <div class="article-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span><?php echo format_date($article['date'] ?? $article['created_at']); ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-user"></i>
                        <span><?php echo htmlspecialchars($article['author'] ?? 'Admin'); ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-eye"></i>
                        <span><?php echo number_format($article['views'] ?? 0); ?> views</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-comment"></i>
                        <span><?php echo count($comments); ?> comments</span>
                    </div>
                </div>
            </div>

            <?php if (!empty($article['image'])): ?>
                <div class="article-featured-image">
                    <img src="<?php echo $article_image_path; ?>"
                    alt="<?php echo htmlspecialchars($article['title']); ?>"
                    onerror="this.src='assets/images/default-news.jpg'"
                    loading="lazy">
                </div>
            <?php endif; ?>

            <div class="article-layout">
                <div class="article-main">
                    <div class="article-content">
                        <?php echo $article['content']; ?>
                    </div>

                    <!-- Share Section -->
                    <div class="article-share">
                        <h3><i class="fas fa-share-alt"></i> Share this article</h3>
                        <div class="share-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                               target="_blank" class="share-button facebook">
                                <i class="fab fa-facebook-f"></i> 
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($article['title']); ?>" 
                               target="_blank" class="share-button twitter">
                                <i class="fab fa-twitter"></i> 
                            </a>
                            <a href="https://wa.me/?text=<?php echo urlencode($article['title'] . ' ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                               target="_blank" class="share-button whatsapp">
                                <i class="fab fa-whatsapp"></i> 
                            </a>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="article-comments">
                        <h3><i class="fas fa-comments"></i> Comments (<?php echo count($comments); ?>)</h3>
                        
                        <?php if (is_logged_in()): ?>
                            <form action="news-detail.php?id=<?php echo $id; ?>" method="post" class="comment-form">
                                <div class="form-group">
                                    <label for="comment">Leave a comment:</label>
                                    <textarea name="comment" id="comment" rows="4" 
                                              placeholder="Write your comment..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Post Comment
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="login-prompt">
                                <p><i class="fas fa-sign-in-alt"></i> Please <a href="login.php">sign in</a> to leave a comment.</p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="comments-list">
                            <?php if (empty($comments)): ?>
                                <div class="no-comments">
                                    <i class="fas fa-comment-slash"></i>
                                    <p>No comments yet. Be the first to comment!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($comments as $comment): ?>
                                    <?php
                                    $profile_image_name = $comment['profile_image'] ?? '';
                                    $profile_image_src = (!empty($profile_image_name) && file_exists($profile_image_name))
                                        ? $profile_image_name
                                        : 'assets/images/user-image.jpg';
                                    ?>
                                    <div class="comment">
                                        <div class="comment-avatar">
                                            <img src="<?php echo $profile_image_src; ?>"
                                                alt="<?php echo htmlspecialchars($comment['display_name'] ?? 'User'); ?>"
                                                loading="lazy"
                                                onerror="this.src='assets/images/user-image.jpg'">
                                        </div>
                                        <div class="comment-content">
                                            <div class="comment-header">
                                                <span class="comment-author">
                                                    <?php echo htmlspecialchars($comment['display_name'] ?? 'Anonymous'); ?>
                                                </span>
                                                <span class="comment-date">
                                                    <?php echo get_time_ago($comment['created_at']); ?>
                                                </span>
                                            </div>
                                            <div class="comment-text">
                                                <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="article-sidebar">
                    <?php if (!empty($related_articles)): ?>
                        <div class="sidebar-section">
                            <h3><i class="fas fa-newspaper"></i> Related Articles</h3>
                            <div class="related-articles">
                                <?php foreach ($related_articles as $related): ?>
                                    <div class="related-article">
                                        <a href="news-detail.php?id=<?php echo $related['id']; ?>">
                                            <div class="related-image">
                                                <img src="<?php echo get_news_image_path($related['image']); ?>"
                                                alt="<?php echo htmlspecialchars($related['title']); ?>"
                                                onerror="this.src='assets/images/placeholder.svg'"
                                                loading="lazy">
                                            </div>
                                            <div class="related-content">
                                                <h4><?php echo htmlspecialchars($related['title']); ?></h4>
                                                <div class="related-date">
                                                    <?php echo format_date($related['date'] ?? $related['created_at']); ?>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Viewer Statistics (Admin Only) -->
                    <?php if (is_admin()): ?>
                        <div class="sidebar-section">
                            <h3><i class="fas fa-chart-bar"></i> View Statistics</h3>
                            <div class="stats-box">
                                <div class="stat-item">
                                    <span class="stat-label">Total Views:</span>
                                    <span class="stat-value"><?php echo number_format($article['views'] ?? 0); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Today's Views:</span>
                                    <span class="stat-value"><?php echo get_today_views_count($id); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="sidebar-section">
                        <h3><i class="fas fa-envelope"></i> Newsletter</h3>
                        <div class="newsletter-box">
                            <p>Semoga Bermanfaat Sob</p>
                        </div>
                    </div>
                </aside>
            </div>
        </article>
    </div>
</div>

<link rel="stylesheet" href="assets/css/news-detail.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Highlight the comment form when empty
    const commentForm = document.querySelector('.comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            const textarea = this.querySelector('textarea');
            if (textarea.value.trim() === '') {
                e.preventDefault();
                textarea.focus();
                textarea.style.borderColor = '#dc3545';
                setTimeout(() => {
                    textarea.style.borderColor = '#e1e5e9';
                }, 2000);
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
