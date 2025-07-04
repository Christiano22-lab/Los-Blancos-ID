// Logout Modal Functionality
document.addEventListener("DOMContentLoaded", () => {
  // Create logout modal HTML
  const modalHTML = `
        <div id="logoutModal" class="logout-modal-overlay">
            <div class="logout-modal">
                <div class="logout-modal-header">
                    <i class="fas fa-sign-out-alt"></i>
                    <h3>Konfirmasi Logout</h3>
                </div>
                <p>Apakah Anda yakin ingin keluar?</p>
                <div class="logout-modal-buttons">
                    <button id="confirmLogout" class="btn-logout">Ya, Logout</button>
                    <button id="cancelLogout" class="btn-cancel">Batal</button>
                </div>
            </div>
        </div>
    `

  // Add modal to body
  document.body.insertAdjacentHTML("beforeend", modalHTML)

  // Get modal elements
  const modal = document.getElementById("logoutModal")
  const confirmBtn = document.getElementById("confirmLogout")
  const cancelBtn = document.getElementById("cancelLogout")

  // Handle logout links
  const logoutLinks = document.querySelectorAll('a[href="logout.php"]')

  logoutLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault()
      showLogoutModal()
    })
  })

  // Show modal function
  function showLogoutModal() {
    modal.style.display = "flex"
    document.body.style.overflow = "hidden"

    // Add animation
    setTimeout(() => {
      modal.classList.add("show")
    }, 10)
  }

  // Hide modal function
  function hideLogoutModal() {
    modal.classList.remove("show")
    setTimeout(() => {
      modal.style.display = "none"
      document.body.style.overflow = ""
    }, 300)
  }

  // Confirm logout
  confirmBtn.addEventListener("click", () => {
    // Show goodbye message
    showGoodbyeMessage()

    // Redirect after 2 seconds
    setTimeout(() => {
      // Create form and submit
      const form = document.createElement("form")
      form.method = "POST"
      form.action = "logout.php"

      const input = document.createElement("input")
      input.type = "hidden"
      input.name = "confirm_logout"
      input.value = "yes"

      form.appendChild(input)
      document.body.appendChild(form)
      form.submit()
    }, 2000)
  })

  // Cancel logout
  cancelBtn.addEventListener("click", hideLogoutModal)

  // Close modal when clicking overlay
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      hideLogoutModal()
    }
  })

  // Close modal with ESC key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modal.style.display === "flex") {
      hideLogoutModal()
    }
  })

  // Show goodbye message
  function showGoodbyeMessage() {
    modal.innerHTML = `
            <div class="logout-modal goodbye">
                <div class="goodbye-content">
                    <i class="fas fa-hand-wave goodbye-icon"></i>
                    <h3>Sampai Jumpa Lagi!</h3>
                    <p>Terima kasih telah berkunjung</p>
                    <div class="loading-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        `
  }
})
