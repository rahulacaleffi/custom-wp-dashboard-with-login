<?php

/**
 * Hide Menu Items Class
 *
 * Handles the hiding of specific menu items in the WordPress admin
 * for non-administrator users.
 *
 * @package Custom_WP_Dashboard
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Custom_Dashboard_Hide_Menu_Items
{
    /**
     * Initialize the class and set its hooks
     * 
     * Sets up the action hooks needed for removing menu items
     * from the admin dashboard for non-administrator users.
     * Currently, the menu removal functionality is commented out
     * to prevent unintended menu hiding.
     *
     * @since  1.0.0
     * @access public
     * 
     * @return void
     */
    public function __construct()
    {
        // add_action('admin_menu', array($this, 'custom_remove_menus'));
    }

    /**
     * Removes specified menu items for non-administrator users
     * 
     * Checks the current user's role and removes configured menu items
     * if the user is not an administrator. This includes menus like
     * Yoast SEO, Comments, Tools, General Settings, Contact Form 7,
     * and other potentially sensitive admin areas.
     *
     * @since  1.0.0
     * @access public
     * 
     * @global WP_User $current_user The current user object
     * @uses wp_get_current_user() To get the current user object
     * @uses remove_menu_page() To remove individual menu items
     * 
     * @return void
     */
    public function custom_remove_menus()
    {
        // Get the current user's role
        $user = wp_get_current_user();
        $user_roles = $user->roles;

        // Define the menu items to be removed for non-admin roles
        $menus_to_remove = array(
            'wpseo_menu',
            'edit-comments.php',
            'tools.php',
            'options-general.php',
            'wpcf7',
            'wpseo_workouts',
            'wpseo_redirects',
        );

        if (!in_array('administrator', $user_roles)) {
            foreach ($menus_to_remove as $menu_slug) {
                remove_menu_page($menu_slug);
            }
        }
    }
}
