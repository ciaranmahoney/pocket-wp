<?php

//===============================================
// Shortcode
//===============================================

if(class_exists('PocketWP_Shortcode')) {
    class PocketWP_Shortcode {

        function pwp_shortcode ($atts, $content = null){
            extract( shortcode_atts( array(
                         'count' => '',
                         'tag' => '',
                         'excerpt' => '',
                         'tag_list' => '',
                         'credit' => ''
                        ), $atts ));

            //Get the array that was extracted from the cURL request
            $pwp_items = pwp_get_links($count, $tag);

            // Loop through array and get link details.
            foreach($pwp_items as $item){
                echo '<h3><a href="' . $item[0] . '" class="pwp_item_sc_link" target="_blank">' . $item[1] . '</a></h3>';
                
                //Display excerpt if excerpt is not set to no.  
                if (strtolower($excerpt) != 'no'){
                    echo '<p class="pwp_item_excerpt">' . $item[2] . '</p>';
                }

                // Display tag list if tag_list not set to no.
                if(strtolower($tag_list) != 'no') {
                    echo '<p class="pwp_tag_list">';
                    foreach($item[3] as $tag) {
                        echo '<span class="pwp_tags">' . $tag['tag'] . '</span>';
                    }
                    echo'</p>';
                 }
            }

            //print_r($pwp_items); //used for testing only

            if (strtolower($credit) == "no") {
                // Show nothing
            } else { 
                // Display author credit links
                echo '<p id="pwp_plugin_credit_sc"><a href="http://ciaranmahoney.me/code/pocket-wp/?utm_campaign=wp-plugins&utm_source=pocket-wp-shortcode&utm_medium=credit-link" target="_blank">Pocket WP</a> by <a href="https://twitter.com/ciaransm" target="_blank">@ciaransm</a></p>';
            }
        } // end pwp_shortcode
    } // end class
} // end if class exists
?>