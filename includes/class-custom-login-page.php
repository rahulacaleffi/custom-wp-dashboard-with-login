<?php

/**
 * Custom Login Page Class
 *
 * Handles customization of the WordPress login page including styles,
 * branding, and functionality modifications.
 *
 * @package Simple_Dashboard_Login_Customizer
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Simple_Dashboard_Login
{
    /**
     * Initialize the login page customization
     * 
     * Sets up the necessary action hooks for customizing the login page
     * appearance and functionality.
     *
     * @since  1.0.0
     * @access public
     * 
     * @return void
     */
    public function __construct()
    {
        add_action('login_enqueue_scripts', array($this, 'custom_login_enqueue_styles'));
        add_action('login_enqueue_scripts', array($this, 'enqueue_custom_login_scripts'));
        add_action('login_enqueue_scripts', array($this, 'change_login_url'));
    }

    /**
     * Enqueues custom styles for the login page
     * 
     * Loads and applies custom CSS styles to customize the WordPress login page.
     * This includes setting custom colors from the plugin options for the
     * background, form elements, and buttons. Also handles the display of a 
     * custom background image on the login page.
     *
     * @since  1.0.0
     * @access public
     * 
     * @uses get_option() To retrieve custom dashboard options
     * @uses wp_enqueue_style() To register and enqueue the main stylesheet
     * @uses wp_add_inline_style() To add dynamic CSS styles
     * @uses plugin_dir_url() To get the URL of stylesheet assets
     * 
     * @return void
     */
    public function custom_login_enqueue_styles()
    {
        $options = get_option('simple_dashboard_option');
        $primary_color = isset($options['primary_color']) ? $options['primary_color'] : '#cea176';
        $secondary_color = isset($options['secondary_color']) ? $options['secondary_color'] : '#00a478';
        $background_url = !empty($options['login_background_url']) ? $options['login_background_url'] : plugin_dir_url(__FILE__) . '../assets/images/worldmap.svg';

        wp_enqueue_style(
            'custom-login',
            plugin_dir_url(__FILE__) . 'template/custom-login.css',
            array(),
            CUSTOM_DASHBOARD_VERSION
        );

        // Add dynamic color styles for login page
        $dynamic_css = "
            .login {
                background: {$primary_color} url({$background_url});
                background-size: 80vw 70vh;
                background-position: right top;
                background-repeat: no-repeat;
            }
            .login input:focus {
                border: 2.5px solid {$primary_color};
            }
            .login p.submit input[type='submit'] {
                background-image: linear-gradient(to right, {$primary_color} 50%, {$secondary_color} 50%);
            }
        ";

        wp_add_inline_style('custom-login', $dynamic_css);
    }

    /**
     * Enqueues custom JavaScript for the login page
     * 
     * Loads any custom JavaScript functionality needed for
     * the login page customizations. This script handles dynamic
     * behaviors like smooth transitions and interactive elements
     * on the custom login page.
     *
     * @since  1.0.0
     * @access public
     * 
     * @uses wp_enqueue_script() To register and enqueue the script
     * @uses plugin_dir_url() To get the URL of the script file
     * 
     * @return void
     */
    public function enqueue_custom_login_scripts()
    {
        wp_enqueue_script(
            'custom-login-scripts',
            plugin_dir_url(__FILE__) . 'assets/js/custom-login-scripts.js',
            array('jquery'),
            CUSTOM_DASHBOARD_VERSION,
            true
        );
    }

    /**
     * Modifies the login page logo URL and appearance
     * 
     * Changes the default WordPress logo link and updates
     * the logo image if a custom one is set in the options.
     *
     * @since  1.0.0
     * @access public
     * 
     * @return void
     */
    public function change_login_url()
    {
        $options = get_option('simple_dashboard_option');
        $website_url = !empty($options['website_url']) ? $options['website_url'] : get_site_url();
        $login_logo_url = !empty($options['login_logo_url']) ? $options['login_logo_url'] : '';

        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var loginForm = document.querySelector("#login h1 a");
            var loginUrl = "' . esc_js(esc_url($website_url)) . '";
        
            if (loginForm && loginUrl) {
                loginForm.setAttribute("href", loginUrl);
            }
        });
        </script>';

        // If custom login logo is set, add it
        if (!empty($login_logo_url)) {
            echo '<style type="text/css">
                .login h1 a {
                    background-image: url("' . esc_url($login_logo_url) . '") !important;
                    background-size: contain;
                    background-position: center;
                    width: 100%;
                    height: 70px;
                    margin-bottom: 35px;
                }
            </style>';
        }
    }
}
