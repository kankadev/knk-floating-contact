<?php
/** Validated per-site design values; no database migration or CSS code input. */
if (!defined('ABSPATH')) { exit; }

class KNK_Floating_Contact_Design {
    public static function sanitize_color($value) {
        if (!is_string($value)) { return ''; }
        $value = trim($value);
        return preg_match('/\A#(?:[0-9a-f]{3}|[0-9a-f]{6})\z/i', $value) ? strtolower($value) : '';
    }

    public static function sanitize_radius($value) {
        if ($value === '') { return ''; }
        if ((!is_string($value) && !is_int($value)) || !preg_match('/\A[0-9]{1,2}\z/', (string) $value)) { return ''; }
        return (string) min(30, (int) $value);
    }

    public static function style() {
        $css = array();
        foreach (array('background', 'hover_background', 'text', 'icon') as $key) {
            $color = self::sanitize_color(get_option('knk_floating_contact_color_' . $key, ''));
            if ($color !== '') { $css[] = '--knk-contact-' . str_replace('_', '-', $key) . ':' . $color; }
        }
        $radius = self::sanitize_radius(get_option('knk_floating_contact_radius', ''));
        if ($radius !== '') { $css[] = '--knk-contact-radius:' . $radius . 'px'; }
        return implode(';', $css);
    }

    public static function svg($input) {
        $svg = KNK_Floating_Contact_SVG::sanitize($input);
        $color = self::sanitize_color(get_option('knk_floating_contact_color_icon', ''));
        if ($color === '') { return $svg; }
        // Recolor only explicit opaque solid paints, after the established sanitizer.
        // No geometry, saved SVG, transparent paint, opacity or gradient is changed.
        return preg_replace_callback("/\\s(fill|stroke)=([\"'])(.*?)\\2/i", function ($match) use ($color) {
            $paint = trim($match[3]);
            if (in_array(strtolower($paint), array('none', 'transparent', 'inherit'), true)) { return $match[0]; }
            if (preg_match('/\A(?:#[0-9a-f]{3}|#[0-9a-f]{6}|[a-z]+)\z/i', $paint)) {
                return ' ' . $match[1] . '="' . $color . '"';
            }
            return $match[0];
        }, $svg);
    }
}
