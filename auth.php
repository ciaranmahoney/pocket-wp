<?php

class PwpAuth {

	// cURL function
	private function pwp_cURL($url, $post) {
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

	/* Contact Pocket to get access token */
	public function pwp_get_tokens(){
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

			 // (3) Redirect user to Pocket to continue authorization
			 echo '<meta http-equiv="refresh" content="0;url=' . 'https://getpocket.com/auth/authorize?request_token=' . urlencode($oAuthRequestToken[1]) . '&redirect_uri=' . urlencode("http://localhost/pocket-test/wp-admin/options-general.php?page=pocket_wp&consumer_key=$pwp_consumer_key" . '&token=' . $oAuthRequestToken[1]) . '" />';

			update_option( 'pwp_request_token', $oAuthRequestToken[1] );
		}
	} // End contact Pocket to get access token
} // End Class Auth