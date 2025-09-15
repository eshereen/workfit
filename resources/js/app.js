"use strict";
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

console.log('[app.js] loaded');

// Mobile menu toggle (used by inline or programmatic click)
function toggleMobileMenu() {
    console.log('[app.js] toggleMobileMenu called');
    var menu = document.getElementById('mobileMenu');
    var btn = document.getElementById('mobile-menu-button');
    if (!menu) return;
    var isHidden = menu.classList.contains('hidden') || window.getComputedStyle(menu).display === 'none';
    if (isHidden) {
        menu.classList.remove('hidden');
        menu.style.display = 'block';
    } else {
        menu.classList.add('hidden');
        menu.style.display = 'none';
    }
    if (btn) {
        var expanded = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', (!expanded).toString());
    }
}

// Expose globally
window.toggleMobileMenu = toggleMobileMenu;

function initMobileMenu() {
    var menu = document.getElementById('mobileMenu');
    if (menu) {
        menu.addEventListener('click', function (e) {
            var target = e.target;
            if (target && target.tagName === 'A') {
                if (!menu.classList.contains('hidden')) {
                    toggleMobileMenu();
                }
            }
        });
    }

    document.addEventListener('click', function (e) {
        var menuEl = document.getElementById('mobileMenu');
        var btnEl = document.getElementById('mobile-menu-button');
        if (!menuEl || !btnEl) return;
        var clickInsideMenu = menuEl.contains(e.target);
        var clickOnButton = btnEl.contains(e.target);
        if (!clickInsideMenu && !clickOnButton) {
            var isOpen = !menuEl.classList.contains('hidden') || window.getComputedStyle(menuEl).display !== 'none';
            if (isOpen) {
                menuEl.classList.add('hidden');
                menuEl.style.display = 'none';
                btnEl.setAttribute('aria-expanded', 'false');
            }
        }
    });
}

function initEnhancements() {
    // Smooth scrolling for anchor links
    var anchors = document.querySelectorAll('a[href^="#"]');
    anchors.forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var href = this.getAttribute('href');
            if (!href || href === '#') return;
            var target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Note: Removed demo form submission handler to avoid interfering with Livewire forms
}

function initScrollEffects() {
    // Scroll state
    window.addEventListener('scroll', function () {
        var body = document.body;
        if (window.scrollY > 10) {
            body.classList.add('scrolled');
        } else {
            body.classList.remove('scrolled');
        }
    });

    // Intersection observer for animations
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.animate-on-scroll').forEach(function (el) {
        observer.observe(el);
    });
}

function initializeApp() {
    initMobileMenu();
    initEnhancements();
    initScrollEffects();
}

// Initialize based on document ready state
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeApp);
} else {
    initializeApp();
}
