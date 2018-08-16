<?php

// Callback function for adding books
function wpbooklist_buyback_addbooks_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_buyback_addbooks_action_callback', 'security' );
	$library = filter_var($_POST['library'],FILTER_SANITIZE_STRING);
	$isbn = filter_var($_POST['isbn'],FILTER_SANITIZE_STRING);
	$page = filter_var($_POST['page'],FILTER_SANITIZE_STRING);
	$post = filter_var($_POST['post'],FILTER_SANITIZE_STRING);
	$woo = filter_var($_POST['woo'],FILTER_SANITIZE_STRING);
	
	$book_array = array(
		'amazon_auth_yes' => true,
		'library' => $library,
		'use_amazon_yes' => true,
		'isbn' => $isbn,
		'page_yes' => $page,
		'post_yes' =>$post,
		'woocommerce' => $woo
	);

	require_once(CLASS_DIR.'class-book.php');
	$book_class = new WPBookList_Book('add', $book_array, null);
	$insert_result = $book_class->add_result;

	// If book added succesfully, get the ID of the book we just inserted, and return the result and that ID
	if($insert_result == 1){
		$book_table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
  		$id_result = $wpdb->get_var("SELECT MAX(ID) from $library");
  		$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $library WHERE ID = %d", $id_result));

  		// Get saved page URL
		$table_name = $wpdb->prefix.'wpbooklist_jre_saved_page_post_log';
  		$page_results = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE book_uid = %s AND type = 'page'" , $row->book_uid));

  		// Get saved post URL
		$table_name = $wpdb->prefix.'wpbooklist_jre_saved_page_post_log';
  		$post_results = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE book_uid = %s AND type = 'post'", $row->book_uid));

  		echo $insert_result.'sep'.$id_result.'sep'.$library.'sep'.$page_yes.'sep'.$post_yes.'sep'.$page_results->post_url.'sep'.$post_results->post_url.'sep'.$book_class->title.'sep'.$book_class->isbn;
	}
	wp_die();
}

// Callback function for opening book in colorbox
function wpbooklist_buyback_colorbox_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_buyback_colorbox_action_callback', 'security' );

	$title = filter_var($_POST['title'],FILTER_SANITIZE_STRING);
	$author = filter_var($_POST['author'],FILTER_SANITIZE_STRING);
	$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	$pages = filter_var($_POST['pages'],FILTER_SANITIZE_STRING);
	$pub_year = filter_var($_POST['pubYear'],FILTER_SANITIZE_STRING);
	$publisher = filter_var($_POST['publisher'],FILTER_SANITIZE_STRING);
	$description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);
	$image = filter_var($_POST['image'],FILTER_SANITIZE_URL);
	$reviews = filter_var($_POST['reviews'],FILTER_SANITIZE_STRING);
	$isbn = filter_var($_POST['isbn'],FILTER_SANITIZE_STRING);
	$details = filter_var($_POST['details'],FILTER_SANITIZE_STRING);
	$similar = filter_var($_POST['similar'],FILTER_SANITIZE_STRING);


	$book_array = array(
		'title' => $title,
		'author' => $author,
		'category' => $category,
		'pages' => $pages,
		'pub_year' => $pub_year,
		'publisher' => $publisher,
		'description' => $description,
		'image' => $image,
		'reviews' => $reviews,
		'isbn' => $isbn,
		'details' => $details,
		'similar_products' => $similar
	);

	require_once(CLASS_DIR.'class-book.php');
	$book_class = new WPBookList_Book('buyback-colorbox', $book_array, null);
	$category = $book_class->category;
	$itunes_page = $book_class->itunes_page;
	$kobo_link = $book_class->kobo_link;
	$bam_link = $book_class->bam_link;

	$book_array = array(
		'title' => $title,
		'author' => $author,
		'category' => $category,
		'pages' => $pages,
		'pub_year' => $pub_year,
		'publisher' => $publisher,
		'description' => $description,
		'image' => $image,
		'reviews' => $reviews,
		'isbn' => $isbn,
		'details' => $details,
		'category' => $category,
		'itunes_page' => $itunes_page,
		'similar_products' => $similar,
		'kobo_link' => $kobo_link,
		'bam_link' => $bam_link,

	);

	// Instantiate the class that shows the book in colorbox
	require_once(CLASS_DIR.'class-show-book-in-colorbox.php');
	$colorbox = new WPBookList_Show_Book_In_Colorbox(null, null, $book_array, null);

	echo $colorbox->output;

	wp_die();
}

// Callback function for searching for books
function wpbooklist_buyback_add_to_cart_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_buyback_add_to_cart_action_callback', 'security' );

	$image = filter_var($_POST['image'],FILTER_SANITIZE_STRING);
	$title = filter_var($_POST['title'],FILTER_SANITIZE_STRING);
	$isbn = filter_var($_POST['isbn'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);

	// Get current user 
	$current_user = wp_get_current_user();

	// Create empty user array
	$table_name = $wpdb->prefix.'wpbooklist_buyback_users';
	$user_stuff = $wpdb->get_row("SELECT * FROM $table_name WHERE wpuserid = $current_user->ID");
	$newcart = $user_stuff->cart.'----'.$isbn.';;;'.$title.';;;'.$image.';;;'.$value;
	$newcart = ltrim($newcart, '----');

	$data = array(
    	'cart' => $newcart
    );
    $format = array( '%s'); 
    $where = array( 'wpuserid' => $current_user->ID );
    $where_format = array( '%d' );
    $wpdb->update( $table_name, $data, $where, $format, $where_format );

	//9781492650959;;;The Radium Girls: The Dark Story of America's Shining Women;;;https://images-na.ssl-images-amazon.com/images/I/51t86%2BXZO3L.jpg;;;3.43----9781492650959;;;The Radium Girls: The Dark Story of America's Shining Women;;;https://images-na.ssl-images-amazon.com/images/I/51t86%2BXZO3L.jpg;;;3.43
	wp_die($current_user->ID);
}

// Callback function for searching for books
function wpbooklist_buyback_add_to_cart_remove_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_buyback_add_to_cart_remove_action_callback', 'security' );

	$dbstring = filter_var($_POST['dbString'],FILTER_SANITIZE_STRING);

	// Get current user 
	$current_user = wp_get_current_user();

	// Create empty user array
	$table_name = $wpdb->prefix.'wpbooklist_buyback_users';
	$user_stuff = $wpdb->get_row("SELECT * FROM $table_name WHERE wpuserid = $current_user->ID");
	$newcart = $user_stuff->cart;

	// Problem is it removes ALL instances of book
	if(stripos($newcart, $dbstring.'----') !== false){
		$newcart = str_replace($dbstring.'----', '', $newcart);
	} else if(stripos($newcart, $dbstring) !== false){
		$newcart = str_replace($dbstring, '', $newcart);
	}

	// Check for instances of --------
	if(stripos($newcart, '--------') !== false){
		$newcart = str_replace('--------','----', $newcart);
	}


	$data = array(
    	'cart' => $newcart
    );
    $format = array( '%s'); 
    $where = array( 'wpuserid' => $current_user->ID );
    $where_format = array( '%d' );
    $wpdb->update( $table_name, $data, $where, $format, $where_format );


	//9780804137270;;;Armada: A novel by the author of Ready Player One;;;https://images-na.ssl-images-amazon.com/images/I/518jP8yB%2BML.jpg;;;2.32----9780553448146;;;Artemis: A Novel;;;https://images-na.ssl-images-amazon.com/images/I/41mpiaisIIL.jpg;;;2.25--------9780804137270;;;Armada: A novel by the author of Ready Player One;;;https://images-na.ssl-images-amazon.com/images/I/518jP8yB%2BML.jpg;;;2.32
	wp_die($dbstring);
}



// Callback function for searching for books
function wpbooklist_buyback_search_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_buyback_search_action_callback', 'security' );

	$author = filter_var($_POST['author'],FILTER_SANITIZE_STRING);
	$title = filter_var($_POST['title'],FILTER_SANITIZE_STRING);
	$isbn = filter_var($_POST['isbn'],FILTER_SANITIZE_STRING);
	$username = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
	$storefront == 'false';
	$insert_result = array();
	$insert_result2 = array();
	$insert_result3 = array();

	require_once(CLASS_DIR.'class-book.php');

	for ($i=1; $i < 2; $i++) { 
		$book_array = array(
			'amazon_auth_yes' => 'true',
			'use_amazon_yes' => 'true',
			'title' => $title,
			'author' => $author,
			'isbn' => $isbn,
			'book_page'=> $i
		);

		$book_class = new WPBookList_Book('search', $book_array, null);

		if($i == 1){
			$insert_result = $book_class->amazon_array;
		}

		if($i == 2){
			$insert_result2 = $book_class->amazon_array;
		}

		if($i == 3){
			$insert_result3 = $book_class->amazon_array;
		}
	}

	$insert_result = array_merge($insert_result2, $insert_result);

	$settings_table = $wpdb->prefix.'wpbooklist_buyback_settings';;
	$pricing_scheme = $wpdb->get_results("SELECT * FROM $settings_table");

	// Create empty user array
	$table_name = $wpdb->prefix.'wpbooklist_buyback_users';
	$user = array();



	// User is not logged in, so return form that displays the 'Log in' and the 'Register for an account' options.
	if(!is_user_logged_in()){
		$form = '<p id="wpbooklist-buyback-checkout-title">Login or Register</p>
		<p id="wpbooklist-buyback-checkout-subtitle">Log in below to Sell your books - not a member? Register Below!</p>
		<div class="wpbooklist-buyback-login-reg-div">
			<div class="wpbooklist-buyback-login-reg-row">
				<p class="wpbooklist-checkout-contact-title">Username/E-Mail Address</p>
				<input class="wpbooklist-buyback-login-reg-contact-field" id="wpbooklist-buyback-login-reg-contact-field-username" type="text"/>
			</div>
			<div class="wpbooklist-buyback-login-reg-row">
				<p class="wpbooklist-checkout-contact-title">Password</p>
				<input class="wpbooklist-buyback-login-reg-contact-field" id="wpbooklist-buyback-login-reg-contact-field-password" type="text"/>
			</div>
			<div class="wpbooklist-buyback-login-reg-button-div">
				<button id="wpbooklist-buyback-login-button">Login</button>
				<div class="wpbooklist-spinner" id="wpbooklist-spinner-buyback-login"></div>
				<div id="wpbooklist-buyback-login-reg-button-error"><p id="wpbooklist-buyback-login-reg-button-error-p"></p></div>
			</div>
		</div>
		<div class="wpbooklist-buyback-login-reg-div">
			<div class="wpbooklist-buyback-login-reg-row">
				<p class="wpbooklist-checkout-contact-title">First Name</p>
				<input class="wpbooklist-buyback-login-reg-contact-field" id="wpbooklist-buyback-login-reg-contact-field-firstname" type="text"/>
			</div>
			<div class="wpbooklist-buyback-login-reg-row">
				<p class="wpbooklist-checkout-contact-title">Last Name</p>
				<input class="wpbooklist-buyback-login-reg-contact-field" id="wpbooklist-buyback-login-reg-contact-field-lastname" type="text"/>
			</div>
			<div class="wpbooklist-buyback-login-reg-row">
				<p class="wpbooklist-checkout-contact-title">Street Address</p>
				<input class="wpbooklist-buyback-login-reg-contact-field" id="wpbooklist-buyback-login-reg-contact-field-streetaddress" type="text"/>
			</div>
			<div class="wpbooklist-buyback-login-reg-row">
				<div class="wpbooklist-buyback-login-reg-inner-row" id="wpbooklist-buyback-login-reg-inner-row-city">
					<p class="wpbooklist-checkout-contact-title">City</p>
					<input class="wpbooklist-buyback-login-reg-contact-field" id="wpbooklist-buyback-login-reg-contact-field-city" type="text"/>
				</div>
			</div>
			<div class="wpbooklist-buyback-login-reg-row">
				<div class="wpbooklist-buyback-login-reg-inner-row" id="wpbooklist-buyback-login-reg-inner-row-state">
					<p class="wpbooklist-checkout-contact-title">State</p>
						<select class="wpbooklist-buyback-login-reg-contact-field" id="wpbooklist-buyback-login-reg-contact-field-state">
							<option selected default disabled>Select a State...</option>
							<option value="AL">Alabama</option>
							<option value="AK">Alaska</option>
							<option value="AZ">Arizona</option>
							<option value="AR">Arkansas</option>
							<option value="CA">California</option>
							<option value="CO">Colorado</option>
							<option value="CT">Connecticut</option>
							<option value="DE">Delaware</option>
							<option value="DC">District Of Columbia</option>
							<option value="FL">Florida</option>
							<option value="GA">Georgia</option>
							<option value="HI">Hawaii</option>
							<option value="ID">Idaho</option>
							<option value="IL">Illinois</option>
							<option value="IN">Indiana</option>
							<option value="IA">Iowa</option>
							<option value="KS">Kansas</option>
							<option value="KY">Kentucky</option>
							<option value="LA">Louisiana</option>
							<option value="ME">Maine</option>
							<option value="MD">Maryland</option>
							<option value="MA">Massachusetts</option>
							<option value="MI">Michigan</option>
							<option value="MN">Minnesota</option>
							<option value="MS">Mississippi</option>
							<option value="MO">Missouri</option>
							<option value="MT">Montana</option>
							<option value="NE">Nebraska</option>
							<option value="NV">Nevada</option>
							<option value="NH">New Hampshire</option>
							<option value="NJ">New Jersey</option>
							<option value="NM">New Mexico</option>
							<option value="NY">New York</option>
							<option value="NC">North Carolina</option>
							<option value="ND">North Dakota</option>
							<option value="OH">Ohio</option>
							<option value="OK">Oklahoma</option>
							<option value="OR">Oregon</option>
							<option value="PA">Pennsylvania</option>
							<option value="RI">Rhode Island</option>
							<option value="SC">South Carolina</option>
							<option value="SD">South Dakota</option>
							<option value="TN">Tennessee</option>
							<option value="TX">Texas</option>
							<option value="UT">Utah</option>
							<option value="VT">Vermont</option>
							<option value="VA">Virginia</option>
							<option value="WA">Washington</option>
							<option value="WV">West Virginia</option>
							<option value="WI">Wisconsin</option>
							<option value="WY">Wyoming</option>
						</select>
				</div>
			</div>
			<div class="wpbooklist-buyback-login-reg-row">
				<div class="wpbooklist-buyback-login-reg-inner-row" id="wpbooklist-buyback-login-reg-inner-row-zip">
					<p class="wpbooklist-checkout-contact-title">Zip Code</p>
					<input class="wpbooklist-buyback-login-reg-contact-field" id="wpbooklist-buyback-login-reg-contact-field-zip" type="text"/>
				</div>
			</div>
			<div class="wpbooklist-buyback-login-reg-row">
				<p class="wpbooklist-checkout-contact-title">Password</p>
				<input class="wpbooklist-buyback-login-reg-contact-field" id="wpbooklist-buyback-login-reg-contact-field-register-password" type="text"/>
			</div>
			<div class="wpbooklist-buyback-login-reg-row">
				<p class="wpbooklist-checkout-contact-title">Re-enter Password</p>
				<input class="wpbooklist-buyback-login-reg-contact-field" id="wpbooklist-buyback-login-reg-contact-field-register-reenterpassword" type="text"/>
			</div>
			<div class="wpbooklist-buyback-login-reg-row">
				<p class="wpbooklist-checkout-contact-title">E-Mail Address</p>
				<input class="wpbooklist-buyback-login-reg-contact-field" id="wpbooklist-buyback-login-reg-contact-field-email" type="text"/>
			</div>
			<div class="wpbooklist-buyback-login-reg-button-div">
				<button id="wpbooklist-buyback-register-button">Register</button>
				<div class="wpbooklist-spinner" id="wpbooklist-spinner-buyback-register"></div>
				<div id="wpbooklist-buyback-login-reg-button-error"><p id="wpbooklist-buyback-register-button-error-p"></p></div>
			</div>
		</div>';
	} else {

		$table_name = $wpdb->prefix.'wpbooklist_buyback_users';
		$user = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE wpuserid=%d", get_current_user_id()));
		$cart_total = 0;
		$cart_value = 0.00;

		$cart_html = '<div id="wpbooklist-buyback-cart-div">
		<div class="wpbooklist-buyback-cart-title-div">
			<p class="wpbooklist-buyback-cart-title-heading">Your Cart:</p>
		</div>';

		// If user has more than one cart item saved...
		if(stripos($user[0]->cart, '----') !== false){
			$user[0]->cart = explode('----', $user[0]->cart);
			foreach ($user[0]->cart as $key => $value) {

				$value = explode(';;;', $value);
				$cart_total++;
				$cart_value += round($value[3],2);

				$dbString = $value[0].';;;'.$value[1].';;;'.$value[2].';;;'.$value[3];

				$cart_html = $cart_html.'<div class="wpbooklist-buyback-cart-div-row">
					<div class="wpbooklist-buyback-cart-img-div">
						<img class="wpbooklist-buyback-cart-img-actual" src="'.$value[2].'"/>
					</div>
					<div class="wpbooklist-buyback-cart-title-div">
						<p class="wpbooklist-buyback-cart-title-actual">'.stripslashes($value[1]).'</p>
						<p class="wpbooklist-buyback-cart-title-isbn-actual">'.$value[0].'</p>
					</div>
					<div class="wpbooklist-buyback-cart-value-div">
						<p class="wpbooklist-buyback-cart-value-actual">$'.$value[3].'</p>
						<div class="wpbooklist-buyback-cart-value-remove-div" data-dbstring="'.$dbString.'">
							<img class="wpbooklist-buyback-cart-value-remove-img-actual" src="'.BUYBACK_ROOT_IMG_ICONS_URL.'cancel-button.svg" />
							<p class="wpbooklist-buyback-cart-value-remove-text-actual">Remove from Cart</p>
						</div>
					</div>
				</div>';

			}
		} else {

			if($user[0]->cart != ''){

				$value = explode(';;;', $user[0]->cart);
				$cart_total++;
				$cart_value += round($value[3],2);

				$dbString = $value[0].';;;'.$value[1].';;;'.$value[2].';;;'.$value[3];

				$cart_html = $cart_html.'<div class="wpbooklist-buyback-cart-div-row">
					<div class="wpbooklist-buyback-cart-img-div">
						<img class="wpbooklist-buyback-cart-img-actual" src="'.$value[2].'"/>
					</div>
					<div class="wpbooklist-buyback-cart-title-div">
						<p class="wpbooklist-buyback-cart-title-actual">'.stripslashes($value[1]).'</p>
						<p class="wpbooklist-buyback-cart-title-isbn-actual">'.$value[0].'</p>
					</div>
					<div class="wpbooklist-buyback-cart-value-div">
						<p class="wpbooklist-buyback-cart-value-actual">$'.$value[3].'</p>
						<div class="wpbooklist-buyback-cart-value-remove-div" data-dbstring="'.$dbString.'">
							<img class="wpbooklist-buyback-cart-value-remove-img-actual" src="'.BUYBACK_ROOT_IMG_ICONS_URL.'cancel-button.svg" />
							<p class="wpbooklist-buyback-cart-value-remove-text-actual">Remove from Cart</p>
						</div>
					</div>
				</div>';
			}
		}

		$cart_html = $cart_html.'
			<div class="wpbooklist-buyback-cart-summary-row">
				<div class="wpbooklist-buyback-cart-summary-total-books">
					<p class="wpbooklist-buyback-cart-summary-total-books-p">
						Total Book(s) in Cart: <span class="wpbooklist-buyback-cart-summary-span"><span id="wpbooklist-buyback-cart-summary-span-total-calc">'.$cart_total.'</span> Books</span>
					</p>
				</div>
				<div class="wpbooklist-buyback-cart-summary-total-value">
					<p class="wpbooklist-buyback-cart-summary-total-value-p">
						Total Cart Value: <span class="wpbooklist-buyback-cart-summary-span">$<span id="wpbooklist-buyback-cart-summary-span-value-calc">'.$cart_value.'</span></span>
					</p>
				</div>
				<div class="wpbooklist-buyback-cart-summary-message-div">

				</div>
			</div>
		</div>';


		$form = '<p id="wpbooklist-buyback-checkout-title">Sell Your Books</p>
			'.$cart_html.'
			<p id="wpbooklist-buyback-checkout-subtitle">Verify Contact Information</p>
			<div id="wpbooklist-buyback-checkout-contact-div">
				<div class="wpbooklist-buyback-checkout-row">
					<p class="wpbooklist-checkout-contact-title">First Name</p>
					<input class="wpbooklist-buyback-checkout-contact-field" id="wpbooklist-buyback-checkout-contact-field-firstname" type="text" value="'.$user[0]->firstname.'"/>
				</div>
				<div class="wpbooklist-buyback-checkout-row">
					<p class="wpbooklist-checkout-contact-title">Last Name</p>
					<input class="wpbooklist-buyback-checkout-contact-field" id="wpbooklist-buyback-checkout-contact-field-lastname" type="text" value="'.$user[0]->lastname.'"/>
				</div>
				<div class="wpbooklist-buyback-checkout-row">
					<p class="wpbooklist-checkout-contact-title">E-Mail Address</p>
					<input class="wpbooklist-buyback-checkout-contact-field" id="wpbooklist-buyback-checkout-contact-field-email" type="text" value="'.$user[0]->email.'"/>
				</div>
				<div class="wpbooklist-buyback-checkout-row">
					<p class="wpbooklist-checkout-contact-title">Street Address</p>
					<input class="wpbooklist-buyback-checkout-contact-field" id="wpbooklist-buyback-checkout-contact-field-streetaddress" type="text" value="'.$user[0]->streetaddress.'"/>
				</div>
				<div class="wpbooklist-buyback-checkout-row">

					<div class="wpbooklist-buyback-checkout-inner-row" id="wpbooklist-buyback-checkout-inner-row-city">
						<p class="wpbooklist-checkout-contact-title">City</p>
						<input class="wpbooklist-buyback-checkout-contact-field" id="wpbooklist-buyback-checkout-contact-field-city" type="text" value="'.$user[0]->city.'"/>
					</div>
					<div class="wpbooklist-buyback-checkout-inner-row" id="wpbooklist-buyback-checkout-inner-row-state">
						<p class="wpbooklist-checkout-contact-title">State</p>
							<select class="wpbooklist-buyback-checkout-contact-field" id="wpbooklist-buyback-checkout-contact-field-state">
								<option selected default disabled>Select a State...</option>
								<option value="AL">Alabama</option>
								<option value="AK">Alaska</option>
								<option value="AZ">Arizona</option>
								<option value="AR">Arkansas</option>
								<option value="CA">California</option>
								<option value="CO">Colorado</option>
								<option value="CT">Connecticut</option>
								<option value="DE">Delaware</option>
								<option value="DC">District Of Columbia</option>
								<option value="FL">Florida</option>
								<option value="GA">Georgia</option>
								<option value="HI">Hawaii</option>
								<option value="ID">Idaho</option>
								<option value="IL">Illinois</option>
								<option value="IN">Indiana</option>
								<option value="IA">Iowa</option>
								<option value="KS">Kansas</option>
								<option value="KY">Kentucky</option>
								<option value="LA">Louisiana</option>
								<option value="ME">Maine</option>
								<option value="MD">Maryland</option>
								<option value="MA">Massachusetts</option>
								<option value="MI">Michigan</option>
								<option value="MN">Minnesota</option>
								<option value="MS">Mississippi</option>
								<option value="MO">Missouri</option>
								<option value="MT">Montana</option>
								<option value="NE">Nebraska</option>
								<option value="NV">Nevada</option>
								<option value="NH">New Hampshire</option>
								<option value="NJ">New Jersey</option>
								<option value="NM">New Mexico</option>
								<option value="NY">New York</option>
								<option value="NC">North Carolina</option>
								<option value="ND">North Dakota</option>
								<option value="OH">Ohio</option>
								<option value="OK">Oklahoma</option>
								<option value="OR">Oregon</option>
								<option value="PA">Pennsylvania</option>
								<option value="RI">Rhode Island</option>
								<option value="SC">South Carolina</option>
								<option value="SD">South Dakota</option>
								<option value="TN">Tennessee</option>
								<option value="TX">Texas</option>
								<option value="UT">Utah</option>
								<option value="VT">Vermont</option>
								<option value="VA">Virginia</option>
								<option value="WA">Washington</option>
								<option value="WV">West Virginia</option>
								<option value="WI">Wisconsin</option>
								<option value="WY">Wyoming</option>
							</select>
					</div>
				</div>
				<div class="wpbooklist-buyback-checkout-row">
					<p class="wpbooklist-checkout-contact-title">Zip Code</p>
					<input class="wpbooklist-buyback-checkout-contact-field" id="wpbooklist-buyback-checkout-contact-field-zipcode" type="text" value="'.$user[0]->zipcode.'"/>
				</div>
				<div class="wpbooklist-buyback-checkout-row">
					<p class="wpbooklist-checkout-contact-title">Phone</p>
					<input class="wpbooklist-buyback-checkout-contact-field" id="wpbooklist-buyback-checkout-contact-field-phone" type="text" />
				</div>
				<div class="wpbooklist-buyback-checkout-row">
					<p class="wpbooklist-checkout-contact-title">Receive Payment Via:</p>
					<select class="wpbooklist-buyback-checkout-contact-field" id="wpbooklist-buyback-checkout-contact-field-payment">
						<option selected default disabled>Select a Payment Method...</option>
						<option>PayPal</option>
						<option>Check by Mail</option>
					</select>
				</div>
				<div class="wpbooklist-buyback-checkout-row wpbooklist-buyback-checkout-row-paypal-email">
					<p class="wpbooklist-checkout-contact-title">Enter PayPal E-Mail Address:</p>
					<input class="wpbooklist-buyback-checkout-contact-field" id="wpbooklist-buyback-checkout-contact-field-paypalemail-1" type="text"/>
				</div>
				<div class="wpbooklist-buyback-checkout-row wpbooklist-buyback-checkout-row-paypal-email">
					<p class="wpbooklist-checkout-contact-title">Verify PayPal E-Mail Address:</p>
					<input class="wpbooklist-buyback-checkout-contact-field" id="wpbooklist-buyback-checkout-contact-field-paypalemail-2" type="text"/>
				</div>
				<div class="wpbooklist-buyback-checkout-row">
					<button id="wpbooklist-buyback-finalize-sale-button">Sell Your Books!</button>
					<div class="wpbooklist-spinner" id="wpbooklist-spinner-buyback-finalize-sale"></div>
					<div id="wpbooklist-buyback-finalize-sale-button-error"><p id="wpbooklist-buyback-finalize-sale-button-error-p"></p></div>
					</div>
			</div>';
	}


	echo json_encode($insert_result).'--sep--seperator--sep--'.json_encode($pricing_scheme).'--sep--seperator--sep--'.$form.'--sep--seperator--sep--'.json_encode($user);

	wp_die();
}

// Callback function for logging user in
function wpbooklist_buyback_login_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_buyback_login_action_callback', 'security' );

	$email = filter_var($_POST['email'],FILTER_SANITIZE_STRING);
	$password = filter_var($_POST['password'],FILTER_SANITIZE_STRING);

	$table_name = $wpdb->prefix.'wpbooklist_buyback_users';
	$users = $wpdb->get_results("SELECT * FROM $table_name");

	$result_string = '';
	$result_un_string = '';
	$result_pw_string = '';

	foreach ($users as $key => $user) {
		if($user->email == $email){
			$result_string = $result_string.'--Username Found--';


			$userdata = get_user_by('login', $email);
			$result = wp_check_password($password, $userdata->user_pass, $userdata->ID);
			$wp_login_result = wp_signon(array('user_login' => $email, 'user_password' => $password));

			if(!$result){
				$result_string = $result_string.'--Password Does Not Match--';
				wp_die($result_string);
			} else {
				$result_string = $result_string.'--Password Matches--';
				wp_die($result_string);
			}
		}
	}

	$result_string = 'Username not found!';
	wp_die($result_string.'--'.$username.'--'.$password);
}

// Callback function for logging user in
function wpbooklist_buyback_populate_userdata_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_buyback_populate_userdata_action_callback', 'security' );

	$email = filter_var($_POST['email'],FILTER_SANITIZE_STRING);

	$table_name = $wpdb->prefix.'wpbooklist_buyback_users';
	$user = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE email=%s", $email));

	wp_die(json_encode($user));

}

// Callback function for finalizing the sale.
function wpbooklist_buyback_finalize_sale_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_buyback_finalize_sale_action_callback', 'security' );

	$email = filter_var($_POST['email'],FILTER_SANITIZE_STRING);
	$firstname = filter_var($_POST['firstname'],FILTER_SANITIZE_STRING);
	$lastname = filter_var($_POST['lastname'],FILTER_SANITIZE_STRING);
	$books = filter_var($_POST['books'],FILTER_SANITIZE_STRING);
	$streetaddress = filter_var($_POST['streetaddress'],FILTER_SANITIZE_STRING);
	$phone = filter_var($_POST['phone'],FILTER_SANITIZE_STRING);
	$city = filter_var($_POST['city'],FILTER_SANITIZE_STRING);
	$state = filter_var($_POST['state'],FILTER_SANITIZE_STRING);
	$zip = filter_var($_POST['zip'],FILTER_SANITIZE_STRING);
	$email = filter_var($_POST['email'],FILTER_SANITIZE_STRING);
	$paypalemail = filter_var($_POST['paypalemail'],FILTER_SANITIZE_STRING);
	$paymentmethod = filter_var($_POST['paymentmethod'],FILTER_SANITIZE_STRING);

	// Get current user 
	$current_user = wp_get_current_user();
	$wpuserid = $current_user->ID;


	$order_array = array(
		'email' => $email,
		'firstname' => $firstname,
		'lastname' => $lastname,
		'books' => $books,
		'streetaddress' => $streetaddress,
		'phone' => $phone,
		'city' => $city,
		'state' => $state,
		'email' => $email,
		'paypalemail' => $paypalemail,
		'paymentmethod' => $paymentmethod,
		'orderstatus' => 'initial',
		'wpuserid' => $wpuserid,
		'zipcode' => $zip,
	);


	$table_name = $wpdb->prefix.'wpbooklist_buyback_orders';
	$response_string = $wpdb->insert( $table_name, $order_array, array( '%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d'));

	if($response_string == 1){
				$response_string = 'Success!';	

				// Clear User's cart
				$table_name = $wpdb->prefix.'wpbooklist_buyback_users';
				$data = array(
			    	'cart' => ''
			    );
			    $format = array( '%s'); 
			    $where = array( 'wpuserid' => $wpuserid );
			    $where_format = array( '%d' );
			    $wpdb->update( $table_name, $data, $where, $format, $where_format );

			    // E-mail message
			    $message = "Thank you for doing business with the Bookkeeper's Den!\nWe recommend that you use USPS Media Mail as it is the more affordable
method of mailing books.\nPlease mail your book(s) to:\n\nThe Bookkeeper's Den\n813 Shipton Court\nChesapeake, VA 23320\n\nAfter we receive the book(s), we will submit a payment to you within 3
business days.\n\nRegards,\nThe Bookkeeper's Den";

				$admin_message = "You've received a new Bookkepper's Den Order! Here are the details:\n\n".$firstname.' '.$lastname."\n".$email."\nPayment Method: ".$paymentmethod;

			    // Now E-mail the user
			    wp_mail($email, "Your Bookkeeper's Den Order", $message);

			    // Now e-mail the admin
			    wp_mail('dukenet.admin@gmail.com', "A New Bookkeeper's Den Order Has Arrived!", $admin_message);

			    


	} else {
			$response_string = 'Looks like there\'s was a problem placing your order. Please call';
	}


	wp_die($response_string);

}

// Callback function for logging user in
function wpbooklist_buyback_save_settings_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_buyback_save_settings_action_callback', 'security' );
	$settingstring = filter_var($_POST['settingstring'],FILTER_SANITIZE_STRING);

	$table_name = $wpdb->prefix.'wpbooklist_buyback_settings';
	$data = array(
    	'rankcalcs' => $settingstring
    );
    $format = array( '%s'); 
    $where = array( 'ID' => 1 );
    $where_format = array( '%d' );
    $result = $wpdb->update( $table_name, $data, $where, $format, $where_format );
	wp_die($result);

}

// Callback function for logging user in
function wpbooklist_buyback_save_order_changes_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_buyback_save_order_changes_action_callback', 'security' );
	$orderstatus = filter_var($_POST['orderStatus'],FILTER_SANITIZE_STRING);

	$table_name = $wpdb->prefix.'wpbooklist_buyback_orders';
	$data = array(
    	'orderstatus' => $orderstatus
    );
    $format = array( '%s'); 
    $where = array( 'ID' => 1 );
    $where_format = array( '%d' );
    $result = $wpdb->update( $table_name, $data, $where, $format, $where_format );
	wp_die($result);

}

// Callback function for logging user in
function wpbooklist_buyback_register_userdata_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_buyback_register_userdata_action_callback', 'security' );

	$email = filter_var($_POST['email'],FILTER_SANITIZE_STRING);
	$firstname = filter_var($_POST['firstname'],FILTER_SANITIZE_STRING);
	$lastname = filter_var($_POST['lastname'],FILTER_SANITIZE_STRING);
	$password = filter_var($_POST['password'],FILTER_SANITIZE_STRING);
	$state = filter_var($_POST['state'],FILTER_SANITIZE_STRING);
	$city = filter_var($_POST['city'],FILTER_SANITIZE_STRING);
	$streetaddress = filter_var($_POST['streetaddress'],FILTER_SANITIZE_STRING);
	$zip = filter_var($_POST['zip'],FILTER_SANITIZE_STRING);

	$table_name = $wpdb->prefix.'wpbooklist_buyback_users';
	$user = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE username=%s", $email));

	$response_string = '';

	if(sizeof($user) > 0){
		$response_string = 'Looks like there\'s already a user registered with this E-mail address!';
	} else {


		// Create a WordPress user, and then create my own unique user
		$user_id = username_exists( $email );
		if ( !$user_id and email_exists($email) == false ) {
			$user_id = wp_create_user( $email, $password, $email );

			$user_array = array(
				'email' => $email,
				'firstname' => $firstname,
				'lastname' => $lastname,
				'state' => $state,
				'city' => $city,
				'streetaddress' => $streetaddress,
				'zipcode' => $zip,
				'wpuserid' => $user_id
			);

			$response_string = $wpdb->insert( $table_name, $user_array, array( '%s','%s','%s','%s','%s','%s','%s','%d'));

			if($response_string == 1){
				$response_string = 'Success!';
				$userdata = get_user_by('login', $email);
				$result = wp_check_password($password, $userdata->user_pass, $userdata->ID);
				$wp_login_result = wp_signon(array('user_login' => $email, 'user_password' => $password));
				}

		} else {
			$response_string = 'Looks like there\'s already a user registered with this E-mail address!';
		}
	}
	wp_die($response_string);
}



// Callback function for editing order details
function wpbooklist_buyback_delete_order_changes_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_buyback_delete_order_changes_action_callback', 'security' );
	$orderid = filter_var($_POST['orderId'],FILTER_SANITIZE_STRING);

	$table_name = $wpdb->prefix.'wpbooklist_buyback_orders';
	$where = array(
    	'ID' => $orderid
    );
    $where_format = array( '%d' );
    $result = $wpdb->delete( $table_name, $where, $where_format );
	wp_die($result);

}




?>