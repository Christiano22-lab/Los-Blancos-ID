<?php
$page_title = "Komunitas";
$page_description = "Bergabunglah dengan komunitas Madridista di berbagai platform sosial media";
$current_page = "community";

require_once 'includes/functions.php';
require_once 'includes/db.php';

// Get communities
$all_communities = get_communities();

include 'includes/header.php';
?>

<div class="community-page">
    <!-- Hero Section with Background Image -->
    <section class="community-hero" style="background-image: linear-gradient(rgba(0, 38, 96, 0.85), rgba(0, 38, 96, 0.75)), url('assets/images/Santiago.jpeg');">
        <div class="container">
            <div class="hero-content">
                <h1><i class="fas fa-users"></i> Komunitas Madridista</h1>
                <p class="hero-subtitle">Bergabunglah dengan ribuan Madridista Indonesia di berbagai platform sosial media</p>
                
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo count($all_communities); ?></div>
                        <div class="stat-label">Komunitas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php 
                            $total_members = 0;
                            foreach($all_communities as $community) {
                                $total_members += $community['member_count'];
                            }
                            echo format_member_count($total_members);
                        ?></div>
                        <div class="stat-label">Total Member</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">5</div>
                        <div class="stat-label">Platform</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter Tabs -->
    <section class="community-filter">
        <div class="container">
            <div class="filter-tabs">
                <button class="filter-btn active" data-filter="all">
                    <i class="fas fa-th-large"></i> Semua
                </button>
                <button class="filter-btn" data-filter="instagram">
                    <i class="fab fa-instagram"></i> Instagram
                </button>
                <button class="filter-btn" data-filter="whatsapp">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </button>
                <button class="filter-btn" data-filter="facebook">
                    <i class="fab fa-facebook-f"></i> Facebook
                </button>
                <button class="filter-btn" data-filter="telegram">
                    <i class="fab fa-telegram-plane"></i> Telegram
                </button>
            </div>
        </div>
    </section>

    <!-- Communities Grid -->
    <section class="communities-section">
        <div class="container">
            <div class="communities-grid">
                <?php if (!empty($all_communities)): ?>
                    <?php foreach ($all_communities as $community): ?>
                        <div class="community-card" data-platform="<?php echo $community['platform']; ?>">
                            <div class="card-header">
                                <div class="platform-badge" style="background-color: <?php echo get_platform_color($community['platform']); ?>">
                                    <i class="<?php echo get_platform_icon($community['platform']); ?>"></i>
                                    <?php echo ucfirst($community['platform']); ?>
                                </div>
                                <?php if ($community['is_official']): ?>
                                    <div class="official-badge">
                                        <i class="fas fa-check-circle"></i> Official
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-content">
                                <h3><?php echo htmlspecialchars($community['name']); ?></h3>
                                <p class="description"><?php echo htmlspecialchars($community['description']); ?></p>
                                
                                <div class="community-stats">
                                    <div class="stat">
                                        <i class="fas fa-users"></i>
                                        <span><?php echo format_member_count($community['member_count']); ?> Members</span>
                                    </div>
                                    <div class="stat">
                                        <i class="fas fa-calendar"></i>
                                        <span><?php echo date('M Y', strtotime($community['created_at'])); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <a href="<?php echo htmlspecialchars($community['link']); ?>" 
                                   target="_blank" 
                                   class="join-btn"
                                   style="background-color: <?php echo get_platform_color($community['platform']); ?>">
                                    <i class="<?php echo get_platform_icon($community['platform']); ?>"></i>
                                    Bergabung
                                </a>
                                <button class="share-btn" onclick="shareCommunity('<?php echo htmlspecialchars($community['name']); ?>', '<?php echo htmlspecialchars($community['link']); ?>')">
                                    <i class="fas fa-share-alt"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-content">
                        <i class="fas fa-users-slash"></i>
                        <h3>Belum Ada Komunitas</h3>
                        <p>Komunitas akan segera hadir!</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Add Community Button -->
            <?php if (is_logged_in()): ?>
                <div class="add-community-section">
                    <div class="add-community-card">
                        <div class="add-content">
                            <i class="fas fa-plus-circle"></i>
                            <h3>Tambah Komunitas Baru</h3>
                            <p>Punya komunitas Madridista? Bagikan dengan yang lain!</p>
                            <a href="add-community.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Komunitas
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<link rel="stylesheet" href="assets/css/community.css">

<script>
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const communityCards = document.querySelectorAll('.community-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const filter = btn.getAttribute('data-filter');
            
            // Update active button
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Filter cards
            communityCards.forEach(card => {
                const platform = card.getAttribute('data-platform');
                
                if (filter === 'all' || platform === filter) {
                    card.style.display = 'flex';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 50);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(10px)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 200);
                }
            });
        });
    });
});

// Share functionality
function shareCommunity(name, link) {
    if (navigator.share) {
        navigator.share({
            title: `Komunitas Madridista: ${name}`,
            text: `Bergabung dengan komunitas ${name} - Madridista Indonesia`,
            url: link
        }).catch(err => {
            console.log('Error sharing:', err);
        });
    } else {
        // Fallback for desktop
        const shareUrl = `${link}?utm_source=share&utm_medium=web&utm_campaign=community`;
        navigator.clipboard.writeText(shareUrl).then(() => {
            alert('Link komunitas telah disalin ke clipboard!');
        }).catch(err => {
            // Final fallback - open in new tab
            window.open(link, '_blank');
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?>