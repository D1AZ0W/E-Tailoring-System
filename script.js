// Mobile menu toggle
document.addEventListener("DOMContentLoaded", () => {
  const hamburger = document.querySelector(".hamburger")
  const navMenu = document.querySelector(".nav-menu")

  if (hamburger && navMenu) {
    hamburger.addEventListener("click", () => {
      navMenu.classList.toggle("active")
    })

    // Close menu when clicking on a link
    document.querySelectorAll(".nav-menu a").forEach((link) => {
      link.addEventListener("click", () => {
        navMenu.classList.remove("active")
      })
    })
  }

  // Category filtering for products and gallery
  const filterButtons = document.querySelectorAll(".filter-btn")
  const items = document.querySelectorAll(".product-card, .gallery-item")

  filterButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const category = this.dataset.category

      // Update active button
      filterButtons.forEach((btn) => btn.classList.remove("active"))
      this.classList.add("active")

      // Filter items
      items.forEach((item) => {
        if (category === "all" || item.dataset.category === category) {
          item.style.display = "block"
        } else {
          item.style.display = "none"
        }
      })
    })
  })

  // Auto-hide alerts after 5 seconds
  setTimeout(() => {
    const alerts = document.querySelectorAll(".alert")
    alerts.forEach((alert) => {
      alert.style.display = "none"
    })
  }, 5000)

  // Form validation
  const forms = document.querySelectorAll("form")
  forms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      const requiredFields = form.querySelectorAll("[required]")
      let isValid = true

      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          field.style.borderColor = "#dc3545"
          isValid = false
        } else {
          field.style.borderColor = "#ddd"
        }
      })

      if (!isValid) {
        e.preventDefault()
        alert("Please fill in all required fields.")
      }
    })
  })

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
})

// Update cart count (for demonstration)
function updateCartCount(count) {
  const cartLinks = document.querySelectorAll(".cart-link")
  cartLinks.forEach((link) => {
    link.textContent = `Cart (${count})`
  })
}

// Price calculator for customize page
function updatePrice(basePrice, fabricModifier = 0) {
  const priceElement = document.getElementById("product-price")
  if (priceElement) {
    const totalPrice = basePrice + fabricModifier
    priceElement.textContent = `NPR ${totalPrice.toLocaleString()}`
  }
}

// Swatch selection
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("swatch")) {
    const container = e.target.closest(".swatch-container")
    if (container) {
      container.querySelectorAll(".swatch").forEach((swatch) => {
        swatch.classList.remove("selected")
      })
      e.target.classList.add("selected")
    }
  }
})
