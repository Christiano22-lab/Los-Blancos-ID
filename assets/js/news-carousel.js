class NewsCarousel {
  constructor() {
    this.currentSlide = 0
    this.slides = []
    this.totalSlides = 0
    this.slidesPerView = this.getSlidesPerView()
    this.slideWidth = 0
    this.carousel = null
    this.isDragging = false
    this.startPos = 0
    this.currentTranslate = 0
    this.prevTranslate = 0
    this.animationId = 0

    this.init()
  }

  init() {
    this.carousel = document.getElementById("newsCarousel")
    if (!this.carousel) return

    this.slides = this.carousel.querySelectorAll(".news-slide")
    this.totalSlides = this.slides.length

    if (this.totalSlides === 0) return

    this.calculateDimensions()
    this.createDots()
    this.bindEvents()
    this.updateNavButtons()

    // Auto-slide every 5 seconds
    this.startAutoSlide()
  }

  getSlidesPerView() {
    const width = window.innerWidth
    if (width >= 1200) return 3
    if (width >= 768) return 2
    return 1
  }

  calculateDimensions() {
    if (this.slides.length > 0) {
      const slideStyle = window.getComputedStyle(this.slides[0])
      this.slideWidth = this.slides[0].offsetWidth + Number.parseInt(slideStyle.marginRight)
    }
  }

  createDots() {
    const dotsContainer = document.getElementById("newsDots")
    if (!dotsContainer) return

    dotsContainer.innerHTML = ""
    const totalDots = this.totalSlides


    for (let i = 0; i < totalDots; i++) {
      const dot = document.createElement("span")
      dot.className = "carousel-dot"
      if (i === 0) dot.classList.add("active")
      dot.addEventListener("click", () => this.goToSlide(i))
      dotsContainer.appendChild(dot)
    }
  }

  bindEvents() {
    // Mouse events
    this.carousel.addEventListener("mousedown", this.dragStart.bind(this))
    this.carousel.addEventListener("mousemove", this.dragMove.bind(this))
    this.carousel.addEventListener("mouseup", this.dragEnd.bind(this))
    this.carousel.addEventListener("mouseleave", this.dragEnd.bind(this))

    // Touch events
    this.carousel.addEventListener("touchstart", this.dragStart.bind(this))
    this.carousel.addEventListener("touchmove", this.dragMove.bind(this))
    this.carousel.addEventListener("touchend", this.dragEnd.bind(this))

    // Prevent context menu on long press
    this.carousel.addEventListener("contextmenu", (e) => e.preventDefault())

    // Window resize
    window.addEventListener("resize", this.handleResize.bind(this))
  }

  dragStart(e) {
    this.isDragging = true
    this.carousel.classList.add("dragging")
    this.startPos = this.getPositionX(e)
    this.animationId = requestAnimationFrame(this.animation.bind(this))
    this.stopAutoSlide()
  }

  dragMove(e) {
    if (!this.isDragging) return
    e.preventDefault()

    const currentPosition = this.getPositionX(e)
    this.currentTranslate = this.prevTranslate + currentPosition - this.startPos
  }

  dragEnd() {
    if (!this.isDragging) return

    this.isDragging = false
    this.carousel.classList.remove("dragging")
    cancelAnimationFrame(this.animationId)

    const movedBy = this.currentTranslate - this.prevTranslate

    // Determine if we should slide to next/prev
    if (movedBy < -100 && this.currentSlide < this.getMaxSlide()) {
      this.currentSlide++
    } else if (movedBy > 100 && this.currentSlide > 0) {
      this.currentSlide--
    }

    this.setSlideByIndex()
    this.startAutoSlide()
  }

  getPositionX(e) {
    return e.type.includes("mouse") ? e.clientX : e.touches[0].clientX
  }

  animation() {
    if (this.isDragging) {
      this.setSliderPosition()
      requestAnimationFrame(this.animation.bind(this))
    }
  }

  setSliderPosition() {
    this.carousel.style.transform = `translateX(${this.currentTranslate}px)`
  }

  setSlideByIndex() {
    this.currentTranslate = this.currentSlide * -this.slideWidth
    this.prevTranslate = this.currentTranslate
    this.setSliderPosition()
    this.updateDots()
    this.updateNavButtons()
  }

  slide(direction) {
    const maxSlide = this.getMaxSlide()

    if (direction === 1 && this.currentSlide < maxSlide) {
      this.currentSlide++
    } else if (direction === -1 && this.currentSlide > 0) {
      this.currentSlide--
    } else if (direction === 1 && this.currentSlide >= maxSlide) {
      this.currentSlide = 0 // Loop to beginning
    } else if (direction === -1 && this.currentSlide <= 0) {
      this.currentSlide = maxSlide // Loop to end
    }

    this.setSlideByIndex()
  }

  goToSlide(slideIndex) {
    this.currentSlide = slideIndex
    this.setSlideByIndex()
  }

  getMaxSlide() {
    return this.totalSlides - 1
  }

  updateDots() {
    const dots = document.querySelectorAll("#newsDots .carousel-dot")
    dots.forEach((dot, index) => {
      dot.classList.toggle("active", index === this.currentSlide)
    })
  }

  updateNavButtons() {
    const prevBtn = document.getElementById("newsPrevBtn")
    const nextBtn = document.getElementById("newsNextBtn")

    if (prevBtn) {
      prevBtn.disabled = this.currentSlide === 0
    }

    if (nextBtn) {
      nextBtn.disabled = this.currentSlide >= this.getMaxSlide()
    }
  }

  startAutoSlide() {
    this.stopAutoSlide()
    this.autoSlideInterval = setInterval(() => {
      this.slide(1)
    }, 5000)
  }

  stopAutoSlide() {
    if (this.autoSlideInterval) {
      clearInterval(this.autoSlideInterval)
    }
  }

  handleResize() {
    this.slidesPerView = this.getSlidesPerView()
    this.calculateDimensions()
    this.createDots()
    this.setSlideByIndex()
  }
}

// Global functions for button clicks
function slideNews(direction) {
  if (window.newsCarousel) {
    window.newsCarousel.slide(direction)
  }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  window.newsCarousel = new NewsCarousel()
})

class Carousel {
  constructor(rootId, prevBtnId, nextBtnId, dotsContainerId) {
    this.root = document.getElementById(rootId)
    this.slides = this.root.querySelectorAll('.slide, .news-slide, .gallery-slide')
    this.totalSlides = this.slides.length
    this.currentSlide = 0
    this.prevBtn = document.getElementById(prevBtnId)
    this.nextBtn = document.getElementById(nextBtnId)
    this.dotsContainer = document.getElementById(dotsContainerId)
    this.slideWidth = this.slides[0]?.offsetWidth || 0

    this.prevBtn.addEventListener('click', () => this.slide(-1))
    this.nextBtn.addEventListener('click', () => this.slide(1))
    window.addEventListener('resize', () => {
      this.slideWidth = this.slides[0]?.offsetWidth || 0
      this.goToSlide(this.currentSlide) 
    })

    this.createDots()
    this.goToSlide(0)
  }

  createDots() {
    this.dotsContainer.innerHTML = ''
    this.dots = []
    for (let i = 0; i < this.totalSlides; i++) {
      const dot = document.createElement('span')
      dot.classList.add('carousel-dot')
      dot.addEventListener('click', () => this.goToSlide(i))
      this.dotsContainer.appendChild(dot)
      this.dots.push(dot)
    }
  }

  slide(direction) {
    this.goToSlide((this.currentSlide + direction + this.totalSlides) % this.totalSlides)
  }

  goToSlide(index) {
    this.currentSlide = index
    const translate = -this.slideWidth * this.currentSlide
    this.root.style.transform = `translateX(${translate}px)`
    this.dots.forEach((d, i) => d.classList.toggle('active', i === index))
  }
}

// Inisialisasi semua carousel otomatis
document.addEventListener('DOMContentLoaded', function() {
  new Carousel('newsCarousel', 'newsPrevBtn', 'newsNextBtn', 'newsDots')
  new Carousel('galleryCarousel', 'galleryPrevBtn', 'galleryNextBtn', 'galleryDots')
})
