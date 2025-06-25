document.addEventListener('DOMContentLoaded', function() {
  // Set current year in footer
  document.getElementById('current-year').textContent = new Date().getFullYear();
  
  // Mobile menu toggle
  const mobileMenuButton = document.getElementById('mobileMenuToggle');
  const mobileMenu = document.getElementById('mobileMenu');
  
  if (mobileMenuButton && mobileMenu) {
      mobileMenuButton.addEventListener('click', function() {
          mobileMenu.classList.toggle('active');
          
          // Change icon
          const icon = mobileMenuButton.querySelector('i');
          if (icon.classList.contains('fa-bars')) {
              icon.classList.remove('fa-bars');
              icon.classList.add('fa-times');
          } else {
              icon.classList.remove('fa-times');
              icon.classList.add('fa-bars');
          }
      });
  }
  
  // Theme toggle
  const themeToggle = document.getElementById('themeToggle');
  
  if (themeToggle) {
      // Check for saved theme preference or use system preference
      const savedTheme = localStorage.getItem('theme');
      const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      
      if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
          document.body.classList.add('dark-theme');
      }
      
      themeToggle.addEventListener('click', function() {
          const isDark = document.body.classList.toggle('dark-theme');
          localStorage.setItem('theme', isDark ? 'dark' : 'light');
      });
  }
  
  // Tab functionality
  const tabButtons = document.querySelectorAll('.tab-button');
  
  if (tabButtons.length > 0) {
      tabButtons.forEach(button => {
          button.addEventListener('click', function() {
              const tabId = this.getAttribute('data-tab');
              
              // Remove active class from all buttons and panes
              document.querySelectorAll('.tab-button').forEach(btn => {
                  btn.classList.remove('active');
              });
              document.querySelectorAll('.tab-pane').forEach(pane => {
                  pane.classList.remove('active');
              });
              
              // Add active class to clicked button and corresponding pane
              this.classList.add('active');
              document.getElementById(tabId).classList.add('active');
          });
      });
  }
  
  // Photo carousel functionality
  const carousel = document.querySelector('.photo-carousel');
  
  if (carousel) {
      const slides = carousel.querySelectorAll('.carousel-slide');
      const prevButton = carousel.querySelector('.prev');
      const nextButton = carousel.querySelector('.next');
      const indicators = carousel.querySelectorAll('.indicator');
      let currentSlide = 0;
      let interval;
      
      // Function to show a specific slide
      function showSlide(index) {
          // Remove active class from all slides and indicators
          slides.forEach(slide => slide.classList.remove('active'));
          indicators.forEach(indicator => indicator.classList.remove('active'));
          
          // Add active class to current slide and indicator
          slides[index].classList.add('active');
          indicators[index].classList.add('active');
          
          // Update current slide index
          currentSlide = index;
      }
      
      // Function to show next slide
      function nextSlide() {
          let next = currentSlide + 1;
          if (next >= slides.length) {
              next = 0;
          }
          showSlide(next);
      }
      
      // Function to show previous slide
      function prevSlide() {
          let prev = currentSlide - 1;
          if (prev < 0) {
              prev = slides.length - 1;
          }
          showSlide(prev);
      }
      
      // Set up event listeners
      if (prevButton) {
          prevButton.addEventListener('click', function() {
              prevSlide();
              resetInterval();
          });
      }
      
      if (nextButton) {
          nextButton.addEventListener('click', function() {
              nextSlide();
              resetInterval();
          });
      }
      
      // Set up indicator clicks
      indicators.forEach((indicator, index) => {
          indicator.addEventListener('click', function() {
              showSlide(index);
              resetInterval();
          });
      });
      
      // Auto-advance slides
      function startInterval() {
          interval = setInterval(nextSlide, 5000);
      }
      
      function resetInterval() {
          clearInterval(interval);
          startInterval();
      }
      
      // Start the carousel
      startInterval();
  }
  
  // Newsletter form submission
  const newsletterForm = document.querySelector('.newsletter-form');
  
  if (newsletterForm) {
      newsletterForm.addEventListener('submit', function(e) {
          e.preventDefault();
          const email = this.querySelector('input[type="email"]').value;
          
          // Here you would typically send this to your server
          alert(`Thank you for subscribing with ${email}! You'll receive our latest updates soon.`);
          
          // Reset the form
          this.reset();
      });
  }
  
  // Comment reply functionality
  const replyButtons = document.querySelectorAll('.comment-reply-btn');
  
  if (replyButtons.length > 0) {
      replyButtons.forEach(button => {
          button.addEventListener('click', function() {
              const commentId = this.getAttribute('data-comment-id');
              const commentForm = document.querySelector('.comment-form');
              
              if (commentForm) {
                  // Scroll to comment form
                  commentForm.scrollIntoView({ behavior: 'smooth' });
                  
                  // Focus on textarea
                  setTimeout(() => {
                      const textarea = commentForm.querySelector('textarea');
                      textarea.focus();
                      textarea.value = `@${this.closest('.comment').querySelector('.comment-author').textContent} `;
                  }, 500);
              }
          });
      });
  }
  
  // Comment like functionality
  const likeButtons = document.querySelectorAll('.comment-like-btn');
  
  if (likeButtons.length > 0) {
      likeButtons.forEach(button => {
          button.addEventListener('click', function() {
              alert('You need to be logged in to like comments.');
          });
      });
  }
  
  // Alert auto-dismiss
  const alerts = document.querySelectorAll('.alert');
  
  if (alerts.length > 0) {
      alerts.forEach(alert => {
          setTimeout(() => {
              alert.style.opacity = '0';
              setTimeout(() => {
                  alert.style.display = 'none';
              }, 500);
          }, 5000);
      });
  }
});