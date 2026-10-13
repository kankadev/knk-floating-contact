<?php
/**
 * Plugin Name: KNK Floating Contact
 * Description: Floating contact icons for email and phone with hover effects
 * Version: 1.2.0
 * Plugin URI: https://github.com/kankadev/knk-floating-contact
 * Update URI: https://github.com/kankadev/knk-floating-contact
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Author: kanka.dev
 * Author URI: https://kanka.dev
 *
 * Text Domain: knk-floating-contact
 *
 * @link https://kanka.dev
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('KNK_FLOATING_CONTACT_VERSION', '1.2.0');
define('KNK_FLOATING_CONTACT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KNK_FLOATING_CONTACT_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once KNK_FLOATING_CONTACT_PLUGIN_DIR . 'includes/class-svg.php';
require_once KNK_FLOATING_CONTACT_PLUGIN_DIR . 'includes/class-github-updater.php';
new KNK_Floating_Contact_GitHub_Updater(__FILE__);

class KNK_Floating_Contact {

    public function __construct() {
        add_action('init', array($this, 'init'));
    }

    public function init() {
        do_action('wpml_register_single_string', 'knk-floating-contact', 'booking_text', get_option('knk_floating_contact_booking_text', 'Termin buchen'));
        // Load admin settings
        if (is_admin()) {
            require_once KNK_FLOATING_CONTACT_PLUGIN_DIR . 'includes/class-admin-settings.php';
            new KNK_Floating_Contact_Admin_Settings();
        }

        // Load frontend functionality
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('wp_footer', array($this, 'render_floating_icons'));
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'knk-floating-contact-css',
            KNK_FLOATING_CONTACT_PLUGIN_URL . 'assets/css/floating-contact.css',
            array(),
            KNK_FLOATING_CONTACT_VERSION
        );

        wp_enqueue_script(
            'knk-floating-contact-js',
            KNK_FLOATING_CONTACT_PLUGIN_URL . 'assets/js/floating-contact.js',
            array('jquery'),
            KNK_FLOATING_CONTACT_VERSION,
            true
        );
    }

    private function channel_enabled($channel) {
        return in_array(get_option('knk_floating_contact_' . $channel . '_enabled', '1'), array(true, 1, '1'), true);
    }

    public function render_floating_icons() {
        $email = get_option('knk_floating_contact_email', '');
        $email_svg = get_option('knk_floating_contact_email_svg', '');
        $phone = get_option('knk_floating_contact_phone', '');
        $phone_international = get_option('knk_floating_contact_phone_international', '');
        $phone_svg = get_option('knk_floating_contact_phone_svg', '');
        $booking_url = get_option('knk_floating_contact_booking_url', '');
        $booking_svg = get_option('knk_floating_contact_booking_svg', '');
        $booking_text = get_option('knk_floating_contact_booking_text', 'Termin buchen');

        // Make booking text translatable with WPML
        $booking_text = apply_filters('wpml_translate_single_string', $booking_text, 'knk-floating-contact', 'booking_text');

        // Missing flags preserve pre-1.2 configurations without writing defaults.
        $show_email = $this->channel_enabled('email') && !empty($email);
        $show_phone = $this->channel_enabled('phone') && !empty($phone_international);
        $show_booking = $this->channel_enabled('booking') && !empty($booking_url);

        // Do not leave an empty floating wrapper.
        if (!$show_email && !$show_phone && !$show_booking) {
            return;
        }

        echo '<div id="knk-floating-contact">';

        // Email icon
        if ($show_email) {
            echo '<div class="knk-floating-item knk-floating-email">';
            echo '<a href="mailto:' . esc_attr($email) . '" class="knk-floating-link" aria-label="' . esc_attr($email) . '">';
            echo '<div class="knk-floating-icon">';
            if (!empty($email_svg)) {
                // Also sanitize existing stored icons at the output boundary.
                echo KNK_Floating_Contact_SVG::sanitize($email_svg);
            } else {
                echo '<span>📧</span>';
            }
            echo '</div>';
            echo '<div class="knk-floating-text">' . esc_html($email) . '</div>';
            echo '</a>';
            echo '</div>';
        }

        // Phone icon
        if ($show_phone) {
            $display_phone = !empty($phone) ? $phone : $phone_international;
            echo '<div class="knk-floating-item knk-floating-phone">';
            echo '<a href="tel:' . esc_attr($phone_international) . '" class="knk-floating-link" aria-label="' . esc_attr($display_phone) . '">';
            echo '<div class="knk-floating-icon">';
            if (!empty($phone_svg)) {
                // Also sanitize existing stored icons at the output boundary.
                echo KNK_Floating_Contact_SVG::sanitize($phone_svg);
            } else {
                echo '<span>📞</span>';
            }
            echo '</div>';
            echo '<div class="knk-floating-text">' . esc_html($display_phone) . '</div>';
            echo '</a>';
            echo '</div>';
        }

        // Booking icon
        if ($show_booking) {
            echo '<div class="knk-floating-item knk-floating-booking">';
            echo '<a href="' . esc_url($booking_url) . '" class="knk-floating-link" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr($booking_text) . '">';
            echo '<div class="knk-floating-icon">';
            if (!empty($booking_svg)) {
                // Also sanitize existing stored icons at the output boundary.
                echo KNK_Floating_Contact_SVG::sanitize($booking_svg);
            } else {
                echo '<span>📅</span>';
            }
            echo '</div>';
            echo '<div class="knk-floating-text">' . esc_html($booking_text) . '</div>';
            echo '</a>';
            echo '</div>';
        }

        echo '</div>';
    }

}

// Initialize the plugin
new KNK_Floating_Contact();
