<?php
/**
 * Class WPBooklist_Buyback_General_Functions - WPBooklist_Buyback-functions.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes
 * @version  0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBooklist_Buyback_General_Functions', false ) ) :

	/**
	 * WPBooklist_Buyback_General_Functions class. Here we'll do things like enqueue scripts/css, set up menus, etc.
	 */
	class WPBooklist_Buyback_General_Functions {

		/**
		 * For removing the admin bar for all users except admins.
		 */
		public function wpbooklist_jre_buyback_remove_admin_bar() {
			if ( ! current_user_can( 'administrator' ) && ! is_admin() ) {
				show_admin_bar( true );
			}
		}

		/**
		 * For registering table names.
		 */
		public function wpbooklist_jre_buyback_register_table_names() {
			global $wpdb;
			$wpdb->wpbooklist_buyback_users    = "{$wpdb->prefix}wpbooklist_buyback_users";
			$wpdb->wpbooklist_buyback_settings = "{$wpdb->prefix}wpbooklist_buyback_settings";
			$wpdb->wpbooklist_buyback_orders   = "{$wpdb->prefix}wpbooklist_buyback_orders";
			return $wpdb->wpbooklist_buyback_users . $wpdb->wpbooklist_buyback_settings . $wpdb->wpbooklist_buyback_orders;
		}

		/**
		 * Runs once upon plugin activation and creates the Users tables
		 */
		public function wpbooklist_jre_buyback_create_user_table() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_jre_buyback_register_table_names();

			// If table doesn't exist, create table.
			$test_name = $wpdb->prefix . 'wpbooklist_buyback_users';
			if ( $test_name !== $wpdb->get_var( "SHOW TABLES LIKE '$test_name'" ) ) {

				// This is the table that holds static data about users - things like username, all orders, etc...
				$sql_create_table = "CREATE TABLE {$wpdb->wpbooklist_buyback_users} 
				(
					  ID smallint(190) auto_increment,
					  firstname varchar(190),
					  lastname varchar(255),
					  wpuserid smallint(6),
					  soldbooks MEDIUMTEXT,
					  pendingsales MEDIUMTEXT,
					  streetaddress MEDIUMTEXT,
					  phone varchar(255),
					  email varchar(255),
					  username varchar(255),
					  city varchar(255),
					  state varchar(255),
					  cart MEDIUMTEXT,
					  zipcode bigint(255),
					  PRIMARY KEY  (ID),
						KEY firstname (firstname)
				) $charset_collate; ";
				$db_delta_result  = dbDelta( $sql_create_table );

				$key = $wpdb->prefix . 'wpbooklist_buyback_users';

				return $db_delta_result[ $key ];
			} else {
				return 'Table already exists';
			}

		}

		/**
		 * Runs once upon plugin activation and creates the Orders table
		 */
		public function wpbooklist_jre_buyback_create_orders_table() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_jre_buyback_register_table_names();

			// If table doesn't exist, create table.
			$test_name = $wpdb->prefix . 'wpbooklist_buyback_orders';
			if ( $test_name !== $wpdb->get_var( "SHOW TABLES LIKE '$test_name'" ) ) {

				// This is the table that holds static data about users - things like username, password, height, gender...
				$sql_create_table = "CREATE TABLE {$wpdb->wpbooklist_buyback_orders} 
				(
					  ID smallint(190) auto_increment,
					  firstname varchar(190),
					  lastname varchar(255),
					  wpuserid smallint(6),
					  books MEDIUMTEXT,
					  streetaddress MEDIUMTEXT,
					  phone varchar(255),
					  email varchar(255),
					  paypalemail varchar(255),
					  paymentmethod varchar(255),
					  orderstatus varchar(255),
					  username varchar(255),
					  city varchar(255),
					  state varchar(255),
					  zipcode bigint(255),
					  PRIMARY KEY  (ID),
						KEY firstname (firstname)
				) $charset_collate; ";
				$db_delta_result  = dbDelta( $sql_create_table );

				$key = $wpdb->prefix . 'wpbooklist_buyback_orders';

				return $db_delta_result[ $key ];
			} else {
				return 'Table already exists';
			}
		}

		/**
		 * Runs once upon plugin activation and creates the Settings tables
		 */
		function wpbooklist_jre_buyback_create_settings_table() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate; 

			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_jre_buyback_register_table_names();

			// If table doesn't exist, create table.
			$test_name = $wpdb->prefix . 'wpbooklist_buyback_settings';
			if ( $test_name !== $wpdb->get_var( "SHOW TABLES LIKE '$test_name'" ) ) {

				// This is the table that holds the admin's settings, mainly the pricing scheme.
				$sql_create_table = "CREATE TABLE {$wpdb->wpbooklist_buyback_settings} 
				(
					  ID smallint(190) auto_increment,
					  rankcalcs varchar(255),
					  PRIMARY KEY  (ID),
						KEY rankcalcs (rankcalcs)
				) $charset_collate; ";
				$db_delta_result = dbDelta( $sql_create_table );
				$wpdb->insert( $test_name,
					array(
						'ID'        => 1,
						'rankcalcs' => '0-20000-30-8;20001-30000-20-6;30001-400000-15-5',
					)
				);

				$key = $wpdb->prefix . 'wpbooklist_buyback_settings';

				return $db_delta_result[ $key ];
			} else {
				return 'Table already exists';
			}
		}

		/**
		 * Create the nonces array
		 */
		public function wpbooklist_jre_buyback_create_nonces() {

			$temp_array   = array();
			$unserialized = json_decode( WPBOOKLIST_BUYBACK_NONCES_ARRAY );
			foreach ( $unserialized as $key => $noncetext ) {
				$nonce              = wp_create_nonce( $noncetext );
				$temp_array[ $key ] = $nonce;
			}

			// Adding some more values we may need in our Javascript file.
			$temp_array['wpbooklistbuybackrootimgiconsurl'] = BUYBACK_ROOT_IMG_ICONS_URL;

			// Defining our final nonce array.
			define( 'WPBOOKLIST_BUYBACK_FINAL_NONCES_ARRAY', wp_json_encode( $temp_array ) );

		}

		// Function that adds the Ajax Library into the head of the doc: <script type="text/javascript">var ajaxurl = "http://localhost:8888/local/wp-admin/admin-ajax.php"</script>
		function wpbooklist_jre_buyback_add_ajax_library() {

			$html = '<script type="text/javascript">';

			// checking $protocol in HTTP or HTTPS
			if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) {
				// this is HTTPS
				$protocol = 'https';
			} else {
				// this is HTTP
				$protocol = 'http';
			}
			$tempAjaxPath = admin_url( 'admin-ajax.php' );
			$goodAjaxUrl  = $protocol . strchr( $tempAjaxPath, ':' );

			$html .= 'var ajaxurl = "' . $goodAjaxUrl . '"';
			$html .= '</script>';
			echo $html;
			return $html;
		}

		/**
		 * Adding the front-end ui css file for this extension
		 */
		public function wpbooklist_jre_buyback_frontend_ui_style() {
			wp_register_style( 'wpbooklist-buyback-frontend-ui', BUYBACK_ROOT_CSS_URL . 'buyback-main-frontend.css', null, BUYBACK_VERSION_NUM );
			wp_enqueue_style( 'wpbooklist-buyback-frontend-ui' );
		}

		/**
		 * Code for adding the general admin CSS file
		 */
		public function wpbooklist_jre_buyback_admin_style() {
			if ( current_user_can( 'administrator' ) ) {
				wp_register_style( 'wpbooklist-buyback-admin-ui', BUYBACK_ROOT_CSS_URL . 'buyback-main-admin.css', null, BUYBACK_VERSION_NUM );
				wp_enqueue_style( 'wpbooklist-buyback-admin-ui' );
			}
		}

		/**
		 * Code for adding the admin js file
		 */
		public function wpbooklist_jre_buyback_admin_script() {

			// First just register the script.
			wp_register_script( 'wpbooklist-jre-buyback-admin', BUYBACK_JS_URL . 'wpbooklist_buyback_admin.min.js', array( 'jquery' ), BUYBACK_VERSION_NUM, true );

			$final_array_of_php_values = json_decode( WPBOOKLIST_BUYBACK_FINAL_NONCES_ARRAY, true );

			// Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'wpbooklist_php_variables' object (like wpbooklist_php_variables.nameofkey, like any other JavaScript object).
			wp_localize_script( 'wpbooklist-jre-buyback-admin', 'wpbooklist_buyback_php_variables', $final_array_of_php_values );

			// Enqueued script with localized data.
			wp_enqueue_script( 'wpbooklist-jre-buyback-admin', array( 'jquery' ), false, BUYBACK_VERSION_NUM, true );
		}

		/**
		 * Code for adding the frontend js
		 */
		public function wpbooklist_jre_buyback_frontend_script() {

			// First just register the script.
			wp_register_script( 'wpbooklist-jre-buyback-frontend', BUYBACK_JS_URL . 'wpbooklist_buyback_frontend.min.js', array( 'jquery' ), BUYBACK_VERSION_NUM, true );

			$final_array_of_php_values = json_decode( WPBOOKLIST_BUYBACK_FINAL_NONCES_ARRAY, true );

			// Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'wpbooklist_php_variables' object (like wpbooklist_php_variables.nameofkey, like any other JavaScript object).
			wp_localize_script( 'wpbooklist-jre-buyback-frontend', 'wpbooklist_buyback_php_variables', $final_array_of_php_values );

			// Enqueued script with localized data.
			wp_enqueue_script( 'wpbooklist-jre-buyback-frontend', array( 'jquery' ), false, BUYBACK_VERSION_NUM, true );
		}

		/**
		 * Code for adding the frontend book sort/search shortcode
		 */
		public function wpbooklist_jre_buyback_search_cart_shortcode_function() {
			global $wpdb;

			ob_start();
			include_once BUYBACK_CLASS_DIR . 'class-buyback-form.php';
			$front_end_library_ui = new WPBookList_Buyback_Form();
			echo $front_end_library_ui->output_buyback_form();
			return ob_get_clean();
		}
        
        /**
		 * Code for adding the frontend book sort/search shortcode for non-logged in members
		 */
		public function wpbooklist_jre_buyback_search_cart_nonmembers_shortcode_function() {
			global $wpdb;

			ob_start();
			include_once BUYBACK_CLASS_DIR . 'class-buyback-nonmembers-form.php';
			$front_end_library_ui = new WPBookList_Buyback_Nonmembers_Form();
			echo $front_end_library_ui->output_buyback_nonmembers_form();
			return ob_get_clean();
		}

		/**
		 * Code for adding the frontend login/Register page
		 */
		public function wpbooklist_jre_buyback_login_register_shortcode_function() {
			global $wpdb;

			ob_start();
			include_once BUYBACK_CLASS_DIR . 'class-buyback-login-register-form.php';
			$user_page_ui = new Buyback_Login_Register_Form();
			echo $user_page_ui->echo_form();
			return ob_get_clean();
		}
	}
endif;