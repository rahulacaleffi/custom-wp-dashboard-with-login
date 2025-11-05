<?php

/**
 * Simple Dashboard & Login Customizer by RahulaPalu
 *
 * @package           Simple_Dashboard_Login_Customizer
 * @author            Rahula Palu
 * @copyright         2025 Rahula Palu
 * @license           GPL-2.0-or-later
 *
 * Plugin Name:       Simple Dashboard & Login Customizer by RahulaPalu
 * Plugin URI:        https://github.com/rahulacaleffi/simple-dashboard-login-customizer-by-rahulapalu-by-rahulapalu
 * Description:       Customize your WordPress dashboard and login page easily by adding your own branding, logo, and styling, without any hassle. Keep it simple!
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Rahula Palu
 * Author URI:        https://github.com/rahulacaleffi
 * Text Domain:       simple-dashboard-login-customizer
 * Domain Path:       /languages
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CUSTOM_DASHBOARD_VERSION', '1.0.0');
define('CUSTOM_DASHBOARD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CUSTOM_DASHBOARD_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main plugin class
 */
class Simple_Dashboard_Plugin
{
    /**
     * Sets up the plugin
     *
     * @since 1.0.0 
     */
    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'init'));

        // Add settings link to plugins page
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_plugin_action_links'));
    }

    /**
     * Initializes plugin classes and functionality
     *
     * @since 1.0.0
     */
    public function init()
    {
        // Load class files
        $this->load_dependencies();

        // Instantiate classes
        new Simple_Dashboard_Settings();
        new Simple_Dashboard_Template();
        new Simple_Dashboard_Login();
    }

    /**
     * Loads required plugin files
     *
     * @since 1.0.0
     * @access private
     */
    private function load_dependencies()
    {
        $includes_dir = CUSTOM_DASHBOARD_PLUGIN_DIR . 'includes/';

        $files = array(
            'class-settings.php',
            'class-dashboard-template.php',
            'class-custom-login-page.php',
        );

        foreach ($files as $file) {
            $file_path = $includes_dir . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }

    /**
     * Runs on plugin activation
     *
     * @since 1.0.0
     * @static
     */
    public static function activate()
    {
        // Check WordPress version
        if (version_compare(get_bloginfo('version'), '6.0', '<')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(
                esc_html__('This plugin requires WordPress 6.0 or higher.', 'simple-dashboard-login-customizer'),
                esc_html__('Plugin Activation Error', 'simple-dashboard-login-customizer'),
                array('back_link' => true)
            );
        }

        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(
                esc_html__('This plugin requires PHP 7.4 or higher.', 'simple-dashboard-login-customizer'),
                esc_html__('Plugin Activation Error', 'simple-dashboard-login-customizer'),
                array('back_link' => true)
            );
        }

        // Set default options on activation if they don't exist
        $default_options = array(
            'logo_url' => '',
            'login_logo_url' => '',
            'website_url' => get_site_url(),
            'enable_support_link' => false,
            'support_link_url' => '',
            'support_link_text' => __('Support', 'simple-dashboard-login-customizer'),
            'copyright_text' => '',
            'primary_color' => '#c60b30',
            'secondary_color' => '#00a478'
        );

        // Only add defaults if options don't exist
        if (false === get_option('simple_dashboard_option')) {
            add_option('simple_dashboard_option', $default_options);
        }

        // Set plugin version
        update_option('simple_dashboard_version', CUSTOM_DASHBOARD_VERSION);

        // Clear any cached data
        wp_cache_flush();
    }

    /**
     * Runs on plugin deactivation
     *
     * @since 1.0.0
     * @static
     */
    public static function deactivate()
    {
        // Clear any cached data
        wp_cache_flush();
    }

    /**
     * Adds settings link to the plugin action links
     *
     * @since 1.0.0
     * @param array $links Array of plugin action links
     * @return array Modified array of plugin action links
     */
    public function add_plugin_action_links($links)
    {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            esc_url(admin_url('admin.php?page=custom-dashboard-settings')),
            esc_html__('Settings', 'simple-dashboard-login-customizer')
        );

        array_unshift($links, $settings_link);

        return $links;
    }
}

/**
 * Plugin activation hook
 */
register_activation_hook(__FILE__, array('Simple_Dashboard_Plugin', 'activate'));

/**
 * Plugin deactivation hook
 */
register_deactivation_hook(__FILE__, array('Simple_Dashboard_Plugin', 'deactivate'));

// Initialize the plugin
new Simple_Dashboard_Plugin();
