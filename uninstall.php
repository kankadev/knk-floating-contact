<?php
/** Only an explicit per-site opt-in permits deletion during actual uninstall. */
if (!defined('WP_UNINSTALL_PLUGIN')) { exit; }

function knk_floating_contact_uninstall_site() {
    if (!in_array(get_option('knk_floating_contact_delete_on_uninstall', false), array(true, 1, '1'), true)) { return; }
    foreach (array('color_background','color_hover_background','color_text','color_icon','radius','email_enabled','phone_enabled','booking_enabled','email','email_svg','phone','phone_international','phone_svg','booking_url','booking_svg','booking_text','delete_on_uninstall') as $name) {
        delete_option('knk_floating_contact_' . $name);
    }
    do_action('wpml_unregister_single_string', 'knk-floating-contact', 'booking_text');
}

if (is_multisite()) {
    $offset = 0;
    do {
        $ids = get_sites(array('fields' => 'ids', 'number' => 100, 'offset' => $offset));
        foreach ($ids as $id) {
            switch_to_blog($id);
            knk_floating_contact_uninstall_site();
            restore_current_blog();
        }
        $offset += 100;
    } while (count($ids) === 100);
} else {
    knk_floating_contact_uninstall_site();
}
delete_site_transient('knk_floating_contact_release_v1');
