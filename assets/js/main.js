document.addEventListener("DOMContentLoaded", () => {
  // Sidebar toggle functionality
  const toggleSidebar = document.querySelector(".sidebar-toggle")
  const sidebar = document.querySelector(".sidebar")
  const contentWrapper = document.querySelector(".content-wrapper")
  const header = document.querySelector(".header")
  const body = document.body

  // Create overlay element for mobile
  let overlay = document.querySelector(".sidebar-overlay")
  if (!overlay) {
    overlay = document.createElement("div")
    overlay.className = "sidebar-overlay"
    body.appendChild(overlay)
  }

  // Check if mobile
  function isMobile() {
    return window.innerWidth <= 768
  }

  // Toggle sidebar function
  function toggleSidebarState() {
    if (isMobile()) {
      // Mobile behavior
      sidebar.classList.toggle("show")
      overlay.classList.toggle("show")
      body.style.overflow = sidebar.classList.contains("show") ? "hidden" : ""
    } else {
      // Desktop behavior
      sidebar.classList.toggle("collapsed")
      contentWrapper.classList.toggle("sidebar-collapsed")
      header.classList.toggle("sidebar-collapsed")

      // Save state to localStorage
      const isCollapsed = sidebar.classList.contains("collapsed")
      localStorage.setItem("sidebarCollapsed", isCollapsed)
    }
  }

  // Initialize sidebar state on desktop
  function initSidebarState() {
    if (!isMobile()) {
      const savedState = localStorage.getItem("sidebarCollapsed")
      if (savedState === "true") {
        sidebar.classList.add("collapsed")
        contentWrapper.classList.add("sidebar-collapsed")
        header.classList.add("sidebar-collapsed")
      }
    }
  }

  // Handle window resize
  function handleResize() {
    if (isMobile()) {
      // Reset desktop classes on mobile
      sidebar.classList.remove("collapsed")
      contentWrapper.classList.remove("sidebar-collapsed")
      header.classList.remove("sidebar-collapsed")

      // Close sidebar if open
      if (sidebar.classList.contains("show")) {
        sidebar.classList.remove("show")
        overlay.classList.remove("show")
        body.style.overflow = ""
      }
    } else {
      // Reset mobile classes on desktop
      sidebar.classList.remove("show")
      overlay.classList.remove("show")
      body.style.overflow = ""

      // Restore saved state
      initSidebarState()
    }
  }

  // Event listeners
  if (toggleSidebar) {
    toggleSidebar.addEventListener("click", toggleSidebarState)
  }

  // Close sidebar when clicking overlay (mobile)
  overlay.addEventListener("click", () => {
    if (isMobile()) {
      sidebar.classList.remove("show")
      overlay.classList.remove("show")
      body.style.overflow = ""
    }
  })

  // Close sidebar when pressing escape key
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      if (isMobile() && sidebar.classList.contains("show")) {
        sidebar.classList.remove("show")
        overlay.classList.remove("show")
        body.style.overflow = ""
      }
    }
  })

  // Handle window resize
  window.addEventListener("resize", handleResize)

  // Initialize sidebar state
  initSidebarState()

  // User dropdown functionality
  const userDropdownToggle = document.querySelector(".user-dropdown-toggle")
  const userDropdownMenu = document.querySelector(".user-dropdown-menu")

  if (userDropdownToggle && userDropdownMenu) {
    userDropdownToggle.addEventListener("click", (event) => {
      event.stopPropagation()
      userDropdownMenu.classList.toggle("show")
    })

    // Close dropdown when clicking outside
    document.addEventListener("click", (event) => {
      if (!userDropdownToggle.contains(event.target) && !userDropdownMenu.contains(event.target)) {
        userDropdownMenu.classList.remove("show")
      }
    })
  }

  // Modal functionality
  const modalTriggers = document.querySelectorAll('[data-toggle="modal"]')

  modalTriggers.forEach((trigger) => {
    trigger.addEventListener("click", function () {
      const targetModal = document.querySelector(this.dataset.target)
      if (targetModal) {
        targetModal.classList.add("show")
        body.style.overflow = "hidden"
      }
    })
  })

  const modalCloseButtons = document.querySelectorAll('[data-dismiss="modal"]')

  modalCloseButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const modal = this.closest(".modal")
      if (modal) {
        modal.classList.remove("show")
        body.style.overflow = ""
      }
    })
  })

  // Close modal when clicking outside
  const modals = document.querySelectorAll(".modal")

  modals.forEach((modal) => {
    modal.addEventListener("click", function (event) {
      if (event.target === this) {
        this.classList.remove("show")
        body.style.overflow = ""
      }
    })
  })

  // Alert dismissible
  const alertCloseButtons = document.querySelectorAll(".alert .close")

  alertCloseButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const alert = this.closest(".alert")
      if (alert) {
        alert.remove()
      }
    })
  })

  // Stock validation for borrow form
  const saranaSelect = document.getElementById("sarana_id")
  const jumlahInput = document.getElementById("jumlah")
  const stockWarning = document.getElementById("stockWarning")
  const submitBtn = document.getElementById("submitBtn")

  if (saranaSelect && jumlahInput && stockWarning && submitBtn) {
    function checkStock() {
      if (saranaSelect.selectedIndex > 0) {
        const selectedOption = saranaSelect.options[saranaSelect.selectedIndex]
        const availableStock = Number.parseInt(selectedOption.dataset.stock)
        const requestedAmount = Number.parseInt(jumlahInput.value)

        if (requestedAmount > availableStock) {
          stockWarning.style.display = "block"
          submitBtn.disabled = true
        } else {
          stockWarning.style.display = "none"
          submitBtn.disabled = false
        }
      }
    }

    saranaSelect.addEventListener("change", checkStock)
    jumlahInput.addEventListener("input", checkStock)
  }

  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault()
      const target = document.querySelector(this.getAttribute("href"))
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
        })
      }
    })
  })

  // Auto-hide alerts after 5 seconds
  const alerts = document.querySelectorAll(".alert")
  alerts.forEach((alert) => {
    setTimeout(() => {
      if (alert.parentNode) {
        alert.style.opacity = "0"
        alert.style.transform = "translateY(-20px)"
        setTimeout(() => {
          alert.remove()
        }, 300)
      }
    }, 5000)
  })
})

// Print report function - opens print dialog directly
function printReport(startDate, endDate) {
  // Create URL for print page
  const printUrl = `print-report.php?start_date=${startDate}&end_date=${endDate}`

  // Open print page in new window
  const printWindow = window.open(printUrl, "printWindow", "width=800,height=600,scrollbars=yes")

  // Focus on the new window
  if (printWindow) {
    printWindow.focus()
  }
}

// Alternative: Print current page content (if needed)
function printCurrentReport() {
  // Hide elements that shouldn't be printed
  const elementsToHide = document.querySelectorAll(".sidebar, .header, .no-print, .btn, .form-row")
  const originalDisplay = []

  elementsToHide.forEach((element, index) => {
    originalDisplay[index] = element.style.display
    element.style.display = "none"
  })

  // Print
  window.print()

  // Restore hidden elements
  elementsToHide.forEach((element, index) => {
    element.style.display = originalDisplay[index]
  })
}

// Utility function to format currency
function formatCurrency(amount) {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
  }).format(amount)
}

// Utility function to format date
function formatDate(dateString) {
  const options = {
    year: "numeric",
    month: "long",
    day: "numeric",
  }
  return new Date(dateString).toLocaleDateString("id-ID", options)
}
