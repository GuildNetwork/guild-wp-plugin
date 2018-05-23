<?php
/*
   Plugin Name: Guild Network
   Plugin URI: http://wordpress.org/extend/plugins/guild-network/
   Version: 0.1
   Author: Hivepoint, Inc.
   Author URI: https://guild.network
   Description: Integrate Guild into the site, enabling access pass purchases
   Text Domain: guild-network
   License: GPLv2 or later
  */

/*
    "WordPress Plugin Template" Copyright (C) 2018 Michael Simpson  (email : michael.d.simpson@gmail.com)

    This following part of this file is part of WordPress Plugin Template for WordPress.

    WordPress Plugin Template is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WordPress Plugin Template is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database Extension.
    If not, see http://www.gnu.org/licenses/gpl-3.0.html
*/

$GuildNetwork_minimalRequiredPhpVersion = '5.0';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function GuildNetwork_noticePhpVersionWrong() {
    global $GuildNetwork_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "Guild Network" requires a newer version of PHP to be running.',  'guild-network').
            '<br/>' . __('Minimal version of PHP required: ', 'guild-network') . '<strong>' . $GuildNetwork_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', 'guild-network') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}


function GuildNetwork_PhpVersionCheck() {
    global $GuildNetwork_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $GuildNetwork_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'GuildNetwork_noticePhpVersionWrong');
        return false;
    }
    return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function GuildNetwork_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('guild-network', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// Initialize i18n
add_action('plugins_loadedi','GuildNetwork_i18n_init');

// Run the version check.
// If it is successful, continue with initialization for this plugin
if (GuildNetwork_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once('guild-network_init.php');
    GuildNetwork_init(__FILE__);
}
