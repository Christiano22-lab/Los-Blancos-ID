// Carousel functionality
let newsCurrentSlide = 0
let galleryCurrentSlide = 0
let newsSlides = []
let gallerySlides = []
let SimpleLightbox // Declare SimpleLightbox variable

document.addEventListener("DOMContentLoaded", () => {
  // Initialize carousels
  initNewsCarousel()
  initGalleryCarousel()

  // Auto-slide every 5 seconds
  setInterval(() => {
    slideNews(1)
    slideGallery(1)
  }, 5000)

  // Tab functionality
  initMatchesTabs()

  // Smooth scroll for hero scroll indicator
  initSmoothScrolling()

  // Intersection Observer for animations
  initScrollAnimations()

  // Parallax effect for hero background
  window.addEventListener("scroll", () => {
    const scrolled = window.pageYOffset
    const heroBackground = document.querySelector(".hero-background")
    if (heroBackground && scrolled < window.innerHeight) {
      const rate = scrolled * -0.5
      heroBackground.style.transform = `translateY(${rate}px)`
    }
  })

  // Gallery lightbox functionality (basic)
  initGalleryLightbox()

  // Initialize lightbox for gallery if available
  if (typeof window.SimpleLightbox !== "undefined") {
    SimpleLightbox = window.SimpleLightbox
    new SimpleLightbox(".gallery-link", {
      captionsData: "caption",
      captionDelay: 250,
    })
  }

  // Add loading states for images
  const images = document.querySelectorAll("img")
  images.forEach((img) => {
    if (!img.complete) {
      img.style.opacity = "0"
      img.style.transition = "opacity 0.3s ease"

      img.addEventListener("load", () => {
        img.style.opacity = "1"
      })

      img.addEventListener("error", () => {
        img.style.opacity = "1"
      })
    }
  })
})

function initNewsCarousel() {
  const carousel = document.getElementById("newsCarousel")
  if (!carousel) return

  newsSlides = carousel.querySelectorAll(".news-slide")
  if (newsSlides.length === 0) return

  // Create dots
  const dotsContainer = document.getElementById("newsDots")
  dotsContainer.innerHTML = ""

  const visibleSlides = getVisibleSlides()
  const totalDots = Math.ceil(newsSlides.length / visibleSlides)

  for (let i = 0; i < totalDots; i++) {
    const dot = document.createElement("span")
    dot.className = "dot"
    if (i === 0) dot.classList.add("active")
    dot.onclick = () => goToNewsSlide(i)
    dotsContainer.appendChild(dot)
  }

  updateNewsCarousel()
}

function initGalleryCarousel() {
  const carousel = document.getElementById("galleryCarousel")
  if (!carousel) return

  gallerySlides = carousel.querySelectorAll(".gallery-slide")
  if (gallerySlides.length === 0) return

  // Create dots
  const dotsContainer = document.getElementById("galleryDots")
  dotsContainer.innerHTML = ""

  const visibleSlides = getVisibleGallerySlides()
  const totalDots = Math.ceil(gallerySlides.length / visibleSlides)

  for (let i = 0; i < totalDots; i++) {
    const dot = document.createElement("span")
    dot.className = "dot"
    if (i === 0) dot.classList.add("active")
    dot.onclick = () => goToGallerySlide(i)
    dotsContainer.appendChild(dot)
  }

  updateGalleryCarousel()
}

function getVisibleSlides() {
  const width = window.innerWidth
  if (width >= 1200) return 4
  if (width >= 768) return 3
  if (width >= 576) return 2
  return 1
}

function getVisibleGallerySlides() {
  const width = window.innerWidth
  if (width >= 1200) return 5
  if (width >= 768) return 4
  if (width >= 576) return 3
  return 2
}

function slideNews(direction) {
  if (newsSlides.length === 0) return

  const visibleSlides = getVisibleSlides()
  const maxSlide = Math.ceil(newsSlides.length / visibleSlides) - 1

  newsCurrentSlide += direction

  if (newsCurrentSlide > maxSlide) {
    newsCurrentSlide = 0
  } else if (newsCurrentSlide < 0) {
    newsCurrentSlide = maxSlide
  }

  updateNewsCarousel()
}

function slideGallery(direction) {
  if (gallerySlides.length === 0) return

  const visibleSlides = getVisibleGallerySlides()
  const maxSlide = Math.ceil(gallerySlides.length / visibleSlides) - 1

  galleryCurrentSlide += direction

  if (galleryCurrentSlide > maxSlide) {
    galleryCurrentSlide = 0
  } else if (galleryCurrentSlide < 0) {
    galleryCurrentSlide = maxSlide
  }

  updateGalleryCarousel()
}

function goToNewsSlide(slideIndex) {
  newsCurrentSlide = slideIndex
  updateNewsCarousel()
}

function goToGallerySlide(slideIndex) {
  galleryCurrentSlide = slideIndex
  updateGalleryCarousel()
}

function updateNewsCarousel() {
  const carousel = document.getElementById("newsCarousel")
  if (!carousel) return

  const visibleSlides = getVisibleSlides()
  const slideWidth = 300 + 32 // slide width + margin
  const offset = newsCurrentSlide * slideWidth * visibleSlides

  carousel.style.transform = `translateX(-${offset}px)`

  // Update dots
  const dots = document.querySelectorAll("#newsDots .dot")
  dots.forEach((dot, index) => {
    dot.classList.toggle("active", index === newsCurrentSlide)
  })
}

function updateGalleryCarousel() {
  const carousel = document.getElementById("galleryCarousel")
  if (!carousel) return

  const visibleSlides = getVisibleGallerySlides()
  const slideWidth = 250 + 24 // slide width + margin
  const offset = galleryCurrentSlide * slideWidth * visibleSlides

  carousel.style.transform = `translateX(-${offset}px)`

  // Update dots
  const dots = document.querySelectorAll("#galleryDots .dot")
  dots.forEach((dot, index) => {
    dot.classList.toggle("active", index === galleryCurrentSlide)
  })
}

// Handle window resize
window.addEventListener("resize", () => {
  updateNewsCarousel()
  updateGalleryCarousel()
})

// Matches tabs functionality
function initMatchesTabs() {
  const tabButtons = document.querySelectorAll(".tab-btn")
  const tabContents = document.querySelectorAll(".tab-content")

  tabButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const targetTab = button.getAttribute("data-tab")

      // Remove active class from all buttons and contents
      tabButtons.forEach((btn) => btn.classList.remove("active"))
      tabContents.forEach((content) => content.classList.remove("active"))

      // Add active class to clicked button and corresponding content
      button.classList.add("active")
      document.getElementById(targetTab).classList.add("active")
    })
  })
}

// Smooth scrolling functionality
function initSmoothScrolling() {
  const scrollIndicator = document.querySelector(".hero-scroll-indicator")

  if (scrollIndicator) {
    scrollIndicator.addEventListener("click", () => {
      const firstSection = document.querySelector(".section")
      if (firstSection) {
        firstSection.scrollIntoView({
          behavior: "smooth",
          block: "start",
        })
      }
    })
  }
}

// Gallery lightbox functionality
function initGalleryLightbox() {
  const galleryItems = document.querySelectorAll(".gallery-item")

  galleryItems.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault()
      const img = item.querySelector("img")
      const title = item.querySelector("h4")?.textContent || "Gallery Image"

      if (img) {
        openLightbox(img.src, title)
      }
    })
  })
}

// Simple lightbox implementation
function openLightbox(imageSrc, title) {
  // Create lightbox overlay
  const lightbox = document.createElement("div")
  lightbox.className = "lightbox-overlay"
  lightbox.innerHTML = `
    <div class="lightbox-content">
      <button class="lightbox-close">&times;</button>
      <img src="${imageSrc}" alt="${title}">
      <div class="lightbox-title">${title}</div>
    </div>
  `

  // Add lightbox styles
  lightbox.style.cssText = `
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    transition: opacity 0.3s ease;
  `

  const content = lightbox.querySelector(".lightbox-content")
  content.style.cssText = `
    position: relative;
    max-width: 90%;
    max-height: 90%;
    text-align: center;
  `

  const img = lightbox.querySelector("img")
  img.style.cssText = `
    max-width: 100%;
    max-height: 80vh;
    object-fit: contain;
    border-radius: 10px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
  `

  const closeBtn = lightbox.querySelector(".lightbox-close")
  closeBtn.style.cssText = `
    position: absolute;
    top: -40px;
    right: 0;
    background: none;
    border: none;
    color: white;
    font-size: 2rem;
    cursor: pointer;
    z-index: 10000;
  `

  const titleEl = lightbox.querySelector(".lightbox-title")
  titleEl.style.cssText = `
    color: white;
    margin-top: 1rem;
    font-size: 1.2rem;
    font-weight: 600;
  `

  // Add to document
  document.body.appendChild(lightbox)

  // Animate in
  setTimeout(() => {
    lightbox.style.opacity = "1"
  }, 10)

  // Close functionality
  const closeLightbox = () => {
    lightbox.style.opacity = "0"
    setTimeout(() => {
      document.body.removeChild(lightbox)
    }, 300)
  }

  closeBtn.addEventListener("click", closeLightbox)
  lightbox.addEventListener("click", (e) => {
    if (e.target === lightbox) {
      closeLightbox()
    }
  })

  // ESC key to close
  const handleEsc = (e) => {
    if (e.key === "Escape") {
      closeLightbox()
      document.removeEventListener("keydown", handleEsc)
    }
  }
  document.addEventListener("keydown", handleEsc)
}

// Scroll animations with Intersection Observer
function initScrollAnimations() {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = "1"
        entry.target.style.transform = "translateY(0)"
      }
    })
  }, observerOptions)

  // Observe elements for animation
  const animatedElements = document.querySelectorAll(".news-card, .match-card, .gallery-item")

  animatedElements.forEach((el, index) => {
    // Set initial state
    el.style.opacity = "0"
    el.style.transform = "translateY(30px)"
    el.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`

    // Observe element
    observer.observe(el)
  })
}
