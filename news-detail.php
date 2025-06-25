<?php
$current_page = "news";

require_once 'includes/db.php';

// Get article ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get article
$article = get_news_by_id($id);

if (!$article) {
    $_SESSION['message'] = "Article not found.";
    $_SESSION['message_type'] = "error";
    header("Location: news.php");
    exit;
}

$page_title = $article['title'];
$page_description = $article['excerpt'];

// Get related articles
$related_articles = get_related_news($id, 3);

// Get comments
$query = "SELECT c.*, u.name, u.profile_image FROM comments c 
          JOIN users u ON c.user_id = u.id 
          WHERE c.news_id = $id ORDER BY c.created_at DESC";
$result = db_query($query);
$comments = db_fetch_all($result);

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {
    $comment = db_escape($_POST['comment']);
    $user_id = $_SESSION['user_id'];
    
    $query = "INSERT INTO comments (news_id, user_id, content, created_at) 
              VALUES ($id, $user_id, '$comment', NOW())";
    
    if (db_query($query)) {
        $_SESSION['message'] = "Comment added successfully.";
        $_SESSION['message_type'] = "success";
        header("Location: news-detail.php?id=$id");
        exit;
    } else {
        $_SESSION['message'] = "Error adding comment.";
        $_SESSION['message_type'] = "error";
    }
}

// Increment view count
$query = "UPDATE news SET views = views + 1 WHERE id = $id";
db_query($query);

include 'includes/header.php';
?>

<div class="container">
    <div class="article-navigation mb-6">
        <a href="news.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to News
        </a>
    </div>

    <article class="article">
        <div class="article-header">
            <span class="article-category"><?php echo $article['category']; ?></span>
            <h1><?php echo $article['title']; ?></h1>
            
            <div class="article-meta">
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    <span><?php echo format_date($article['date']); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-user"></i>
                    <span><?php echo $article['author']; ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-comment"></i>
                    <span><?php echo count($comments); ?> comments</span>
                </div>
            </div>
        </div>

        <div class="article-featured-image">
            <img src="<?php echo $article['image']; ?>" alt="<?php echo $article['title']; ?>">
        </div>

        <div class="article-content-wrapper">
            <div class="article-main">
                <div class="article-content">
                    <?php echo $article['content']; ?>
                </div>

                <div class="article-share">
                    <h3>Share this article</h3>
                    <div class="share-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/news-detail.php?id=' . $id); ?>" target="_blank" class="share-button facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . '/news-detail.php?id=' . $id); ?>&text=<?php echo urlencode($article['title']); ?>" target="_blank" class="share-button twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode($article['title'] . ' ' . SITE_URL . '/news-detail.php?id=' . $id); ?>" target="_blank" class="share-button whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <div class="article-comments">
                    <h3>Comments (<?php echo count($comments); ?>)</h3>
                    
                    <?php if (is_logged_in()): ?>
                        <form action="news-detail.php?id=<?php echo $id; ?>" method="post" class="comment-form">
                            <div class="form-group">
                                <textarea name="comment" placeholder="Write your comment..." required></textarea>
                            </div>
                            <button type="submit" class="btn">Post Comment</button>
                        </form>
                    <?php else: ?>
                        <div class="login-prompt">
                            <p>Please <a href="login.php">sign in</a> to leave a comment.</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="comments-list">
                        <?php if (empty($comments)): ?>
                            <p class="no-comments">No comments yet. Be the first to comment!</p>
                        <?php else: ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment">
                                    <div class="comment-avatar">
                                        <img src="<?php echo !empty($comment['profile_image']) ? $comment['profile_image'] : 'assets/images/default-avatar.png'; ?>" alt="<?php echo $comment['name']; ?>">
                                    </div>
                                    <div class="comment-content">
                                        <div class="comment-header">
                                            <span class="comment-author"><?php echo $comment['name']; ?></span>
                                            <span class="comment-date"><?php echo get_time_ago($comment['created_at']); ?></span>
                                        </div>
                                        <div class="comment-text">
                                            <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                                        </div>
                                        <div class="comment-actions">
                                            <button class="comment-reply-btn" data-comment-id="<?php echo $comment['id']; ?>">Reply</button>
                                            <button class="comment-like-btn" data-comment-id="<?php echo $comment['id']; ?>">Like</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <aside class="article-sidebar">
                <div class="sidebar-section">
                    <h3>Related Articles</h3>
                    <div class="related-articles">
                        <?php foreach ($related_articles as $related): ?>
                            <div class="related-article">
                                <a href="news-detail.php?id=<?php echo $related['id']; ?>">
                                    <img src="<?php echo $related['image']; ?>" alt="<?php echo $related['title']; ?>">
                                    <h4><?php echo $related['title']; ?></h4>
                                </a>
                                <div class="related-date"><?php echo format_date($related['date']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="sidebar-section">
                    <h3>Newsletter</h3>
                    <div class="newsletter-box">
                        <p>Subscribe to our newsletter for the latest Real Madrid updates.</p>
                        <form action="subscribe.php" method="post" class="sidebar-form">
                            <input type="email" name="email" placeholder="Your email address" required>
                            <button type="submit" class="btn btn-block">Subscribe</button>
                        </form>
                    </div>
                </div>
            </aside>
        </div>
    </article>
</div>

<?php include 'includes/footer.php'; ?>