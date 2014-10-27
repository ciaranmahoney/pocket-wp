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

defined('ABSPATH') or die("No script kiddies please!");

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
			<p>If you are having issues, please let me know on Twitter <a href="https://twitter.com/ciaransm">@ciaransm</a></p>

			<h3>Setup Instructions</h3>

			<ol>
				<li>To get started you will need to create an application on the Pocket Developer\'s site. Visit the <a href="http://getpocket.com/developer/apps/new">Pocket Developers New App page</a> and create your application.
				</li>
				<li>Ensure you select <strong><em>Retrieve</em></strong> under the <strong><em>Permissions section</em></strong> and <strong></m>Web</em></strong> under the <strong><em>Platforms</em></strong> section. 
				</li>
				<li>Once you have done this, copy your <strong><em>Consumer Key</em></strong> from the list of apps and paste into the field below.</li>
				<li>Click Save Changes to save the key.</li>
				<li>Once the settings have been saved click the <strong> Authorize</strong> link below. You may be directed to the pocket website.</li>

	   	', 'wordpress' );

}

function pwp_consumer_key_field_render(  ) { 
	$options = get_option( 'pwp_settings' );
	$pwp_consumer_key = $options['pwp_consumer_key_field'];
	?>
	<input type='text' name='pwp_settings[pwp_consumer_key_field]' value='<?php echo $pwp_consumer_key; ?>'>
	<?php

	if( isset($_GET['settings-updated']) && $_GET['settings-updated'] == true ){
       pwp_get_tokens();
   	}
}

function pwp_options_page(  ) { 
	?>
	<form action='options.php' method='post'>		
		<?php
		settings_fields( 'pwp_pluginPage' );
		do_settings_sections( 'pwp_pluginPage' );
		submit_button();
		?>
		
	</form>
	<?php

}
//End options page setup

// Initialize options page and add to menu
add_action( 'admin_menu', 'pwp_add_admin_menu' );
add_action( 'admin_init', 'pwp_settings_init' );


// cURL function
function pwp_cURL($url, $post) {
	$cURL = curl_init();
	curl_setopt($cURL, CURLOPT_URL, $url);
	curl_setopt($cURL, CURLOPT_HEADER, 0);
	curl_setopt($cURL, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded;charset=UTF-8'));
	curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cURL, CURLOPT_TIMEOUT, 5);
	curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($cURL, CURLOPT_POST, count($post));
	curl_setopt($cURL, CURLOPT_POSTFIELDS, http_build_query($post));
	$output = curl_exec($cURL);
	curl_close($cURL);

	return $output;
} //End cURL function

// Contact Pocket to get access token
function pwp_get_tokens(){
	$options = get_option( 'pwp_settings' );
	$pwp_consumer_key = $options['pwp_consumer_key_field']; // gets consumer key saved in option page.

	$pwp_options_url = site_url() . '/wp-admin/options-general.php?page=pocket_wp';

	//If access token is already set, use it to get request token.
	if (isset ($_GET["token"])) {
		$oAuthRequest = pwp_cURL('https://getpocket.com/auth/authorize', 
			array(
				'consumer_key' => $pwp_consumer_key,
				'code' => $_GET['token']
				)
			);

		$access_token = explode('&', $oAuthRequest);
		$access_token = $access_token[0];
		$access_token = explode('=', $access_token);
		$access_token = $access_token[1];
	} else {
		$oAuthRequestToken = explode('=', pwp_cURL(
		   'https://getpocket.com/v3/oauth/request',
		   array(
		  	 'consumer_key' => $pwp_consumer_key,
		  	 'redirect_uri' => $pwp_options_url."?consumer_key=$pwp_consumer_key"
		   )
		 ));

		update_option( 'pwp_request_token', $oAuthRequestToken[1] );

		 // (3) Redirect user to Pocket to continue authorization
		echo '<meta http-equiv="refresh" content="0;url=' . 'https://getpocket.com/auth/authorize?request_token=' . urlencode($oAuthRequestToken[1]) . '&redirect_uri=' . site_url() . "/wp-admin/options-general.php?page=pocket_wp";
	}
} // End contact Pocket to get access token

// Get Pocket links array
function pwp_get_links () {

}

// Display Pocket links in Widget
function pwp_widget(){

}