/**
 * Justidea Agency - Custom JavaScript
 * Child Theme for Hummingbird
 *
 * Add your custom JavaScript code here
 */

(function() {
  'use strict';

  // Wait for DOM to be ready
  document.addEventListener('DOMContentLoaded', function() {

    console.log('Justidea Agency theme loaded');

    // ===================================
    // EXAMPLE: Smooth Scroll
    // ===================================
    /*
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });
    */

    // ===================================
    // EXAMPLE: Product Quick View Enhancement
    // ===================================
    /*
    const productCards = document.querySelectorAll('.product-miniature');
    productCards.forEach(card => {
      card.addEventListener('mouseenter', function() {
        this.classList.add('hovered');
      });
      card.addEventListener('mouseleave', function() {
        this.classList.remove('hovered');
      });
    });
    */

    // ===================================
    // EXAMPLE: Custom Search Enhancement
    // ===================================
    /*
    const searchInput = document.querySelector('#search_widget input[type="text"]');
    if (searchInput) {
      searchInput.addEventListener('focus', function() {
        this.parentElement.classList.add('search-focused');
      });
      searchInput.addEventListener('blur', function() {
        this.parentElement.classList.remove('search-focused');
      });
    }
    */

    // ===================================
    // EXAMPLE: Add to Cart Animation
    // ===================================
    /*
    document.querySelectorAll('.add-to-cart').forEach(button => {
      button.addEventListener('click', function(e) {
        // Add your custom animation here
        this.classList.add('adding');
        setTimeout(() => {
          this.classList.remove('adding');
        }, 1000);
      });
    });
    */

    // ===================================
    // EXAMPLE: Custom Cookie Notice
    // ===================================
    /*
    function showCookieNotice() {
      const cookieAccepted = localStorage.getItem('cookiesAccepted');
      if (!cookieAccepted) {
        // Show your cookie notice
        console.log('Show cookie notice');
      }
    }

    function acceptCookies() {
      localStorage.setItem('cookiesAccepted', 'true');
      // Hide cookie notice
    }

    showCookieNotice();
    */

    // ===================================
    // Your Custom Code Here
    // ===================================

  });

  // ===================================
  // UTILITY FUNCTIONS
  // ===================================

  /**
   * Debounce function for performance optimization
   */
  function debounce(func, wait, immediate) {
    let timeout;
    return function() {
      const context = this, args = arguments;
      const later = function() {
        timeout = null;
        if (!immediate) func.apply(context, args);
      };
      const callNow = immediate && !timeout;
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
      if (callNow) func.apply(context, args);
    };
  }

  /**
   * Check if element is in viewport
   */
  function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
      rect.top >= 0 &&
      rect.left >= 0 &&
      rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
      rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
  }

  // Make utility functions available globally if needed
  // window.JustideaUtils = { debounce, isInViewport };

})();
