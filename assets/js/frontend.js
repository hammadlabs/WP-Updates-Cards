/**
 * Frontend JavaScript for Custom Div Box Manager
 */

jQuery(document).ready(function($) {
    'use strict';
    
    // Initialize lazy loading for better performance
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('cdbm-lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        // Observe all lazy images
        document.querySelectorAll('.cdbm-box-image img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // Smooth scroll for anchor links
    $('.cdbm-box-card a[href^="#"]').on('click', function(e) {
        const target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 600);
        }
    });
    
    // Add hover effects for better UX
    $('.cdbm-box-card').hover(
        function() {
            $(this).addClass('cdbm-hover');
        },
        function() {
            $(this).removeClass('cdbm-hover');
        }
    );
    
    // Handle window resize for responsive adjustments
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Trigger any resize-specific adjustments here
            $('.cdbm-container').trigger('resize');
        }, 250);
    });
    
    // Add loading state management
    $('.cdbm-container').on('loading', function() {
        $(this).addClass('loading');
    }).on('loading-complete', function() {
        $(this).removeClass('loading');
    });
    
    // Animation on scroll (optional enhancement)
    if ('IntersectionObserver' in window) {
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('cdbm-animate-in');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        document.querySelectorAll('.cdbm-box-card').forEach(card => {
            animationObserver.observe(card);
        });
    }
    
    // Prevent broken image display
    $('.cdbm-box-image img').on('error', function() {
        $(this).closest('.cdbm-box-image').html('<div class="cdbm-no-image">Image not available</div>');
    });
    
    // Add keyboard navigation support
    $('.cdbm-box-card').attr('tabindex', '0').on('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            const link = $(this).find('a[href]').first();
            if (link.length) {
                window.location.href = link.attr('href');
            }
        }
    });
    
    // Theme color inheritance helper - ensure proper color inheritance on load and theme changes
    function ensureThemeColorInheritance() {
        $('.cdbm-box-card').each(function() {
            const $card = $(this);
            const $body = $('body');
            
            // Check if body has your theme's specific classes
            if ($body.hasClass('defaultcolor')) {
                // Light mode - ensure inheritance
                $card.css({
                    'background': 'inherit',
                    'color': 'inherit'
                });
            } else if ($body.hasClass('dark')) {
                // Dark mode - ensure inheritance
                $card.css({
                    'background': 'inherit',
                    'color': 'inherit'
                });
            }
        });
    }
    
    // Theme class change detection for your specific theme
    function initThemeChangeDetection() {
        // Use MutationObserver to watch for class changes on body element
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    // Body class changed, update plugin colors
                    setTimeout(ensureThemeColorInheritance, 100);
                }
            });
        });
        
        // Observe body element for class changes
        observer.observe(document.body, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
    
    // Initialize theme change detection
    initThemeChangeDetection();
    
    // Run on load and resize
    ensureThemeColorInheritance();
    $(window).on('resize load', ensureThemeColorInheritance);
});
