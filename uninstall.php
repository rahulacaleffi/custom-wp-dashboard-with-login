<?php

/**
 * Uninstall Custom WP Dashboard Plugin
 *
 * This file is called when the plugin is uninstalled via the WordPress admin.
 *
 * @package Custom_WP_Dashboard
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
delete_option('custom_dashboard_options');
delete_option('custom_dashboard_version');

// For multisite installations
delete_site_option('custom_dashboard_options');
delete_site_option('custom_dashboard_version');

// Clear any cached data
wp_cache_flush();
