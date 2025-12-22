/*
 * ----------------------------------------------------
 * Custom Login Styles Starts
 * ----------------------------------------------------
 */

document.addEventListener("DOMContentLoaded", function () {
    // Toggle Password Visibility
    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener("click", function () {
            const type =
                passwordInput.getAttribute("type") === "password"
                    ? "text"
                    : "password";
            passwordInput.setAttribute("type", type);
            this.innerHTML =
                type === "password"
                    ? '<i class="fas fa-eye"></i>'
                    : '<i class="fas fa-eye-slash"></i>';

            // Update aria-label for accessibility
            this.setAttribute(
                "aria-label",
                type === "password" ? "Show password" : "Hide password"
            );

            // Switch all borders to white
            const inputGroup = passwordInput.closest(".input-group");
            if (inputGroup) {
                const inputIcon = inputGroup.querySelector(".custom-login-input-icon");
                const passwordToggle = inputGroup.querySelector(".custom-login-password-toggle");

                if (type === "text") {
                    // Show password - switch borders to white
                    if (inputIcon) {
                        inputIcon.style.borderColor = "white";
                    }
                    if (passwordToggle) {
                        passwordToggle.style.borderColor = "white";
                    }
                } else {
                    // Hide password - revert to default border
                    if (inputIcon) {
                        inputIcon.style.borderColor = "var(--custom-login-border)";
                    }
                    if (passwordToggle) {
                        passwordToggle.style.borderColor = "var(--custom-login-border)";
                    }
                }
            }
        });

        // Add keyboard support for password toggle
        togglePassword.addEventListener("keydown", function (e) {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                this.click();
            }
        });
    }

    // Form submission animation
    const loginForm = document.getElementById("loginForm");
    const submitBtn = loginForm?.querySelector(".custom-login-submit-btn");

    if (loginForm && submitBtn) {
        loginForm.addEventListener("submit", function (e) {
            const btnText = submitBtn.querySelector(".custom-login-btn-text");
            const btnIcon = submitBtn.querySelector("i");

            // Show loading state
            if (btnText) btnText.textContent = "Signing In...";
            if (btnIcon) {
                btnIcon.className = "fas fa-spinner fa-spin ms-2";
            }

            submitBtn.disabled = true;
            submitBtn.style.opacity = "0.8";
            submitBtn.style.cursor = "wait";

            // Prevent multiple submissions
            if (loginForm.getAttribute("data-submitting") === "true") {
                e.preventDefault();
                return;
            }

            loginForm.setAttribute("data-submitting", "true");
        });
    }

    // Input focus effects
    const inputs = document.querySelectorAll(".custom-login-input");
    inputs.forEach((input) => {
        const inputGroup = input.closest(".input-group");
        if (!inputGroup) return;

        const inputIcon = inputGroup.querySelector(".custom-login-input-icon");
        const passwordToggle = inputGroup.querySelector(
            ".custom-login-password-toggle"
        );

        input.addEventListener("focus", function () {
            if (inputIcon) {
                inputIcon.style.borderColor = "var(--custom-login-primary)";
                inputIcon.style.color = "var(--custom-login-primary)";
            }
        });

        input.addEventListener("blur", function () {
            if (inputIcon) {
                inputIcon.style.borderColor = "var(--custom-login-border)";
                inputIcon.style.color = "var(--custom-login-gray)";
            }
        });

        // Handle password toggle focus
        if (passwordToggle) {
            passwordToggle.addEventListener("focus", function () {
                inputIcon.style.borderColor = "var(--custom-login-primary)";
                inputIcon.style.color = "var(--custom-login-primary)";
            });

            passwordToggle.addEventListener("blur", function () {
                if (!input.matches(":focus")) {
                    inputIcon.style.borderColor = "var(--custom-login-border)";
                    inputIcon.style.color = "var(--custom-login-gray)";
                }
            });
        }
    });

    // Alert auto-dismiss
    const alert = document.querySelector(".custom-login-alert");
    if (alert) {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 5000);
    }

    // Add responsive class for mobile
    function handleResponsiveLayout() {
        const leftSection = document.querySelector(".custom-login-left");
        const rightSection = document.querySelector(".custom-login-right");

        // Only execute if elements exist (login page)
        if (!leftSection || !rightSection) {
            return;
        }

        if (window.innerWidth < 992) {
            // Mobile layout
            leftSection.classList.remove("col-lg-6");
            rightSection.classList.remove("col-lg-6");
            leftSection.classList.add("col-12");
            rightSection.classList.add("col-12");

            // Adjust heights for mobile
            leftSection.style.minHeight = "200px";
            rightSection.style.minHeight = "calc(100vh - 200px)";
        } else {
            // Desktop layout
            leftSection.classList.remove("col-12");
            rightSection.classList.remove("col-12");
            leftSection.classList.add("col-lg-6");
            rightSection.classList.add("col-lg-6");

            // Reset heights
            leftSection.style.minHeight = "";
            rightSection.style.minHeight = "";
        }
    }

    // Initialize responsive layout
    handleResponsiveLayout();

    // Update on resize
    let resizeTimer;
    window.addEventListener("resize", function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(handleResponsiveLayout, 250);
    });

    // Add smooth scroll for mobile
    if (window.innerWidth < 992) {
        const loginRight = document.querySelector(".custom-login-right");
        if (loginRight) {
            loginRight.scrollIntoView({
                behavior: "smooth",
                block: "start",
            });
        }
    }

    // Form validation feedback
    const usernameInput = document.getElementById("username");
    const passwordInputField = document.getElementById("password");

    if (usernameInput && passwordInputField) {
        [usernameInput, passwordInputField].forEach((input) => {
            input.addEventListener("input", function () {
                if (this.classList.contains("is-invalid")) {
                    this.classList.remove("is-invalid");
                    const errorDiv = this.closest(".mb-3")?.querySelector(
                        ".custom-login-error"
                    );
                    if (errorDiv) errorDiv.remove();
                }
            });
        });
    }
});

/*
 * ----------------------------------------------------
 * Custom Login Styles Ends
 * ----------------------------------------------------
 */

/*
 * ----------------------------------------------------
 * Custom Dashboard Styles Starts
 * ----------------------------------------------------
 */

document.addEventListener("DOMContentLoaded", function () {
    // Initialize dashboard cards
    initializeDashboardCards();

    // Add loading animation on card click
    setupCardClickHandlers();

    // Add ripple effect to cards
    setupRippleEffects();

    // Setup keyboard navigation
    setupKeyboardNavigation();

    // Update stats with animation
    animateStats();

    // Handle responsive behaviors
    handleResponsiveBehaviors();

    // Add subtle hover animations
    setupHoverAnimations();

    // Function to initialize dashboard cards
    function initializeDashboardCards() {
        const cards = document.querySelectorAll(".custom-dashboard-card");

        cards.forEach((card) => {
            // Add accessible attributes
            card.setAttribute("role", "button");
            card.setAttribute("tabindex", "0");

            // Add ARIA labels
            const title = card.querySelector(".custom-dashboard-card-title");
            if (title) {
                card.setAttribute(
                    "aria-label",
                    `Navigate to ${title.textContent} section`
                );
            }

            // Add data attribute for tracking
            card.setAttribute("data-card-type", "dashboard-module");
        });

        console.log(`Initialized ${cards.length} dashboard cards`);
    }

    // Function to setup card click handlers
    function setupCardClickHandlers() {
        const cardLinks = document.querySelectorAll(
            ".custom-dashboard-card-link"
        );

        cardLinks.forEach((link) => {
            link.addEventListener("click", function (e) {
                const card = this.querySelector(".custom-dashboard-card");

                if (card) {
                    // Add loading state
                    card.classList.add("custom-dashboard-card-loading");

                    // Get card type for analytics
                    const cardType = card.getAttribute("data-card-type");
                    console.log(
                        `Navigating to: ${this.href} (Card type: ${cardType})`
                    );

                    // Remove loading state after navigation (or timeout)
                    setTimeout(() => {
                        card.classList.remove("custom-dashboard-card-loading");
                    }, 1500);
                }
            });
        });
    }

    // Function to setup ripple effects
    function setupRippleEffects() {
        const cards = document.querySelectorAll(".custom-dashboard-card");

        cards.forEach((card) => {
            card.addEventListener("click", function (e) {
                // Remove any existing ripple
                const existingRipple = this.querySelector(
                    ".custom-dashboard-ripple"
                );
                if (existingRipple) {
                    existingRipple.remove();
                }

                // Create ripple element
                const ripple = document.createElement("span");
                ripple.classList.add("custom-dashboard-ripple");

                // Get click position
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                // Set ripple position and size
                ripple.style.width = ripple.style.height = size + "px";
                ripple.style.left = x + "px";
                ripple.style.top = y + "px";

                // Add ripple to card
                this.appendChild(ripple);

                // Remove ripple after animation
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Add ripple styles dynamically
        const rippleStyle = document.createElement("style");
        rippleStyle.textContent = `
                .custom-dashboard-ripple {
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(79, 70, 229, 0.2);
                    transform: scale(0);
                    animation: custom-dashboard-ripple-animation 0.6s linear;
                    pointer-events: none;
                }
                
                @keyframes custom-dashboard-ripple-animation {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
                
                .custom-dashboard-card-loading {
                    position: relative;
                    overflow: hidden;
                }
                
                .custom-dashboard-card-loading::after {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
                    animation: custom-dashboard-loading 1.5s infinite;
                }
                
                @keyframes custom-dashboard-loading {
                    100% {
                        left: 100%;
                    }
                }
            `;
        document.head.appendChild(rippleStyle);
    }

    // Function to setup keyboard navigation
    function setupKeyboardNavigation() {
        const cards = document.querySelectorAll(".custom-dashboard-card");

        cards.forEach((card, index) => {
            card.addEventListener("keydown", function (e) {
                // Enter or Space to trigger click
                if (e.key === "Enter" || e.key === " ") {
                    e.preventDefault();
                    this.click();
                }

                // Arrow key navigation
                if (e.key === "ArrowRight" || e.key === "ArrowDown") {
                    e.preventDefault();
                    const nextCard = cards[index + 1];
                    if (nextCard) {
                        nextCard.focus();
                    }
                }

                if (e.key === "ArrowLeft" || e.key === "ArrowUp") {
                    e.preventDefault();
                    const prevCard = cards[index - 1];
                    if (prevCard) {
                        prevCard.focus();
                    }
                }
            });

            // Focus styles
            card.addEventListener("focus", function () {
                this.style.outline =
                    "2px solid var(--custom-dashboard-primary)";
                this.style.outlineOffset = "2px";
            });

            card.addEventListener("blur", function () {
                this.style.outline = "none";
            });
        });
    }

    // Function to animate stats
    function animateStats() {
        const statNumbers = document.querySelectorAll(
            ".custom-dashboard-stat-number"
        );

        // Animate numbers if they have values
        statNumbers.forEach((stat) => {
            const targetValue = parseInt(stat.textContent);
            if (targetValue > 0) {
                animateCounter(stat, 0, targetValue, 1500);
            }
        });

        // Animate counter function
        function animateCounter(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min(
                    (timestamp - startTimestamp) / duration,
                    1
                );
                const currentValue = Math.floor(
                    progress * (end - start) + start
                );
                element.textContent = currentValue;
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }
    }

    // Function to handle responsive behaviors
    function handleResponsiveBehaviors() {
        // Check screen size and adjust layout
        function checkScreenSize() {
            const isMobile = window.innerWidth < 768;
            const cards = document.querySelectorAll(".custom-dashboard-card");

            cards.forEach((card) => {
                if (isMobile) {
                    card.setAttribute("data-device", "mobile");
                } else {
                    card.setAttribute("data-device", "desktop");
                }
            });
        }

        // Initial check
        checkScreenSize();

        // Check on resize (with debounce)
        let resizeTimer;
        window.addEventListener("resize", function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(checkScreenSize, 250);
        });

        // Add touch feedback for mobile
        if ("ontouchstart" in window) {
            document
                .querySelectorAll(".custom-dashboard-card")
                .forEach((card) => {
                    card.addEventListener("touchstart", function () {
                        this.classList.add("custom-dashboard-card-touch");
                    });

                    card.addEventListener("touchend", function () {
                        this.classList.remove("custom-dashboard-card-touch");
                    });
                });
        }
    }

    // Function to setup hover animations
    function setupHoverAnimations() {
        // Parallax effect on mouse move
        document.addEventListener("mousemove", function (e) {
            const cards = document.querySelectorAll(".custom-dashboard-card");
            const mouseX = e.clientX / window.innerWidth;
            const mouseY = e.clientY / window.innerHeight;

            cards.forEach((card) => {
                const speed = 0.5;
                const x = mouseX * speed * 10 - speed * 5;
                const y = mouseY * speed * 10 - speed * 5;

                // Only apply on desktop
                if (window.innerWidth >= 768) {
                    card.style.transform = `translateY(-5px) translate3d(${x}px, ${y}px, 0)`;
                }
            });
        });

        // Reset transform on mouse leave container
        const container = document.querySelector(".custom-dashboard-container");
        if (container) {
            container.addEventListener("mouseleave", function () {
                const cards = document.querySelectorAll(
                    ".custom-dashboard-card"
                );
                cards.forEach((card) => {
                    card.style.transform = "";
                });
            });
        }

        // Add CSS for touch feedback
        const touchStyle = document.createElement("style");
        touchStyle.textContent = `
                .custom-dashboard-card-touch {
                    transform: scale(0.98) !important;
                    transition: transform 0.1s ease;
                }
            `;
        document.head.appendChild(touchStyle);
    }

    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== "undefined") {
        const tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Log initialization
    console.log("Dashboard initialized successfully");
});

/*
 * ----------------------------------------------------
 * Custom Dashboard Styles Ends
 * ----------------------------------------------------
 */

/*
 * ----------------------------------------------------
 * Custom AddCustomer Styles Starts
 * ----------------------------------------------------
 */

document.addEventListener("DOMContentLoaded", function () {
    // Purely visual enhancements

    // Add focus effects to form inputs
    const inputs = document.querySelectorAll(
        ".custom-addcustomer-input, .custom-addcustomer-select, .custom-addcustomer-textarea"
    );

    inputs.forEach((input) => {
        // Add focus class
        input.addEventListener("focus", function () {
            this.parentElement.classList.add("custom-addcustomer-focused");
        });

        input.addEventListener("blur", function () {
            this.parentElement.classList.remove("custom-addcustomer-focused");
        });

        // Add value detection for labels
        if (input.value) {
            input.parentElement.classList.add("custom-addcustomer-has-value");
        }

        input.addEventListener("input", function () {
            if (this.value) {
                this.parentElement.classList.add(
                    "custom-addcustomer-has-value"
                );
            } else {
                this.parentElement.classList.remove(
                    "custom-addcustomer-has-value"
                );
            }
        });
    });

    // Add subtle animation to form submission
    const submitBtn = document.querySelector(".custom-addcustomer-submit-btn");
    const form = document.getElementById("customerForm");

    if (submitBtn && form) {
        form.addEventListener("submit", function () {
            // Add loading animation (visual only)
            submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Processing...
                `;
            submitBtn.disabled = true;
        });
    }

    // Add CSS for focus effects
    const style = document.createElement("style");
    style.textContent = `
            .custom-addcustomer-focused .custom-addcustomer-label {
                color: var(--custom-addcustomer-primary);
            }
            
            .custom-addcustomer-has-value .custom-addcustomer-label {
                color: #374151;
            }
            
            .spinner-border {
                vertical-align: middle;
            }
        `;
    document.head.appendChild(style);

    // Make form card appear with animation
    const card = document.querySelector(".custom-addcustomer-card");
    if (card) {
        card.style.opacity = "0";
        card.style.transform = "translateY(20px)";

        setTimeout(() => {
            card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
        }, 100);
    }

    console.log("Custom add customer design enhancements loaded");
});

/*
 * ----------------------------------------------------
 * Custom AddCustomer Styles Ends
 * ----------------------------------------------------
 */

/*
 * ----------------------------------------------------
 * Custom Nav Styles Starts
 * ----------------------------------------------------
 */

document.addEventListener("DOMContentLoaded", function () {
    // Add active state to current page
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll(".custom-nav-link");

    navLinks.forEach((link) => {
        if (link.getAttribute("href") === currentPath) {
            link.classList.add("active");
        }
    });

    // Smooth dropdown animations
    const dropdowns = document.querySelectorAll(".dropdown");

    dropdowns.forEach((dropdown) => {
        dropdown.addEventListener("show.bs.dropdown", function () {
            const menu = this.querySelector(".dropdown-menu");
            menu.style.animation =
                "custom-nav-dropdown-animation 0.2s ease-out";
        });
    });

    // Add ripple effect to user avatar
    const userAvatar = document.querySelector(".custom-nav-user-avatar");

    if (userAvatar) {
        userAvatar.addEventListener("click", function (e) {
            // Create ripple effect
            const ripple = document.createElement("span");
            ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(79, 70, 229, 0.4);
                    transform: scale(0);
                    animation: ripple-animation 0.6s linear;
                    pointer-events: none;
                `;

            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + "px";
            ripple.style.left = x + "px";
            ripple.style.top = y + "px";

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    }

    // Add ripple animation CSS
    const rippleStyle = document.createElement("style");
    rippleStyle.textContent = `
            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
    document.head.appendChild(rippleStyle);

    // Mobile menu close on click
    if (window.innerWidth < 992) {
        const navLinks = document.querySelectorAll(".custom-nav-link");
        const navbarCollapse = document.querySelector(".custom-nav-collapse");

        navLinks.forEach((link) => {
            link.addEventListener("click", () => {
                if (navbarCollapse.classList.contains("show")) {
                    const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                    bsCollapse.hide();
                }
            });
        });
    }
});

/*
 * ----------------------------------------------------
 * Custom Nav Styles Ends
 * ----------------------------------------------------
 */

/*
 * ----------------------------------------------------
 * Custom Customer Index Styles Starts
 * ----------------------------------------------------
 */

// Initialize table interactions
document.addEventListener("DOMContentLoaded", function () {
    // Add row hover effects
    const tableRows = document.querySelectorAll(
        ".custom-customer-index-table-row"
    );
    tableRows.forEach((row) => {
        row.addEventListener("mouseenter", function () {
            this.style.transform = "translateY(-1px)";
            this.style.boxShadow = "0 4px 12px rgba(0, 0, 0, 0.05)";
        });

        row.addEventListener("mouseleave", function () {
            this.style.transform = "";
            this.style.boxShadow = "";
        });
    });

    // Add loading state to delete buttons
    const deleteForms = document.querySelectorAll(
        ".custom-customer-index-delete-form"
    );
    deleteForms.forEach((form) => {
        form.addEventListener("submit", function (e) {
            const button = this.querySelector(
                ".custom-customer-index-delete-btn"
            );
            if (button) {
                button.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Deleting...
                    `;
                button.disabled = true;
            }
        });
    });

    // Add animation to table rows on load
    setTimeout(() => {
        tableRows.forEach((row, index) => {
            row.style.opacity = "0";
            row.style.transform = "translateY(10px)";

            setTimeout(() => {
                row.style.transition = "opacity 0.3s ease, transform 0.3s ease";
                row.style.opacity = "1";
                row.style.transform = "translateY(0)";
            }, index * 50);
        });
    }, 100);

    // Make table responsive with horizontal scroll indicators
    const tableContainer = document.querySelector(
        ".custom-customer-index-table-responsive"
    );
    if (
        tableContainer &&
        tableContainer.scrollWidth > tableContainer.clientWidth
    ) {
        // Add scroll indicators
        const style = document.createElement("style");
        style.textContent = `
                .custom-customer-index-table-responsive {
                    position: relative;
                }
                
                .custom-customer-index-table-responsive::after {
                    content: '→';
                    position: absolute;
                    right: 10px;
                    top: 50%;
                    transform: translateY(-50%);
                    color: var(--custom-customer-index-primary);
                    font-size: 1rem;
                    animation: custom-customer-index-scroll-hint 2s infinite;
                    opacity: 0.5;
                    pointer-events: none;
                }
                
                @keyframes custom-customer-index-scroll-hint {
                    0%, 100% { opacity: 0.5; transform: translateY(-50%) translateX(0); }
                    50% { opacity: 1; transform: translateY(-50%) translateX(5px); }
                }
            `;
        document.head.appendChild(style);
    }
});

/*
 * ----------------------------------------------------
 * Custom Customer Index Styles Ends
 * ----------------------------------------------------
 */
