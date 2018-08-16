<?php
/**
 * WordPress Health Tracker
 *
 * @package     WordPress Health Tracker
 * @author      Jake Evans
 * @copyright   2018 Jake Evans
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: WPBookList BuyBack Extension
 * Plugin URI: https://www.jakerevans.com
 * Description: Extension that allows users to let their website visitors submit books to be sold to the website owner, with offerered prices dependent on current Amazon prices.
 * Author: Jake Evans
 * Version: 2.0.0
 * Author URI: https://www.jakerevans.com
 */



global $wpdb;
require_once('includes/buyback-functions.php');
require_once('includes/buyback-ajaxfunctions.php');

// Root plugin folder URL of this extension.
define('BUYBACK_ROOT_URL', plugins_url().'/wpbooklist-buyback/');

// Grabbing database prefix
define('BUYBACK_PREFIX', $wpdb->prefix);

// Root plugin folder directory for this extension
define('BUYBACK_ROOT_DIR', plugin_dir_path(__FILE__));

// Root Image Icons URL of this extension
define('BUYBACK_ROOT_IMG_ICONS_URL', BUYBACK_ROOT_URL.'assets/img/');

// Root Classes Directory for this extension
define('BUYBACK_CLASS_DIR', BUYBACK_ROOT_DIR.'includes/classes/');

// Root JS Directory for this extension
define('BUYBACK_JS_DIR', BUYBACK_ROOT_DIR.'assets/js/');

// Root JS URL for this extension
define('BUYBACK_JS_URL', BUYBACK_ROOT_URL.'assets/js/');

// Root CSS URL for this extension
define('BUYBACK_ROOT_CSS_URL', BUYBACK_ROOT_URL.'assets/css/');

// Adding the front-end ui css file for this extension
//add_action('wp_enqueue_scripts', 'wpbooklist_jre_buyback_frontend_ui_style');

// Adding the admin css file for this extension
add_action('admin_enqueue_scripts', 'wpbooklist_jre_buyback_admin_style' );

// Adding the front-end ui css file for this extension
add_action('wp_enqueue_scripts', 'wpbooklist_jre_buyback_frontend_ui_style');

/* For buyback books by ISBN number
add_action( 'admin_footer', 'wpbooklist_buyback_action_javascript' );
add_action( 'wp_ajax_wpbooklist_buyback_action', 'wpbooklist_buyback_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_buyback_action', 'wpbooklist_buyback_action_callback' );
*/

// Registers table names
add_action( 'init', 'wpbooklist_jre_buyback_register_table_names');

// Creates User table upon activation
register_activation_hook( __FILE__,'wpbooklist_jre_buyback_create_user_table');

// Creates Settings table upon activation
register_activation_hook( __FILE__,'wpbooklist_jre_buyback_create_settings_table');

// Creates Orders table upon activation
register_activation_hook( __FILE__,'wpbooklist_jre_buyback_create_orders_table');

// Adding the front-end library shortcode
add_shortcode('wpbooklist_buyback', 'wpbooklist_jre_buyback_shortcode_function');

// For creating nonces array
add_action( 'init', 'wpbooklist_jre_buyback_create_nonces' );

// For adding books
//add_action( 'wp_footer', 'wpbooklist_buyback_addbooks_action_javascript' );
//add_action( 'admin_footer', 'wpbooklist_buyback_addbooks_action_javascript' );
add_action( 'wp_ajax_wpbooklist_buyback_addbooks_action', 'wpbooklist_buyback_addbooks_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_buyback_addbooks_action', 'wpbooklist_buyback_addbooks_action_callback' );

// For searching for books
//add_action( 'wp_footer', 'wpbooklist_buyback_search_action_javascript' );
//add_action( 'admin_footer', 'wpbooklist_buyback_search_action_javascript' );
add_action( 'wp_ajax_wpbooklist_buyback_search_action', 'wpbooklist_buyback_search_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_buyback_search_action', 'wpbooklist_buyback_search_action_callback' );

// For adding books to cart
//add_action( 'wp_footer', 'wpbooklist_buyback_add_to_cart_action_javascript' );
//add_action( 'admin_footer', 'wpbooklist_buyback_add_to_cart_action_javascript' );
add_action( 'wp_ajax_wpbooklist_buyback_add_to_cart_action', 'wpbooklist_buyback_add_to_cart_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_buyback_add_to_cart_action', 'wpbooklist_buyback_add_to_cart_action_callback' );

// For removing books to cart
//add_action( 'wp_footer', 'wpbooklist_buyback_add_to_cart_action_javascript' );
//add_action( 'admin_footer', 'wpbooklist_buyback_add_to_cart_action_javascript' );
add_action( 'wp_ajax_wpbooklist_buyback_add_to_cart_remove_action', 'wpbooklist_buyback_add_to_cart_remove_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_buyback_add_to_cart_remove_action', 'wpbooklist_buyback_add_to_cart_remove_action_callback' );

// For finalizing sale
//add_action( 'wp_footer', 'wpbooklist_buyback_add_to_cart_action_javascript' );
//add_action( 'admin_footer', 'wpbooklist_buyback_add_to_cart_action_javascript' );
add_action( 'wp_ajax_wpbooklist_buyback_finalize_sale_action', 'wpbooklist_buyback_finalize_sale_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_buyback_finalize_sale_action', 'wpbooklist_buyback_finalize_sale_action_callback' );

// For saving settings
//add_action( 'wp_footer', 'wpbooklist_buyback_add_to_cart_action_javascript' );
//add_action( 'admin_footer', 'wpbooklist_buyback_add_to_cart_action_javascript' );
add_action( 'wp_ajax_wpbooklist_buyback_save_settings_action', 'wpbooklist_buyback_save_settings_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_buyback_save_settings_action', 'wpbooklist_buyback_save_settings_action_callback' );

// For updating orders
//add_action( 'wp_footer', 'wpbooklist_buyback_add_to_cart_action_javascript' );
//add_action( 'admin_footer', 'wpbooklist_buyback_add_to_cart_action_javascript' );
add_action( 'wp_ajax_wpbooklist_buyback_save_order_changes_action', 'wpbooklist_buyback_save_order_changes_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_buyback_save_order_changes_action', 'wpbooklist_buyback_save_order_changes_action_callback' );

// For deleting orders
//add_action( 'wp_footer', 'wpbooklist_buyback_add_to_cart_action_javascript' );
//add_action( 'admin_footer', 'wpbooklist_buyback_add_to_cart_action_javascript' );
add_action( 'wp_ajax_wpbooklist_buyback_delete_order_changes_action', 'wpbooklist_buyback_delete_order_changes_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_buyback_delete_order_changes_action', 'wpbooklist_buyback_delete_order_changes_action_callback' );

// For displaying in colorbox
//add_action( 'wp_footer', 'wpbooklist_buyback_colorbox_action_javascript' );
//add_action( 'admin_footer', 'wpbooklist_buyback_colorbox_action_javascript' );
add_action( 'wp_ajax_wpbooklist_buyback_colorbox_action', 'wpbooklist_buyback_colorbox_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_buyback_colorbox_action', 'wpbooklist_buyback_colorbox_action_callback' );

// For logging user in
add_action( 'wp_ajax_wpbooklist_buyback_login_action', 'wpbooklist_buyback_login_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_buyback_login_action', 'wpbooklist_buyback_login_action_callback' );

// For grabbing userdata and populating fields
add_action( 'wp_ajax_wpbooklist_buyback_populate_userdata_action', 'wpbooklist_buyback_populate_userdata_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_buyback_populate_userdata_action', 'wpbooklist_buyback_populate_userdata_action_callback' );

// For registering a new user
add_action( 'wp_ajax_wpbooklist_buyback_register_userdata_action', 'wpbooklist_buyback_register_userdata_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_buyback_register_userdata_action', 'wpbooklist_buyback_register_userdata_action_callback' );


// Code for adding file that prevents computer sleep during the buyback process
add_action('admin_enqueue_scripts', 'wpbooklist_jre_buyback_sleep_script' );

// Code for adding the main js file
add_action('wp_enqueue_scripts', 'wpbooklist_jre_buyback_main_script' );
add_action('admin_enqueue_scripts', 'wpbooklist_jre_buyback_main_script' );
/*
 * Function that utilizes the filter in the core WPBookList plugin, resulting in a new tab. Possible options for the first argument in the 'Add_filter' function below are:
 *  - 'wpbooklist_add_tab_books'
 *  - 'wpbooklist_add_tab_display'
 *
 *
 *
 * The instance of "Bookfinder" in the $extra_tab array can be replaced with whatever you want - but the 'buyback' instance MUST be your one-word descriptor.
*/
add_filter('wpbooklist_add_sub_menu', 'wpbooklist_buyback_submenu');
function wpbooklist_buyback_submenu($submenu_array) {
      $extra_submenu = array(
            'BuyBack'
      );
 
      // combine the two arrays
      $submenu_array = array_merge($submenu_array,$extra_submenu);
      return $submenu_array;
}


define('WPBOOKLIST_BUYBACK_NONCES_ARRAY', serialize(array(
	"wpbooklistbuybacknonce1" => "wpbooklist_buyback_addbooks_action_callback",
      "wpbooklistbuybacknonce2" => "wpbooklist_buyback_colorbox_action_callback",
      'wpbooklistbuybacknonce3' => 'wpbooklist_buyback_search_action_callback',
      'wpbooklistbuybacknonce4' => 'wpbooklist_buyback_colorbox_action_callback',
      'wpbooklistbuybacknonce5' => 'wpbooklist_buyback_login_action_callback',
      'wpbooklistbuybacknonce6' => 'wpbooklist_buyback_populate_userdata_action_callback',
      'wpbooklistbuybacknonce7' => 'wpbooklist_buyback_register_userdata_action_callback',
      'wpbooklistbuybacknonce8' => 'wpbooklist_buyback_add_to_cart_action_callback',
      'wpbooklistbuybacknonce9' => 'wpbooklist_buyback_add_to_cart_remove_action_callback',
      'wpbooklistbuybacknonce10' => 'wpbooklist_buyback_finalize_sale_action_callback',
      'wpbooklistbuybacknonce11' => 'wpbooklist_buyback_save_settings_action_callback',
      'wpbooklistbuybacknonce12' => 'wpbooklist_buyback_save_order_changes_action_callback',
      'wpbooklistbuybacknonce13' => 'wpbooklist_buyback_delete_order_changes_action_callback'
    ))
);





?>