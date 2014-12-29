<?php        

//===============================================
// Plugin Auth
//===============================================

if(class_exists('PocketWP_Auth')) {
    class PocketWP_Auth {

        // Setting an option to true once the plugin has been activated.
        // This ensures the activation notice is only shown on activation
        public function pwp_activation_notice_displayed(){
            update_option('pwp_activation_notice_displayed','TRUE');
        }

        public function pwp_activation_notice(){
            echo'
                <div class="pwp-activation-notice"><p>Thank you for installing Pocket WP. To get setup you will need to create an application on the Pocket Developers site. To do so, please follow the instructions on the <a href="' . get_site_url() . '/wp-admin/options-general.php?page=pocket_wp">Pocket WP Options Page</a> and create your application. </p>
                 </div>
                '
            ;
        }

        // Contact Pocket to get request token
        public function pwp_get_request_token(){
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

            // Redirect user to Pocket to continue authorization
             echo '<meta http-equiv="refresh" content="0;url=https://getpocket.com/auth/authorize?request_token=' . urlencode($oAuthRequestToken[1]) . '&redirect_uri=' . urlencode(site_url()) . urlencode("/wp-admin/options-general.php?page=pocket_wp&pwpsuccess=true/");

        } // End contact Pocket to get access token

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
                    $('#pwp_get_access_key_button').hide().before('<div class="pwp_success" style="color:green;">Access key authentication was successfull. Setup complete!</div>');


                } else if (getQueryVariable('pwpaccess') == "false"){
                    // If returns false, show failed notice.
                    $('#pwp_get_access_key_button').before('<div class="pwp_warning" style="color:red;">Access key authentication failed. Please click save changes to retrieve a new request token, then try authenticating again.</div>');
                } else {
                    $('#pwp_get_access_key_button').before('<div class="pwp_notice">Please click the GET ACCESS KEY button once only. If you get a failed message, try clicking Save Changes above, then try again.</div>');
                }
            

            });
            </script> 

            <?php
        }

        // Function to convert the request token to an access token
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

?>