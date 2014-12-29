<?php 
//===============================================
// cURL Stuff
//===============================================

if(class_exists('PocketWP_Get')) {
    class PocketWP_Get {
        var $apiUrl = 'https://getpocket.com/v3/get';

        // Get Pocket links array
        function pwp_get_links ($pwp_count, $pwp_tag) {
            $pwp_options = get_option( 'pwp_settings' );
            $pwp_consumer_key = $pwp_options['pwp_consumer_key_field'];
            $pwp_access_token = get_option('pwp_access_token');

            $pwp_pocket_request = wp_remote_get($this->apiUrl,
                array(
                    'consumer_key'  => $pwp_consumer_key,
                    'access_token'  => $pwp_access_token,
                    'tag'           => $pwp_tag,
                    'detailType'    => 'complete',
                    'state'         => 'all',
                    'count'         => $pwp_count
                    ),
                false
                );

            //Loop over cURL output
            $pwp_links_output = array();

            foreach( $pwp_pocket_request['list'] as $item){

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
                if ($item['tags'] != ''){
                    $pwp_tags = $item['tags'];
                } else {
                    $pwp_tags = '';
                }

                array_push($pwp_links_output, 
                    array($pwp_url, $pwp_title, $pwp_excerpt, $pwp_tags
                    )
                );
            }
            return $pwp_links_output;
        }
    
    } // end class
} // end if class exists
?>