<?php
$current_page = 'about';
$page_title = 'About Us';
$page_description = 'Learn more about Los Blancos ID - The ultimate Real Madrid fans community in Indonesia';

require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/about.css">

<div class="about-hero">
    <div class="hero-background">
        <div class="hero-overlay"></div>
        <img src="assets/images/Santiago.jpeg" alt="Santiago Bernabéu" class="hero-bg-image">
    </div>
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">
                <span class="title-main">Los Blancos ID</span>
                <span class="title-sub">Komunitas Madridista Indonesia</span>
            </h1>
            <p class="hero-description">
                Bergabunglah dengan ribuan penggemar Real Madrid di Indonesia. 
                Bersama kita dukung Los Blancos menuju kemenangan!
            </p>
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-number">5000+</div>
                    <div class="stat-label">Members</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Cities</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">2018</div>
                    <div class="stat-label">Founded</div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="about-story">
    <div class="container">
        <div class="story-grid">
            <div class="story-content">
                <h2 class="section-title">Our Story</h2>
                <p class="story-text">
                    Los Blancos ID didirikan pada tahun 2018 oleh sekelompok penggemar fanatik Real Madrid 
                    di Indonesia. Berawal dari diskusi kecil di media sosial, kami berkembang menjadi 
                    komunitas terbesar penggemar Real Madrid di Indonesia.
                </p>
                <p class="story-text">
                    Dengan semangat "Hala Madrid", kami tidak hanya mendukung tim kesayangan, 
                    tetapi juga membangun persaudaraan yang kuat antar Madridista di seluruh Nusantara.
                </p>
                <div class="story-highlights">
                    <div class="highlight-item">
                        <i class="fas fa-trophy"></i>
                        <span>15 UCL Titles Celebrated Together</span>
                    </div>
                    <div class="highlight-item">
                        <i class="fas fa-users"></i>
                        <span>Active in 50+ Cities</span>
                    </div>
                    <div class="highlight-item">
                        <i class="fas fa-heart"></i>
                        <span>United by Passion</span>
                    </div>
                </div>
            </div>
            <div class="story-image">
                <img src="assets/images/gallery/1.jpeg" alt="Community gathering" class="story-img">
                <div class="image-overlay">
                    <span>Watch Our Journey</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="vision-mission">
    <div class="container">
        <div class="vm-grid">
            <div class="vm-card vision-card">
                <div class="vm-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <h3>Visi Kami</h3>
                <p>
                    Menjadi komunitas penggemar Real Madrid terbesar dan paling solid di Indonesia, 
                    yang mampu menyatukan seluruh Madridista dalam satu semangat: Hala Madrid!
                </p>
            </div>
            <div class="vm-card mission-card">
                <div class="vm-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3>Misi Kami</h3>
                <ul class="mission-list">
                    <li>Membangun komunitas yang solid dan saling mendukung</li>
                    <li>Menyediakan platform diskusi dan informasi terkini</li>
                    <li>Mengorganisir acara nonton bareng dan gathering</li>
                    <li>Mendukung Real Madrid dalam setiap pertandingan</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="team-section">
    <div class="container">
        <h2 class="section-title text-center">Meet Our Team</h2>
        <p class="section-subtitle">Tim yang berdedikasi untuk membangun komunitas terbaik</p>
        
        <div class="team-grid">
            <div class="team-card">
                <div class="team-avatar">
                    <img src="/placeholder.svg?height=120&width=120" alt="Founder">
                    <div class="avatar-overlay">
                        <i class="fas fa-crown"></i>
                    </div>
                </div>
                <h4 class="team-name">Ahmad Rizki</h4>
                <p class="team-role">Founder & President</p>
                <p class="team-bio">Madridista sejak 2002, memimpin komunitas dengan visi menyatukan seluruh penggemar Real Madrid di Indonesia.</p>
                <div class="team-social">
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            
            <div class="team-card">
                <div class="team-avatar">
                    <img src="/placeholder.svg?height=120&width=120" alt="Vice President">
                    <div class="avatar-overlay">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <h4 class="team-name">Sari Dewi</h4>
                <p class="team-role">Vice President</p>
                <p class="team-bio">Mengatur strategi komunitas dan memastikan setiap member mendapat pengalaman terbaik sebagai Madridista.</p>
                <div class="team-social">
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            
            <div class="team-card">
                <div class="team-avatar">
                    <img src="/placeholder.svg?height=120&width=120" alt="Community Manager">
                    <div class="avatar-overlay">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <h4 class="team-name">Budi Santoso</h4>
                <p class="team-role">Community Manager</p>
                <p class="team-bio">Mengelola aktivitas harian komunitas dan memastikan setiap diskusi berjalan dengan positif dan konstruktif.</p>
                <div class="team-social">
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="achievements">
    <div class="container">
        <h2 class="section-title text-center">Our Achievements</h2>
        <div class="achievements-grid">
            <div class="achievement-card">
                <div class="achievement-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h4>Best Fan Community 2023</h4>
                <p>Penghargaan dari Real Madrid Official Fan Club untuk komunitas terbaik di Asia Tenggara</p>
            </div>
            
            <div class="achievement-card">
                <div class="achievement-icon">
                    <i class="fas fa-medal"></i>
                </div>
                <h4>Largest Gathering</h4>
                <p>Mengorganisir nonton bareng terbesar dengan 2000+ Madridista di Jakarta</p>
            </div>
            
            <div class="achievement-card">
                <div class="achievement-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <h4>Charity Champion</h4>
                <p>Berhasil mengumpulkan dana bantuan Rp 500 juta untuk korban bencana alam</p>
            </div>
            
            <div class="achievement-card">
                <div class="achievement-icon">
                    <i class="fas fa-globe"></i>
                </div>
                <h4>International Recognition</h4>
                <p>Diakui oleh Real Madrid Foundation sebagai official supporter club</p>
            </div>
        </div>
    </div>
</section>

<section class="join-us">
    <div class="container">
        <div class="join-content">
            <h2 class="join-title">Ready to Join Los Blancos ID?</h2>
            <p class="join-description">
                Bergabunglah dengan ribuan Madridista lainnya dan rasakan pengalaman mendukung 
                Real Madrid bersama keluarga besar Los Blancos ID!
            </p>
            <div class="join-buttons">
                <?php if (is_logged_in()): ?>
                    <a href="community.php" class="btn btn-primary">
                        <i class="fas fa-users"></i> Join Community
                    </a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Sign Up Now
                    </a>
                    <a href="login.php" class="btn btn-outline">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
