    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <a href="index.php" class="footer-logo">
                        <img src="assets/images/logo.png" alt="<?php echo SITE_NAME; ?>">
                        <span><?php echo SITE_NAME; ?></span>
                    </a>
                    <p>The ultimate destination for Madridistas worldwide. Join our community of passionate fans.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <div class="footer-links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="news.php">News</a></li>
                        <li><a href="matches.php">Matches</a></li>
                        <li><a href="standings.php">Standings</a></li>
                        <li><a href="media.php">Media Gallery</a></li>
                        <li><a href="community.php">Community</a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h3>Information</h3>
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="terms.php">Terms of Service</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                    </ul>
                </div>

                <div class="footer-newsletter">
                    <h3>Newsletter</h3>
                    <p>Subscribe to our newsletter for the latest news and updates.</p>
                    <form class="newsletter-form" action="subscribe.php" method="post">
                        <input type="email" name="email" placeholder="Your email address" required>
                        <button type="submit" class="btn btn-block">Subscribe</button>
                    </form>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                <p>This is an unofficial fan site and is not affiliated with Real Madrid C.F.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <?php if (isset($extra_js)): ?>
        <?php echo $extra_js; ?>
    <?php endif; ?>
</body>
</html>