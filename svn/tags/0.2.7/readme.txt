=== Guild Network ===
Contributors: kduffie
Donate link:  https://guild.network
Tags:
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.0
Tested up to: 4.9.6
Stable tag: 0.2.7

Integrate your website into the [Guild](https://guild.network) network:  a paywall that you share with other sites.

== Description ==

This plugin enables Guild integration for your website.  Guild is a network of websites organized by the folks at [https://guild.network](https://guild.network).  Once Guild is integrated into your site, your site will be reviewed.  Once accepted, your pages will have a "Guild stamp" added on the side and visitors will be prompted to purchase a Guild access pass, whose proceeds are distributed to site owners based on conversions and engagement.

Simply by assigning posts to the "Guild Exclusive" category (or assigning a "guild-exclusive" tag to them), the plugin will take care of restricting access to that content except to those who have purchased an access pass.

To learn more, visit [Guild](https://guild.network).

Note that the plugin remains effectively invisible until the site is approved by Guild.

The plugin adds a script tag containing your unique site code to the page header.  This script handles all of the work involved in Guild integration: presenting a Guild stamp on pages, collecting payments, hiding ads when appropriate, exposing protected content when appropriate, etc.

== Installation ==

1. Install and activate this plugin.  
2. Visit [https://guild.network](https://guild.network) and add a site.  This will give you a unique alphanumeric site code.
3. In WordPress dashboard, open the settings page ("Guild Network" under Settings) and enter the site code and click Save.

With this done, if you inspect your site pages, there should be a few new lines in the <head> section that loads a script from guild.network and initializes it with your site code.

*Important*: At this point, Guild will not be visible to your visitors because your site has not yet been approved.  Guild staff will be alerted that your site has gone live with Guild integration and will review it.  If approved, your pages will begin to show a Guild tab on your pages (at least for the fraction of visitors that you configured for your site at guild.network)  and you will be notified by email.

== Frequently Asked Questions ==


== Screenshots ==
 1. Guild adds a small "stamp" to the side of your site's pages
 2. When opened, the Guild tab invites visitors to activate an access pass
 3. After activating the plugin, configure the Guild site code and customize using Guild Network under Settings

== Changelog ==

= 0.2.1 =
- Initial Revision

= 0.2.2 =
- Added wordpress.org assets (banner, icon, screenshots)

= 0.2.3 =
- Documentation cleanup

= 0.2.4 =
- Removed settings now configured via guild.network
- New "exclusive content" banner

= 0.2.5 =
- Fixed bug with extraneous banners
