/**
 * KNK Floating Contact JavaScript - Desktop Hover Expansion
 */
jQuery(document).ready(function($) {
    
    // Check if device is desktop (min-width: 769px)
    function isDesktop() {
        return window.innerWidth >= 769;
    }
    
    // Enhanced hover effects for desktop only
    $('.knk-floating-link').each(function() {
        const $link = $(this);
        const $text = $link.find('.knk-floating-text');
        
        // Add smooth entrance animation on page load
        setTimeout(function() {
            $link.addClass('knk-loaded');
        }, 500);
        
        // Enhanced hover effects only on desktop
        if (isDesktop()) {
            $link.on('mouseenter', function() {
                $(this).addClass('knk-hovered');
                
                // Add subtle bounce effect
                $(this).css('transform', 'translateX(-10px) scale(1.02)');
                
                // Animate text appearance
                $text.css({
                    'transition-delay': '0.1s'
                });
            });
            
            $link.on('mouseleave', function() {
                $(this).removeClass('knk-hovered');
                $(this).css('transform', 'translateX(0) scale(1)');
                
                // Reset text animation
                $text.css({
                    'transition-delay': '0s'
                });
            });
            
            // Add click animation
            $link.on('mousedown', function() {
                $(this).css('transform', 'translateX(-10px) scale(0.98)');
            });
            
            $link.on('mouseup', function() {
                if ($(this).hasClass('knk-hovered')) {
                    $(this).css('transform', 'translateX(-10px) scale(1.02)');
                } else {
                    $(this).css('transform', 'translateX(0) scale(1)');
                }
            });
        }
    });
    
    // Add entrance animation styles
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .knk-floating-link {
                opacity: 0;
                transform: translateX(100px) scale(0.8);
                transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }
            
            .knk-floating-link.knk-loaded {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
            
            @media (min-width: 769px) {
                .knk-floating-link.knk-hovered {
                    animation: pulse 2s infinite;
                }
                
                @keyframes pulse {
                    0% { box-shadow: 0 6px 30px rgba(0, 0, 0, 0.2); }
                    50% { box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3); }
                    100% { box-shadow: 0 6px 30px rgba(0, 0, 0, 0.2); }
                }
                
                /* Smooth transitions for all states */
                .knk-floating-icon svg {
                    transition: transform 0.3s ease;
                }
                
                .knk-floating-link:hover .knk-floating-icon svg {
                    transform: scale(1.1);
                }
            }
        `)
        .appendTo('head');
    
    // Add staggered animation for multiple items
    $('.knk-floating-item').each(function(index) {
        const $item = $(this);
        const delay = index * 200; // 200ms delay between items
        
        setTimeout(function() {
            $item.find('.knk-floating-link').addClass('knk-loaded');
        }, 500 + delay);
    });
    
    // Keyboard accessibility
    $('.knk-floating-link').on('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            this.click();
        }
    });
    
    // Add focus styles for accessibility
    $('.knk-floating-link').on('focus', function() {
        $(this).addClass('knk-focused');
    });
    
    $('.knk-floating-link').on('blur', function() {
        $(this).removeClass('knk-focused');
    });
    
    // Add focus styles via CSS
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .knk-floating-link.knk-focused {
                outline: 2px solid #ffffff;
                outline-offset: 2px;
            }
            
            .knk-floating-link:focus {
                outline: none;
            }
        `)
        .appendTo('head');
});
