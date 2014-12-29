<?php 

//===============================================
// Widget
//===============================================

if(class_exists('Pwp_Widget')){
        // Display Pocket links in Widget
        class Pwp_Widget extends WP_Widget {

            //Sets up the widgets name etc
            public function __construct() {
                parent::__construct(
                    'pwp_widget', // Base ID
                    __('Pocket WP', 'text_domain'), // Name
                    array( 'description' => __( 'Display Pocket links in a widget', 'text_domain' ), ) // Args
                );

                // register Pocket WP widget
                add_action( 'widgets_init', array($this, 'register_pwp_widget' ));

                // Register stylesheet
                add_action( 'wp_enqueue_scripts', array($this, 'pwp_add_stylesheet' ));
            }

            // Outputs the content of the widget
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

                $pwp_items = pwp_get_links($pwp_count, $instance['tag']);

                // Loop through array and get link details.
                echo '<ul class="pwp_widget_list">';
                foreach($pwp_items as $item){
                    echo '<li><a href="' . $item[0] . '" class="pwp_item_widget_link" target="_blank">' . $item[1] . '</a>';
                }

                echo '</ul>';

                if($instance['credit'] == 'no') {
                    // Do nothing
                } else {
             echo '<span id="pwp_plugin_credit_widget"><a href="http://ciaranmahoney.me/code/pocket-wp/?utm_campaign=wp-plugins&utm_source=pocket-wp-widget&utm_medium=credit-link" target="_blank">Pocket WP</a> by <a href="https://twitter.com/ciaransm" target="_blank">@ciaransm</a></span>';
            }
                
                echo $args['after_widget'];
            } // end widget output

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

        // Register widget  
        public function register_pwp_widget() {
            register_widget( 'Pwp_Widget' );
        }

        //Register css
        public function pwp_add_stylesheet() {
            wp_register_style( 'pwp-style', plugins_url('style.css', __FILE__) );
            wp_enqueue_style( 'pwp-style' );
        }
    }
} // end if class exists for widget
?>