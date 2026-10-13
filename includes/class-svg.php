<?php
/** Restricted inline SVG icons: no scripts, event handlers, styles or external resources. */
if (!defined('ABSPATH')) { exit; }

class KNK_Floating_Contact_SVG {
    public static function safe_paint($value) {
        return (bool) preg_match('/\A(?:none|currentColor|transparent|inherit|[a-z]+|\#[a-f0-9]{3,8}|rgba?\([0-9.,%\s]+\)|url\(\#[a-z0-9_-]+\))\z/i', $value);
    }

    public static function sanitize($input) {
        if (!is_string($input)) { return ''; }
        // Preserve simple Illustrator-style .class { fill: COLOR; } icons safely.
        // Styles never reach the page: only validated paint values become attributes.
        $paints = array();
        $input = preg_replace_callback('~<style\b[^>]*>(.*?)</style\s*>~is', function($block) use (&$paints) {
            preg_match_all('/\.([a-z_][a-z0-9_-]*)\s*\{([^{}]*)\}/i', $block[1], $rules, PREG_SET_ORDER);
            if (trim(preg_replace('/\.([a-z_][a-z0-9_-]*)\s*\{([^{}]*)\}/i', '', $block[1])) !== '') { return ''; }
            foreach ($rules as $rule) {
                foreach (explode(';', $rule[2]) as $declaration) {
                    $pair = explode(':', $declaration, 2);
                    if (count($pair) !== 2) { continue; }
                    $property = strtolower(trim($pair[0])); $value = trim($pair[1]);
                    if (in_array($property, array('fill','stroke','stop-color'), true) && self::safe_paint($value)) {
                        $paints[$rule[1]][$property] = $value;
                    }
                }
            }
            return '';
        }, $input);
        $input = preg_replace('~<(script|foreignObject)\b[^>]*>.*?</\1\s*>~is', '', $input);
        $attrs = array_fill_keys(array('id','class','data-name','xmlns','viewbox','width','height','x','y','x1','y1','x2','y2','cx','cy','r','rx','ry','d','points','transform','opacity','fill-opacity','fill-rule','stroke-width','stroke-opacity','stroke-linecap','stroke-linejoin','stroke-miterlimit','stroke-dasharray','stroke-dashoffset','clip-rule','preserveaspectratio','role','aria-hidden','aria-label','focusable','offset','stop-opacity','gradientunits','gradienttransform'), true);
        foreach (array('fill','stroke','stop-color') as $paint) {
            $attrs[$paint] = true;
        }
        $tags = array_fill_keys(array('svg','g','path','rect','circle','ellipse','line','polyline','polygon','defs','lineargradient','radialgradient','stop','title','desc'), $attrs);
        $clean = trim(wp_kses($input, $tags, array()));
        // Validate paint independently of version-specific KSES attribute callbacks.
        $clean = preg_replace_callback('/\s(fill|stroke|stop-color)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', function($attribute) {
            $value = html_entity_decode(trim($attribute[2], "\"'"), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return self::safe_paint($value) ? $attribute[0] : '';
        }, $clean);
        $clean = preg_replace_callback('~<[a-z][^>]*>~i', function($tag) use ($paints) {
            $markup = $tag[0];
            if (!preg_match('/\sclass\s*=\s*([\'"])(.*?)\1/i', $markup, $class)) { return $markup; }
            $classes = preg_split('/\s+/', $class[2]);
            foreach ($paints as $name => $properties) {
                if (!in_array($name, $classes, true)) { continue; }
                foreach ($properties as $property => $value) {
                    $markup = preg_replace('/\s' . preg_quote($property, '/') . '\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $markup);
                    $markup = preg_replace('~\s*(/?)>$~', ' ' . $property . '="' . esc_attr($value) . '" $1>', $markup);
                }
            }
            return $markup;
        }, $clean);
        // KSES lowercases names; retain SVG's case-sensitive geometry attributes.
        return str_replace(array(' viewbox=', ' preserveaspectratio=', ' gradientunits=', ' gradienttransform='), array(' viewBox=', ' preserveAspectRatio=', ' gradientUnits=', ' gradientTransform='), $clean);
    }
}
