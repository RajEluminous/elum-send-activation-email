<?php 
/**
 * Plugin Name: Elum Send Activation Email
 * Plugin URI: https://eluminoustechnologies.com/
 * Description: This plugin shows the list of inactive users who have used promocode and sends activation email(s) to them.
 * Version: 1.0.0
 * Text Domain: elum-send-activation-emails
 * Author: Rajendra Mahajan
 * Author URI: https://eluminoustechnologies.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
  
 // Plugin directory url.
 define('ETSAEMAIL_URL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );
 
    /**
     * For development: To display error, set WP_DEBUG to true.
     * In production: set it to false
    */
 define('WP_DEBUG',true);
 
 // Get absolute path 
 if ( !defined('ETSAEMAIL_ABSPATH'))
    define('ETSAEMAIL_ABSPATH', dirname(__FILE__) . '/');

 // Get absolute path 
if ( !defined('ABSPATH'))
    define('ABSPATH', dirname(__FILE__) . '/');

/**
 *  Current plugin version.
 */
 if ( ! defined( 'ETSAEMAIL_VER' ) ) {
	define( 'ETSAEMAIL_VER', '1.0.0' );
 }
  
 define('ETSAEMAIL_TEMPLATES',ETSAEMAIL_ABSPATH.'templates');
 define('ETSAEMAIL_PAGE_TITLE','Send Activation Email to Inactive Users (Promo code)');
 
add_action( 'template_redirect', 'etsaemail_inspect_page_id' );
function etsaemail_inspect_page_id() {
    $page_object = get_queried_object();
   // var_dump($page_object);

    $page_id = get_queried_object_id();
    //echo $page_id;
	
}

 // Main Class	etsaemail
 class ElumSendActivationEmail {
	
	var $etsaemail_page_menu;
	 
	// for user info
	var $etsaemail_uid;
	var $etsaemail_user_login;
	var $etsaemail_display_name;
	var $etsaemail_user_email;
	 
		
	function __construct() {	
		global $wpdb;
		global $wp;
       		 
		//Initial loading				 		 
		add_action('admin_init',array($this,'init'),0);	 
		add_action('admin_menu', array($this, 'etsaemail_admin_menu')); 		  
	}	
		 
	// initial processing	
	function init() {
		// if session is not start, then start it.
		if(!session_id()) {
			session_start();
		} 
		$this->load(); 		
	} 
	
	// to perform any action on plugin page load.
	function load() {		
		  
	}
	 	 
	// add menu to admin
	function etsaemail_admin_menu() {
		add_menu_page('Send Activation Email(PC)','Send Activation Email','administrator', __FILE__,array($this,'etsaemail_admin_uploaded_docs_page'),'',100);   		 
    }
	
	// Display uploaded document listing, include template file
	function etsaemail_admin_uploaded_docs_page() {	
		global $wpdb;
						
		// get all users meta information	
		$fivesdrafts = $wpdb->get_results("SELECT * FROM $wpdb->usermeta");
		$rowcount = $wpdb->num_rows;
			
		$arrUserMeta = array();
		/*foreach ($fivesdrafts as $fivesdraft) { 
			if(!$arrUserMeta[$fivesdraft->user_id]['id']) {
				$arrUserMeta[$fivesdraft->user_id]['id'] = $fivesdraft->user_id;
			}
			else { 	
				$arrUserMeta[$fivesdraft->user_id][$fivesdraft->meta_key] = $fivesdraft->meta_value;				 
			}	
		}
		*/ 
		$users_count = get_users( array('fields' => array('ID'), 
								  'meta_key' => 'activated',
								  'meta_value' => 'false'		
								  ));
		/* print_r($users_count);
		   die();	 
		*/
			
		//----- for pagination ---------//
		$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;      

        $limit = 10; // number of rows in page
        $offset = ( $pagenum - 1 ) * $limit;
        $total = count($users_count);
        $num_of_pages = ceil( $total / $limit );
		//---------- end pagination -------//
		
		$users = get_users( array('fields' => array('ID'), 
								  'meta_key' => 'usr_vdoc_isapproved', 
								  'offset' => $offset,
								  'number' => $limit
								  ));
		 	
		foreach($users as $user_id){
			$usr_meta = get_user_meta ( $user_id->ID);
			//print_r($usr_meta);
			$arrUserMeta[$user_id->ID]['id'] = $user_id->ID; 		
			$arrUserMeta[$user_id->ID]['first_name'] = current($usr_meta['first_name']);
			$arrUserMeta[$user_id->ID]['last_name'] = current($usr_meta['last_name']);
			$arrUserMeta[$user_id->ID]['usr_vdoc_img_passport'] = current($usr_meta['usr_vdoc_img_passport']);
			$arrUserMeta[$user_id->ID]['usr_vdoc_img_drivinglicense'] = current($usr_meta['usr_vdoc_img_drivinglicense']);
			$arrUserMeta[$user_id->ID]['usr_vdoc_img_idcard'] = current($usr_meta['usr_vdoc_img_idcard']);
			$arrUserMeta[$user_id->ID]['usr_vdoc_img_bill'] = current($usr_meta['usr_vdoc_img_bill']);
			$arrUserMeta[$user_id->ID]['usr_vdoc_isapproved'] = current($usr_meta['usr_vdoc_isapproved']);			
		} 
		 
		
		 $page_links = paginate_links( array(
            'base' => add_query_arg( 'pagenum', '%#%' ),
            'format' => '',
            'prev_text' => __( '&laquo;', 'text-domain' ),
            'next_text' => __( '&raquo;', 'text-domain' ),
            'total' => $num_of_pages,
            'current' => $pagenum
        ) );
		$page_pagination_nav = "";
        if ( $page_links ) {
            $page_pagination_nav = '<div class="tablenav" style="width: 99%; float:right"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
        }
	
		
		require_once(ETSAEMAIL_TEMPLATES . '/inactive_user_listing_template.php');
	}			

	// To check and return valid img path
	function eluTransGetImgPath($edocimgpath) {
		 
		if(file_exists(str_replace(site_url(),'..',$edocimgpath))) {
			return $edocimgpath;
		} else {
			return ETSAEMAIL_URL.'/assets/noimg.png';
		}
	}
	
	
 } // Classe
 
 // Call class
 new ElumSendActivationEmail();
 
?>