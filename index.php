<?php 
/**
 * Plugin Name: jQuery Twitter
 * Plugin URI: http://www.makesites.org/wp-jquery-twitter/
 * Description: Twitter client using the jQuery plugin created by Damien du Toit (http://coda.co.za/)
 * Version: 1.0
 * Author: Makis Tracend
 * Author URI: http://makesit.es/
 *
*/


/**
 * Plugin activation. 
 */
 register_activation_hook(__FILE__, 'jqueryTwitter_activate');
/**
 * Load external scripts / styles 
 */
add_action('init', "jqueryTwitter_init");
/**
 * Activate "twitter" shortcode
 */
add_shortcode( 'twitter', 'jqueryTwitter_shortcode' );
/**
 * Register widget
 */
add_action('widgets_init', create_function('', 'return register_widget("TwitterWidget");'));


function jqueryTwitter_activate()
{
	global $wpdb;
	
	// Set default username to jQuery
	add_option('jquery-twitter-container', 'twitter');
	add_option('jquery-twitter-userName', 'jquery');
	add_option('jquery-twitter-numTweets', "5");
	add_option('jquery-twitter-headingText', "Latest Tweets");
	add_option('jquery-twitter-slideDuration', "750");
	add_option('jquery-twitter-showProfileLink', "true");
	add_option('jquery-twitter-showTimestamp', 'true');
}

function jqueryTwitter_init() {
	$plugin_url = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	
	if (is_admin() ){ 
		// action taking place in the administration area
		// Register settings on administration
		add_action( 'admin_init', 'jqueryTwitter_settings' );
		//Create administration menu
		add_action('admin_menu', 'jqueryTwitter_menu');
	} else { 
		// enqueue script
		wp_deregister_script( 'jquery' );
        wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js');
        wp_enqueue_script( 'jquery' );
		wp_enqueue_script('jquery-twitter', $plugin_url . 'assets/js/jquery.twitter.js', array('jquery'));
		// enqueue style sheet
    	wp_enqueue_style('jquery-twitter-style', $plugin_url . 'assets/css/jquery.twitter.css');
	}
}

function jqueryTwitter_shortcode( $atts, $content = null ) {
   $data = shortcode_atts( array(
      'container' => get_option('jquery-twitter-container'),
      'userName' => get_option('jquery-twitter-userName'),
      'numTweets' => get_option('jquery-twitter-numTweets'),
      'slideDuration' => get_option('jquery-twitter-slideDuration'),
      'headingText' => get_option('jquery-twitter-headingText'),
      'showProfileLink' => get_option('jquery-twitter-showProfileLink'),
      'showTimestamp' => get_option('jquery-twitter-showTimestamp'),
      ), $atts );
   
   $output = generateJS( $data );
   echo $output;
}


function generateJS( $data ){


/* <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline
 * <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
 *  <script>window.jQuery || document.write('<script src="assets/js/jquery-1.3.2.min.js"><\/script>')</script>
 * --> 
 */
 
	$output =	'<script type="text/javascript">' . "\n";
	$output .=	"\t" . '$(document).ready(function() {' . "\n";
	$output .=	"\t\t" . '$("#'. $data['container'] .'").getTwitter({' . "\n";
	$output .=	"\t\t\t" . 'userName: "'. $data['userName'] .'",' . "\n";
	$output .=	"\t\t\t" . 'numTweets: '. $data['numTweets'] .',' . "\n";
	$output .=	"\t\t\t" . 'loaderText: "Loading tweets...",' . "\n";
	
	if( $data['slideDuration'] != "0"){
		$output .=	"\t\t\t" . 'slideIn: true,' . "\n";
		$output .=	"\t\t\t" . 'slideDuration: '. $data['slideDuration'] .',' . "\n";
	}
	if( $data['headingText'] != ""){
		$output .=	"\t\t\t" . 'showHeading: true,' . "\n";
		$output .=	"\t\t\t" . 'headingText: "'. $data['headingText'] .'",' . "\n";
	}
	
	$output .=	"\t\t\t" . 'showProfileLink: '. $data['showProfileLink'] .',' . "\n";
	$output .=	"\t\t\t" . 'showTimestamp: '. $data['showTimestamp'] .'' . "\n";
	$output .=	"\t\t" . '});' . "\n";
	$output .=	"\t" . '});' . "\n";
	$output .=	'</script>' . "\n";
	$output .=	'<div id="'. $data['container'] .'"></div>' . "\n";
	
	return $output;

}


function jqueryTwitter_menu() {
	add_options_page('Twitter Settings', 'jQuery Twitter', 'manage_options', 'jquery-twitter-options', 'jqueryTwitter_options_page');
	//create new top-level menu
	//add_menu_page('BAW Plugin Settings', 'BAW Settings', 'administrator', __FILE__, 'jqueryTwitter_options_page',plugins_url('/assets/img/icon.png', __FILE__));
}

function jqueryTwitter_settings() {
	register_setting( 'jquery-twitter-options', 'jquery-twitter-container', 'jqueryTwitter_options_validate' );
	register_setting( 'jquery-twitter-options', 'jquery-twitter-userName', 'jqueryTwitter_options_validate' );
	register_setting( 'jquery-twitter-options', 'jquery-twitter-numTweets', 'jqueryTwitter_options_validate' );
	register_setting( 'jquery-twitter-options', 'jquery-twitter-slideDuration', 'jqueryTwitter_options_validate' );
	register_setting( 'jquery-twitter-options', 'jquery-twitter-headingText', 'jqueryTwitter_options_validate' );
	register_setting( 'jquery-twitter-options', 'jquery-twitter-showProfileLink', 'jqueryTwitter_options_validate' );
	register_setting( 'jquery-twitter-options', 'jquery-twitter-showTimestamp', 'jqueryTwitter_options_validate' );
	
	add_settings_section('jqueryTwitter_options', 'Main Settings', 'jqueryTwitter_option_main', 'jquery-twitter-admin');
	
	add_settings_field('jquery-twitter-container', 'Container', 'jqueryTwitter_option_field', 'jquery-twitter-admin', 'jqueryTwitter_options', $args = array('id'=>'jquery-twitter-container'));
	add_settings_field('jquery-twitter-userName', 'Username', 'jqueryTwitter_option_field', 'jquery-twitter-admin', 'jqueryTwitter_options', $args = array('id'=>'jquery-twitter-userName'));
	add_settings_field('jquery-twitter-numTweets', 'Number of Tweets', 'jqueryTwitter_option_field', 'jquery-twitter-admin', 'jqueryTwitter_options', $args = array('id'=>'jquery-twitter-numTweets'));
	add_settings_field('jquery-twitter-slideDuration', 'Slide Duration', 'jqueryTwitter_option_field', 'jquery-twitter-admin', 'jqueryTwitter_options', $args = array('id'=>'jquery-twitter-slideDuration'));
	add_settings_field('jquery-twitter-headingText', 'Heading Text', 'jqueryTwitter_option_field', 'jquery-twitter-admin', 'jqueryTwitter_options', $args = array('id'=>'jquery-twitter-headingText'));
	add_settings_field('jquery-twitter-showProfileLink', 'Show Profile Link (true/false)', 'jqueryTwitter_option_field', 'jquery-twitter-admin', 'jqueryTwitter_options', $args = array('id'=>'jquery-twitter-showProfileLink'));
	add_settings_field('jquery-twitter-showTimestamp', 'Show Timestamp (true/false)', 'jqueryTwitter_option_field', 'jquery-twitter-admin', 'jqueryTwitter_options', $args = array('id'=>'jquery-twitter-showTimestamp'));

}

function jqueryTwitter_option_main( ) {
	// output markup specific to the main section...
}

function jqueryTwitter_option_field( $args ) {
	echo '<input id="'. $args['id'] .'" name="'. $args['id'] .'" size="40" type="text" value="' . get_option( $args['id'] ) . '" />';
}



// display the admin options page
function jqueryTwitter_options_page() {
?>
<div class="wrap">
<h2>jQuery Twitter</h2>

<form method="post" action="options.php">
	<?php settings_fields( 'jquery-twitter-options' ); ?>
	<?php do_settings_sections('jquery-twitter-admin'); ?>

    <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form>
</div>
<?php 
}

function jqueryTwitter_options_validate($input) {
	$newinput = trim($input);
	// add extra validation filtering here
	return $newinput;
}

/**
 * Widget Class
 */
class TwitterWidget extends WP_Widget {
    /** constructor */
    function TwitterWidget() {
        parent::WP_Widget(false, $name = 'Twitter');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['jquery-twitter-userName']);
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
              
			  <?php echo do_shortcode("[twitter]"); ?>
              
              <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['jquery-twitter-userName'] = strip_tags($new_instance['jquery-twitter-userName']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $username = esc_attr($instance['jquery-twitter-userName']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('jquery-twitter-userName'); ?>"><?php _e('Username:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('jquery-twitter-userName'); ?>" name="<?php echo $this->get_field_name('jquery-twitter-userName'); ?>" type="text" value="<?php echo $username; ?>" />
        </p>
        <?php 
    }

} // class FooWidget

?>
