<?php

  // For removing the admin bar for all users except admins
  add_action('after_setup_theme', 'remove_admin_bar');
  function remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
      show_admin_bar(false);
    }
  }

  // For registering table names
  function wpbooklist_jre_buyback_register_table_names() {
    global $wpdb;
    $table1 = $wpdb->wpbooklist_buyback_users = "{$wpdb->prefix}wpbooklist_buyback_users";
    $table2 = $wpdb->wpbooklist_buyback_settings = "{$wpdb->prefix}wpbooklist_buyback_settings";
    $table3 = $wpdb->wpbooklist_buyback_orders = "{$wpdb->prefix}wpbooklist_buyback_orders";
    return $table1.$table2.$table3;
  }

  // Runs once upon plugin activation and creates the Users tables
  function wpbooklist_jre_buyback_create_user_table() {
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    global $charset_collate; 

    // Call this manually as we may have missed the init hook
    wpbooklist_jre_buyback_register_table_names();

    // If table doesn't exist, create table
    $test_name = $wpdb->prefix.'wpbooklist_buyback_users';
    if($wpdb->get_var("SHOW TABLES LIKE '$test_name'") != $test_name) {
    
      // This is the table that holds static data about users - things like username, password, height, gender...
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
      $dbDeltaResult = dbDelta( $sql_create_table );

      $key = $wpdb->prefix.'wpbooklist_buyback_users';

      return $dbDeltaResult[$key];
    } else {
      return 'Table already exists';
    }

  }

  // Runs once upon plugin activation and creates the Users tables
  function wpbooklist_jre_buyback_create_orders_table() {
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    global $charset_collate; 

    // Call this manually as we may have missed the init hook
    wpbooklist_jre_buyback_register_table_names();

    // If table doesn't exist, create table
    $test_name = $wpdb->prefix.'wpbooklist_buyback_orders';
    if($wpdb->get_var("SHOW TABLES LIKE '$test_name'") != $test_name) {
    
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
      $dbDeltaResult = dbDelta( $sql_create_table );

      $key = $wpdb->prefix.'wpbooklist_buyback_orders';

      return $dbDeltaResult[$key];
    } else {
      return 'Table already exists';
    }
  }

  // Runs once upon plugin activation and creates the Users tables
  function wpbooklist_jre_buyback_create_settings_table() {
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    global $charset_collate; 

    // Call this manually as we may have missed the init hook
    wpbooklist_jre_buyback_register_table_names();

    // If table doesn't exist, create table
    $test_name = $wpdb->prefix.'wpbooklist_buyback_settings';
    if($wpdb->get_var("SHOW TABLES LIKE '$test_name'") != $test_name) {
    
      // This is the table that holds the admin's settings, mainly the pricing scheme
      $sql_create_table = "CREATE TABLE {$wpdb->wpbooklist_buyback_settings} 
      (
            ID smallint(190) auto_increment,
            rankcalcs varchar(255),
            PRIMARY KEY  (ID),
              KEY rankcalcs (rankcalcs)
      ) $charset_collate; ";
      $dbDeltaResult = dbDelta( $sql_create_table );
      $wpdb->insert( $test_name, array('ID' => 1, 'rankcalcs' => '0-20000-30-8;20001-30000-20-6;30001-400000-15-5'));

      $key = $wpdb->prefix.'wpbooklist_buyback_settings';

      return $dbDeltaResult[$key];
    } else {
      return 'Table already exists';
    }

  }

function wpbooklist_jre_buyback_create_nonces() {

    $temp_array = array();
    $unserialized = unserialize(WPBOOKLIST_BUYBACK_NONCES_ARRAY);
    foreach ($unserialized as $key => $noncetext) {
      $nonce = wp_create_nonce($noncetext);
      $temp_array[$key] = $nonce;
    }

    // Adding some more values we may need in our Javascript file
    $temp_array['wpbooklistbuybackrootimgiconsurl'] = BUYBACK_ROOT_IMG_ICONS_URL;

    // Defining our final nonce array
    define('WPBOOKLIST_BUYBACK_FINAL_NONCES_ARRAY', serialize($temp_array));

}

// Adding the front-end ui css file for this extension
function wpbooklist_jre_buyback_frontend_ui_style() {
    wp_register_style( 'wpbooklist-buyback-frontend-ui', BUYBACK_ROOT_CSS_URL.'buyback-main-frontend.css' );
    wp_enqueue_style('wpbooklist-buyback-frontend-ui');
}

// Code for adding the general admin CSS file
function wpbooklist_jre_buyback_admin_style() {
  if(current_user_can( 'administrator' )){
      wp_register_style( 'wpbooklist-buyback-admin-ui', BUYBACK_ROOT_CSS_URL.'buyback-main-admin.css');
      wp_enqueue_style('wpbooklist-buyback-admin-ui');
  }
}


// Code for adding file that prevents computer sleep during backup
function wpbooklist_jre_buyback_sleep_script() {
	if(current_user_can( 'administrator' )){
    	wp_register_script( 'wpbooklist-jre-buyback-sleepjs', BUYBACK_JS_URL.'nosleep/sleep.js', array('jquery') );
    	wp_enqueue_script('wpbooklist-jre-buyback-sleepjs');
	}
}

// Code for adding the main js
function wpbooklist_jre_buyback_main_script() {

  // First just register the script
  wp_register_script( 'wpbooklist-jre-buyback-mainjs', BUYBACK_JS_URL.'wpbooklist_buyback_main.min.js', array('jquery'), false, true );

  $final_array_of_php_values = unserialize(WPBOOKLIST_BUYBACK_FINAL_NONCES_ARRAY);

  // Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'wpbooklist_php_variables' object (like wpbooklist_php_variables.nameofkey, like any other JavaScript object)
  wp_localize_script( 'wpbooklist-jre-buyback-mainjs', 'wpbooklist_buyback_php_variables', $final_array_of_php_values );

  // Enqueued script with localized data.
  wp_enqueue_script('wpbooklist-jre-buyback-mainjs', array('jquery'), false, true);
}

function wpbooklist_jre_buyback_shortcode_function(){
  global $wpdb;

  ob_start();
  include_once( BUYBACK_CLASS_DIR . 'class-buyback-form.php');
  $front_end_library_ui = new WPBookList_Buyback_Form();
  echo $front_end_library_ui->output_buyback_form();
  return ob_get_clean();
}



?>