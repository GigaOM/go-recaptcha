<?php

class GO_reCaptcha
{
	public $slug          = 'go-recaptcha';
	public $api_create    = 'https://www.google.com/recaptcha/admin/create';
	public $api_server    = 'https://www.google.com/recaptcha/api';
	public $verify_server = 'http://www.google.com/recaptcha/api/verify';
	public $instance      = 1;
	public $is_valid      = FALSE;
	public $message;
	public $config;
	public $options = array(
		'theme'               => 'custom',
		'lang'                => 'en',
		'custom_translations' => NULL,
		'custom_theme_widget' => 'recaptcha-widget',
		'tabindex'            => 0,
	);
	public $messages = array(
		'invalid-site-private-key' => 'The private key could not be verified.',
		'invalid-request-cookie'   => 'The challenge parameter did not validate.',
		'incorrect-captcha-sol'    => 'The CAPTCHA solution was incorrect.',
		'captcha-timeout'          => 'You waited too long please try answering the CAPTCHA again.',
		'recaptcha-not-reachable'  => 'The connection to the reCaptcha service failed.',
	);
	public $script_added = FALSE;

	/**
	 * We need to register a script here because this class is called
	 * by a shortcode and thus misses the boat for wp_enqueue_scripts
	 */
	public function __construct()
	{
		wp_register_script(
			$this->slug . '-js',
			plugins_url( 'js/' . $this->slug . '.js', __FILE__ ),
			array( 'jquery' ),
			FALSE,
			TRUE
		);
	}//end contruct

	/**
	 *	Singleton for config data
	 */
	public function config()
	{
		if ( ! $this->config )
		{
			$this->config = (object) apply_filters(
				'go_config',
				array(
					'public_key'  => 'YOUR_PUBLIC_KEY',
					'private_key' => 'YOUR_PRIVATE_KEY',
				),
				$this->slug
			);
		} // END if

		return $this->config;
	} // END config

	/**
	 * Get the human version of a message
	 */
	public function get_message_text( $message = '' )
	{
		$message = $message ? $message : $this->message;
		return isset( $this->messages[ $message ] ) ? $this->messages[ $message ] : $this->messages['incorrect-captcha-sol'];
	} // END get_message_text

	/**
	 * Return captcha input HTML
	 */
	public function get_inputs( $options = array() )
	{
		$message = $this->message ? '&amp;error=' . $this->message : '';

		$options = shortcode_atts(
			$this->options,
			(array) $options
		);


		if ( 1 < $this->instance || ! wp_script_is( $this->slug . '-js', 'enqueued' ) )
		{
			// There's more than one instance of reCaptcha on the page so we need to get fancy
			wp_enqueue_script( $this->slug . '-js' );

			wp_localize_script(
				$this->slug . '-js',
				'go_recaptcha',
				array(
					'recaptcha_options' => $options,
					'public_key' => $this->config()->public_key,
				)
			);
		} // END if

		ob_start();
		?>
		<div class="<?php echo esc_attr( $this->slug ); ?>" id="<?php echo esc_attr( $this->slug ); ?>-<?php echo esc_attr( $this->instance ); ?>">
			<?php
			if ( 1 == $this->instance )
			{
				echo '<script type="text/javascript">var RecaptchaOptions = ' . json_encode( $options ) . ';</script>';
				include __DIR__ . '/templates/custom.php';
			} // END if
			?>
		</div>
		<?php
		$this->instance++;
		//removes whitespace and line endings & etc from between tags to ensure the 'empty' div is.
		$str = preg_replace('/(?<=^|>)[^\w]+?(?=<|$)/', '', ob_get_clean());
		return $str;
	} // END get_inputs

	/**
	 *	Check the request for reCaptcha challenge field data
	 */
	public function check_request()
	{
		if ( ! isset( $_REQUEST['recaptcha_challenge_field'] ) || ! isset( $_REQUEST['recaptcha_response_field'] ) )
		{
			$this->message = 'incorrect-captcha-sol';
			return $this->is_valid;
		} // END if

	  	return go_recaptcha()->check_answer( $_REQUEST['recaptcha_challenge_field'], $_REQUEST['recaptcha_response_field'] );
	} // END check_request

	/**
	 * Check a captcha response against the challenge
	 */
	public function check_answer( $challenge, $response )
	{
		$this->message = 'incorrect-captcha-sol';

		// Fail immediately if appropriate
		if ( ! $challenge || ! $response )
		{
			return $this->is_valid;
		} // END if

		// Ask Google to check the response
		$arguments = array(
			'privatekey' => $this->config()->private_key,
			'remoteip'   => $_SERVER['REMOTE_ADDR'],
			'challenge'  => $challenge,
			'response'   => $response,
		);

		$response = wp_remote_post( $this->verify_server, array( 'body' => $arguments ) );

		if ( 200 != wp_remote_retrieve_response_code( $response ) )
		{
			$this->message = 'recaptcha-not-reachable';
			return FALSE;
		} // END if

		$response = explode( "\n", wp_remote_retrieve_body( $response ) );

		if ( ! isset( $response[0] ) || ! isset( $response[1] ) )
		{
			return FALSE;
		} // END if

		$this->is_valid = 'true' == trim( $response[0] ) ? TRUE : FALSE;
		$this->message  = '' != trim( $response[1] ) ? $response[1] : $this->error_message;

		return $this->is_valid;
	} // END check_answer
} // END GO_reCaptcha