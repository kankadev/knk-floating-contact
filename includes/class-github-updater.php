<?php
/** Public, stable GitHub release updates through the WordPress upgrader. */
if (!defined('ABSPATH')) { exit; }

class KNK_Floating_Contact_GitHub_Updater {
    const REPOSITORY = 'kankadev/knk-floating-contact';
    const URI = 'https://github.com/kankadev/knk-floating-contact';
    const CACHE = 'knk_floating_contact_release_v1';
    private $plugin_file;

    public function __construct($plugin_file) {
        $this->plugin_file = plugin_basename($plugin_file);
        add_filter('update_plugins_github.com', array($this, 'update'), 10, 4);
        add_filter('plugins_api', array($this, 'information'), 10, 3);
        add_action('upgrader_process_complete', array($this, 'clear_cache'), 10, 2);
    }

    public function release() {
        $cached = get_site_transient(self::CACHE);
        if (false !== $cached) { return $cached; }
        $response = wp_remote_get('https://api.github.com/repos/' . self::REPOSITORY . '/releases/latest', array(
            'timeout' => 10,
            'headers' => array('Accept' => 'application/vnd.github+json', 'X-GitHub-Api-Version' => '2022-11-28'),
            'limit_response_size' => 262144,
        ));
        $release = array();
        if (!is_wp_error($response) && 200 === wp_remote_retrieve_response_code($response)) {
            $release = $this->validate(json_decode(wp_remote_retrieve_body($response), true));
        }
        set_site_transient(self::CACHE, $release, $release ? 6 * HOUR_IN_SECONDS : 15 * MINUTE_IN_SECONDS);
        return $release;
    }

    public function validate($data) {
        if (!is_array($data) || !empty($data['draft']) || !empty($data['prerelease']) || empty($data['tag_name']) || !is_string($data['tag_name']) || !preg_match('/\Av(\d+\.\d+\.\d+)\z/', $data['tag_name'], $match)) { return array(); }
        $version = $match[1];
        $filename = 'knk-floating-contact-' . $version . '.zip';
        $url = self::URI . '/releases/download/' . $data['tag_name'] . '/' . $filename;
        if (empty($data['assets']) || !is_array($data['assets'])) { return array(); }
        foreach ($data['assets'] as $asset) {
            if (!is_array($asset) || ($asset['name'] ?? '') !== $filename || ($asset['state'] ?? '') !== 'uploaded' || ($asset['browser_download_url'] ?? '') !== $url || empty($asset['size'])) { continue; }
            return array('version' => $version, 'package' => $url, 'url' => self::URI . '/releases/tag/' . $data['tag_name']);
        }
        return array();
    }

    public function update($update, $plugin_data, $plugin_file, $locales) {
        if ($plugin_file !== $this->plugin_file) { return $update; }
        $release = $this->release();
        if (!$release) { return false; }
        return array_merge($release, array('id' => self::URI, 'slug' => 'knk-floating-contact', 'requires' => '5.8', 'requires_php' => '7.4'));
    }

    public function information($result, $action, $args) {
        if ('plugin_information' !== $action || !isset($args->slug) || 'knk-floating-contact' !== $args->slug) { return $result; }
        $release = $this->release();
        if (!$release) { return $result; }
        return (object) array(
            'name' => 'KNK Floating Contact', 'slug' => 'knk-floating-contact',
            'version' => $release['version'], 'author' => '<a href="https://kanka.dev">kanka.dev</a>',
            'homepage' => self::URI, 'requires' => '5.8', 'requires_php' => '7.4',
            'download_link' => $release['package'],
            'sections' => array('description' => 'Floating contact links for email, telephone and booking. Settings are stored separately in each WordPress database.', 'changelog' => '<a href="' . esc_url($release['url']) . '">GitHub release notes</a>'),
        );
    }

    public function clear_cache($upgrader, $options) {
        if ('plugin' === ($options['type'] ?? '') && 'update' === ($options['action'] ?? '') && in_array($this->plugin_file, $options['plugins'] ?? array(), true)) {
            delete_site_transient(self::CACHE);
        }
    }
}
