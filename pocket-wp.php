<?php

/**
 * Plugin Name: Pocket WP
 * Plugin URI: TO DO
 * Description: Adds a shortcode which allows you to display your pocket links in a WordPress page/post.
 * Version: 0.1
 * Author: Ciaran Mahoney
 * Author URI: http://ciaranmahoney.me/pocket-wp
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

//defined('ABSPATH') or die("No script kiddies please!");

// Display activation notice with setup information when plugin is activated
register_activation_hook( __FILE__,'pwp_activation_notice');

// Setting an option to true once the plugin has been activated.
// This ensures the activation notice is only shown on activation
function pwp_activation_notice_displayed(){
    update_option('pwp_activation_notice_displayed','TRUE');
}

function pwp_activation_notice(){
    echo'
    	<div class="pwp-activation-notice"><p>Thank you for installing Pocket WP. To get setup you will need to create an application on the Pocket Developers site. To do so, please follow the instructions on the <a href="' . get_site_url() . '/wp-admin/options-general.php?page=pocket_wp">Pocket WP Options Page</a> and create your application. </p>
   		 </div>
   		'
   	;
}

// Create an options page for Pocket WP consumer key
function pwp_add_admin_menu(  ) { 
	add_options_page( 'Pocket WP', 'Pocket WP', 'manage_options', 'pocket_wp', 'pwp_options_page' );
}

function pwp_settings_exist(  ) { 
	if( false == get_option( 'pocket_wp_settings' ) ) { 
		add_option( 'pocket_wp_settings' );
	}
}

function pwp_settings_init(  ) { 
	register_setting( 'pwp_pluginPage', 'pwp_settings' );

	add_settings_section(
		'pwp_pluginPage_section', 
		__( '', 'wordpress' ), 
		'pwp_settings_section_callback', 
		'pwp_pluginPage'
	);

	add_settings_field( 
		'pwp_consumer_key_field', 
		__( 'Pocket Consumer Key', 'wordpress' ), 
		'pwp_consumer_key_field_render', 
		'pwp_pluginPage', 
		'pwp_pluginPage_section' 
	);
}

function pwp_settings_section_callback(  ) { 
	echo __( 
		'
			<h2>Pocket WP</h2>
			<p>Plugin by <a href="http://ciaranmahoney.me" target="_blank">Ciaran Mahoney</a></p>
			<h3>Setup Instructions</h3>

			<ol>
				<li>To get started you will need to create an application on the Pocket Developer\'s site. Visit the <a href="http://getpocket.com/developer/apps/new">Pocket Developers New App page</a> and create your application. Ensure you select <strong><em>Retrieve</em></strong> under the <strong><em>Permissions </em></strong>section and <strong><em>Web</em></strong> under the <strong><em>Platforms</em></strong> section. 
				</li>
				<li>Once you have done this, copy your <strong><em>Consumer Key</em></strong> from the list of apps and paste into the field below.</li>
				<li>Click <em><strong>Save Changes</em></strong> to save the key and get a <strong><em>Request Token</em></strong> from Pocket. You may be sent to Pocket to authorize your app (if so sign in and click the yellow Authorize button).</li>
				<li>After you have authorized your app with Pocket, you will be brought back to this page.</li>
				<li>Click the green <strong><em>GET ACCESS KEY</strong></em> button below to authorize your access key from Pocket. Please do this once only. You should get a popup to confirm your access key was authenticated successfully. If you get the authentication failed message, you just need to click <strong><em>Save Changes</strong></em> and then <strong><em>GET ACCESS KEY</strong></em> again. </li>
			</ol>
			<p>If you are having issues, please let me know on Twitter <a href="https://twitter.com/ciaransm">@ciaransm</a></p>

	   	', 'wordpress' );

}

function pwp_consumer_key_field_render(  ) { 
	$pwp_options = get_option( 'pwp_settings' );
	$pwp_consumer_key = $pwp_options['pwp_consumer_key_field'];
	?>
	<input type='text' name='pwp_settings[pwp_consumer_key_field]' size="50" value='<?php echo $pwp_consumer_key; ?>'>
	<p>Request: <?php echo get_option(' pwp_request_token'); ?> </p>
	<p>Access: <?php print_r( get_option('pwp_access_token')); ?> </p>

	<?php

	if( isset($_GET['settings-updated']) && $_GET['settings-updated'] == true ){
       pwp_get_request_token();
   	}
}

function pwp_options_page(  ) { 
	?>
	<form action='options.php' method='post'>		
		<?php
		settings_fields( 'pwp_pluginPage' );
		do_settings_sections( 'pwp_pluginPage' );
		?>
		<p class="pwp_save_changes_notice">When you click Save Changes you will be taken to Pocket to authorize your app</p>
		<?php
		submit_button();
		?>
		
	</form>
	
	<a href="#" id="pwp_get_access_key_button">GET ACCESS KEY</a>
	<?php


} //End options page setup

// Initialize options page and add to menu
add_action( 'admin_menu', 'pwp_add_admin_menu' );
add_action( 'admin_init', 'pwp_settings_init' );


// cURL function
function pwp_cURL($url, $post, $returnstring) {
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
function pwp_get_request_token(){
	$pwp_options = get_option( 'pwp_settings' );
	$pwp_consumer_key = $pwp_options['pwp_consumer_key_field']; // gets consumer key saved in option page.

	$pwp_options_url = site_url() . '/wp-admin/options-general.php?page=pocket_wp';

	$oAuthRequestToken = explode('=', pwp_cURL(
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

// Create AJAX call for authorize button.
add_action( 'admin_footer', 'pwp_authorize_button' ); // Write our JS below here
function pwp_authorize_button () {
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
			$('#pwp_get_access_key_button').hide().before('<div class="pwp_success">Access key authentication was successfull! Setup complete!</div>');


		} else if (getQueryVariable('pwpaccess') == "false"){
			// If returns false, show failed notice.
			$('#pwp_get_access_key_button').before('<div class="pwp_warning">Access key authentication failed. Please click save changes to retrieve a new request token, then try authenticating again.</div>');
		} else {
			$('#pwp_get_access_key_button').before('<div class="pwp_notice">Please click this button once only. If you get a failed message, try clicking Save Changes above, then try again.</div>');
		}
	

	});
	</script> 

	<?php
}

// Function to convert the request token to an access token
add_action( 'wp_ajax_pwp_click_authorization_button', 'pwp_get_access_token' );
function pwp_get_access_token(){
	$pwp_options = get_option( 'pwp_settings' );
	$pwp_consumer_key = $pwp_options['pwp_consumer_key_field'];
	$pwp_request_token = get_option('pwp_request_token');

	$pwp_oAuthRequest = pwp_cURL('https://getpocket.com/v3/oauth/authorize', 
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
function pwp_get_links ($pwp_count, $pwp_tags) {
	$pwp_options = get_option( 'pwp_settings' );
	$pwp_consumer_key = $pwp_options['pwp_consumer_key_field'];
	$pwp_access_token = get_option('pwp_access_token');

	$pwp_pocket_request = pwp_cURL('https://getpocket.com/v3/get',
		array(
			'consumer_key' 	=> $pwp_consumer_key,
			'access_token' 	=> $pwp_access_token,
			'tag'			=> $pwp_tags,
			'count'			=> $pwp_count
			),
		false
		);
	return $pwp_pocket_request;
}

// Adding a shortcode to display Pocket links in a post/page
add_shortcode('pocket_links', 'pwp_shortcode' );
function pwp_shortcode ($atts, $content = null){
	extract( shortcode_atts( array(
							 'count' => '',
							 'tags' => ''
							), $atts 
			)
	);

	//Get the output from the cURL request
	$pwp_items = pwp_get_links($count, $tags);

	//Loop over cURL output
    foreach( $pwp_items['list'] as $item){
    	foreach($item as $key => $value){
    		echo $key . " | " . $value;

    	}
    	echo "<br/><br/>";
    }

    echo "<br/><br/>";
    print_r($pwp_items);
	echo "<br/><br/>";

	// foreach ($pwp_items as $item){
	// 	echo $item;
	// }

}


// Display Pocket links in Widget
function pwp_widget(){

}