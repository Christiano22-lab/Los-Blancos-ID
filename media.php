<?php
$page_title = "Media";
$page_description = "Koleksi foto dan video Real Madrid";
$current_page = "media";

require_once 'includes/functions.php';
require_once 'includes/db.php';

// Get media content
$videos = get_media_videos();
$photos = get_media_photos();

include 'includes/header.php';
?>

<div class="media-page">
    <!-- Hero Section with Background Image -->
    <section class="media-hero" style="background-image: linear-gradient(rgba(0, 38, 96, 0.8), rgba(0, 38, 96, 0.6)), url('assets/images/Santiago.jpeg');">
        <div class="container">
            <div class="hero-content">
                <h1><i class="fas fa-play-circle"></i> Media Los Blancos</h1>
                <p>Koleksi eksklusif momen terbaik Real Madrid</p>
            </div>
        </div>
    </section>

    <!-- Media Tabs -->
    <section class="media-tabs">
        <div class="container">
            <div class="tab-buttons">
                <button class="tab-btn active" data-tab="videos">
                    <i class="fas fa-video"></i> Video
                </button>
                <button class="tab-btn" data-tab="photos">
                    <i class="fas fa-camera"></i> Galeri Foto
                </button>
            </div>
        </div>
    </section>

    <!-- Videos Section -->
    <section class="tab-content active" id="videos">
        <div class="container">
            <h2><i class="fas fa-video"></i> Video Terbaru</h2>
            
            <div class="videos-grid">
                <?php if (!empty($videos)): ?>
                    <?php foreach ($videos as $video): ?>
                        <div class="video-card">
                            <div class="video-container">
                                <iframe 
                                    src="<?php echo htmlspecialchars($video['embed_url']); ?>" 
                                    title="<?php echo htmlspecialchars($video['title']); ?>"
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <div class="video-info">
                                <h3><?php echo htmlspecialchars($video['title']); ?></h3>
                                <p><?php echo htmlspecialchars($video['description']); ?></p>
                                <div class="video-meta">
                                    <span class="date">
                                        <i class="fas fa-calendar"></i> 
                                        <?php echo format_date_id($video['created_at']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-content">
                        <i class="fas fa-video-slash"></i>
                        <h3>Belum Ada Video</h3>
                        <p>Video akan segera hadir!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Photos Section -->
    <section class="tab-content" id="photos">
        <div class="container">
            <h2><i class="fas fa-camera"></i> Galeri Foto</h2>
            
            <div class="photos-grid">
                <?php if (!empty($photos)): ?>
                    <?php foreach ($photos as $index => $photo): ?>
                        <div class="photo-card">
                            <div class="photo-container">
                                <img src="<?php echo htmlspecialchars($photo['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($photo['title']); ?>"
                                     onerror="this.src='/placeholder.svg?height=300&width=400'"
                                     loading="lazy"
                                     onclick="openPhotoModal(<?php echo $index; ?>)">
                                <div class="photo-overlay">
                                    <h4><?php echo htmlspecialchars($photo['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($photo['description']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-content">
                        <i class="fas fa-camera-slash"></i>
                        <h3>Belum Ada Foto</h3>
                        <p>Foto akan segera hadir!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<!-- Photo Modal -->
<div class="photo-modal" id="photoModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closePhotoModal()">
            <i class="fas fa-times"></i>
        </button>
        <button class="modal-prev" onclick="prevPhoto()">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="modal-next" onclick="nextPhoto()">
            <i class="fas fa-chevron-right"></i>
        </button>
        <div class="photo-container">
            <img id="modalPhoto" src="/placeholder.svg" alt="">
            <div class="photo-info">
                <h3 id="photoTitle"></h3>
                <p id="photoDescription"></p>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="assets/css/media.css">

<script>
// Media Page JavaScript
let currentPhotoIndex = 0;
let photosData = <?php echo json_encode($photos); ?>;

// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetTab = btn.getAttribute('data-tab');
            
            // Remove active class from all buttons and contents
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            btn.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
            
            // Smooth scroll to the content
            document.getElementById(targetTab).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
});

// Photo modal functions
function openPhotoModal(index) {
    currentPhotoIndex = index;
    const modal = document.getElementById('photoModal');
    const photo = document.getElementById('modalPhoto');
    const title = document.getElementById('photoTitle');
    const description = document.getElementById('photoDescription');
    
    if (photosData[index]) {
        photo.src = photosData[index].image_url;
        title.textContent = photosData[index].title;
        description.textContent = photosData[index].description;
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closePhotoModal() {
    const modal = document.getElementById('photoModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function nextPhoto() {
    currentPhotoIndex = (currentPhotoIndex + 1) % photosData.length;
    openPhotoModal(currentPhotoIndex);
}

function prevPhoto() {
    currentPhotoIndex = (currentPhotoIndex - 1 + photosData.length) % photosData.length;
    openPhotoModal(currentPhotoIndex);
}

// Close modal when clicking outside
window.addEventListener('click', function(e) {
    const photoModal = document.getElementById('photoModal');
    
    if (e.target === photoModal) {
        closePhotoModal();
    }
});

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    const photoModal = document.getElementById('photoModal');
    
    if (photoModal.style.display === 'block') {
        switch(e.key) {
            case 'Escape':
                closePhotoModal();
                break;
            case 'ArrowLeft':
                prevPhoto();
                break;
            case 'ArrowRight':
                nextPhoto();
                break;
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>