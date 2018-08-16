<?php
/**
 * WPBookList WPBookList_Buyback_Form Tab Class
 *
 * @author   Jake Evans
 * @category ??????
 * @package  ??????
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Buyback_Form', false ) ) :
/**
 * WPBookList_Buyback_Form Class.
 */
class WPBookList_Buyback_Form {

	public static function output_buyback_form(){

		// Perform check for previously-saved Amazon Authorization
		global $wpdb;
		$string2 = '';
		$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
		$opt_results = $wpdb->get_row("SELECT * FROM $table_name");

		$table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
		$db_row = $wpdb->get_results("SELECT * FROM $table_name");

		$string1 = '<div id="wpbooklist-buyback-container">
			<p id="wpbooklist-buyback-instructional">Simply enter a title, author, or both in the fields below, click the \'Find Book\' button, and select the books to add to your <span class="wpbooklist-color-orange-italic">WPBookList</span> Libraries!<br/><br/><span ';

				if($opt_results->amazonauth == 'true'){ 
					$string2 = 'style="display:none;"';
				} else {
					$string2 = '';
				}

		$string3 = ' ></span></p>
      		<form id="wpbooklist-buyback-form" method="post" action="">
	          	<div id="wpbooklist-authorize-amazon-container">
	    			<table>';

	    			if($opt_results->amazonauth == 'true'){ 
						$string4 = '<tr style="display:none;"">
	    					<td><p id="auth-amazon-question-label">Authorize Amazon Usage?</p></td>
	    				</tr>
	    				<tr style="display:none;"">
	    					<td>
	    						<input checked type="checkbox" name="authorize-amazon-yes" />
	    						<label for="authorize-amazon-yes">Yes</label>
	    						<input type="checkbox" name="authorize-amazon-no" />
	    						<label for="authorize-amazon-no">No</label>
	    					</td>
	    				</tr>';
					} else {
						$string4 = '<tr>
	    					<td><p id="auth-amazon-question-label">Authorize Amazon Usage?</p></td>
	    				</tr>
	    				<tr>
	    					<td>
	    						<input type="checkbox" name="authorize-amazon-yes" />
	    						<label for="authorize-amazon-yes">Yes</label>
	    						<input type="checkbox" name="authorize-amazon-no" />
	    						<label for="authorize-amazon-no">No</label>
	    					</td>
	    				</tr>';
					}

					$string5 = '</table>
		    		</div>
		    		<div id="wpbooklist-buyback-div-container">
		    			<div id="wpbooklist-buyback-search-title">
		    				<div>
		    					<label>Enter an ISBN/ASIN Number Below:</label>
		    					<input id="wpbooklist-buyback-isbn" placeholder="ISBN/ASIN Number" type="text"   />
		    				</div>'
		    				/*'<div>
		    					<label>Enter a Title</label>
		    					<input id="wpbooklist-buyback-title-input" placeholder="Search by Title" type="text"   />
		    				</div>
		    				<div>
		    					<label>Enter an Author</label>
		    					<input id="wpbooklist-buyback-author-input" placeholder="Search by Author" type="text"   />
		    				</div>'*/
		    				.'<button id="wpbooklist-buyback-search-button">Find Book</button>
		    			</div>
		    			<div class="wpbooklist-spinner" id="wpbooklist-spinner-buyback"></div>
		    			<div id="wpbooklist-buyback-status-div"></div>
			    		<div id="wpbooklist-buyback-div-for-hiding-scroll">
			    			<div id="wpbooklist-buyback-title-response"></div>
			    		</div>
		    			<div id="wpbooklist-buyback-results-div"></div>
		    		</div>';

		    		$string8 = '</form><div id="wpbooklist-buyback-checkout-div"></div></div>';

		return $string1.$string2.$string3.$string4.$string5.$string8;
	}
}

endif;