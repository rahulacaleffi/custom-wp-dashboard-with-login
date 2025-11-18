<?php

/**
 * Uninstall RahulaPalu Dashboard & Login Customizer Plugin
 *
 * This file is called when the plugin is uninstalled via the WordPress admin.
 *
 * @package Simple_Dashboard_Login_Customizer
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Delete plugin options
 * 
 * Remove all plugin settings from the database when uninstalled.
 * This ensures a clean removal of the plugin.
 */
delete_option('rahulapalu_dashboard_option');
delete_option('simple_dashboard_version');

// For multisite installations
delete_site_option('rahulapalu_dashboard_option');
delete_site_option('simple_dashboard_version');

// Clear any cached data
wp_cache_flush();
