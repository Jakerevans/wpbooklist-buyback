<?php
/**
 * WPBookList Buyback_Login_Register_Form Tab Class
 *
 * @author   Jake Evans
 * @category ??????
 * @package  ??????
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Buyback_Login_Register_Form', false ) ) :
	/**
	 * Buyback_Login_Register_Form.
	 */
	class Buyback_Login_Register_Form {

		/** Common member variable
		 *
		 *  @var string $logged_in
		 */
		public $logged_in = '';

		/** Common member variable
		 *
		 *  @var string $final_form
		 */
		public $final_form = '';

		/** Common member variable
		 *
		 *  @var object $user_data
		 */
		public $user_data = '';

		/** Common member variable
		 *
		 *  @var object $user_order_rows
		 */
		public $user_order_rows = '';

		/** Common member variable
		 *
		 *  @var int $wpuserid
		 */
		public $wpuserid = null;

		/** Common member variable
		 *
		 *  @var object $current_wp_user
		 */
		public $current_wp_user = '';

		/**
		 * Class Constructor
		 */
		public function __construct() {

			$logged_in = $this->determine_user_logged_in();

			if ( $logged_in ) {

				$this->output_user_data_form();

			} else {

				$this->output_login_register_form();

			}

		}

		/**
		 * Code for determining if user is logged in or not.
		 */
		public function echo_form() {
			echo $this->final_form;
		}

		/**
		 * Code for determining if user is logged in or not.
		 */
		public function determine_user_logged_in() {

			global $wpdb;

			// If user is logged into WordPress.
			if ( is_user_logged_in() ) {

				// Get current user.
				$this->current_wp_user = wp_get_current_user();
				$this->wpuserid        = $this->current_wp_user->ID;

				// Now get the user's registration info.
				$table_name      = $wpdb->prefix . 'wpbooklist_buyback_users';
				$this->user_data = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE wpuserid=%s", $this->wpuserid ) );

				// Now get the user's order info.
				$table_name      = $wpdb->prefix . 'wpbooklist_buyback_orders';
				$this->user_order_rows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $table_name WHERE wpuserid=%s", $this->wpuserid ) );

				return true;

			} else {

				return false;

			}
		}

		/**
		 * Code for outputting the form the user will see if logged in - all their past/ actiev orders and whatnot.
		 */
		public function output_user_data_form() {

			$row_html          = '';
			$books_html_string = '';
			$order_tracker     = 0;
			$row_html          = '';
			$current_order_title_html = '';

			$default_html = '
				<div id="wpbooklist-buyback-userpage-welcome-div">	
					<div id="wpbooklist-buyback-userpage-welcome-div-name-title">Welcome ' . $this->user_data->firstname . '!</div>
					<div id="wpbooklist-buyback-userpage-welcome-div-logout-reset-div">
						<button id="wpbooklist-buyback-userpage-welcome-div-logout-button">Log Out</button>
					</div>
				</div>
			';

			// If the user has active orders.
			if ( count( $this->user_order_rows ) > 0 ) {

				$current_order_title_html = '
					<div id="wpbooklist-buyback-userpage-welcome-div-name-title">Here are your currently active BooksaBillions orders:</div>
				';

				foreach ( $this->user_order_rows as $key => $order ) {

					// If the order has any status besides Completed.
					if ( 'Completed' !== $order->orderstatus ) {

						$order_tracker++;

						$order->books = rtrim( $order->books, '----' );
						if ( false !== stripos( $order->books, '----' ) ) {
							$books = explode( '----', $order->books );
							foreach ( $books as $key => $valueindiv ) {
								$tempvalue = explode( ';;;', $valueindiv );

								$order_total   += $tempvalue[3];
								$data_dbstring += $tempvalue[0] . ';;;' . $tempvalue[1] . ';;;' . $tempvalue[2] . ';;;' . $tempvalue[3] . '----';

								$books_html_string = $books_html_string . '<div class="wpbooklist-buyback-cart-div-row"><div class="wpbooklist-buyback-cart-img-div"><img class="wpbooklist-buyback-cart-img-actual" src="' . $tempvalue[2] . '"></div><div style="margin-left:4px;" class="wpbooklist-buyback-cart-title-div"><p class="wpbooklist-buyback-cart-title-actual">' . stripslashes( stripslashes( $tempvalue[1] ) ) . '</p><p class="wpbooklist-buyback-cart-title-isbn-actual">' . $tempvalue[0] . '</p></div><div style="margin-left:3px;" class="wpbooklist-buyback-cart-value-div"><p class="wpbooklist-buyback-cart-value-actual">$' . $tempvalue[3] . '</p><div class="wpbooklist-buyback-cart-value-remove-div" data-dbstring="' . $data_dbstring . '"></div></div></div>';
							}
						} else {
							$books = explode( ';;;', $order->books );

							$order_total   += $books[3];
							$data_dbstring += $books[0] . ';;;' . $books[1] . ';;;' . $books[2] . ';;;' . $books[3];

							$books_html_string = $books_html_string . '<div class="wpbooklist-buyback-cart-div-row"><div class="wpbooklist-buyback-cart-img-div"><img class="wpbooklist-buyback-cart-img-actual" src="' . $books[2] . '"></div><div style="margin-left:4px;" class="wpbooklist-buyback-cart-title-div"><p class="wpbooklist-buyback-cart-title-actual">' . stripslashes( stripslashes( $books[1] ) ) . '</p><p class="wpbooklist-buyback-cart-title-isbn-actual">' . $books[0] . '</p></div><div style="margin-left:3px;" class="wpbooklist-buyback-cart-value-div"><p class="wpbooklist-buyback-cart-value-actual">$' . $books[3] . '</p><div class="wpbooklist-buyback-cart-value-remove-div" data-dbstring="' . $data_dbstring . '"></div></div></div>';

						}

						$row_html = $row_html . '
						<div class="wpbooklist-buyback-settings-row-actual">
							<div class="wpbooklist-buyback-settings-row-actual-inner-row">
								<div class="wpbooklist-buyback-settings-row-actual-inner-row-top">
									<p class="wpbooklist-buyback-settings-row-actual-inner-row-top-p1">Order #' . $order_tracker . '</p>
									<p class="wpbooklist-buyback-settings-row-actual-inner-row-top-p1">Order Status: ' . ucfirst( $order->orderstatus ) . '</p>
									<p class="wpbooklist-buyback-settings-row-actual-inner-row-top-p1">Payment Method: ' . ucfirst( $order->paymentmethod ) . '</p>
								</div>
								<div class="wpbooklist-buyback-settings-row-actual-inner-row-bottom">
									' . $books_html_string . '
								</div>
							</div>
							<div class="wpbooklist-buyback-settings-order-contact-div">
								<p>' . $order->firstname . ' ' . $order->lastname . '</p>
								<p>' . $order->streetaddress . ' ' . $order->city . ', ' . $order->state . ' ' . $order->zipcode . '</p>
								<p>' . $order->phone . ' ' . $order->email . '</p>
								<p>PayPal E-Mail: ' . $order->paypalemail . '</p>
								<p>Order Total: $' . number_format( $order_total, 2 ) . '</p>
							</div>
						</div>';

					}
				}
			} else {

				$current_order_title_html = '
				<div id="wpbooklist-buyback-userpage-no-orders-div">
					<p>Looks like you don\'t have any currently pending BooksaBillion orders!</p>
					<a href="https://booksabillions.net/">Click Here to get started!</a>
				</div>';
			}

			$final_html = $default_html . $current_order_title_html . $row_html;

			$this->final_form = $final_html;

		}

		/**
		 * Code for outputting the form the user will see if not logged in.
		 */
		public function output_login_register_form() {

			$this->final_form = '<p id="wpbooklist-buyback-checkout-title">Login or Register</p>
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
						<button class="wpbooklist-buyback-login-button-user-page" id="wpbooklist-buyback-login-button">Login</button>
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
						<button class="wpbooklist-buyback-login-button-user-page" id="wpbooklist-buyback-register-button">Register</button>
						<div class="wpbooklist-spinner" id="wpbooklist-spinner-buyback-register"></div>
						<div id="wpbooklist-buyback-login-reg-button-error"><p id="wpbooklist-buyback-register-button-error-p"></p></div>
					</div>
				</div>';


		}
	}

endif;
