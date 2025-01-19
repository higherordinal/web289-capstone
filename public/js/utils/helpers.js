/**
 * Helper functions for common JavaScript operations
 */

// Debounce function to limit how often a function can be called
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Format date to a readable string
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Format time elapsed since date
function timeAgo(date) {
    const seconds = Math.floor((new Date() - new Date(date)) / 1000);
    
    let interval = seconds / 31536000;
    if (interval > 1) return Math.floor(interval) + ' years ago';
    
    interval = seconds / 2592000;
    if (interval > 1) return Math.floor(interval) + ' months ago';
    
    interval = seconds / 86400;
    if (interval > 1) return Math.floor(interval) + ' days ago';
    
    interval = seconds / 3600;
    if (interval > 1) return Math.floor(interval) + ' hours ago';
    
    interval = seconds / 60;
    if (interval > 1) return Math.floor(interval) + ' minutes ago';
    
    return Math.floor(seconds) + ' seconds ago';
}

// Validate form inputs
function validateForm(formElement, rules) {
    const errors = {};
    
    for (const [fieldName, fieldRules] of Object.entries(rules)) {
        const field = formElement.querySelector(`[name="${fieldName}"]`);
        if (!field) continue;
        
        const value = field.value.trim();
        
        if (fieldRules.required && !value) {
            errors[fieldName] = `${fieldName} is required`;
            continue;
        }
        
        if (fieldRules.minLength && value.length < fieldRules.minLength) {
            errors[fieldName] = `${fieldName} must be at least ${fieldRules.minLength} characters`;
        }
        
        if (fieldRules.maxLength && value.length > fieldRules.maxLength) {
            errors[fieldName] = `${fieldName} must be less than ${fieldRules.maxLength} characters`;
        }
        
        if (fieldRules.pattern && !fieldRules.pattern.test(value)) {
            errors[fieldName] = `${fieldName} format is invalid`;
        }
    }
    
    return errors;
}

// Show error messages in form
function showFormErrors(formElement, errors) {
    // Clear existing error messages
    formElement.querySelectorAll('.error-message').forEach(el => el.remove());
    
    // Add new error messages
    for (const [fieldName, message] of Object.entries(errors)) {
        const field = formElement.querySelector(`[name="${fieldName}"]`);
        if (!field) continue;
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        
        field.parentNode.appendChild(errorDiv);
        field.classList.add('is-invalid');
    }
}

// Handle dropdown menus
function initializeDropdowns() {
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            const dropdown = toggle.closest('.dropdown');
            dropdown.querySelector('.dropdown-menu').classList.toggle('show');
            
            // Close other dropdowns
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                if (!menu.closest('.dropdown').contains(toggle)) {
                    menu.classList.remove('show');
                }
            });
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
}

// Initialize mobile menu
function initializeMobileMenu() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    
    if (mobileMenuToggle && mainNav) {
        mobileMenuToggle.addEventListener('click', () => {
            mainNav.classList.toggle('show');
            mobileMenuToggle.setAttribute('aria-expanded', 
                mainNav.classList.contains('show'));
        });
    }
}

// Initialize all interactive elements
document.addEventListener('DOMContentLoaded', () => {
    initializeDropdowns();
    initializeMobileMenu();
});
