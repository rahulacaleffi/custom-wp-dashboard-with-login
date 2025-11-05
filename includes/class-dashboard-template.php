<?php

/**
 * Dashboard Template Class
 *
 * @package Simple_Dashboard_Login_Customizer
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Simple_Dashboard_Template
{
    /**
     * Sets up the template functionality
     * 
     * @since 1.0.0
     */
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_custom_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'custom_dynamic_javascript_dashboard'));
        add_action('wp_before_admin_bar_render', array($this, 'dashboard_logo'));
    }

    /**
     * Loads styles and scripts for the admin dashboard
     * 
     * @since 1.0.0
     * @param string $hook The current admin page
     */
    public function enqueue_custom_admin_styles()
    {
        $options = get_option('simple_dashboard_option');
        $is_debug_enabled = isset($options['enable_debug_mode']) ? $options['enable_debug_mode'] : false;

        // Get color settings with defaults
        $primary_color = isset($options['primary_color']) ? $options['primary_color'] : '#c60b30';
        $secondary_color = isset($options['secondary_color']) ? $options['secondary_color'] : '#00a478';

        wp_enqueue_style(
            'custom-admin-styles',
            plugin_dir_url(__FILE__) . 'template/template.css',
            array(),
            $is_debug_enabled ? time() : CUSTOM_DASHBOARD_VERSION
        );

        // Add dynamic color styles
        $dynamic_css = "
            #wpadminbar *,
            #wp-toolbar ul li,
            #wp-toolbar ul li a.ab-item,
            #wpadminbar .ab-empty-item,
            #wpadminbar a.ab-item,
            #wpadminbar > #wp-toolbar span.ab-label,
            #wpadminbar > #wp-toolbar span.noticon,
            #collapse-button:hover {
                color: {$primary_color};
            }

            #adminmenuwrap .wp-menu-open > a > div.wp-menu-name {
                color: {$primary_color};
            }

            #adminmenuwrap .wp-menu-open > a > div.wp-menu-name:hover,
            #wpadminbar .ab-top-menu > li.hover > .ab-item {
            color: {$primary_color} !important;
            }
            
            #adminmenuwrap .wp-menu-open > a > div.wp-menu-image:before,
            #adminmenu li.menu-top.wp-menu-open:hover,
            #adminmenu li.menu-top.wp-menu-open:hover > a > div.wp-menu-name,
            #wpadminbar:not(.mobile) .ab-top-menu > li:hover > .ab-item,
            #adminmenu .current div.wp-menu-image:before {
                color: {$primary_color} !important;
            }
            
            #adminmenu li.current a.menu-top,
            #adminmenu li.current a.menu-top:hover,
            #adminmenu .current div.wp-menu-image:before{
                color: white !important;
            }

            #adminmenu .wp-submenu,
            #adminmenu li.menu-top:hover,
            #adminmenu li.menu-top.opensub:hover,
            #adminmenu li.opensub > a.menu-top,
            #adminmenu li.current a.menu-top,
            #adminmenu li.current a.menu-top:visited,
            #adminmenu li > a.menu-top:focus {
                background-color: {$primary_color};
            }

            #adminmenu > li.wp-first-item:hover {
                color: {$secondary_color};
            }

            ul#adminmenu > li.menu-top.wp-first-item:hover a {
                color: {$secondary_color};
            }

            #wpadminbar:not(.mobile) > #wp-toolbar li:hover span.ab-label,
            #wpadminbar > #wp-toolbar li.hover span.ab-label,
            #wpadminbar #wp-admin-bar-new-content .ab-icon:before,
            #adminmenu li.current:hover div.wp-menu-image:before {
            color: {$primary_color};
            }

            #wpadminbar .menupop .ab-sub-wrapper,
            #wpadminbar .shortlink-input {
                background: {$primary_color}ed;
            }           

            #wp-toolbar .custom-support-link a:hover {
                background: {$secondary_color};
            }
                
            body.is-fullscreen-mode .components-button.is-primary:not(:disabled),
            body.is-fullscreen-mode .acf-button.button.button-primary,
            .acf-actions a.acf-button.button.button-primary,
            #publish.button.button-primary {
                background-color: {$secondary_color};
                border-color: {$secondary_color};
            }
        ";

        wp_add_inline_style('custom-admin-styles', $dynamic_css);

        wp_enqueue_script(
            'custom-admin-script',
            plugin_dir_url(__FILE__) . 'assets/js/custom-admin.js',
            array('jquery'),
            $is_debug_enabled ? time() : CUSTOM_DASHBOARD_VERSION,
            true
        );

        // Pass settings to JavaScript
        $logo_url = !empty($options['logo_url']) ? $options['logo_url'] : plugin_dir_url(__FILE__) . 'assets/images/wordpress-color.svg';
        $website_url = !empty($options['website_url']) ? $options['website_url'] : get_site_url();
        $enable_support = isset($options['enable_support_link']) ? $options['enable_support_link'] : false;
        $support_url = !empty($options['support_link_url']) ? $options['support_link_url'] : '';
        $support_text = !empty($options['support_link_text']) ? $options['support_link_text'] : __('Support', 'simple-dashboard-login-customizer');
        $copyright_text = !empty($options['copyright_text']) ? $options['copyright_text'] : '';

        wp_localize_script('custom-admin-script', 'customDashboardSettings', array(
            'logoUrl' => esc_url($logo_url),
            'websiteUrl' => esc_url($website_url),
            'enableSupport' => (bool) $enable_support,
            'supportUrl' => esc_url($support_url),
            'supportText' => esc_html($support_text),
            'copyrightText' => esc_html($copyright_text),
            'currentYear' => date('Y'),
            'followIcon' => esc_url(plugin_dir_url(__FILE__) . 'assets/images/follow.svg')
        ));
    }

    /**
     * Adds JavaScript for custom dashboard features
     * 
     * @since 1.0.0
     */
    public function custom_dynamic_javascript_dashboard()
    {
        $options = get_option('simple_dashboard_option');
        $logo_url = !empty($options['logo_url']) ? $options['logo_url'] : esc_url(plugin_dir_url(__FILE__) . 'assets/images/wordpress-color.svg');
        $website_url = !empty($options['website_url']) ? $options['website_url'] : get_site_url();
        $follow_icon = plugin_dir_url(__FILE__) . 'assets/images/follow.svg';

        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var firstItemComponent = \'<div class="custom-first-item-dashboard"><a href="' . esc_url($website_url) . '"><img class="custom-logo-dashboard" src="' . esc_url($logo_url) . '" alt="' . esc_attr__('Logo', 'simple-dashboard-login-customizer') . '"> <span>' . esc_html__('View website', 'simple-dashboard-login-customizer') . '</span> <img src="' . esc_url($follow_icon) . '" style="width:16px;height:16px" alt="' . esc_attr__('Follow', 'simple-dashboard-login-customizer') . '"></a></div>\';
            
            var firstMenuItem = document.querySelector("ul#adminmenu > li.wp-first-item");
            if (firstMenuItem) {
                firstMenuItem.innerHTML = firstItemComponent;
                firstMenuItem.style.opacity = "1";
            }
        });
        </script>';
    }

    /**
     * Updates the admin bar logo with custom branding
     * 
     * @since 1.0.0
     */
    public function dashboard_logo()
    {
        $options = get_option('simple_dashboard_option');
        $logo_url = !empty($options['logo_url']) ? $options['logo_url'] : plugin_dir_url(__FILE__) . 'assets/images/wordpress-color.svg';

        echo '<style type="text/css">
            #wpadminbar #wp-admin-bar-wp-logo>.ab-item {
                padding: 0 7px;
                background-image: url("' . esc_url($logo_url) . '");
                background-size: 70%;
                background-position: center;
                background-repeat: no-repeat;
                opacity: 0.8;
        }
        #wpadminbar #wp-admin-bar-wp-logo>.ab-item .ab-icon:before {
            content: " ";
            top: 2px;
        }

        body.is-fullscreen-mode .edit-post-header a.components-button.edit-post-fullscreen-mode-close:before,
        body.is-fullscreen-mode .edit-post-header a.components-button.has-icon:before,
        body.is-fullscreen-mode .edit-site-navigation-toggle button.components-button.has-icon:before {
            background-image: url("' . esc_url($logo_url) . '");
        }

        #wp-admin-bar-wp-logo > a {
            background-image: url("' . esc_url($logo_url) . '");
        }
        </style>';
    }
}
