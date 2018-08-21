<?php
/**
 * WPBookList WPBookList_Bookfinder_Form Tab Class
 *
 * @author   Jake Evans
 * @category ??????
 * @package  ??????
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_BuyBack_Orders_Form', false ) ) :
/**
 * WPBookList_Bookfinder_Form Class.
 */
class WPBookList_BuyBack_Orders_Form {

	public static function output_buyback_form(){

		// Perform check for previously-saved Amazon Authorization
		global $wpdb;
		$string2 = '';
		$table_name = $wpdb->prefix . 'wpbooklist_buyback_orders';
		$orders_results = $wpdb->get_results("SELECT * FROM $table_name");

		error_log(print_r($orders_results,TRUE));

		//0-20000-30-8;20001-30000-20-6;30001-400000-15-5
// bottomrange toprange percentage minimum dollar value

		$row_html = '';
		$disabledCount = 0;
		foreach ($orders_results as $key => $value) {

			// Build books HTML
			//9780060005696;;;The Paradox of Choice: Why More Is Less;;;https://images-na.ssl-images-amazon.com/images/I/41i%2B8IPnk0L.jpg;;;0.25----

			//9780060005696;;;The Paradox of Choice: Why More Is Less;;;https://images-na.ssl-images-amazon.com/images/I/41i%2B8IPnk0L.jpg;;;0.25----9780060005696;;;The Paradox of Choice: Why More Is Less;;;https://images-na.ssl-images-amazon.com/images/I/41i%2B8IPnk0L.jpg;;;0.25----
			$order_total = 0.00;
			$books_html_string = '';
			$data_dbstring = '';
			$value->books = rtrim($value->books, '----');
			$isbn_string = '';
			if(stripos($value->books, '----') !== false){
				$books = explode('----', $value->books);
				foreach ($books as $key => $valueindiv) {
					$tempvalue = explode(';;;', $valueindiv);

					$order_total += $tempvalue[3];
					$data_dbstring += $tempvalue[0].';;;'.$tempvalue[1].';;;'.$tempvalue[2].';;;'.$tempvalue[3] . '----';

					$books_html_string = $books_html_string.'<div class="wpbooklist-buyback-cart-div-row"><div class="wpbooklist-buyback-cart-img-div"><img class="wpbooklist-buyback-cart-img-actual" src="'.$tempvalue[2].'"></div><div style="margin-left:4px;" class="wpbooklist-buyback-cart-title-div"><p class="wpbooklist-buyback-cart-title-actual">'.stripslashes(stripslashes($tempvalue[1])).'</p><p class="wpbooklist-buyback-cart-title-isbn-actual">'.$tempvalue[0].'</p></div><div style="margin-left:3px;" class="wpbooklist-buyback-cart-value-div"><p class="wpbooklist-buyback-cart-value-actual">$'.$tempvalue[3].'</p><div class="wpbooklist-buyback-cart-value-remove-div" data-orderid="'.$value->ID.'" data-isbntoremove="'.$tempvalue[0].'"><img class="wpbooklist-buyback-cart-value-remove-img-actual" src="' . BUYBACK_ROOT_IMG_ICONS_URL . 'cancel-button.svg"><p style="margin-left:3px;" class="wpbooklist-buyback-cart-value-remove-text-actual">Remove Title</p></div></div></div>';

					// Build the total isbn string to recalculate prices
					$isbn_string .= ',' . $tempvalue[0];

				}

			} else {
				$books = explode(';;;', $value->books);

				$order_total += $books[3];
				$data_dbstring += $books[0].';;;'.$books[1].';;;'.$books[2].';;;'.$books[3];

				$books_html_string = $books_html_string.'<div class="wpbooklist-buyback-cart-div-row"><div class="wpbooklist-buyback-cart-img-div"><img class="wpbooklist-buyback-cart-img-actual" src="'.$books[2].'"></div><div style="margin-left:4px;" class="wpbooklist-buyback-cart-title-div"><p class="wpbooklist-buyback-cart-title-actual">'.stripslashes(stripslashes($books[1])).'</p><p class="wpbooklist-buyback-cart-title-isbn-actual">'.$books[0].'</p></div><div style="margin-left:3px;" class="wpbooklist-buyback-cart-value-div"><p class="wpbooklist-buyback-cart-value-actual">$'.$books[3].'</p><div class="wpbooklist-buyback-cart-value-remove-div" data-orderid="'.$value->ID.'" data-isbntoremove="'.$books[0].'"><img class="wpbooklist-buyback-cart-value-remove-img-actual" src="' . BUYBACK_ROOT_IMG_ICONS_URL . 'cancel-button.svg"><p style="margin-left:3px;" class="wpbooklist-buyback-cart-value-remove-text-actual">Remove Title</p></div></div></div>';

					// Build the total isbn string to recalculate prices
					$isbn_string .= ',' . $books[0];

			}

			// Build select string
			switch ($value->orderstatus) {
				case 'initial':
					$status_select = '
						<select id="wpbooklist-buyback-select-id-'.$value->ID.'">
							<option default disabled>Set The Order Status...</option>
							<option selected>Initial</option>
							<option>Approved - E-Mail Customer</option>
							<option>Payment Submitted</option>
							<option>Payment Confirmed</option>
							<option>Awaiting Shipment</option>
							<option>Shipment in Transit</option>
							<option>Shipment Received</option>
							<option>Awaiting Customer Response</option>
							<option>Pending</option>
							<option>Completed</option>
						</select>';
				break;
				case 'Payment Submitted':
					$status_select = '
						<select id="wpbooklist-buyback-select-id-'.$value->ID.'">
							<option default disabled>Set The Order Status...</option>
							<option>Initial</option>
							<option>Approved - E-Mail Customer</option>
							<option selected>Payment Submitted</option>
							<option>Payment Confirmed</option>
							<option>Awaiting Shipment</option>
							<option>Shipment in Transit</option>
							<option>Shipment Received</option>
							<option>Awaiting Customer Response</option>
							<option>Pending</option>
							<option>Completed</option>
						</select>';
				break;
				case 'Payment Confirmed':
					$status_select = '
						<select id="wpbooklist-buyback-select-id-'.$value->ID.'">
							<option default disabled>Set The Order Status...</option>
							<option>Initial</option>
							<option>Approved - E-Mail Customer</option>
							<option>Payment Submitted</option>
							<option selected>Payment Confirmed</option>
							<option>Awaiting Shipment</option>
							<option>Shipment in Transit</option>
							<option>Shipment Received</option>
							<option>Awaiting Customer Response</option>
							<option>Pending</option>
							<option>Completed</option>
						</select>';
				break;
				case 'Awaiting Shipment':
					$status_select = '
						<select id="wpbooklist-buyback-select-id-'.$value->ID.'">
							<option default disabled>Set The Order Status...</option>
							<option>Initial</option>
							<option>Approved - E-Mail Customer</option>
							<option>Payment Submitted</option>
							<option>Payment Confirmed</option>
							<option selected>Awaiting Shipment</option>
							<option>Shipment in Transit</option>
							<option>Shipment Received</option>
							<option>Awaiting Customer Response</option>
							<option>Pending</option>
							<option>Completed</option>
						</select>';
				break;
				case 'Shipment in Transit':
					$status_select = '
						<select id="wpbooklist-buyback-select-id-'.$value->ID.'">
							<option default disabled>Set The Order Status...</option>
							<option>Initial</option>
							<option>Approved - E-Mail Customer</option>
							<option>Payment Submitted</option>
							<option>Payment Confirmed</option>
							<option>Awaiting Shipment</option>
							<option selected>Shipment in Transit</option>
							<option>Shipment Received</option>
							<option>Awaiting Customer Response</option>
							<option>Pending</option>
							<option>Completed</option>
						</select>';
				break;
				case 'Shipment Received':
					$status_select = '
						<select id="wpbooklist-buyback-select-id-'.$value->ID.'">
							<option default disabled>Set The Order Status...</option>
							<option>Initial</option>
							<option>Approved - E-Mail Customer</option>
							<option>Payment Submitted</option>
							<option>Payment Confirmed</option>
							<option>Awaiting Shipment</option>
							<option>Shipment in Transit</option>
							<option selected>Shipment Received</option>
							<option>Awaiting Customer Response</option>
							<option>Pending</option>
							<option>Completed</option>
						</select>';
				break;
				case 'Awaiting Customer Response':
					$status_select = '
						<select id="wpbooklist-buyback-select-id-'.$value->ID.'">
							<option default disabled>Set The Order Status...</option>
							<option>Initial</option>
							<option>Approved - E-Mail Customer</option>
							<option>Payment Submitted</option>
							<option>Payment Confirmed</option>
							<option>Awaiting Shipment</option>
							<option>Shipment in Transit</option>
							<option>Shipment Received</option>
							<option selected>Awaiting Customer Response</option>
							<option>Pending</option>
							<option>Completed</option>
						</select>';
				break;
				case 'Pending':
					$status_select = '
						<select id="wpbooklist-buyback-select-id-'.$value->ID.'">
							<option default disabled>Set The Order Status...</option>
							<option>Initial</option>
							<option>Approved - E-Mail Customer</option>
							<option>Payment Submitted</option>
							<option>Payment Confirmed</option>
							<option>Awaiting Shipment</option>
							<option>Shipment in Transit</option>
							<option>Shipment Received</option>
							<option>Awaiting Customer Response</option>
							<option selected>Pending</option>
							<option>Completed</option>
						</select>';
				break;
				case 'Completed':
					$status_select = '
						<select id="wpbooklist-buyback-select-id-'.$value->ID.'">
							<option default disabled>Set The Order Status...</option>
							<option>Initial</option>
							<option>Approved - E-Mail Customer</option>
							<option>Payment Submitted</option>
							<option>Payment Confirmed</option>
							<option>Awaiting Shipment</option>
							<option>Shipment in Transit</option>
							<option>Shipment Received</option>
							<option>Awaiting Customer Response</option>
							<option>Pending</option>
							<option selected>Completed</option>
						</select>';
					break;
				case 'Approved - E-Mail Customer':
					$status_select = '
						<select id="wpbooklist-buyback-select-id-'.$value->ID.'">
							<option default disabled>Set The Order Status...</option>
							<option>Initial</option>
							<option>Payment Submitted</option>
							<option>Payment Confirmed</option>
							<option selected>Awaiting Shipment</option>
							<option>Shipment in Transit</option>
							<option>Shipment Received</option>
							<option>Awaiting Customer Response</option>
							<option>Pending</option>
							<option>Completed</option>
						</select>';
					break;
				default:
					$status_select = '
						<select id="wpbooklist-buyback-select-id-'.$value->ID.'">
							<option default disabled>Set The Order Status...</option>
							<option>Initial</option>
							<option>Approved - E-Mail Customer</option>
							<option>Payment Submitted</option>
							<option>Payment Confirmed</option>
							<option>Awaiting Shipment</option>
							<option>Shipment in Transit</option>
							<option>Shipment Received</option>
							<option>Awaiting Customer Response</option>
							<option>Pending</option>
							<option>Completed</option>
						</select>';
					break;
			}

			$disabledCount++;

			if($value->paypalemail == '' || $value->paypalemail == null){
				$value->paypalemail = 'N/A';
			}

			$row_html = $row_html . '
				<div class="wpbooklist-buyback-settings-row-actual"  id="wpbooklist-buyback-settings-row-actual-'.$value->ID.'">
					<div class="wpbooklist-buyback-settings-row-actual-inner-row">
						<div class="wpbooklist-buyback-settings-row-actual-inner-row-top">
							<p class="wpbooklist-buyback-settings-row-actual-inner-row-top-p1">Order #'.($key+1).'</p>
							<p class="wpbooklist-buyback-settings-row-actual-inner-row-top-p1">Order Status: '.ucfirst($value->orderstatus).'</p>
							<p class="wpbooklist-buyback-settings-row-actual-inner-row-top-p1">Payment Method: '.ucfirst($value->paymentmethod).'</p>
						</div>
						<div class="wpbooklist-buyback-settings-row-actual-inner-row-bottom">
							'.$books_html_string.'
						</div>
					</div>
					<div class="wpbooklist-buyback-settings-order-controls-div">
						<div class="wpbooklist-buyback-settings-set-order-status-div">
						<p>Set Order Status</p>
							'.$status_select.'
						</div>
						<div id="wpbooklist-buyback-settings-add-delete-div">

							<button data-orderid="'.$value->ID.'" data-dbstring="'.$data_dbstring.'" data-allisbns="' . $isbn_string . '" class="wpbooklist-buyback-settings-recalculate-order-row-div" id="wpbooklist-buyback-settings-recalculate-order-row-div-'.$value->ID.'">Recalculate Prices</button>
							<button data-orderid="'.$value->ID.'" data-dbstring="'.$data_dbstring.'" class="wpbooklist-buyback-settings-save-changes-row-div">Save Changes</button>
							<button data-orderid="'.$value->ID.'" data-dbstring="'.$data_dbstring.'" class="wpbooklist-buyback-settings-delete-order-row-div">Delete Order</button>
							<div class="wpbooklist-spinner" id="wpbooklist-spinner-buyback"></div>
						</div>
					</div>
					<div class="wpbooklist-buyback-settings-order-contact-div">
						<p>'.$value->firstname.' '.$value->lastname.'</p>
						<p>'.$value->streetaddress.' '.$value->city. ', '.$value->state.' '.$value->zipcode.'</p>
						<p>'.$value->phone.' '.$value->email.'</p>
						<p>PayPal E-Mail: '.$value->paypalemail.'</p>
						<p>Order Total: $<span id="wpbooklist-buyback-admin-order-total">'.number_format($order_total, 2).'</span></p>
					</div>
				</div>';
		}

		$string1 = '<div id="wpbooklist-buyback-container">
			<p id="wpbooklist-buyback-instructional">Here you view and manage your WPBookList BuyBack orders<br/><br/><span</span></p>
			<div id="wpbooklist-buyback-settings-cont"> 

				<div id="wpbooklist-buyback-settings-rows-holder">
					'.$row_html.'
				</div>
			</div>
		</div>';


				

		return $string1;
	}
}

endif;