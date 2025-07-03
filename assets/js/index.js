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

  // Initialize lightbox for gallery if available
  if (typeof window.SimpleLightbox !== "undefined") {
    SimpleLightbox = window.SimpleLightbox
    new SimpleLightbox(".gallery-link", {
      captionsData: "caption",
      captionDelay: 250,
    })
  }
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
