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

if ( ! class_exists( 'WPBookList_BuyBack_Form', false ) ) :
/**
 * WPBookList_Bookfinder_Form Class.
 */
class WPBookList_BuyBack_Form {

	public static function output_buyback_form(){

		// Perform check for previously-saved Amazon Authorization
		global $wpdb;
		$string2 = '';
		$table_name = $wpdb->prefix . 'wpbooklist_buyback_settings';
		$settings_results = $wpdb->get_results("SELECT * FROM $table_name");

		error_log(print_r($settings_results,TRUE));

		//0-20000-30-8;20001-30000-20-6;30001-400000-15-5
// bottomrange toprange percentage minimum dollar value

		$row_html = '';
		$disabledCount = 0;
		foreach ($settings_results as $key => $value) {

			$value = explode(';', $value->rankcalcs);
			foreach ($value as $key => $value2) {

				$disabledCount++;

				$split_vals = explode('-', $value2);

				$row_html = $row_html . '
					<div class="wpbooklist-buyback-settings-row-actual">
						<div class="wpbooklist-buyback-settings-row-actual-inner-row">
							<div class="wpbooklist-buyback-settings-row-actual-inner-row-top">
								<p class="wpbooklist-buyback-settings-row-actual-inner-row-top-p1">Amazon Sales Ranking</p>
								<p class="wpbooklist-buyback-settings-row-actual-inner-row-top-p2">Percentage of Amazon Book Price to offer</p>
							</div>
							<div class="wpbooklist-buyback-settings-row-actual-inner-row-bottom">
								<p class="wpbooklist-buyback-settings-row-actual-inner-row-bottom-1">From: 
									<input class="wpbooklist-buyback-settings-input" id=wpbooklist-buyback-settings-input-from-'.$key.' type="number" value="'.$split_vals[0].'" />To: 
									<input class="wpbooklist-buyback-settings-input" id=wpbooklist-buyback-settings-input-to-'.$key.' type="number" value="'.$split_vals[1].'" /></p>
								<p class="wpbooklist-buyback-settings-row-actual-inner-row-bottom-2">Percentage:
									<input class="wpbooklist-buyback-settings-input-perc" id=wpbooklist-buyback-settings-input-perc-'.$key.' type="number" value="'.$split_vals[2].'" />%</p>
								<p class="wpbooklist-buyback-settings-row-actual-inner-row-bottom-3">Threshold:
									<input class="wpbooklist-buyback-settings-input-perc" id=wpbooklist-buyback-settings-input-threshold-'.$key.' type="number" step="0.01" value="'.$split_vals[3].'" /></p>
							</div>
						</div>
					</div>';
			}
		}

		$string1 = '<div id="wpbooklist-buyback-container">
			<p id="wpbooklist-buyback-instructional">Here you can specify certain pricing thresholds and what percentage to offer your visitors for their books.<br/><br/><span</span></p>';

			$buttonAtts = '';
			if ($disabledCount <= 1){

				$buttonAtts = '<button disabled id="wpbooklist-buyback-settings-delete-row-div">';

			} else {
				$buttonAtts = '<button id="wpbooklist-buyback-settings-delete-row-div">';
			}

		$string2 = '
			<div id="wpbooklist-buyback-settings-cont"> 

				<div id="wpbooklist-buyback-settings-rows-holder">
					'.$row_html.'
					<div id="wpbooklist-buyback-settings-add-delete-div">
						<button id="wpbooklist-buyback-settings-add-row-div">Add Row</button>
						'.$buttonAtts.'Delete Row</button>
					</div>
				</div>
			</div>
			<div id="wpbooklist-buyback-settings-save-div">
				<button>Save Pricing Settings</button>
				<div class="wpbooklist-spinner" id="wpbooklist-spinner-buyback"></div>
			</div>
		</div>';

				

		return $string1.$string2;
	}
}

endif;