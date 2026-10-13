<?php
/** Offline contract tests with real WordPress escaping/KSES and in-memory options. */
error_reporting(E_ALL);
$wp = rtrim($argv[1] ?? '', '/\\');
if (!is_file($wp . '/wp-includes/kses.php')) { fwrite(STDERR, "Pass an unpacked WordPress root.\n"); exit(1); }
define('ABSPATH', $wp . '/');
define('WPINC', 'wp-includes');
define('WP_PLUGIN_DIR', dirname(__DIR__, 2));
define('WPMU_PLUGIN_DIR', dirname(__DIR__, 2) . '/mu-plugins');
define('HOUR_IN_SECONDS', 3600);
define('MINUTE_IN_SECONDS', 60);
define('WP_UNINSTALL_PLUGIN', 'knk-floating-contact/knk-floating-contact.php');
$GLOBALS['blog_id']=1; $GLOBALS['sites']=array(1=>array('blog_charset'=>'UTF-8')); $GLOBALS['stack']=array();
$GLOBALS['cache']=array(); $GLOBALS['requests']=0; $GLOBALS['multi']=false;
$GLOBALS['wp_plugin_paths']=array();
function get_option($name,$default=false) { return $GLOBALS['sites'][$GLOBALS['blog_id']][$name] ?? $default; }
function wp_load_alloptions() { return $GLOBALS['sites'][$GLOBALS['blog_id']]; }
function delete_option($name) { unset($GLOBALS['sites'][$GLOBALS['blog_id']][$name]); }
function get_site_transient($name) { return $GLOBALS['cache'][$name] ?? false; }
function set_site_transient($name,$value,$ttl) { $GLOBALS['cache'][$name]=$value; $GLOBALS['ttl']=$ttl; }
function delete_site_transient($name) { unset($GLOBALS['cache'][$name]); }
function is_multisite() { return $GLOBALS['multi']; }
function get_sites($args) { return array_slice(array_keys($GLOBALS['sites']),$args['offset'],$args['number']); }
function switch_to_blog($id) { $GLOBALS['stack'][]=$GLOBALS['blog_id']; $GLOBALS['blog_id']=$id; }
function restore_current_blog() { $GLOBALS['blog_id']=array_pop($GLOBALS['stack']); }
function plugins_url($path='',$file='') { return 'https://example.org/plugins/knk-floating-contact'; }
function wp_normalize_path($path) { return str_replace('\\', '/', $path); }
function is_admin() { return false; }
function current_user_can($cap) { return $GLOBALS['can_manage'] ?? false; }
function register_setting($group,$name,$args) { $GLOBALS['registered'][$name]=$args; }
function add_settings_section($id,$title,$callback,$page) { $GLOBALS['sections'][$page]=$id; }
function add_settings_field($id,$title,$callback,$page,$section,$args) { $GLOBALS['fields'][$id]=array($page,$section,$args); }
function settings_fields($group) { echo '<input name="option_page" value="'.esc_attr($group).'" />'; }
function do_settings_sections($page) { foreach ($GLOBALS['fields'] as $field) { if ($field[0]===$page) { (new KNK_Floating_Contact_Admin_Settings())->render_field($field[2]); } } }
function wp_enqueue_style(...$args) { $GLOBALS['enqueued'][]=$args; }
function wp_enqueue_script(...$args) { $GLOBALS['enqueued'][]=$args; }
function submit_button() { echo '<button>Save</button>'; }
function wp_remote_get($url,$args) { $GLOBALS['requests']++; return $GLOBALS['http']; }
function wp_remote_retrieve_response_code($r) { return $r['code']; }
function wp_remote_retrieve_body($r) { return $r['body']; }
function is_wp_error($r) { return $r instanceof WP_Error; }
function wp_allowed_protocols() { return array('http','https','mailto','tel'); }
function get_bloginfo($show) { return 'UTF-8'; }
function is_utf8_charset($charset=null) { return true; }
function _canonical_charset($charset) { return 'UTF-8'; }
require $wp.'/wp-includes/class-wp-error.php';
require $wp.'/wp-includes/plugin.php';
require $wp.'/wp-includes/compat.php';
if (is_file($wp.'/wp-includes/compat-utf8.php')) { require $wp.'/wp-includes/compat-utf8.php'; }
if (is_file($wp.'/wp-includes/utf8.php')) { require $wp.'/wp-includes/utf8.php'; }
if (is_file($wp.'/wp-includes/class-wp-token-map.php')) { require $wp.'/wp-includes/class-wp-token-map.php'; }
foreach (array('html5-named-character-references','class-wp-html-attribute-token','class-wp-html-span','class-wp-html-doctype-info','class-wp-html-text-replacement','class-wp-html-decoder','class-wp-html-tag-processor') as $part) {
    if (is_file($wp.'/wp-includes/html-api/'.$part.'.php')) { require $wp.'/wp-includes/html-api/'.$part.'.php'; }
}
require $wp.'/wp-includes/formatting.php';
require $wp.'/wp-includes/kses.php';
require dirname(__DIR__).'/knk-floating-contact.php';
require dirname(__DIR__).'/includes/class-admin-settings.php';
$count=0;
function check($ok,$name) { global $count; if (!$ok) { fwrite(STDERR,"FAIL: $name\n"); exit(1); } $count++; }
$svg='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M0 0h24v24H0z" /></svg>';
check(KNK_Floating_Contact_SVG::sanitize($svg)===$svg,'safe SVG preserved');
$bad='<svg onload="alert(1)"><script>alert(1)</script><foreignObject><iframe src="https://example.org"></iframe></foreignObject><path onclick="evil()" style="fill:red" fill="url(https://example.org/a)" d="M0 0"/><use href="https://example.org/a"/></svg>';
$clean=KNK_Floating_Contact_SVG::sanitize($bad);
foreach (array('onload','<script','foreignObject','iframe','onclick','style=','https://','<use') as $needle) { check(stripos($clean,$needle)===false,'SVG blocks '.$needle); }
check(strpos(KNK_Floating_Contact_SVG::sanitize('<svg><path fill="url(#local)"/></svg>'),'url(#local)')!==false,'local paint references preserved');
check(KNK_Floating_Contact_SVG::sanitize(array())==='','invalid SVG type rejected');
$styled='<svg><defs><style>.cls-1 { fill: #fff; }</style></defs><path class="cls-1" d="M0 0"/></svg>';
$styled_clean=KNK_Floating_Contact_SVG::sanitize($styled);
check(strpos($styled_clean,'fill="#fff"')!==false && strpos($styled_clean,'<style')===false,'existing icon paint safely converted');
check(KNK_Floating_Contact_SVG::sanitize($styled_clean)===$styled_clean,'SVG sanitization idempotent');
check(strpos(KNK_Floating_Contact_SVG::sanitize('<svg><style>.x {fill:url(https://example.org/a);}</style><path class="x"/></svg>'),'https://')===false,'embedded CSS external paint blocked');
$admin=new KNK_Floating_Contact_Admin_Settings();
ob_start();$admin->render_settings_page();check(ob_get_clean()==='','settings page capability gate');
$base='knk_floating_contact_';
$admin->init_settings();
check(count($GLOBALS['registered'])===17,'all old and new options registered');
check(count($GLOBALS['sections'])===5,'contact groups plus design and uninstall');
foreach (array('email','phone','booking') as $channel) {
    $id=$base.$channel.'_enabled';
    $field=$GLOBALS['fields'][$id];
    check($field[0]==='knk-floating-contact-'.$channel,'toggle in its contact group '.$channel);
    ob_start();$admin->render_field($field[2]);$output=ob_get_clean();
    check(strpos($output,'checked="checked"')!==false,'missing toggle defaults enabled '.$channel);
    check(strpos($output,'type="hidden"')!==false && strpos($output,'value="0"')!==false,'unchecked toggle submitted '.$channel);
}
foreach (array(false,0,'0','','false',array('1'),null) as $value) { check($admin->sanitize_checkbox($value)==='0','checkbox rejects non-true input'); }
foreach (array(true,1,'1') as $value) { check($admin->sanitize_checkbox($value)==='1','checkbox accepts explicit true'); }
$GLOBALS['can_manage']=true;
ob_start();$admin->render_settings_page();$form=ob_get_clean();$GLOBALS['can_manage']=false;
check(substr_count($form,'<section class="postbox"')===5,'settings groups visible');
check(strpos($form,'id="knk_floating_contact_booking_url"')!==false && strpos($form,'rows="3"')!==false && strpos($form,'wrap="soft"')!==false,'long booking URL wraps in textarea');
check(strpos($form,'regular-text')===false,'text fields use available width');
check(strpos($form,'disabled=')===false,'hidden contacts remain editable');

check(strpos($form,'https://kanka.dev/')!==false && strpos($form,'Version '.KNK_FLOATING_CONTACT_VERSION)!==false,'developer footer uses actual version');
$GLOBALS['enqueued']=array();$admin->enqueue_assets('settings_page_other');
check(!$GLOBALS['enqueued'],'color picker absent on unrelated admin pages');
$admin->enqueue_assets('settings_page_knk-floating-contact');
check(count($GLOBALS['enqueued'])===2 && $GLOBALS['enqueued'][0][0]==='wp-color-picker','native picker only on own page');
check(KNK_Floating_Contact_Design::style()==='','no design options means no inline overrides');
foreach (array('red','url(https://example.org)','</style><script>bad</script>','#12345678','#1234',array(),'#12','var(--evil)',null) as $value) {
    check(KNK_Floating_Contact_Design::sanitize_color($value)==='','reject non-hex or unsafe color');
}
check(KNK_Floating_Contact_Design::sanitize_color(' #ABc123 ')==='#abc123','normalize safe color');
check(KNK_Floating_Contact_Design::sanitize_color('#ABC')==='#abc','short hex color accepted');
foreach (array('-1','12px','4.5',array(),true,'999','1;position:fixed') as $value) { check(KNK_Floating_Contact_Design::sanitize_radius($value)==='','invalid radius rejected'); }
check(KNK_Floating_Contact_Design::sanitize_radius('31')==='30','radius clamped');
check(KNK_Floating_Contact_Design::sanitize_radius('0')==='0','zero radius retained');
foreach (array('color_background'=>'#123456','color_hover_background'=>'#abcdef','color_text'=>'#fedcba','color_icon'=>'#12ab34','radius'=>'12') as $key=>$value) { $GLOBALS['sites'][1][$base.$key]=$value; }
check(KNK_Floating_Contact_Design::style()==='--knk-contact-background:#123456;--knk-contact-hover-background:#abcdef;--knk-contact-text:#fedcba;--knk-contact-icon:#12ab34;--knk-contact-radius:12px','validated CSS variables');
$paint='<svg viewBox="0 0 24 24"><g fill="none" stroke="#fff"><path fill="transparent" d="M0 0"/><path fill="url(#gradient)" opacity="0.5" d="M1 1"/><path fill="#ffffff80" stroke="none" d="M2 2"/><path fill="#fff" d="M3 3"/></g></svg>';
$painted=KNK_Floating_Contact_Design::svg($paint);
foreach (array('fill="none"','fill="transparent"','fill="url(#gradient)"','fill="#ffffff80"','stroke="none"','opacity="0.5"','d="M3 3"') as $needle) { check(strpos($painted,$needle)!==false,'custom icon preserves '.$needle); }
check(strpos($painted,'stroke="#12ab34"')!==false && strpos($painted,'fill="#12ab34"')!==false,'solid stroke and fill recolored');
check(strpos(KNK_Floating_Contact_Design::svg("<svg><path fill='#fff' d='M0 0'/></svg>"),'fill="#12ab34"')!==false,'single quoted SVG paint recolored');
$GLOBALS['sites'][1][$base.'color_icon']='';
check(KNK_Floating_Contact_Design::svg($paint)===KNK_Floating_Contact_SVG::sanitize($paint),'cleared icon color restores original SVG');
foreach (array('color_background','color_hover_background','color_text','color_icon','radius') as $key) { unset($GLOBALS['sites'][1][$base.$key]); }
$opts=array('email'=>'hello@example.org','email_svg'=>$svg,'phone'=>'0123 456','phone_international'=>'+49123456','phone_svg'=>$svg,'booking_url'=>'https://example.org/book','booking_svg'=>$svg,'booking_text'=>'Book <now>','delete_on_uninstall'=>'0');
foreach ($opts as $key=>$value) { $GLOBALS['sites'][1][$base.$key]=$value; }
$before=$GLOBALS['sites'];
$plugin=new KNK_Floating_Contact();$plugin->init();
ob_start();$plugin->render_floating_icons();$html=ob_get_clean();
check(substr_count($html,'class="knk-floating-link"')===3,'three links');
check(strpos($html,'mailto:hello@example.org')!==false,'email target');
check(strpos($html,'tel:+49123456')!==false,'telephone target');
check(strpos($html,'href="https://example.org/book"')!==false,'booking target');
check(strpos($html,'Book &lt;now&gt;')!==false,'booking text escaped');
check(substr_count($html,'aria-label=')===3,'mobile accessible names');
check($GLOBALS['sites']===$before,'load/init/render preserve all options');
check(!class_exists('SitePress') && !has_filter('wpml_translate_single_string'),'WPML is absent in default rendering test');
check(strpos($html,'Book &lt;now&gt;')!==false,'without WPML stored booking text is used');
for ($mask=0;$mask<8;$mask++) {
    foreach (array('email','phone','booking') as $bit=>$channel) { $GLOBALS['sites'][1][$base.$channel.'_enabled']=(string)(($mask>>$bit)&1); }
    $configured=$GLOBALS['sites'];
    ob_start();$plugin->render_floating_icons();$visible=ob_get_clean();
    foreach (array('email','phone','booking') as $bit=>$channel) {
        check((strpos($visible,'knk-floating-'.$channel.'"')!==false)===(bool)(($mask>>$bit)&1),'visibility '.$mask.' '.$channel);
    }
    check($GLOBALS['sites']===$configured,'visibility preserves settings '.$mask);
    if ($mask===0) { check($visible==='','all hidden renders no wrapper'); }
}
$GLOBALS['sites']=$before;
add_filter('wpml_translate_single_string',function($text,$context,$name) { return $context==='knk-floating-contact' && $name==='booking_text' ? 'Translated booking' : $text; },10,3);
ob_start();$plugin->render_floating_icons();check(strpos(ob_get_clean(),'Translated booking')!==false,'existing WPML contract');
remove_all_filters('wpml_translate_single_string');
foreach (array('email','phone_international','booking_url') as $key) { $GLOBALS['sites'][1][$base.$key]=''; }
ob_start();$plugin->render_floating_icons();check(ob_get_clean()==='','empty configuration renders nothing');
$GLOBALS['sites']=$before;
$u=new KNK_Floating_Contact_GitHub_Updater(dirname(__DIR__).'/knk-floating-contact.php');
$release=array('tag_name'=>'v1.2.0','draft'=>false,'prerelease'=>false,'assets'=>array(array('name'=>'knk-floating-contact-1.2.0.zip','state'=>'uploaded','size'=>1000,'browser_download_url'=>'https://github.com/kankadev/knk-floating-contact/releases/download/v1.2.0/knk-floating-contact-1.2.0.zip')));
check($u->validate($release)['version']==='1.2.0','stable release accepted');
foreach (array('draft','prerelease') as $key) { $r=$release;$r[$key]=true;check(!$u->validate($r),'reject '.$key); }
foreach (array('main','v1.2.0-beta.1','1.2.0') as $tag) { $r=$release;$r['tag_name']=$tag;check(!$u->validate($r),'reject tag '.$tag); }
$r=$release;$r['assets']=array();check(!$u->validate($r),'no source-archive fallback');
$r=$release;$r['assets'][0]['browser_download_url']='https://example.org/update.zip';check(!$u->validate($r),'reject foreign package host');
$r=$release;$r['assets'][0]['name']='other.zip';check(!$u->validate($r),'reject wrong package');
check(!$u->validate(null),'invalid JSON rejected');
$GLOBALS['http']=array('code'=>200,'body'=>json_encode($release));
$update=$u->update(false,array(),plugin_basename(dirname(__DIR__).'/knk-floating-contact.php'),array());
check($update['version']==='1.2.0','update hook metadata');
check($u->update('sentinel',array(),'other/other.php',array())==='sentinel','unrelated plugin untouched');
$u->release();check($GLOBALS['requests']===1,'success cache');check($GLOBALS['ttl']===21600,'six-hour cache');
check($GLOBALS['sites']===$before,'update check preserves options');
check($u->information(false,'plugin_information',(object)array('slug'=>'knk-floating-contact'))->version==='1.2.0','plugin details');
check($u->information('sentinel','plugin_information',(object)array('slug'=>'other'))==='sentinel','unrelated details untouched');
delete_site_transient($u::CACHE);$GLOBALS['http']=array('code'=>403,'body'=>'{}');check(!$u->release(),'rate limit safe');check($GLOBALS['ttl']===900,'failure retry cache');
delete_site_transient($u::CACHE);$GLOBALS['http']=new WP_Error('network','offline');check(!$u->release(),'network failure safe');
require dirname(__DIR__).'/uninstall.php';
check($GLOBALS['sites']===$before,'uninstall default preserves options');
foreach (array('email','phone','booking') as $channel) { $GLOBALS['sites'][1][$base.$channel.'_enabled']='0'; $opts[$channel.'_enabled']='0'; }
foreach (array('color_background','color_hover_background','color_text','color_icon','radius') as $key) { $GLOBALS['sites'][1][$base.$key]='test'; $opts[$key]='test'; }
$GLOBALS['sites'][1][$base.'delete_on_uninstall']='1';$GLOBALS['sites'][1]['unrelated']='keep';
knk_floating_contact_uninstall_site();check(get_option('unrelated')==='keep','uninstall leaves unrelated option');
foreach (array_keys($opts) as $key) { check(get_option($base.$key,null)===null,'opted-in deletion '.$key); }
$GLOBALS['sites']=array(1=>array($base.'delete_on_uninstall'=>'0',$base.'email'=>'one@example.org'),2=>array($base.'delete_on_uninstall'=>'1',$base.'email'=>'two@example.org','unrelated'=>'keep'));
foreach (get_sites(array('offset'=>0,'number'=>100)) as $id) { switch_to_blog($id);knk_floating_contact_uninstall_site();restore_current_blog(); }
check(isset($GLOBALS['sites'][1][$base.'email']),'multisite keeps opted-out site');
check(!isset($GLOBALS['sites'][2][$base.'email']),'multisite deletes opted-in site');
check($GLOBALS['blog_id']===1 && !$GLOBALS['stack'],'site context restored');
echo "PASS: $count checks\n";
