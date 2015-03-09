<?php
/**
 * Plugin Name: Pocket WP
 * Plugin URI: http://ciaranmahoney.me/pocket-wp
 * Description: Adds a shortcode and widget which allows you to display your pocket links in a WordPress page/post.
 * Version: 0.4.3
 * Author: Ciaran Mahoney
 * Author URI: http://ciaranmahoney.me/
 * License: GPL2
 */

/*  Copyright 2014  Ciaran Mahoney - @ciaransm | me@ciaranmahoney.me

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
defined('ABSPATH') or die("No script kiddies please!");

// Set the version of this plugin
if( ! defined( 'POCKET_WP' ) ) {
  define( 'POCKET_WP', '0.4.3' );
}

class PocketWP {

	public function __construct() 
	{
		
		// Display the admin notification on activation (based on plugin version stored in database)
 	   	add_action( 'admin_notices', array( $this, 'pwp_activation_notice' ));

		// Initialize options page and add to menu
		add_action( 'admin_menu', array($this, 'pwp_add_admin_menu' ));
		add_action( 'admin_init', array($this,'pwp_settings_init' ));

		// Create AJAX call for authorize button.
		add_action( 'admin_footer', array($this, 'pwp_authorize_button' )); 

		// Function to convert the request token to an access token
		add_action( 'wp_ajax_pwp_click_authorization_button', array($this,'pwp_get_access_token' ));

		// Adding a shortcode to display Pocket links in a post/page
		add_shortcode('pocket_links', array($this,'pwp_shortcode' ));

		//Register css
		add_action( 'wp_enqueue_scripts', array($this, 'pwp_add_stylesheet' ));

		// Add link to settings page in plugins list
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'pwp_plugin_action_links' ));

	}

	public function pwp_activation_notice() {
		if( !get_option('pwp_activation_run')){

			add_option('pwp_activation_run', true);
		    $html = '<div class="updated">';
		    $html .= '<p>';
			$html .= 'Thank you for installing Pocket WP. You will need to configure the plugin before it will work. <a href="options-general.php?page=pocket_wp" class="button button-primary">Configure Plugin</a></strong>';
			$html .= '</p>';
		    $html .= '</div>';
		    echo $html;
		}
  	}

  // 	public static function pwp_deactivation_notice() {
		// if( false == delete_option('pwp_activation_run')){

		//     $html = '<div class="error">';
		//     $html .= '<p>';
		// 	$html .= 'Error deactivating Pocket WP. Please try again.';
		// 	$html .= '</p>';
		//     $html .= '</div>';
		//     echo $html;
		// }
  // 	}

	// Register stylesheet
	public function pwp_add_stylesheet() {
	    wp_register_style( 'pwp-style', plugins_url('style.css', __FILE__) );
	    wp_enqueue_style( 'pwp-style' );
	}

	// Add link to the plugin settings page in plugin list
	public function pwp_plugin_action_links( $links ) {
	   $links[] = '<a href="'. get_admin_url(null, 'options-general.php?page=pocket_wp') .'">Settings</a>';
	   return $links;
	}

	// Create an options page for Pocket WP consumer key
	public function pwp_add_admin_menu() { 
		add_options_page( 'Pocket WP', 'Pocket WP', 'manage_options', 'pocket_wp', array($this,'pwp_options_page' ));
	}

	public function pwp_settings_exist() { 
		if( false == get_option( 'pocket_wp_settings' ) ) { 
			update_option( 'pocket_wp_settings' );
		}
	}

	public function pwp_settings_init( ){ 
		register_setting( 'pwp_pluginPage', 'pwp_settings' );

		add_settings_section(
			'pwp_pluginPage_section', 
			__( '', 'wordpress' ), 
			array($this,'pwp_settings_section_callback'), 
			'pwp_pluginPage'
		);

		add_settings_field( 
			'pwp_consumer_key_field', 
			__( 'Pocket Consumer Key', 'wordpress' ), 
			array($this,'pwp_consumer_key_field_render'), 
			'pwp_pluginPage', 
			'pwp_pluginPage_section' 
		);
	}

	public function pwp_settings_section_callback(  ) { 
		echo __( 
			'
				<h2>Pocket WP</h2>
				<h3>Setup Instructions</h3>

				<ol>
					<li>To get started you will need to create an application on the Pocket Developer\'s site. Visit the <a href="http://getpocket.com/developer/apps/new">Pocket Developers New App page</a> and create your application. Ensure you select <strong><em>Retrieve</em></strong> under the <strong><em>Permissions </em></strong>section and <strong><em>Web</em></strong> under the <strong><em>Platforms</em></strong> section. 
					</li>
					<li>Once you have done this, copy your <strong><em>Consumer Key</em></strong> from the list of apps and paste into the field below.</li>
					<li>Click <em><strong>Save Changes</em></strong> to save the key and get a <strong><em>Request Token</em></strong> from Pocket. You may be sent to Pocket to authorize your app (if so sign in and click the yellow Authorize button).</li>
					<li>After you have authorized your app with Pocket, you will be brought back to this page.</li>
					<li>Click the grey <strong><em>GET ACCESS KEY</strong></em> button below to generate an access key. <strong>Please do this once only.</strong> You should get a popup to confirm your access key was authenticated successfully. If you get the authentication failed message, you just need to click <strong><em>Save Changes</strong></em> and then <strong><em>GET ACCESS KEY</strong></em> again. </li>
				</ol>
				<h3>Plugin Usage</h3>
					<p><strong>Shortcode</strong>: The basic shortcode is [pocket_links] and it accepts some optional arguments - count, excerpt, tag, credit, tag_list. <br>Example: [pocket_links count="5" tag="analytics" tag_list="yes" credit="no" excerpt="yes"]
					</p>
					<p><strong>Widget</strong>: The Widget is available to drag and drop into any widgetized area. It has four options - title, count, tag and author credit.
					</p>

				<p>For more instructions, please visit the <a href="http://ciaranmahoney.me/code/pocket-wp/?utm_campaign=pocket-wp&utm_source=pwp-options&utm_medium=wp-plugins"target="_blank">plugin site</a>. If you are having issues, please let me know on Twitter <a href="https://twitter.com/ciaransm">@ciaransm</a>.
				</p>
				<p>Plugin by <a href="http://ciaranmahoney.me/?utm_campaign=pocket-wp&utm_source=pwp-options&utm_medium=wp-plugins" target="_blank">Ciaran Mahoney</a></p>
				<h3>Plugin Setup</h3>

		   	', 'wordpress' );

	}

	public function pwp_consumer_key_field_render(  ) { 
		$pwp_options = get_option( 'pwp_settings' );
		$pwp_consumer_key = $pwp_options['pwp_consumer_key_field'];
		?>
		<input type='text' name='pwp_settings[pwp_consumer_key_field]' size="50" value='<?php echo $pwp_consumer_key; ?>'>

		<?php

		if( isset($_GET['settings-updated']) && $_GET['settings-updated'] == true ){
	       $this->pwp_get_request_token();
	   	}
	}

	public function pwp_options_page(  ) { 
		?>
		<form action='options.php' method='post'>		
			<?php
			settings_fields( 'pwp_pluginPage' );
			do_settings_sections( 'pwp_pluginPage' );
			?>
			<?php
			submit_button();
			?>
			
		</form>
		
		<div id="pwp_get_access_key_button"><p><a href="#"class="button-secondary">GET ACCESS KEY</a></p></div>
		<?php

	} //End options page setup

	// cURL function
	public function pwp_cURL($url, $post, $returnstring) {
		$cURL = curl_init();
		curl_setopt($cURL, CURLOPT_URL, $url);
		curl_setopt($cURL, CURLOPT_HEADER, 0);
		curl_setopt($cURL, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded;charset=UTF-8', 'X-Accept: application/x-www-form-urlencoded'));
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($cURL, CURLOPT_TIMEOUT, 5);
		curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($cURL, CURLOPT_POST, count($post));
		curl_setopt($cURL, CURLOPT_POSTFIELDS, http_build_query($post));
		$output = curl_exec($cURL);

		if($errno = curl_errno($cURL)) {
	    	$error_message = curl_strerror($errno);
	    	echo "cURL error ({$errno}):\n {$error_message}";
		}

		curl_close($cURL);

		if ($returnstring){
			return $output; // Returns output as a string.

		} else { 
			return json_decode($output, true); // Provide alternative json output if array is needed (for displaying actual Pocket links).
		}
		
	} //End cURL function

	// Contact Pocket to get request token
	public function pwp_get_request_token(){
		$pwp_options = get_option( 'pwp_settings' );
		$pwp_consumer_key = $pwp_options['pwp_consumer_key_field']; // gets consumer key saved in option page.

		$pwp_options_url = site_url() . '/wp-admin/options-general.php?page=pocket_wp';

		$oAuthRequestToken = explode('=', $this->pwp_cURL(
		   'https://getpocket.com/v3/oauth/request',
		   array(
		  	 'consumer_key' => $pwp_consumer_key,
		  	 'redirect_uri' => $pwp_options_url
		   ), 
		   true
		 ));

		update_option( 'pwp_request_token', $oAuthRequestToken[1] );

		// (3) Redirect user to Pocket to continue authorization
		 echo '<meta http-equiv="refresh" content="0;url=https://getpocket.com/auth/authorize?request_token=' . urlencode($oAuthRequestToken[1]) . '&redirect_uri=' . urlencode(site_url()) . urlencode("/wp-admin/options-general.php?page=pocket_wp&pwpsuccess=true/");

	} // End contact Pocket to get access token

	// Write our JS for ajax call below here
	public function pwp_authorize_button () {
		?>

		<script type="text/javascript" >
		jQuery(document).ready(function($) {

			$("#pwp_get_access_key_button").click(function(){ 
				var data = {
					'action': 'pwp_click_authorization_button'
				};

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				$.post(ajaxurl, data, function(response) {

					if (response == 0) {
						window.location = "?page=pocket_wp&pwpaccess=true";
						console.log(getQueryVariable('pwpaccess'));
					} else {
						window.location = "?page=pocket_wp&pwpaccess=false";
						console.log(getQueryVariable('pwpaccess'));
					}				
				});
			});

			//Function to parse query string
			function getQueryVariable(variable){
		       var query = window.location.search.substring(1);
		       var vars = query.split("&");
		       for (var i=0;i<vars.length;i++) {
		               var pair = vars[i].split("=");
		               if(pair[0] == variable){return pair[1];}
		       }
		       return(false);
			};
			
			if(getQueryVariable('pwpaccess') == "true"){
				//If access key returns successfull, show success notice
				$('#pwp_get_access_key_button').hide().before('<div class="pwp_success" style="color:green;">Access key authentication was successfull. Setup complete!</div>');


			} else if (getQueryVariable('pwpaccess') == "false"){
				// If returns false, show failed notice.
				$('#pwp_get_access_key_button').before('<div class="pwp_warning" style="color:red;">Access key authentication failed. Please click save changes to retrieve a new request token, then try authenticating again.</div>');
			} else {
				$('#pwp_get_access_key_button').before('<div class="pwp_notice">Please click the GET ACCESS KEY button once only. If you get a failed message, try clicking Save Changes above, then try again.</div>');
			}
		});</script> 

		<?php
	}

	public function pwp_get_access_token(){
		$pwp_options = get_option( 'pwp_settings' );
		$pwp_consumer_key = $pwp_options['pwp_consumer_key_field'];
		$pwp_request_token = get_option('pwp_request_token');

		$pwp_oAuthRequest = $this->pwp_cURL('https://getpocket.com/v3/oauth/authorize', 
				array(
					'consumer_key' => $pwp_consumer_key,
					'code' => $pwp_request_token
					),
				true
				);

		$pwp_access_token = explode('&', $pwp_oAuthRequest);
		$pwp_access_token = $pwp_access_token[0];
		$pwp_access_token = explode('=', $pwp_access_token);
		$pwp_access_token = $pwp_access_token[1];

		update_option( 'pwp_access_token', $pwp_access_token );
		update_option( 'pwp_oauth_request', $pwp_oAuthRequest );

	}

	// Get Pocket links array
	public function pwp_get_links($pwp_count, $pwp_tag) {
		$pwp_options = get_option( 'pwp_settings' );
		$pwp_consumer_key = $pwp_options['pwp_consumer_key_field'];
		$pwp_access_token = get_option('pwp_access_token');

		$pwp_pocket_request = $this->pwp_cURL('https://getpocket.com/v3/get',
			array(
				'consumer_key' 	=> $pwp_consumer_key,
				'access_token' 	=> $pwp_access_token,
				'tag'			=> $pwp_tag,
				'detailType'	=> 'complete',
				'state'			=> 'all',
				'count'			=> $pwp_count
				),
			false
			);

		//Loop over cURL output
		$pwp_links_output = array();

	    if(is_array($pwp_pocket_request)){
		    foreach($pwp_pocket_request['list'] as $item){

		    	// Check if given url is set. If not, use resolved url.
		    	if ($item['given_url'] != ""){
		    		$pwp_url = $item['given_url'];

		    	} else{
		    		$pwp_url = $item['resolved_url'];
				}

		    	//Check if a title is set. If not just use url
		    	if ($item['resolved_title'] != ""){
		    		$pwp_title = $item['resolved_title'];

		    	} elseif ($pwp_title = $item['given_title'] != ""){
		    		$pwp_title = $item['given_title'];

		    	} else {
		    		$pwp_title = $item['given_url'];
		    	}

		    	// Check for excerpt
		    	if ($item['excerpt'] != ''){
		    		$pwp_excerpt = $item['excerpt'];
		    	} else {
		    		$pwp_excerpt = "Sorry, Pocket didn't save an excerpt for this link.";
		    	}

		    	// Check for tags
		    	if (isset($item['tags'] )){
		    		$pwp_tags = $item['tags'];
				} else {
					$pwp_tags = '';
				}

		    	array_push($pwp_links_output, 
		    		array($pwp_url, $pwp_title, $pwp_excerpt, $pwp_tags)
		    	);
		    }
			return $pwp_links_output;
		} 
	}

	public function pwp_shortcode ($atts, $content = null){
		extract( shortcode_atts( array(
								 'count' => '',
								 'tag' => '',
								 'excerpt' => '',
								 'tag_list' => '',
								 'credit' => ''
								), $atts 
				)
		);

		//Get the array that was extracted from the cURL request
		$pwp_items = $this->pwp_get_links($count, $tag);

		// Loop through array and get link details.
		if(is_array($pwp_items)){
			foreach($pwp_items as $item){
				$html[] = '<div class="pwp-links-shortcode">';

				$html[] = '<h4><a href="' . $item[0] . '" class="pwp_item_sc_link" target="_blank">' . $item[1] . '</a></h4>';
				
				//Display excerpt if excerpt is not set to no.	
			   	if (strtolower($excerpt) != 'no'){
			   		$html[] = '<p class="pwp_item_excerpt">' . $item[2] . '</p>';
			  	}

			  	// Display tag list if tag_list not set to no.
			  	if(strtolower($tag_list) != 'no') {
			  		if($item[3] != ""){
				  	  	$html[] = '<p class="pwp_tag_list">';
					  	foreach($item[3] as $tag) {
					  		$html[] = '<span class="pwp_tags">' . $tag['tag'] . '</span>';
					  	}
					  	$html[] ='</p>';

					} else {
						$html[] = '<p class="pwp_tag_list"><span class="pwp_tags">untagged</span></p>';
					}
		  		} else {
		  			$html[] ='<p class="pwp_tag_list"></p>';
		  		}
		  		$html[] = '</div>';
		  	}
		
		    if (strtolower($credit) == "yes") {
		    	// Display author credit links
		    	$html[] = '<p id="pwp_plugin_credit_sc"><a href="http://ciaranmahoney.me/code/pocket-wp/?utm_campaign=pocket-wp&utm_source=pwp-shortcode&utm_medium=wp-plugins" target="_blank">Pocket WP</a> by <a href="https://twitter.com/ciaransm" target="_blank">@ciaransm</a></p>';
		    } else { 
		    	// Do not show credit links unless user opts in.
			}

			return implode("\n", $html);

		} else {
			$html[] = '<div class="pwp-links-shortcode">';
			$html[] = 'Cannot retrieve feed from Pocket. Please ensure you have completed setup on the Pocket WP settings page in the WordPress backend (Settings > PocketWP)</div>';
			return implode("\n", $html);
		}

	} // end pwp_shortcode
} // end pocketwp class

// Display Pocket links in Widget
class Pwp_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		parent::__construct(
			'pwp_widget', // Base ID
			__('Pocket WP', 'text_domain'), // Name
			array( 'description' => __( 'Display Pocket links in a widget', 'text_domain' ), ) // Args
		);

		// register Pocket WP widget
		add_action( 'widgets_init', array($this,'register_pwp_widget' ));
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}

		//print_r( pwp_get_links($instance['count'], $instance['tag'])); used for testing only

		//Get the array that was extracted from the cURL request
		if(! empty( $instance['count'] ) ){
			$pwp_count = $instance['count'];

		} else {
			$pwp_count = '5';
		}

		$PocketWP = new PocketWP();
		$pwp_items = $PocketWP->pwp_get_links($pwp_count, $instance['tag']);

		// Loop through array and get link details.
		if(is_array($pwp_items)){

			echo '<ul class="pwp_widget_list">';
			foreach($pwp_items as $item){
				echo '<li><a href="' . $item[0] . '" class="pwp_item_widget_link" target="_blank">' . $item[1] . '</a>';
		  	}

		  	echo '</ul>';

			if($instance['credit'] == 'yes') {
				// If user opts in to give plugin author credit, display credit links.
				echo '<span id="pwp_plugin_credit_widget"><a href="http://ciaranmahoney.me/code/pocket-wp/?utm_campaign=pocket-wp&utm_source=pwp-widget&utm_medium=wp-plugins" target="_blank">Pocket WP</a> by <a href="https://twitter.com/ciaransm" target="_blank">@ciaransm</a></span>';
			} else {
		   	 	// Otherwise do nothing. Do not show credit is default setting.
		   	}
		} else {
			echo 'Cannot retrieve feed from Pocket. Please ensure you have completed setup on the Pocket WP settings page in the WordPress backend (Settings > PocketWP)';
		}


		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ])) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}

		if(isset($instance[ 'tag' ])) {
			$tag = $instance[ 'tag' ];
		} else {
			$tag = '';
		}

		if (isset($instance[ 'count' ])) {
			$count = $instance[ 'count' ];
		} else {
			$count = '';
		}

		if (isset($instance[ 'credit' ])) {
			$credit = $instance[ 'credit' ];
		} else {
			$credit = '';
		}

		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat pwp_widget_field" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">

		<label for="<?php echo $this->get_field_id('tag');?>"><?php _e('tag:'); ?> </label>
		<input class="widefat pwp_widget_field" id="<?php echo $this->get_field_id( 'tag' ); ?>" name="<?php echo $this->get_field_name( 'tag' ); ?>" type="text" value="<?php echo esc_attr( $tag ); ?>" placeholder="enter tag">

		<label for="<?php echo $this->get_field_id('count');?>"><?php _e('How many links do you want to show? (default is 5)'); ?> </label>
		<input class="widefat pwp_widget_field" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" placeholder="Enter number of links to show. Default is 5">

		<label for="<?php echo $this->get_field_id('credit');?>"><?php _e('Give plugin author credit?'); ?> </label>

		<label for="yes">Yes</label>
		<input class="widefat pwp_widget_field" id="<?php echo $this->get_field_id( 'credit' ); ?>-yes" name="<?php echo $this->get_field_name( 'credit' ); ?>" type="radio" value="yes" <?php if($credit == 'yes') echo 'checked';?> >

		<label for="no">No</label>
		<input class="widefat pwp_widget_field" id="<?php echo $this->get_field_id( 'credit' ); ?>-no" name="<?php echo $this->get_field_name( 'credit' ); ?>" type="radio" value="no" <?php if($credit == 'no') echo 'checked';?> >
		</p>
		<?php 
	}

	// Processing widget options on save
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['tag'] = ( ! empty( $new_instance['tag'] ) ) ? strip_tags( $new_instance['tag'] ) : '';
		$instance['count'] = ( ! empty( $new_instance['count'] ) ) ? strip_tags( $new_instance['count'] ) : '';
		$instance['credit'] = ( ! empty( $new_instance['credit'] ) ) ? strip_tags( $new_instance['credit'] ) : '';
		return $instance;

	}

	// register widget
	public function register_pwp_widget() {
    register_widget( 'Pwp_Widget' );
	}
} // end widget class

// Initialize classes
$pocketwp = new PocketWP;
$pocketWpWidget = new Pwp_Widget;

// // Delete pwp_version setting from database
// register_deactivation_hook( __FILE__, array( 'PocketWP', 'pwp_deactivation_notice' ));
?>
