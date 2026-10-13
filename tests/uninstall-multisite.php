<?php
define('WP_UNINSTALL_PLUGIN', 'knk-floating-contact/knk-floating-contact.php');
$blog=1;$stack=array();$sites=array();$queries=0;
for ($i=1;$i<=205;$i++) { $sites[$i]=array('knk_floating_contact_email'=>'site'.$i.'@example.org','knk_floating_contact_delete_on_uninstall'=>in_array($i,array(2,101,205),true)?'1':'0','knk_floating_contact_email_enabled'=>'0','knk_floating_contact_phone_enabled'=>'1','knk_floating_contact_booking_enabled'=>'0','unrelated'=>'keep'); }
function is_multisite() { return true; }
function get_sites($args) { global $sites,$queries;$queries++;return array_slice(array_keys($sites),$args['offset'],$args['number']); }
function get_option($name,$default=false) { global $sites,$blog;return $sites[$blog][$name]??$default; }
function delete_option($name) { global $sites,$blog;unset($sites[$blog][$name]); }
function switch_to_blog($id) { global $blog,$stack;$stack[]=$blog;$blog=$id; }
function restore_current_blog() { global $blog,$stack;$blog=array_pop($stack); }
function delete_site_transient($name) { if ($name!=='knk_floating_contact_release_v1') { exit(1); } }
function do_action(...$args) {}
require dirname(__DIR__).'/uninstall.php';
for ($i=1;$i<=205;$i++) {
    foreach (array('email_enabled','phone_enabled','booking_enabled') as $key) {
        if (isset($sites[$i]['knk_floating_contact_'.$key])===in_array($i,array(2,101,205),true)) { fwrite(STDERR,"FAIL flag $i $key\n");exit(1); }
    }
    if (isset($sites[$i]['knk_floating_contact_email'])===in_array($i,array(2,101,205),true) || $sites[$i]['unrelated']!=='keep') { fwrite(STDERR,"FAIL site $i\n");exit(1); }
}
if ($blog!==1 || $stack || $queries!==3) { fwrite(STDERR,"FAIL context/pagination\n");exit(1); }
echo "PASS: actual multisite uninstall across 205 sites, independent opt-in, pagination and restored context\n";
