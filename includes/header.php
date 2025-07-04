<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' | ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'The ultimate fan site for Real Madrid supporters worldwide'; ?>">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/logout-modal.css">
    <?php if (isset($extra_css)): ?>
        <?php echo $extra_css; ?>
    <?php endif; ?>
</head>
<body class="<?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark-theme' : ''; ?>">
    <header class="header">
        <div class="container header-container">
            <div class="logo">
                <a href="index.php">
                    <img src="assets/images/logo.png" alt="<?php echo SITE_NAME; ?>">
                    <span class="logo-text"><?php echo SITE_NAME; ?></span>
                </a>
            </div>

            <nav class="desktop-nav">
                <ul>
                    <li><a href="index.php" <?php echo $current_page === 'home' ? 'class="active"' : ''; ?>>Home</a></li>
                    <li><a href="about.php" <?php echo $current_page === 'about' ? 'class="active"' : ''; ?>>About Us</a></li>
                    <li><a href="news.php" <?php echo $current_page === 'news' ? 'class="active"' : ''; ?>>News</a></li>
                    <li><a href="matches.php" <?php echo $current_page === 'matches' ? 'class="active"' : ''; ?>>Matches</a></li>
                    <li><a href="media.php" <?php echo $current_page === 'media' ? 'class="active"' : ''; ?>>Media</a></li>
                    <li><a href="community.php" <?php echo $current_page === 'community' ? 'class="active"' : ''; ?>>Community</a></li>
                </ul>
            </nav>

            <div class="header-actions">              
                <?php if (is_logged_in()): ?>
                    <a href="profile.php" class="profile-button" aria-label="Profile">
                        <i class="fas fa-user"></i>
                    </a>
                    <a href="logout.php" class="sign-in-button">Sign Out</a>
                <?php else: ?>
                    <a href="login.php" class="sign-in-button">Sign In</a>
                <?php endif; ?>
                
                <button class="mobile-menu-button" id="mobileMenuToggle" aria-label="Menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <div class="mobile-menu" id="mobileMenu">
            <div class="container">
                <a href="index.php" <?php echo $current_page === 'home' ? 'class="active"' : ''; ?>>Home</a>
                <a href="news.php" <?php echo $current_page === 'news' ? 'class="active"' : ''; ?>>News</a>
                <a href="matches.php" <?php echo $current_page === 'matches' ? 'class="active"' : ''; ?>>Matches</a>
               <a href="media.php" <?php echo $current_page === 'media' ? 'class="active"' : ''; ?>>Media</a>
                <a href="community.php" <?php echo $current_page === 'community' ? 'class="active"' : ''; ?>>Community</a>
                
                <div class="mobile-sign-in">
                    <?php if (is_logged_in()): ?>
                        <a href="profile.php" class="btn btn-outline mobile">Profile</a>
                        <a href="logout.php" class="sign-in-button mobile">Sign Out</a>
                    <?php else: ?>
                        <a href="login.php" class="sign-in-button mobile">Sign In</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main>
        <?php display_message(); ?>

    <script src="assets/js/logout-modal.js"></script>
