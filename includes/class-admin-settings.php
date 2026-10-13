<?php
/**
 * Admin Settings for KNK Floating Contact Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class KNK_Floating_Contact_Admin_Settings {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'init_settings'));
    }

    public function add_admin_menu() {
        add_options_page(
            'KNK Floating Contact',
            'KNK Floating Contact',
            'manage_options',
            'knk-floating-contact',
            array($this, 'render_settings_page')
        );
    }

    public function init_settings() {
        register_setting(
            'knk_floating_contact_settings',
            'knk_floating_contact_email',
            array('sanitize_callback' => 'sanitize_email')
        );

        register_setting(
            'knk_floating_contact_settings',
            'knk_floating_contact_email_svg',
            array('sanitize_callback' => array($this, 'sanitize_svg'))
        );

        register_setting(
            'knk_floating_contact_settings',
            'knk_floating_contact_phone',
            array('sanitize_callback' => 'sanitize_text_field')
        );

        register_setting(
            'knk_floating_contact_settings',
            'knk_floating_contact_phone_international',
            array('sanitize_callback' => 'sanitize_text_field')
        );

        register_setting(
            'knk_floating_contact_settings',
            'knk_floating_contact_phone_svg',
            array('sanitize_callback' => array($this, 'sanitize_svg'))
        );

        register_setting(
            'knk_floating_contact_settings',
            'knk_floating_contact_booking_url',
            array('sanitize_callback' => 'esc_url_raw')
        );

        register_setting(
            'knk_floating_contact_settings',
            'knk_floating_contact_booking_svg',
            array('sanitize_callback' => array($this, 'sanitize_svg'))
        );

        register_setting(
            'knk_floating_contact_settings',
            'knk_floating_contact_booking_text',
            array('sanitize_callback' => 'sanitize_text_field')
        );

        register_setting(
            'knk_floating_contact_settings',
            'knk_floating_contact_delete_on_uninstall',
            array('sanitize_callback' => 'rest_sanitize_boolean')
        );

        add_settings_section(
            'knk_floating_contact_main',
            'Kontakt Einstellungen',
            array($this, 'render_section_description'),
            'knk-floating-contact'
        );

        add_settings_section(
            'knk_floating_contact_advanced',
            'Erweiterte Einstellungen',
            array($this, 'render_advanced_section_description'),
            'knk-floating-contact'
        );

        add_settings_field(
            'knk_floating_contact_email',
            'E-Mail-Adresse',
            array($this, 'render_email_field'),
            'knk-floating-contact',
            'knk_floating_contact_main'
        );

        add_settings_field(
            'knk_floating_contact_email_svg',
            'SVG für E-Mail Icon',
            array($this, 'render_email_svg_field'),
            'knk-floating-contact',
            'knk_floating_contact_main'
        );

        add_settings_field(
            'knk_floating_contact_phone',
            'Telefonnummer',
            array($this, 'render_phone_field'),
            'knk-floating-contact',
            'knk_floating_contact_main'
        );

        add_settings_field(
            'knk_floating_contact_phone_international',
            'Telefonnummer (international)',
            array($this, 'render_phone_international_field'),
            'knk-floating-contact',
            'knk_floating_contact_main'
        );

        add_settings_field(
            'knk_floating_contact_phone_svg',
            'SVG für Telefon Icon',
            array($this, 'render_phone_svg_field'),
            'knk-floating-contact',
            'knk_floating_contact_main'
        );

        add_settings_field(
            'knk_floating_contact_booking_url',
            'Buchungs-URL',
            array($this, 'render_booking_url_field'),
            'knk-floating-contact',
            'knk_floating_contact_main'
        );

        add_settings_field(
            'knk_floating_contact_booking_svg',
            'SVG für Buchungs Icon',
            array($this, 'render_booking_svg_field'),
            'knk-floating-contact',
            'knk_floating_contact_main'
        );

        add_settings_field(
            'knk_floating_contact_booking_text',
            'Buchungs-Text',
            array($this, 'render_booking_text_field'),
            'knk-floating-contact',
            'knk_floating_contact_main'
        );

        add_settings_field(
            'knk_floating_contact_delete_on_uninstall',
            'Daten beim Deinstallieren löschen',
            array($this, 'render_delete_on_uninstall_field'),
            'knk-floating-contact',
            'knk_floating_contact_advanced'
        );
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) { return; }
        ?>
        <div class="wrap">
            <h1>KNK Floating Contact Einstellungen</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('knk_floating_contact_settings');
                do_settings_sections('knk-floating-contact');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function render_section_description() {
        echo '<p>Konfigurieren Sie die floating Kontakt-Icons für Ihre Website.</p>';
    }

    public function render_advanced_section_description() {
        echo '<p>Erweiterte Einstellungen für das Plugin.</p>';
    }

    public function render_email_field() {
        $value = get_option('knk_floating_contact_email', '');
        echo '<input type="email" name="knk_floating_contact_email" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">Die E-Mail-Adresse für das mailto: Link</p>';
    }

    public function render_email_svg_field() {
        $value = get_option('knk_floating_contact_email_svg', '');
        echo '<textarea name="knk_floating_contact_email_svg" rows="5" cols="50" class="large-text">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">SVG-Code für das E-Mail Icon (optional - falls leer wird ein Standard-Emoji verwendet)</p>';
    }

    public function render_phone_field() {
        $value = get_option('knk_floating_contact_phone', '');
        echo '<input type="text" name="knk_floating_contact_phone" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">Die Telefonnummer zur Anzeige (z.B. 0123 456789)</p>';
    }

    public function render_phone_international_field() {
        $value = get_option('knk_floating_contact_phone_international', '');
        echo '<input type="text" name="knk_floating_contact_phone_international" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">Telefonnummer im internationalen Format für tel: Link (z.B. +491234567890)</p>';
    }

    public function render_phone_svg_field() {
        $value = get_option('knk_floating_contact_phone_svg', '');
        echo '<textarea name="knk_floating_contact_phone_svg" rows="5" cols="50" class="large-text">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">SVG-Code für das Telefon Icon (optional - falls leer wird ein Standard-Emoji verwendet)</p>';
    }

    public function render_booking_url_field() {
        $value = get_option('knk_floating_contact_booking_url', '');
        echo '<input type="url" name="knk_floating_contact_booking_url" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">URL für Buchungsseite (z.B. Microsoft Bookings)</p>';
    }

    public function render_booking_svg_field() {
        $value = get_option('knk_floating_contact_booking_svg', '');
        echo '<textarea name="knk_floating_contact_booking_svg" rows="5" cols="50" class="large-text">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">SVG-Code für das Buchungs Icon (optional - falls leer wird ein Standard-Emoji verwendet)</p>';
    }

    public function render_booking_text_field() {
        $value = get_option('knk_floating_contact_booking_text', 'Termin buchen');
        echo '<input type="text" name="knk_floating_contact_booking_text" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">Text für den Buchungs-Link (kann mit WPML übersetzt werden)</p>';
    }

    public function render_delete_on_uninstall_field() {
        $value = get_option('knk_floating_contact_delete_on_uninstall', false);
        echo '<input type="checkbox" name="knk_floating_contact_delete_on_uninstall" value="1" ' . checked(1, $value, false) . ' />';
        echo '<p class="description">Wenn aktiviert, werden alle Plugin-Daten beim Deinstallieren automatisch gelöscht.</p>';
    }

    public function sanitize_svg($input) {
        return KNK_Floating_Contact_SVG::sanitize($input);
    }
}
