<?php
	/**
	 * @package acfp
	 */
	/**
	
	Plugin Name: Ajax contact form plugin
	Plugin URI: http://milton-soft.com
	Description: a plugin to generate ajax contact form
	Version: 1.0.0
	Author: Toni Teofilovic
	Author URI: https://github.com/PHP-dev-temp
	License: GPLv2 or later
	Text Domain: acfp
	*/

	// Register and load the widget
	function acfp_load_widget(){
		register_widget('acfp_widget');
	}
	add_action('widgets_init', 'acfp_load_widget');

	// Creating the widget 
	class acfp_widget extends WP_Widget {

		function __construct() {
			parent::__construct(
				// Base ID of your widget
				'acfp_widget', 

				// Widget name will appear in UI
				__('Ajax Contact Form', 'acfp_widget_domain'), 

				// Widget description
				array('description' => __('Simple Ajax Contact Form Widget', 'acfp_widget_domain'),) 
			);
		}

		// Creating widget front-end
		// This is where the action happens
		public function widget($args, $instance) {
			$title = apply_filters('widget_title', $instance['title']);
			// before and after widget arguments are defined by themes
			echo $args['before_widget'];
			if (empty($title)) $title = 'Send me a message';
			echo $args['before_title'] . $title . $args['after_title'];

			// This is where you run the code and display the output
			include plugin_dir_path(__FILE__) . 'form_template.php';
			
			echo $args['after_widget'];
		}
				
		// Widget Backend 
		public function form($instance) {
			if (isset($instance['title'])) {
				$title = $instance['title'];
			}
			else {
				$title = __('Send me a message', 'acfp_widget_domain');
			}
		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<?php 
		}
			
		// Updating widget replacing old instances with new
		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		return $instance;
		}
	} // Class acfp_widget ends here

	//Register JS

	add_action('wp_enqueue_scripts', 'acfp_load_scripts');
	function acfp_load_scripts(){
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js', false, '1.11.3');
		wp_enqueue_script('jquery');	
		wp_enqueue_script('acfpjs', plugin_dir_url(__FILE__) . 'acfp.ajax.js', array('jquery'), '1.0.0', true);
	}

	// Shortcode
	add_shortcode('acfp', 'acfp_form_creation');
	function acfp_form_creation(){
		include plugin_dir_path(__FILE__) . 'form_template.php';		
	}

	// Ajax call	
    add_action( 'wp_ajax_acfp_contact_form', 'acfp_contact_form_ajax_callback_function' );    // If called from admin panel
    add_action( 'wp_ajax_nopriv_acfp_contact_form', 'acfp_contact_form_ajax_callback_function' );    // If called from front end
    function acfp_contact_form_ajax_callback_function() {
		
        // Implement ajax function		
		$a = 12 / 0;
		// Only process POST reqeusts.
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			// Get the form fields and remove whitespace.
			$email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
			$message = htmlspecialchars (trim($_POST["message"]));
			$ip_address = $_SERVER['REMOTE_ADDR'];

			// Check that data was sent to the mailer.
			if (empty($message) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
				// Set a 400 (bad request) response code and exit.
				http_response_code(400);
				echo "Oops! There was a problem with your submission. Please complete the form and try again. $email , $message!";
				exit;
			}

			// Set the recipient email address.
			// FIXME: Update this to your desired email address.
			$recipient = get_bloginfo('admin_email');

			// Build the email content.
			$email_content = '<html><head><style>body { font-family: Verdana, Arial, sans-serif; font-size: 12px; }</style></head><body>';
			$email_content .= "New mail from:<br><br>Email: $email <br>IP: $ip_address <br><br>Message:<br>";
			$email_content .= "Email: $email<br><br>";
			$email_content .= "Message:<br>$message<br></body></html>";

			// Set the email subject.
			$subject = "New contact from $email";

			// Build the email headers.
			$headers = "From: $email\r\n";
			$headers .= "Reply-To: $email\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=utf-8\r\n";

			// Send the email.
			if (mail($recipient, $subject, $email_content, $headers)) {
				// Set a 200 (okay) response code.
				http_response_code(200);
				echo "Thank You! Your message has been sent.";
				exit;
			} else {
				// Set a 500 (internal server error) response code.
				http_response_code(500);
				echo "Oops! Something went wrong and we couldn't send your message.";
				exit;
			}

		} else {
			// Not a POST request, set a 403 (forbidden) response code.
			http_response_code(403);
			echo "There was a problem with your submission, please try again.";
				exit;
		}
    }