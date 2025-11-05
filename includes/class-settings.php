<?php

/**
 * Admin Settings Page Handler
 *
 * This class handles all functionality related to the plugin's settings page in WordPress admin.
 * It manages the registration, sanitization, and rendering of all plugin settings.
 *
 * @package Custom_WP_Dashboard
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Custom_Dashboard_Settings
{
    /**
     * Initialize the settings class and register all hooks
     * 
     * @since 1.0.0
     * @access public
     * 
     * @return void
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_media_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_color_picker'));

        add_action('admin_init', array($this, 'check_and_disabled_gutenberg_editor'));
    }

    /**
     * Enqueues the WordPress color picker on the settings page
     * 
     * @since 1.0.0
     * @access public
     * 
     * @param string $hook The current admin page hook.
     * @return void
     */
    public function enqueue_color_picker($hook)
    {
        if ('toplevel_page_custom-dashboard-settings' !== $hook) {
            return;
        }

        // Add the color picker css file
        wp_enqueue_style('wp-color-picker');

        // Add our custom JavaScript for color picker
        wp_enqueue_script(
            'custom-dashboard-color-picker',
            plugin_dir_url(__FILE__) . 'assets/js/color-picker.js',
            array('wp-color-picker'),
            CUSTOM_DASHBOARD_VERSION,
            true
        );
    }

    /**
     * Enqueues media uploader scripts and styles for image upload functionality
     * 
     * Loads the WordPress media uploader scripts and custom styles for handling
     * image uploads in the plugin settings. Includes styles for image previews
     * and removal buttons. Only loads on the plugin's settings page.
     * 
     * @since 1.0.0
     * @access public
     * 
     * @param string $hook The current admin page hook. Used to conditionally load assets.
     * 
     * @uses wp_enqueue_media() To load the WordPress media uploader
     * @uses wp_enqueue_style() To register and enqueue stylesheets
     * @uses wp_enqueue_script() To register and enqueue scripts
     * @uses wp_add_inline_style() To add custom styles for media preview
     * 
     * @return void
     */
    public function enqueue_media_scripts($hook)
    {
        // Only load on our settings page
        if ('toplevel_page_custom-dashboard-settings' !== $hook) {
            return;
        }

        // Enqueue WordPress media scripts
        wp_enqueue_media();

        // Enqueue settings page styles
        wp_enqueue_style(
            'custom-dashboard-settings',
            plugin_dir_url(__FILE__) . 'assets/css/settings.css',
            array(),
            CUSTOM_DASHBOARD_VERSION
        );

        // Enqueue custom script for media uploader
        wp_enqueue_script(
            'custom-dashboard-media-upload',
            plugin_dir_url(__FILE__) . 'assets/js/media-upload.js',
            array('jquery'),
            CUSTOM_DASHBOARD_VERSION,
            true
        );

        // Add custom styles for media preview
        wp_add_inline_style('wp-admin', '
            .custom-dashboard-image-preview {
                margin-top: 10px;
            }
            .custom-dashboard-image-preview img {
                max-width: 70px;
                height: auto;
                border: 1px solid #ddd;
                padding: 5px;
                background: #fff;
            }
            .custom-dashboard-remove-image {
                display: block;
                margin-top: 5px;
                color: #a00;
                text-decoration: none;
                width: fit-content;
            }
            .custom-dashboard-remove-image:hover {
                color: #dc3232;
            }
        ');
    }

    /**
     * Adds the settings page to WordPress admin menu
     * 
     * @since 1.0.0
     * @access public
     * 
     * @return void
     */
    public function add_settings_page()
    {
        add_menu_page(
            __('Custom WP Dashboard - Settings', 'custom-wp-dashboard-with-login'),
            __('WP Dashboard', 'custom-wp-dashboard-with-login'),
            'manage_options',
            'custom-dashboard-settings',
            array($this, 'render_settings_page'),
            'dashicons-art',
            99
        );
    }

    /**
     * Registers all plugin settings, sections and fields
     * 
     * @since 1.0.0
     * @access public
     * 
     * @return void
     */
    public function register_settings()
    {
        // Register settings group
        register_setting('custom_dashboard_settings', 'custom_dashboard_options', array($this, 'sanitize_options'));

        // General Settings Section
        add_settings_section(
            'custom_dashboard_general',
            __('General Settings', 'custom-wp-dashboard-with-login'),
            array($this, 'general_section_callback'),
            'custom-dashboard-settings'
        );

        // Logo URL
        add_settings_field(
            'logo_url',
            __('Dashboard Logo URL', 'custom-wp-dashboard-with-login'),
            array($this, 'logo_url_callback'),
            'custom-dashboard-settings',
            'custom_dashboard_general'
        );

        // Login Logo URL
        add_settings_field(
            'login_logo_url',
            __('Login Logo URL', 'custom-wp-dashboard-with-login'),
            array($this, 'login_logo_url_callback'),
            'custom-dashboard-settings',
            'custom_dashboard_general'
        );

        // Login Background Image
        add_settings_field(
            'login_background_url',
            __('Login Background Image', 'custom-wp-dashboard-with-login'),
            array($this, 'login_background_url_callback'),
            'custom-dashboard-settings',
            'custom_dashboard_general'
        );

        // Website URL
        add_settings_field(
            'website_url',
            __('Website URL', 'custom-wp-dashboard-with-login'),
            array($this, 'website_url_callback'),
            'custom-dashboard-settings',
            'custom_dashboard_general'
        );

        // Colors Section
        add_settings_section(
            'custom_dashboard_colors',
            __('Color Settings', 'custom-wp-dashboard-with-login'),
            array($this, 'colors_section_callback'),
            'custom-dashboard-settings'
        );

        // Primary Color
        add_settings_field(
            'primary_color',
            __('Primary Color', 'custom-wp-dashboard-with-login'),
            array($this, 'primary_color_callback'),
            'custom-dashboard-settings',
            'custom_dashboard_colors'
        );

        // Secondary Color
        add_settings_field(
            'secondary_color',
            __('Secondary Color', 'custom-wp-dashboard-with-login'),
            array($this, 'secondary_color_callback'),
            'custom-dashboard-settings',
            'custom_dashboard_colors'
        );

        // Support Link Section
        add_settings_section(
            'custom_dashboard_support',
            __('Support Settings', 'custom-wp-dashboard-with-login'),
            array($this, 'support_section_callback'),
            'custom-dashboard-settings'
        );

        // Enable Support Link
        add_settings_field(
            'enable_support_link',
            __('Enable Support Link', 'custom-wp-dashboard-with-login'),
            array($this, 'enable_support_link_callback'),
            'custom-dashboard-settings',
            'custom_dashboard_support'
        );

        // Support Link URL
        add_settings_field(
            'support_link_url',
            __('Support Link URL', 'custom-wp-dashboard-with-login'),
            array($this, 'support_link_url_callback'),
            'custom-dashboard-settings',
            'custom_dashboard_support'
        );

        // Support Link Text
        add_settings_field(
            'support_link_text',
            __('Support Link Text', 'custom-wp-dashboard-with-login'),
            array($this, 'support_link_text_callback'),
            'custom-dashboard-settings',
            'custom_dashboard_support'
        );

        // Copyright Section
        add_settings_section(
            'custom_dashboard_copyright',
            __('Copyright Settings', 'custom-wp-dashboard-with-login'),
            array($this, 'copyright_section_callback'),
            'custom-dashboard-settings'
        );

        // Copyright Text
        add_settings_field(
            'copyright_text',
            __('Copyright Text', 'custom-wp-dashboard-with-login'),
            array($this, 'copyright_text_callback'),
            'custom-dashboard-settings',
            'custom_dashboard_copyright'
        );

        // Debug mode
        add_settings_field(
            'enable_debug_mode',
            __('Enable Debug Mode', 'custom-wp-dashboard-with-login'),
            array($this, 'enable_debug_mode_callback'),
            'custom-dashboard-settings',
            'custom_dashboard_general'
        );

        // Disable gutenberg
        add_settings_field(
            'disable_gutenberg',
            __('Disable Gutenberg Editor', 'custom-wp-dashboard-with-login'),
            array($this, 'disable_gutenberg_callback'),
            'custom-dashboard-settings',
            'custom_dashboard_general'
        );
    }

    /**
     * Sanitizes all plugin options before saving to database
     * 
     * @since 1.0.0
     * @access public
     * 
     * @param array $input The unsanitized option values.
     * @return array Sanitized option values.
     */
    public function sanitize_options($input)
    {
        $sanitized = array();

        if (isset($input['logo_url'])) {
            $sanitized['logo_url'] = esc_url_raw($input['logo_url']);
        }

        if (isset($input['login_logo_url'])) {
            $sanitized['login_logo_url'] = esc_url_raw($input['login_logo_url']);
        }

        if (isset($input['login_background_url'])) {
            $sanitized['login_background_url'] = esc_url_raw($input['login_background_url']);
        }

        if (isset($input['website_url'])) {
            $sanitized['website_url'] = esc_url_raw($input['website_url']);
        }

        if (isset($input['enable_support_link'])) {
            $sanitized['enable_support_link'] = (bool) $input['enable_support_link'];
        }

        if (isset($input['support_link_url'])) {
            $sanitized['support_link_url'] = esc_url_raw($input['support_link_url']);
        }

        if (isset($input['support_link_text'])) {
            $sanitized['support_link_text'] = sanitize_text_field($input['support_link_text']);
        }

        if (isset($input['copyright_text'])) {
            $sanitized['copyright_text'] = sanitize_text_field($input['copyright_text']);
        }

        if (isset($input['enable_debug_mode'])) {
            $sanitized['enable_debug_mode'] = (bool) $input['enable_debug_mode'];
        }

        if (isset($input['disable_gutenberg'])) {
            $sanitized['disable_gutenberg'] = (bool) $input['disable_gutenberg'];
        }

        if (isset($input['primary_color'])) {
            $sanitized['primary_color'] = sanitize_hex_color($input['primary_color']);
        }

        if (isset($input['secondary_color'])) {
            $sanitized['secondary_color'] = sanitize_hex_color($input['secondary_color']);
        }

        return $sanitized;
    }

    /**
     * Callback functions for settings sections
     * Outputs section descriptions and help text
     * 
     * @since 1.0.0
     * @access public
     */
    public function general_section_callback()
    {
        echo '<p class="section-description">' .
            esc_html__('Configure the general appearance settings of your dashboard, including logos and website URL. These settings affect both the admin dashboard and login page.', 'custom-wp-dashboard-with-login') .
            '</p>';
    }

    public function support_section_callback()
    {
        echo '<p class="section-description">' .
            esc_html__('Add a support link to your admin bar for quick access to help resources. Enable this if you want to provide easy access to support for your users.', 'custom-wp-dashboard-with-login') .
            '</p>';
    }

    public function copyright_section_callback()
    {
        echo '<p class="section-description">' .
            esc_html__('Set your copyright text that appears in the dashboard footer. Use {year} as a placeholder to automatically display the current year.', 'custom-wp-dashboard-with-login') .
            '</p>';
    }

    public function colors_section_callback()
    {
        echo '<p class="section-description">' .
            esc_html__('Customize the color scheme of your dashboard and login page. The primary color is used for main elements and backgrounds, while the secondary color is used for accents and highlights.', 'custom-wp-dashboard-with-login') .
            '</p>';
    }

    public function primary_color_callback()
    {
        $options = get_option('custom_dashboard_options');
        $value = isset($options['primary_color']) ? $options['primary_color'] : '#c60b30';
        echo '<input type="text" name="custom_dashboard_options[primary_color]" value="' . esc_attr($value) . '" class="custom-dashboard-color-picker" />';
        echo '<p class="description">' . esc_html__('Primary color used throughout the dashboard and login page. Consider text as "white" and the selected color as background (menus, texts and the login page)', 'custom-wp-dashboard-with-login') . '</p>';
    }

    public function secondary_color_callback()
    {
        $options = get_option('custom_dashboard_options');
        $value = isset($options['secondary_color']) ? $options['secondary_color'] : '#00a478';
        echo '<input type="text" name="custom_dashboard_options[secondary_color]" value="' . esc_attr($value) . '" class="custom-dashboard-color-picker" />';
        echo '<p class="description">' . esc_html__('Secondary color used throughout the dashboard.', 'custom-wp-dashboard-with-login') . '</p>';
    }

    /**
     * Callback functions for settings fields
     * Renders the HTML input fields and their descriptions
     * 
     * @since 1.0.0
     * @access public
     */
    public function logo_url_callback()
    {
        $options = get_option('custom_dashboard_options');
        $value = isset($options['logo_url']) ? $options['logo_url'] : '';
?>
        <div class="custom-dashboard-logo-upload">
            <input type="url"
                id="logo_url"
                name="custom_dashboard_options[logo_url]"
                value="<?php echo esc_attr($value); ?>"
                class="regular-text custom-dashboard-logo-url" />
            <button type="button"
                class="button custom-dashboard-upload-image"
                data-target="logo_url">
                <?php esc_html_e('Upload/Select Image', 'custom-wp-dashboard-with-login'); ?>
            </button>
            <?php if ($value): ?>
                <div class="custom-dashboard-image-preview">
                    <img src="<?php echo esc_url($value); ?>" alt="<?php esc_attr_e('Dashboard Logo Preview', 'custom-wp-dashboard-with-login'); ?>" />
                    <a href="#" class="custom-dashboard-remove-image" data-target="logo_url">
                        <?php esc_html_e('Remove image', 'custom-wp-dashboard-with-login'); ?>
                    </a>
                </div>
            <?php endif; ?>
            <p class="description"><?php esc_html_e('Upload or enter the URL to the logo image for the dashboard.', 'custom-wp-dashboard-with-login'); ?></p>
        </div>
    <?php
    }

    public function login_logo_url_callback()
    {
        $options = get_option('custom_dashboard_options');
        $value = isset($options['login_logo_url']) ? $options['login_logo_url'] : '';
    ?>
        <div class="custom-dashboard-logo-upload">
            <input type="url"
                id="login_logo_url"
                name="custom_dashboard_options[login_logo_url]"
                value="<?php echo esc_attr($value); ?>"
                class="regular-text custom-dashboard-logo-url" />
            <button type="button"
                class="button custom-dashboard-upload-image"
                data-target="login_logo_url">
                <?php esc_html_e('Upload/Select Image', 'custom-wp-dashboard-with-login'); ?>
            </button>
            <?php if ($value): ?>
                <div class="custom-dashboard-image-preview">
                    <img src="<?php echo esc_url($value); ?>" alt="<?php esc_attr_e('Login Logo Preview', 'custom-wp-dashboard-with-login'); ?>" />
                    <a href="#" class="custom-dashboard-remove-image" data-target="login_logo_url">
                        <?php esc_html_e('Remove image', 'custom-wp-dashboard-with-login'); ?>
                    </a>
                </div>
            <?php endif; ?>
            <p class="description"><?php esc_html_e('Upload or enter the URL to the logo image for the login page.', 'custom-wp-dashboard-with-login'); ?></p>
        </div>
    <?php
    }

    public function website_url_callback()
    {
        $options = get_option('custom_dashboard_options');
        $value = isset($options['website_url']) ? $options['website_url'] : get_site_url();
        echo '<input type="url" name="custom_dashboard_options[website_url]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('URL to your website. Defaults to site URL.', 'custom-wp-dashboard-with-login') . '</p>';
    }

    public function login_background_url_callback()
    {
        $options = get_option('custom_dashboard_options');
        $value = isset($options['login_background_url']) ? $options['login_background_url'] : '';
    ?>
        <div class="custom-dashboard-logo-upload">
            <input type="url"
                id="login_background_url"
                name="custom_dashboard_options[login_background_url]"
                value="<?php echo esc_attr($value); ?>"
                class="regular-text custom-dashboard-logo-url" />
            <button type="button"
                class="button custom-dashboard-upload-image"
                data-target="login_background_url">
                <?php esc_html_e('Upload/Select Image', 'custom-wp-dashboard-with-login'); ?>
            </button>
            <?php if ($value): ?>
                <div class="custom-dashboard-image-preview">
                    <img src="<?php echo esc_url($value); ?>" alt="<?php esc_attr_e('Login Background Preview', 'custom-wp-dashboard-with-login'); ?>" />
                    <a href="#" class="custom-dashboard-remove-image" data-target="login_background_url">
                        <?php esc_html_e('Remove image', 'custom-wp-dashboard-with-login'); ?>
                    </a>
                </div>
            <?php endif; ?>
            <p class="description"><?php esc_html_e('Upload or enter the URL to the background image for the login page.', 'custom-wp-dashboard-with-login'); ?></p>
        </div>
    <?php
    }

    public function enable_support_link_callback()
    {
        $options = get_option('custom_dashboard_options');
        $value = isset($options['enable_support_link']) ? $options['enable_support_link'] : false;
        echo '<label><input type="checkbox" name="custom_dashboard_options[enable_support_link]" value="1" ' . checked(1, $value, false) . ' />';
        echo ' ' . esc_html__('Show support link in admin bar', 'custom-wp-dashboard-with-login') . '</label>';
    }

    public function support_link_url_callback()
    {
        $options = get_option('custom_dashboard_options');
        $value = isset($options['support_link_url']) ? $options['support_link_url'] : '';
        echo '<input type="url" name="custom_dashboard_options[support_link_url]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('URL for the support link (e.g., your support page or contact form).', 'custom-wp-dashboard-with-login') . '</p>';
    }

    public function support_link_text_callback()
    {
        $options = get_option('custom_dashboard_options');
        $value = isset($options['support_link_text']) ? $options['support_link_text'] : __('Support', 'custom-wp-dashboard-with-login');
        echo '<input type="text" name="custom_dashboard_options[support_link_text]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Text to display for the support link.', 'custom-wp-dashboard-with-login') . '</p>';
    }

    public function copyright_text_callback()
    {
        $options = get_option('custom_dashboard_options');
        $value = isset($options['copyright_text']) ? $options['copyright_text'] : '';
        echo '<input type="text" name="custom_dashboard_options[copyright_text]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Copyright text to display in footer. Use {year} as placeholder for current year.', 'custom-wp-dashboard-with-login') . '</p>';
    }

    public function enable_debug_mode_callback()
    {
        $options = get_option('custom_dashboard_options');
        $value = isset($options['enable_debug_mode']) ? $options['enable_debug_mode'] : false;
        echo '<label><input type="checkbox" name="custom_dashboard_options[enable_debug_mode]" value="1" ' . checked(1, $value, false) . ' />';
        echo ' ' . esc_html__('Enable debug mode for troubleshooting.', 'custom-wp-dashboard-with-login') . '<br/><i>Enable/disable caching css</i></label>';
    }

    public function disable_gutenberg_callback()
    {
        $options = get_option('custom_dashboard_options');
        $value = isset($options['disable_gutenberg']) ? $options['disable_gutenberg'] : false;
        echo '<label><input type="checkbox" name="custom_dashboard_options[disable_gutenberg]" value="1" ' . checked(1, $value, false) . ' />';
        echo ' ' . esc_html__('Disable the Gutenberg block editor and use the classic editor instead.', 'custom-wp-dashboard-with-login') . '</label>';
    }

    /**
     * Renders the plugin settings page in WordPress admin
     * 
     * @since 1.0.0
     * @access public
     * 
     * @return void|null Returns early if user cannot manage options
     */
    public function render_settings_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(
                esc_html__('Sorry, you do not have sufficient permissions to access this page.', 'custom-wp-dashboard-with-login')
            );
        }

        // Security check
        if (
            isset($_POST['option_page']) &&
            $_POST['option_page'] === 'custom_dashboard_settings' &&
            !check_admin_referer('custom_dashboard_settings-options')
        ) {
            wp_die(
                esc_html__('Invalid nonce verification', 'custom-wp-dashboard-with-login')
            );
        }

        // Check for settings errors
        $setting_errors = get_settings_errors();
        if (!empty($setting_errors)) {
            foreach ($setting_errors as $error) {
                add_settings_error(
                    'custom_dashboard_messages',
                    esc_attr($error['code']),
                    esc_html($error['message']),
                    'error'
                );
            }
        }
    ?>
        <div class="wrap custom-dashboard-settings">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <?php settings_errors('custom_dashboard_messages'); ?>
            <form action="options.php" method="post">
                <?php
                settings_fields('custom_dashboard_settings');
                do_settings_sections('custom-dashboard-settings');
                submit_button(__('Save Settings', 'custom-wp-dashboard-with-login'));
                ?>
            </form>
        </div>
<?php
    }

    /**
     * Checks if debug mode is enabled in plugin settings
     * 
     * Retrieves the plugin settings and checks if debug mode
     * has been enabled by the administrator. Debug mode can be
     * used for troubleshooting issues with the plugin and disables
     * CSS caching.
     * 
     * @since 1.0.0
     * @access public
     * 
     * @uses get_option() To retrieve plugin settings from the database
     * 
     * @return boolean True if debug mode is enabled, false otherwise.
     */
    public function is_debug_mode_enabled()
    {
        $options = get_option('custom_dashboard_options');
        return isset($options['enable_debug_mode']) && $options['enable_debug_mode'];
    }

    /**
     * Checks settings and disables Gutenberg editor if configured
     * 
     * Retrieves the plugin settings and disables the Gutenberg block editor
     * if the administrator has chosen to use the classic editor instead.
     * This is done by hooking into WordPress' block editor filters.
     * 
     * @since 1.0.0
     * @access public
     * 
     * @uses get_option() To retrieve plugin settings from the database
     * @uses add_filter() To hook into WordPress' block editor filters
     * @uses __return_false A WordPress function that returns false
     * 
     * @return void
     */
    public function check_and_disabled_gutenberg_editor()
    {
        $options = get_option('custom_dashboard_options');
        if (isset($options['disable_gutenberg']) && $options['disable_gutenberg']) {
            add_filter('use_block_editor_for_post', '__return_false', 10);
            add_filter('use_block_editor_for_post_type', '__return_false', 10);
        }
    }
}
