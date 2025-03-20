/**
 * Mobile.js - Main JavaScript file for Mobile WebView App
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize mobile app features
    initMobileApp();
  });
  
  /**
   * Initialize all mobile app features
   */
  function initMobileApp() {
    // Setup navigation handling
    setupNavigationHandling();
    
    // Initialize modals
    initModals();
    
    // Handle form submissions
    setupFormHandling();
    
    // Setup notifications handling
    setupNotifications();
    
    
    // Add touch gestures
    addTouchGestures();
  }
  
  /**
   * Setup mobile navigation
   */
  function setupNavigationHandling() {
    // Bottom navigation active state
    const currentPath = window.location.pathname;
    const navItems = document.querySelectorAll('.mobile-bottom-nav .nav-item');
    
    navItems.forEach(item => {
      const href = item.getAttribute('href');
      if (href && (currentPath.endsWith(href) || (href === 'index.php' && currentPath.endsWith('/mobile/')) || 
          (currentPath.includes(href) && href !== 'index.php'))) {
        item.classList.add('active');
      }
    });
    
    // Handle back button for browsers
    const backButtons = document.querySelectorAll('.back-link, .back-btn');
    backButtons.forEach(button => {
      button.addEventListener('click', function(e) {
        // Check if there's history to go back to
        if (window.history.length > 1) {
          e.preventDefault();
          window.history.back();
        }
      });
    });
  }
  
  /**
   * Initialize modal dialogs
   */
  function initModals() {
    // Show modal function
    window.showModal = function(modalId) {
      const modal = document.getElementById(modalId);
      const backdrop = document.getElementById('backdrop') || 
                      document.getElementById('backdropOverlay');
      
      if (modal) {
        modal.style.display = 'block';
        setTimeout(() => {
          modal.classList.add('active');
        }, 10);
      }
      
      if (backdrop) {
        backdrop.style.display = 'block';
        setTimeout(() => {
          backdrop.classList.add('active');
        }, 10);
      }
      
      document.body.style.overflow = 'hidden';
    };
    
    // Hide modal function
    window.hideModal = function(modalId) {
      const modal = document.getElementById(modalId);
      const backdrop = document.getElementById('backdrop') || 
                      document.getElementById('backdropOverlay');
      
      if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
          modal.style.display = 'none';
        }, 300);
      }
      
      if (backdrop) {
        backdrop.classList.remove('active');
        setTimeout(() => {
          backdrop.style.display = 'none';
        }, 300);
      }
      
      document.body.style.overflow = '';
    };
    
    // Add event listeners for modal close actions
    const closeButtons = document.querySelectorAll('.close-modal, .close-btn');
    closeButtons.forEach(button => {
      button.addEventListener('click', function() {
        const modal = this.closest('.modal') || this.closest('.language-modal');
        if (modal) {
          const modalId = modal.getAttribute('id');
          window.hideModal(modalId);
        }
      });
    });
    
    // Close modal when clicking backdrop
    const backdrops = document.querySelectorAll('.backdrop, .backdrop-overlay');
    backdrops.forEach(backdrop => {
      backdrop.addEventListener('click', function() {
        const openModals = document.querySelectorAll('.modal.active, .language-modal.active');
        openModals.forEach(modal => {
          const modalId = modal.getAttribute('id');
          window.hideModal(modalId);
        });
      });
    });
  }
  
  /**
   * Setup form submission handling
   */
  function setupFormHandling() {
    // Generic form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
      form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
          if (!field.value.trim()) {
            isValid = false;
            field.classList.add('error');
            
            // Add error message if not exists
            let errorMsg = field.nextElementSibling;
            if (!errorMsg || !errorMsg.classList.contains('error-message')) {
              errorMsg = document.createElement('div');
              errorMsg.className = 'error-message';
              errorMsg.textContent = 'This field is required';
              field.parentNode.insertBefore(errorMsg, field.nextSibling);
            }
          } else {
            field.classList.remove('error');
            const errorMsg = field.nextElementSibling;
            if (errorMsg && errorMsg.classList.contains('error-message')) {
              errorMsg.remove();
            }
          }
        });
        
        if (!isValid) {
          e.preventDefault();
          // Scroll to first error
          const firstError = form.querySelector('.error');
          if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
          }
        }
      });
    });
    
    // Clear error state on input
    const formInputs = document.querySelectorAll('input, textarea, select');
    formInputs.forEach(input => {
      input.addEventListener('input', function() {
        this.classList.remove('error');
        const errorMsg = this.nextElementSibling;
        if (errorMsg && errorMsg.classList.contains('error-message')) {
          errorMsg.remove();
        }
      });
    });
  }
  
  /**
   * Setup notifications handling
   */
  function setupNotifications() {
    // Auto dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
      setTimeout(() => {
        alert.classList.add('fade-out');
        setTimeout(() => {
          alert.remove();
        }, 300);
      }, 5000);
    });
    
    // Toast notifications
    window.showToast = function(message, type = 'info') {
      const toast = document.createElement('div');
      toast.className = `toast-notification toast-${type}`;
      toast.textContent = message;
      
      document.body.appendChild(toast);
      
      setTimeout(() => {
        toast.classList.add('show');
      }, 10);
      
      setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
          document.body.removeChild(toast);
        }, 300);
      }, 3000);
    };
  }
  

  
  /**
   * Add touch gestures for better mobile experience
   */
  function addTouchGestures() {
    // Variables for touch handling
    let touchStartX = 0;
    let touchEndX = 0;
    let touchStartY = 0;
    let touchEndY = 0;
    
    // Start touch
    document.addEventListener('touchstart', function(e) {
      touchStartX = e.changedTouches[0].screenX;
      touchStartY = e.changedTouches[0].screenY;
    }, false);
    
    // End touch
    document.addEventListener('touchend', function(e) {
      touchEndX = e.changedTouches[0].screenX;
      touchEndY = e.changedTouches[0].screenY;
      handleGesture();
    }, false);
    
    // Handle the gesture
    function handleGesture() {
      // Calculate distance
      const deltaX = touchEndX - touchStartX;
      const deltaY = touchEndY - touchStartY;
      
      // Only handle horizontal swipes that are significant
      if (Math.abs(deltaX) > 100 && Math.abs(deltaY) < 100) {
        // Right to left swipe (next)
        if (deltaX < 0) {
          handleNextSlide();
        }
        // Left to right swipe (previous/back)
        else if (deltaX > 0) {
          handlePrevSlide();
        }
      }
    }
    
    // Handle next slide in carousels or similar elements
    function handleNextSlide() {
      const carousels = document.querySelectorAll('.testimonial-slider, .carousel');
      carousels.forEach(carousel => {
        const nextButton = carousel.querySelector('.next-btn, .carousel-next');
        if (nextButton) {
          nextButton.click();
        }
      });
    }
// Handle previous slide in carousels or similar elements
function handlePrevSlide() {
    const carousels = document.querySelectorAll('.testimonial-slider, .carousel');
    carousels.forEach(carousel => {
      const prevButton = carousel.querySelector('.prev-btn, .carousel-prev');
      if (prevButton) {
        prevButton.click();
      }
    });
  }
  
  // Add swipe class to elements that should respond to swipe
  const swipeElements = document.querySelectorAll('.swipeable, .testimonial-slider, .carousel');
  swipeElements.forEach(element => {
    element.classList.add('has-swipe-listener');
  });
}

/**
 * Utility Functions
 */

// Format currency
window.formatCurrency = function(amount, currency = 'USDT', decimals = 2) {
  return parseFloat(amount).toFixed(decimals) + ' ' + currency;
};

// Format large numbers
window.formatNumber = function(num) {
  return new Intl.NumberFormat().format(num);
};

// Format date
window.formatDate = function(dateString, format = 'short') {
  const date = new Date(dateString);
  
  if (format === 'short') {
    return date.toLocaleDateString();
  } else if (format === 'long') {
    return date.toLocaleDateString(undefined, { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
  } else if (format === 'time') {
    return date.toLocaleTimeString(undefined, { 
      hour: '2-digit', 
      minute: '2-digit' 
    });
  } else if (format === 'full') {
    return date.toLocaleString(undefined, {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }
  
  return date.toLocaleString();
};

// Copy to clipboard
window.copyToClipboard = function(text, successMessage = 'Copied to clipboard!') {
  // Create temporary input
  const input = document.createElement('input');
  input.style.position = 'fixed';
  input.style.opacity = 0;
  input.value = text;
  document.body.appendChild(input);
  
  // Select and copy
  input.select();
  input.setSelectionRange(0, 99999);
  document.execCommand('copy');
  
  // Remove temporary input
  document.body.removeChild(input);
  
  // Show success toast
  if (window.showToast) {
    window.showToast(successMessage, 'success');
  }
  
  return true;
};

// Debounce function for performance optimization
window.debounce = function(func, wait = 300) {
  let timeout;
  return function(...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), wait);
  };
};

// Throttle function for performance optimization
window.throttle = function(func, limit = 300) {
  let inThrottle;
  return function(...args) {
    if (!inThrottle) {
      func.apply(this, args);
      inThrottle = true;
      setTimeout(() => inThrottle = false, limit);
    }
  };
};

// Check if device is online
window.isOnline = function() {
  return navigator.onLine;
};

// Show offline message
window.showOfflineMessage = function() {
  if (!document.getElementById('offline-message')) {
    const message = document.createElement('div');
    message.id = 'offline-message';
    message.className = 'offline-message';
    message.innerHTML = '<i class="fas fa-wifi"></i> You are offline. Some features may be unavailable.';
    document.body.appendChild(message);
    
    setTimeout(() => {
      message.classList.add('show');
    }, 10);
  }
};

// Hide offline message
window.hideOfflineMessage = function() {
  const message = document.getElementById('offline-message');
  if (message) {
    message.classList.remove('show');
    setTimeout(() => {
      message.remove();
    }, 300);
  }
};

// Listen for online/offline events
window.addEventListener('online', function() {
  window.hideOfflineMessage();
});

window.addEventListener('offline', function() {
  window.showOfflineMessage();
});

// Track visibility changes
document.addEventListener('visibilitychange', function() {
  if (document.visibilityState === 'visible') {
    // App became visible - refresh data if needed
    if (window.refreshData) {
      window.refreshData();
    }
  }
});

// Refresh data function placeholder - to be implemented by specific pages
window.refreshData = function() {
  // This will be overridden by pages that need to refresh data
  console.log('Data refresh function called');
};

// Pull to refresh functionality
function setupPullToRefresh() {
  let pullStartY = 0;
  let pullMoveY = 0;
  let dist = 0;
  let threshold = 100;
  let isTouching = false;
  let isRefreshing = false;
  let refreshElement;
  
  // Create refresh element if it doesn't exist
  if (!document.getElementById('pull-to-refresh')) {
    refreshElement = document.createElement('div');
    refreshElement.id = 'pull-to-refresh';
    refreshElement.className = 'pull-to-refresh';
    refreshElement.innerHTML = '<div class="refresh-icon"><i class="fas fa-sync-alt"></i></div><div class="refresh-text">Pull to refresh</div>';
    document.body.insertBefore(refreshElement, document.body.firstChild);
  } else {
    refreshElement = document.getElementById('pull-to-refresh');
  }
  
  // Touch events
  document.addEventListener('touchstart', function(e) {
    if (window.scrollY === 0 && !isRefreshing) {
      pullStartY = e.touches[0].screenY;
      isTouching = true;
      refreshElement.classList.remove('refreshing');
    }
  });
  
  document.addEventListener('touchmove', function(e) {
    if (isTouching && window.scrollY === 0 && !isRefreshing) {
      pullMoveY = e.touches[0].screenY;
      dist = pullMoveY - pullStartY;
      
      if (dist > 0) {
        document.body.style.overflow = 'hidden';
        e.preventDefault();
        
        const translate = Math.min(dist / 2.5, threshold);
        refreshElement.style.transform = `translateY(${translate}px)`;
        
        if (translate >= threshold) {
          refreshElement.classList.add('ready');
          refreshElement.querySelector('.refresh-text').textContent = 'Release to refresh';
        } else {
          refreshElement.classList.remove('ready');
          refreshElement.querySelector('.refresh-text').textContent = 'Pull to refresh';
        }
      }
    }
  });
  
  document.addEventListener('touchend', function() {
    if (isTouching && dist > 0) {
      if (dist > threshold && !isRefreshing) {
        // Start refreshing
        isRefreshing = true;
        refreshElement.style.transform = 'translateY(60px)';
        refreshElement.classList.add('refreshing');
        refreshElement.querySelector('.refresh-text').textContent = 'Refreshing...';
        
        // Call refresh function
        if (window.refreshData) {
          window.refreshData().then(() => {
            // Finish refreshing
            setTimeout(() => {
              refreshElement.style.transform = 'translateY(0)';
              refreshElement.classList.remove('refreshing');
              refreshElement.classList.remove('ready');
              isRefreshing = false;
            }, 1000);
          }).catch(() => {
            // Error refreshing
            setTimeout(() => {
              refreshElement.style.transform = 'translateY(0)';
              refreshElement.classList.remove('refreshing');
              refreshElement.classList.remove('ready');
              isRefreshing = false;
            }, 1000);
          });
        } else {
          // No refresh function defined, just simulate refresh
          setTimeout(() => {
            refreshElement.style.transform = 'translateY(0)';
            refreshElement.classList.remove('refreshing');
            refreshElement.classList.remove('ready');
            isRefreshing = false;
          }, 2000);
        }
      } else {
        // Not enough pull, reset
        refreshElement.style.transform = 'translateY(0)';
      }
      
      document.body.style.overflow = '';
      isTouching = false;
      dist = 0;
    }
  });
}

// Initialize pull to refresh if this page supports it
if (document.body.classList.contains('supports-pull-refresh')) {
  setupPullToRefresh();
}