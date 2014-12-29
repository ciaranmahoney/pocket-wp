<?php 
//===============================================
// Options Page Stuff
//===============================================

if(class_exists('PWP_Options')) {
    class PWP_Options {

        // Create an options page for Pocket WP consumer key
        public function pwp_add_admin_menu(  ) { 
            add_options_page( 'Pocket WP', 'Pocket WP', 'manage_options', 'pocket_wp', 'pwp_options_page' );
        }

        public function pwp_settings_exist(  ) { 
            if( false == get_option( 'pocket_wp_settings' ) ) { 
                add_option( 'pocket_wp_settings' );
            }
        }

        public function pwp_settings_init(  ) { 
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

        public function pwp_settings_section_callback(  ) { 
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
                        <li>Click the grey <strong><em>GET ACCESS KEY</strong></em> button below to generate an access key. <strong>Please do this once only.</strong> You should get a popup to confirm your access key was authenticated successfully. If you get the authentication failed message, you just need to click <strong><em>Save Changes</strong></em> and then <strong><em>GET ACCESS KEY</strong></em> again. </li>
                    </ol>
                    <p>If you are having issues, please let me know on Twitter <a href="https://twitter.com/ciaransm">@ciaransm</a></p>

                ', 'wordpress' );

        }

        public function pwp_consumer_key_field_render(  ) { 
            $pwp_options = get_option( 'pwp_settings' );
            $pwp_consumer_key = $pwp_options['pwp_consumer_key_field'];
            ?>
            <input type='text' name='pwp_settings[pwp_consumer_key_field]' size="50" value='<?php echo $pwp_consumer_key; ?>'>

            <?php

            if( isset($_GET['settings-updated']) && $_GET['settings-updated'] == true ){
               pwp_get_request_token();
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


        } // end options page setup
    } // end class
} // end if class exists
?>