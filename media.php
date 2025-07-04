<?php
$page_title = "Media";
$page_description = "Koleksi foto dan video Real Madrid";
$current_page = "media";

require_once 'includes/functions.php';
require_once 'includes/db.php';

// Handle form submission for adding/editing media (Admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_admin()) {
    if (isset($_POST['add_video']) || isset($_POST['edit_video'])) {
        $title = sanitize_input($_POST['title']);
        $description = sanitize_input($_POST['description']);
        $youtube_url = sanitize_input($_POST['youtube_url']);
        
        // Extract YouTube ID from URL
        $youtube_id = '';
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $youtube_url, $matches)) {
            $youtube_id = $matches[1];
        }
        
        if (!empty($youtube_id)) {
            $embed_url = "https://www.youtube.com/embed/" . $youtube_id;
            
            // Escape values
            $title_escaped = db_escape($title);
            $description_escaped = db_escape($description);
            $youtube_id_escaped = db_escape($youtube_id);
            $embed_url_escaped = db_escape($embed_url);
            
            // Create table if not exists
            create_simple_media_data();
            
            if (isset($_POST['edit_video'])) {
                // Update existing video
                $video_id = (int)$_POST['video_id'];
                $query = "UPDATE media_videos SET 
                          title = '$title_escaped',
                          description = '$description_escaped',
                          youtube_id = '$youtube_id_escaped',
                          embed_url = '$embed_url_escaped'
                          WHERE id = $video_id";
                
                if (db_query($query)) {
                    $_SESSION['message'] = "Video updated successfully!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Error updating video.";
                    $_SESSION['message_type'] = "error";
                }
            } else {
                // Insert new video
                $query = "INSERT INTO media_videos (title, description, youtube_id, embed_url, created_at) 
                          VALUES ('$title_escaped', '$description_escaped', '$youtube_id_escaped', '$embed_url_escaped', NOW())";
                
                if (db_query($query)) {
                    $_SESSION['message'] = "Video added successfully!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Error adding video.";
                    $_SESSION['message_type'] = "error";
                }
            }
        } else {
            $_SESSION['message'] = "Invalid YouTube URL.";
            $_SESSION['message_type'] = "error";
        }
        
        header("Location: media.php");
        exit;
    }
    
    if (isset($_POST['add_photo']) || isset($_POST['edit_photo'])) {
        $title = sanitize_input($_POST['title']);
        $description = sanitize_input($_POST['description']);
        $category = sanitize_input($_POST['category']);
        
        $image_url = isset($_POST['current_image']) ? $_POST['current_image'] : '';
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = 'assets/images/gallery/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'gallery_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_url = $upload_path;
            }
        }
        
        if (!empty($image_url)) {
            // Escape values
            $title_escaped = db_escape($title);
            $description_escaped = db_escape($description);
            $category_escaped = db_escape($category);
            $image_url_escaped = db_escape($image_url);
            
            // Create table if not exists
            create_dummy_gallery_data();
            
            if (isset($_POST['edit_photo'])) {
                // Update existing photo
                $photo_id = (int)$_POST['photo_id'];
                $query = "UPDATE gallery SET 
                          title = '$title_escaped',
                          description = '$description_escaped',
                          category = '$category_escaped',
                          image_url = '$image_url_escaped'
                          WHERE id = $photo_id";
                
                if (db_query($query)) {
                    $_SESSION['message'] = "Photo updated successfully!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Error updating photo.";
                    $_SESSION['message_type'] = "error";
                }
            } else {
                // Insert new photo
                $query = "INSERT INTO gallery (title, description, image_url, category, created_at) 
                          VALUES ('$title_escaped', '$description_escaped', '$image_url_escaped', '$category_escaped', NOW())";
                
                if (db_query($query)) {
                    $_SESSION['message'] = "Photo added successfully!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Error adding photo to database.";
                    $_SESSION['message_type'] = "error";
                }
            }
        } else {
            $_SESSION['message'] = "Please select a photo to upload.";
            $_SESSION['message_type'] = "error";
        }
        
        header("Location: media.php");
        exit;
    }
    
    // Handle delete video
    if (isset($_POST['delete_video'])) {
        $video_id = (int)$_POST['video_id'];
        $query = "DELETE FROM media_videos WHERE id = $video_id";
        
        if (db_query($query)) {
            $_SESSION['message'] = "Video deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting video.";
            $_SESSION['message_type'] = "error";
        }
        
        header("Location: media.php");
        exit;
    }
    
    // Handle delete photo
    if (isset($_POST['delete_photo'])) {
        $photo_id = (int)$_POST['photo_id'];
        $query = "DELETE FROM gallery WHERE id = $photo_id";
        
        if (db_query($query)) {
            $_SESSION['message'] = "Photo deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting photo.";
            $_SESSION['message_type'] = "error";
        }
        
        header("Location: media.php");
        exit;
    }
}

// Get video/photo for editing if edit_id is provided
$edit_video = null;
$edit_photo = null;

if (isset($_GET['edit_video']) && is_admin()) {
    $edit_id = (int)$_GET['edit_video'];
    create_simple_media_data();
    $edit_query = "SELECT * FROM media_videos WHERE id = $edit_id LIMIT 1";
    $edit_result = db_query($edit_query);
    if (db_num_rows($edit_result) == 1) {
        $edit_video = db_fetch_array($edit_result);
    }
}

if (isset($_GET['edit_photo']) && is_admin()) {
    $edit_id = (int)$_GET['edit_photo'];
    create_dummy_gallery_data();
    $edit_query = "SELECT * FROM gallery WHERE id = $edit_id LIMIT 1";
    $edit_result = db_query($edit_query);
    if (db_num_rows($edit_result) == 1) {
        $edit_photo = db_fetch_array($edit_result);
    }
}

// Get media content
$videos = get_media_videos();
$photos = get_media_photos();

include 'includes/header.php';
?>

<div class="media-page">
    <?php display_message(); ?>
    
    <!-- Hero Section with Background Image -->
    <section class="media-hero" style="background-image: linear-gradient(rgba(0, 38, 96, 0.8), rgba(0, 38, 96, 0.6)), url('assets/images/Santiago.jpeg');">
        <div class="container">
            <div class="hero-content">
                <h1><i class="fas fa-play-circle"></i> Media Los Blancos</h1>
                <p>Koleksi eksklusif momen terbaik Real Madrid</p>
            </div>
        </div>
    </section>

    <!-- Admin Add/Edit Media Section -->
    <?php if (is_admin()): ?>
        <div class="admin-section">
            <div class="container">
                <?php if (!$edit_video && !$edit_photo): ?>
                    <div class="admin-controls">
                        <button class="btn btn-primary" onclick="toggleAddVideoForm()">
                            <i class="fas fa-video"></i> Add Video
                        </button>
                        <button class="btn btn-secondary" onclick="toggleAddPhotoForm()">
                            <i class="fas fa-camera"></i> Add Photo
                        </button>
                    </div>
                <?php else: ?>
                    <div class="edit-mode-header">
                        <h3>
                            <i class="fas fa-edit"></i> 
                            Editing <?php echo $edit_video ? 'Video' : 'Photo'; ?>: 
                            <?php echo htmlspecialchars($edit_video ? $edit_video['title'] : $edit_photo['title']); ?>
                        </h3>
                        <a href="media.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel Edit
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Add/Edit Video Form -->
                <div id="addVideoForm" class="add-media-form" style="display: <?php echo $edit_video ? 'block' : 'none'; ?>;">
                    <div class="form-container">
                        <h3><i class="fas fa-video"></i> <?php echo $edit_video ? 'Edit Video' : 'Add New Video'; ?></h3>
                        <form method="POST">
                            <?php if ($edit_video): ?>
                                <input type="hidden" name="video_id" value="<?php echo $edit_video['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label for="video_title">Video Title *</label>
                                <input type="text" name="title" id="video_title" required maxlength="255" value="<?php echo $edit_video ? htmlspecialchars($edit_video['title']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="video_description">Description *</label>
                                <textarea name="description" id="video_description" rows="3" required placeholder="Brief description of the video..."><?php echo $edit_video ? htmlspecialchars($edit_video['description']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="youtube_url">YouTube URL *</label>
                                <input type="url" name="youtube_url" id="youtube_url" required placeholder="https://www.youtube.com/watch?v=..." value="<?php echo $edit_video ? 'https://www.youtube.com/watch?v=' . htmlspecialchars($edit_video['youtube_id']) : ''; ?>">
                                <small>Paste the full YouTube URL here</small>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="<?php echo $edit_video ? 'edit_video' : 'add_video'; ?>" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?php echo $edit_video ? 'Update Video' : 'Add Video'; ?>
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="<?php echo $edit_video ? 'window.location.href=\'media.php\'' : 'toggleAddVideoForm()'; ?>">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Add/Edit Photo Form -->
                <div id="addPhotoForm" class="add-media-form" style="display: <?php echo $edit_photo ? 'block' : 'none'; ?>;">
                    <div class="form-container">
                        <h3><i class="fas fa-camera"></i> <?php echo $edit_photo ? 'Edit Photo' : 'Add New Photo'; ?></h3>
                        <form method="POST" enctype="multipart/form-data">
                            <?php if ($edit_photo): ?>
                                <input type="hidden" name="photo_id" value="<?php echo $edit_photo['id']; ?>">
                                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($edit_photo['image_url']); ?>">
                            <?php endif; ?>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="photo_title">Photo Title *</label>
                                    <input type="text" name="title" id="photo_title" required maxlength="255" value="<?php echo $edit_photo ? htmlspecialchars($edit_photo['title']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="photo_category">Category *</label>
                                    <select name="category" id="photo_category" required>
                                        <option value="">Select Category</option>
                                        <option value="Stadium" <?php echo ($edit_photo && $edit_photo['category'] == 'Stadium') ? 'selected' : ''; ?>>Stadium</option>
                                        <option value="Matches" <?php echo ($edit_photo && $edit_photo['category'] == 'Matches') ? 'selected' : ''; ?>>Matches</option>
                                        <option value="Training" <?php echo ($edit_photo && $edit_photo['category'] == 'Training') ? 'selected' : ''; ?>>Training</option>
                                        <option value="Celebrations" <?php echo ($edit_photo && $edit_photo['category'] == 'Celebrations') ? 'selected' : ''; ?>>Celebrations</option>
                                        <option value="Fans" <?php echo ($edit_photo && $edit_photo['category'] == 'Fans') ? 'selected' : ''; ?>>Fans</option>
                                        <option value="Team" <?php echo ($edit_photo && $edit_photo['category'] == 'Team') ? 'selected' : ''; ?>>Team</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="photo_description">Description *</label>
                                <textarea name="description" id="photo_description" rows="3" required placeholder="Brief description of the photo..."><?php echo $edit_photo ? htmlspecialchars($edit_photo['description']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Photo File <?php echo $edit_photo ? '' : '*'; ?></label>
                                <?php if ($edit_photo && $edit_photo['image_url']): ?>
                                    <div class="current-image">
                                        <img src="<?php echo htmlspecialchars($edit_photo['image_url']); ?>" alt="Current Photo" style="width: 150px; height: 100px; object-fit: cover; margin-bottom: 0.5rem; border-radius: 8px;">
                                        <small>Current photo (leave empty to keep)</small>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="image" id="image" accept="image/*" <?php echo $edit_photo ? '' : 'required'; ?>>
                                <small>Recommended size: 800x600px or higher</small>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="<?php echo $edit_photo ? 'edit_photo' : 'add_photo'; ?>" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?php echo $edit_photo ? 'Update Photo' : 'Add Photo'; ?>
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="<?php echo $edit_photo ? 'window.location.href=\'media.php\'' : 'toggleAddPhotoForm()'; ?>">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

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
                            <?php if (is_admin()): ?>
                                <div class="admin-actions">
                                    <a href="media.php?edit_video=<?php echo $video['id']; ?>" class="btn-edit" title="Edit Video">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this video?');">
                                        <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                                        <button type="submit" name="delete_video" class="btn-delete" title="Delete Video">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                            
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
                            <?php if (is_admin()): ?>
                                <div class="admin-actions">
                                    <a href="media.php?edit_photo=<?php echo $photo['id']; ?>" class="btn-edit" title="Edit Photo">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this photo?');">
                                        <input type="hidden" name="photo_id" value="<?php echo $photo['id']; ?>">
                                        <button type="submit" name="delete_photo" class="btn-delete" title="Delete Photo">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                            
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

// Admin form functions
function toggleAddVideoForm() {
    const form = document.getElementById('addVideoForm');
    const photoForm = document.getElementById('addPhotoForm');
    
    // Hide photo form if open
    if (photoForm) photoForm.style.display = 'none';
    
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth' });
    } else {
        form.style.display = 'none';
    }
}

function toggleAddPhotoForm() {
    const form = document.getElementById('addPhotoForm');
    const videoForm = document.getElementById('addVideoForm');
    
    // Hide video form if open
    if (videoForm) videoForm.style.display = 'none';
    
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth' });
    } else {
        form.style.display = 'none';
    }
}

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
