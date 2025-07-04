<?php
$page_title = "Komunitas";
$page_description = "Bergabunglah dengan komunitas Madridista di berbagai platform sosial media";
$current_page = "community";

require_once 'includes/functions.php';
require_once 'includes/db.php';

// Handle form submission for adding/editing community (Admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_admin()) {
    if (isset($_POST['add_community']) || isset($_POST['edit_community'])) {
        $name = sanitize_input($_POST['name']);
        $description = sanitize_input($_POST['description']);
        $platform = sanitize_input($_POST['platform']);
        $link = sanitize_input($_POST['link']);
        $member_count = (int)$_POST['member_count'];
        $is_official = isset($_POST['is_official']) ? 1 : 0;
        
        if (isset($_POST['edit_community'])) {
            // Update existing community
            $community_id = (int)$_POST['community_id'];
            
            $name_escaped = db_escape($name);
            $description_escaped = db_escape($description);
            $platform_escaped = db_escape($platform);
            $link_escaped = db_escape($link);
            
            $query = "UPDATE communities SET 
                      name = '$name_escaped',
                      description = '$description_escaped',
                      platform = '$platform_escaped',
                      link = '$link_escaped',
                      member_count = $member_count,
                      is_official = $is_official,
                      updated_at = NOW()
                      WHERE id = $community_id";
            
            if (db_query($query)) {
                $_SESSION['message'] = "Community updated successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error updating community.";
                $_SESSION['message_type'] = "error";
            }
        } else {
            // Insert new community
            if (add_community($name, $description, $platform, $link, $member_count, $_SESSION['user_id'])) {
                // Update official status if needed
                if ($is_official) {
                    $community_id = db_insert_id();
                    $query = "UPDATE communities SET is_official = 1 WHERE id = $community_id";
                    db_query($query);
                }
                
                $_SESSION['message'] = "Community added successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error adding community.";
                $_SESSION['message_type'] = "error";
            }
        }
        
        header("Location: community.php");
        exit;
    }
    
    // Handle delete community
    if (isset($_POST['delete_community'])) {
        $community_id = (int)$_POST['community_id'];
        $query = "DELETE FROM communities WHERE id = $community_id";
        
        if (db_query($query)) {
            $_SESSION['message'] = "Community deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting community.";
            $_SESSION['message_type'] = "error";
        }
        
        header("Location: community.php");
        exit;
    }
}

// Get community for editing if edit_id is provided
$edit_community = null;
if (isset($_GET['edit']) && is_admin()) {
    $edit_id = (int)$_GET['edit'];
    $edit_query = "SELECT * FROM communities WHERE id = $edit_id LIMIT 1";
    $edit_result = db_query($edit_query);
    if (db_num_rows($edit_result) == 1) {
        $edit_community = db_fetch_array($edit_result);
    }
}

// Get communities
$all_communities = get_communities();

include 'includes/header.php';
?>

<div class="community-page">
    <?php display_message(); ?>
    
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

    <!-- Admin Add/Edit Community Section -->
    <?php if (is_admin()): ?>
        <div class="admin-section">
            <div class="container">
                <?php if (!$edit_community): ?>
                    <div class="admin-controls">
                        <button class="btn btn-primary" onclick="toggleAddCommunityForm()">
                            <i class="fas fa-plus"></i> Add New Community
                        </button>
                    </div>
                <?php else: ?>
                    <div class="edit-mode-header">
                        <h3><i class="fas fa-edit"></i> Editing Community: <?php echo htmlspecialchars($edit_community['name']); ?></h3>
                        <a href="community.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel Edit
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Add/Edit Community Form -->
                <div id="addCommunityForm" class="add-community-form" style="display: <?php echo $edit_community ? 'block' : 'none'; ?>;">
                    <div class="form-container">
                        <h3><i class="fas fa-users"></i> <?php echo $edit_community ? 'Edit Community' : 'Add New Community'; ?></h3>
                        <form method="POST">
                            <?php if ($edit_community): ?>
                                <input type="hidden" name="community_id" value="<?php echo $edit_community['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Community Name *</label>
                                    <input type="text" name="name" id="name" required maxlength="255" value="<?php echo $edit_community ? htmlspecialchars($edit_community['name']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="platform">Platform *</label>
                                    <select name="platform" id="platform" required>
                                        <option value="">Select Platform</option>
                                        <option value="instagram" <?php echo ($edit_community && $edit_community['platform'] == 'instagram') ? 'selected' : ''; ?>>Instagram</option>
                                        <option value="whatsapp" <?php echo ($edit_community && $edit_community['platform'] == 'whatsapp') ? 'selected' : ''; ?>>WhatsApp</option>
                                        <option value="facebook" <?php echo ($edit_community && $edit_community['platform'] == 'facebook') ? 'selected' : ''; ?>>Facebook</option>
                                        <option value="telegram" <?php echo ($edit_community && $edit_community['platform'] == 'telegram') ? 'selected' : ''; ?>>Telegram</option>
                                        <option value="discord" <?php echo ($edit_community && $edit_community['platform'] == 'discord') ? 'selected' : ''; ?>>Discord</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description *</label>
                                <textarea name="description" id="description" rows="4" required placeholder="Describe the community..."><?php echo $edit_community ? htmlspecialchars($edit_community['description']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="link">Community Link *</label>
                                    <input type="url" name="link" id="link" required placeholder="https://..." value="<?php echo $edit_community ? htmlspecialchars($edit_community['link']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="member_count">Member Count</label>
                                    <input type="number" name="member_count" id="member_count" min="0" value="<?php echo $edit_community ? $edit_community['member_count'] : '0'; ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="is_official" id="is_official" <?php echo ($edit_community && $edit_community['is_official']) ? 'checked' : ''; ?>>
                                    <label for="is_official">Official Community</label>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="<?php echo $edit_community ? 'edit_community' : 'add_community'; ?>" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?php echo $edit_community ? 'Update Community' : 'Add Community'; ?>
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="<?php echo $edit_community ? 'window.location.href=\'community.php\'' : 'toggleAddCommunityForm()'; ?>">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

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
                            <?php if (is_admin()): ?>
                                <div class="admin-actions">
                                    <a href="community.php?edit=<?php echo $community['id']; ?>" class="btn-edit" title="Edit Community">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this community?');">
                                        <input type="hidden" name="community_id" value="<?php echo $community['id']; ?>">
                                        <button type="submit" name="delete_community" class="btn-delete" title="Delete Community">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                            
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

            <!-- Add Community Button for Regular Users -->
            <?php if (is_logged_in() && !is_admin()): ?>
                <div class="add-community-section">
                    <div class="add-community-card">
                        <div class="add-content">
                            <i class="fas fa-info-circle"></i>
                            <h3>Ingin Menambah Komunitas?</h3>
                            <p>Hubungi admin untuk menambahkan komunitas Madridista Anda!</p>
                            <a href="mailto:admin@losblancosid.com" class="btn btn-primary">
                                <i class="fas fa-envelope"></i> Hubungi Admin
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

// Admin form function
function toggleAddCommunityForm() {
    const form = document.getElementById('addCommunityForm');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth' });
    } else {
        form.style.display = 'none';
    }
}

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
