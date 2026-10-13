<?php
/** Grouped settings; existing option names remain stable across updates. */
if (!defined('ABSPATH')) { exit; }

class KNK_Floating_Contact_Admin_Settings {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'init_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    public function add_admin_menu() {
        add_options_page('KNK Floating Contact', 'KNK Floating Contact', 'manage_options', 'knk-floating-contact', array($this, 'render_settings_page'));
    }

    public function enqueue_assets($hook) {
        if ($hook !== 'settings_page_knk-floating-contact') { return; }
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('knk-floating-contact-admin', KNK_FLOATING_CONTACT_PLUGIN_URL . 'assets/js/admin-settings.js', array('jquery', 'wp-color-picker'), KNK_FLOATING_CONTACT_VERSION, true);
    }

    private function groups() {
        return array('email' => 'E-Mail', 'phone' => 'Telefon', 'booking' => 'Buchung', 'design' => 'Gestaltung', 'advanced' => 'Deinstallation');
    }

    private function fields() {
        return array(
            'email_enabled' => array('email', 'E-Mail anzeigen', 'checkbox', 'Ein- oder ausblenden. Die gespeicherten Angaben bleiben erhalten.'),
            'email' => array('email', 'E-Mail-Adresse', 'email', 'Adresse für den E-Mail-Link.'),
            'email_svg' => array('email', 'E-Mail-Icon (SVG)', 'svg', 'Optional. Ohne SVG wird ein Standard-Emoji verwendet.'),
            'phone_enabled' => array('phone', 'Telefon anzeigen', 'checkbox', 'Ein- oder ausblenden. Die gespeicherten Angaben bleiben erhalten.'),
            'phone' => array('phone', 'Telefonnummer zur Anzeige', 'text', 'Zum Beispiel 0123 456789.'),
            'phone_international' => array('phone', 'Telefonnummer für den Anruf', 'text', 'Internationales Format, zum Beispiel +491234567890.'),
            'phone_svg' => array('phone', 'Telefon-Icon (SVG)', 'svg', 'Optional. Ohne SVG wird ein Standard-Emoji verwendet.'),
            'booking_enabled' => array('booking', 'Buchung anzeigen', 'checkbox', 'Ein- oder ausblenden. Die gespeicherten Angaben bleiben erhalten.'),
            'booking_url' => array('booking', 'Buchungs-URL', 'url', 'Vollständiger Link zur Buchungsseite. Lange Links werden im Feld umgebrochen.'),
            'booking_text' => array('booking', 'Buchungstext', 'text', 'Beschriftung des Buchungs-Links. WPML ist optional; ohne WPML wird dieser Text verwendet.'),
            'booking_svg' => array('booking', 'Buchungs-Icon (SVG)', 'svg', 'Optional. Ohne SVG wird ein Standard-Emoji verwendet.'),
            'color_background' => array('design', 'Hintergrundfarbe', 'color', 'Für alle Kontaktarten. Leer = Standard #6C1300.'),
            'color_hover_background' => array('design', 'Hintergrund bei Hover', 'color', 'Desktop-Hintergrund beim Darüberfahren. Leer = Standard #74675C.'),
            'color_text' => array('design', 'Textfarbe', 'color', 'Für die Kontakttexte. Leer = Weiß (#FFFFFF).'),
            'color_icon' => array('design', 'SVG-Icon-Farbe (optional)', 'color', 'Leer = Originalfarben erhalten. Eine Farbe ersetzt deckende SVG-Farbflächen und Linien. Transparenz und Verläufe bleiben erhalten; Emojis behalten ihre eigenen Farben.'),
            'radius' => array('design', 'Eckenrundung in Pixeln', 'number', '0 bis 30. Leer = bisherige eckige Darstellung. Die Gestaltung wird pro Website gespeichert und bleibt bei Updates erhalten.'),
            'delete_on_uninstall' => array('advanced', 'Daten bei Deinstallation löschen', 'checkbox', 'Nur bei tatsächlicher Deinstallation löschen. Deaktivieren und Updates erhalten Ihre Einstellungen.'),
        );
    }

    public function init_settings() {
        foreach ($this->groups() as $group => $title) {
            add_settings_section('knk_fc_' . $group, '', '__return_false', 'knk-floating-contact-' . $group);
        }
        foreach ($this->fields() as $key => $field) {
            list($group, $label, $type, $description) = $field;
            $name = 'knk_floating_contact_' . $key;
            $sanitize = 'sanitize_text_field';
            if ($type === 'color') { $sanitize = array('KNK_Floating_Contact_Design', 'sanitize_color'); }
            if ($type === 'number') { $sanitize = array('KNK_Floating_Contact_Design', 'sanitize_radius'); }
            if ($type === 'email') { $sanitize = 'sanitize_email'; }
            if ($type === 'url') { $sanitize = 'esc_url_raw'; }
            if ($type === 'svg') { $sanitize = array($this, 'sanitize_svg'); }
            if ($type === 'checkbox') { $sanitize = array($this, 'sanitize_checkbox'); }
            register_setting('knk_floating_contact_settings', $name, array('sanitize_callback' => $sanitize));
            add_settings_field($name, $label, array($this, 'render_field'), 'knk-floating-contact-' . $group, 'knk_fc_' . $group,
                array('label_for' => $name, 'key' => $key, 'type' => $type, 'description' => $description));
        }
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) { return; }
        echo '<div class="wrap"><h1>KNK Floating Contact Einstellungen</h1>';
        echo '<p>Kontaktarten einzeln konfigurieren und ein- oder ausblenden. Ein Kontakt erscheint nur, wenn auch seine Adresse oder sein Ziel hinterlegt ist.</p>';
        echo '<form method="post" action="options.php">';
        settings_fields('knk_floating_contact_settings');
        foreach ($this->groups() as $group => $title) {
            echo '<section class="postbox" aria-labelledby="knk-fc-heading-' . esc_attr($group) . '"><div class="inside">';
            echo '<h2 id="knk-fc-heading-' . esc_attr($group) . '">' . esc_html($title) . '</h2>';
            do_settings_sections('knk-floating-contact-' . $group);
            echo '</div></section>';
        }
        submit_button();
        echo '</form><p class="description">Entwickelt von <a href="https://kanka.dev/" target="_blank" rel="noopener noreferrer">kanka.dev</a> &middot; Version ' . esc_html(KNK_FLOATING_CONTACT_VERSION) . '</p></div>';
    }

    public function render_field($args) {
        $key = $args['key'];
        $name = 'knk_floating_contact_' . $key;
        $default = $key === 'booking_text' ? 'Termin buchen' : '';
        if ($args['type'] === 'checkbox') { $default = $key === 'delete_on_uninstall' ? '0' : '1'; }
        $value = get_option($name, $default);
        $attrs = ' id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" aria-describedby="' . esc_attr($name) . '-description"';
        if ($args['type'] === 'color') {
            $value = KNK_Floating_Contact_Design::sanitize_color($value);
            echo '<input type="text"' . $attrs . ' value="' . esc_attr($value) . '" class="knk-contact-settings__color" placeholder="Standard" />';
        } elseif ($args['type'] === 'number') {
            $value = KNK_Floating_Contact_Design::sanitize_radius($value);
            echo '<input type="number"' . $attrs . ' value="' . esc_attr($value) . '" min="0" max="30" step="1" class="small-text" placeholder="0" />';
        } elseif ($args['type'] === 'checkbox') {
            echo '<input type="hidden" name="' . esc_attr($name) . '" value="0" />';
            echo '<input type="checkbox"' . $attrs . ' value="1"' . (self::sanitize_checkbox($value) === '1' ? ' checked="checked"' : '') . ' />';
        } elseif (in_array($args['type'], array('svg', 'url'), true)) {
            echo '<textarea' . $attrs . ' rows="' . ($args['type'] === 'svg' ? '5' : '3') . '" class="large-text" spellcheck="false" wrap="soft">' . esc_textarea($value) . '</textarea>';
        } else {
            echo '<input type="' . esc_attr($args['type']) . '"' . $attrs . ' value="' . esc_attr($value) . '" class="large-text" />';
        }
        echo '<p class="description" id="' . esc_attr($name) . '-description">' . esc_html($args['description']) . '</p>';
    }

    public static function sanitize_checkbox($input) {
        return in_array($input, array(true, 1, '1'), true) ? '1' : '0';
    }

    public function sanitize_svg($input) {
        return KNK_Floating_Contact_SVG::sanitize($input);
    }
}
