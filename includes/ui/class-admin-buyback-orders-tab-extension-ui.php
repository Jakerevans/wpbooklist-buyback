<?php
/**
 * WPBookList Storefront Tab
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Buyback_Orders', false ) ) :
/**
 * WPBookList_Admin_Menu Class.
 */
class WPBookList_Buyback_Orders {

    public function __construct() {
        require_once(CLASS_DIR.'class-admin-ui-template.php');
        require_once(BUYBACK_CLASS_DIR.'class-buyback-orders-form.php');
        // Instantiate the class
        $this->template = new WPBookList_Admin_UI_Template;
        $this->form = new WPBookList_BuyBack_Orders_Form;
        $this->output_open_admin_container();
        $this->output_tab_content();
        $this->output_close_admin_container();
        $this->output_admin_template_advert();
    }

    private function output_open_admin_container(){
        $title = 'BuyBack Orders';
        $icon_url = BUYBACK_ROOT_IMG_ICONS_URL.'businessman-working-online-with-a-laptop.svg';
        echo $this->template->output_open_admin_container($title, $icon_url);
    }

    private function output_tab_content(){
        echo $this->form->output_buyback_form();
    }

    private function output_close_admin_container(){
        echo $this->template->output_close_admin_container();
    }

    private function output_admin_template_advert(){
        echo $this->template->output_template_advert();
    }


}
endif;

// Instantiate the class
$cm = new WPBookList_Buyback_Orders;