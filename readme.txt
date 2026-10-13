=== KNK Floating Contact ===
Contributors: kanka.dev
Tags: contact, floating, icons, email, phone
Requires at least: 5.8
Requires PHP: 7.4
Tested up to: 7.1
Stable tag: 1.2.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Floating contact icons for email and phone with smooth hover effects.

== Description ==

KNK Floating Contact adds beautiful floating contact icons to your website that stick to the right side of the viewport. When users hover over the icons, they expand to show the contact information.

Features:
* Floating email and phone icons
* Smooth hover animations
* Customizable SVG icons
* Responsive design
* Easy configuration in WordPress admin
* International phone number support

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/knk-floating-contact` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to Settings > KNK Floating Contact to configure your contact information

== Configuration ==

1. Go to Settings > KNK Floating Contact in your WordPress admin
2. Enter your email address
3. Optionally add custom SVG code for the email icon
4. Enter your phone number for display
5. Enter your phone number in international format (e.g., +491234567890)
6. Optionally add custom SVG code for the phone icon
7. Save your settings

The floating icons will automatically appear on your website.

== Updates and data ==

Install the versioned ZIP from https://github.com/kankadev/knk-floating-contact/releases.
Version 1.1.0 adds stable GitHub release updates through WordPress. Automatic updates remain opt-in.
Updates and deactivation preserve all per-site settings. Actual uninstall deletes settings only when the site's deletion option is enabled.
SVG icons accept passive vector shapes, not scripts, event handlers, styles or external resources.

== Changelog ==

= 1.2.0 =
* Getrennte Einstellungsgruppen für E-Mail, Telefon und Buchung.
* Jede Kontaktart einzeln ausblendbar, ohne gespeicherte Angaben zu löschen.
* Breite Eingabefelder und mehrzeilige Buchungs-URL.
* Bestehende Konfigurationen bleiben standardmäßig sichtbar. WPML ist optional.
* Bewährte SVG-Behandlung und Frontend-Assets unverändert.


= 1.1.0 =
* GitHub Release updates and deterministic installation ZIP.
* Safe SVG filtering and accessible contact link names.
* WPML booking text registration.
* Per-site opt-in data deletion on multisite uninstall.
* All existing option names and frontend CSS/JavaScript preserved.


= 1.0.0 =
* Initial release
* Floating email and phone contact icons
* Hover animations and effects
* Admin configuration panel
* SVG icon support
