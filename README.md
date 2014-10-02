Go reCAPTCHA - programmatic recaptcha forms for WP.
===

Requirements
---
Software: none. You will need keys from [Google's reCAPTCHA site](https://www.google.com/recaptcha/).

While not required, this was originally created for use with [go-contact](https://github.com/GigaOM/go-contact) and some of the design decisions were made for working with shortcodes and multiple instances on a single page. Really, we're just saying they work well together. In fact, [go-contact](https://github.com/GigaOM/go-contact) requires go-reCAPTCHA.

Hacking
---
You can create your own templates to use for the reCAPTCHA form. There are two example forms in the `components/templates` folder.

You can modify the response messages in `components/class-go-recaptcha.php`

Usage
---
You have to set the recaptcha private and public keys for this to work. If you are using [go-config](https://github.com/GigaOM/go-config) that's the best way to go. If not you will have to edit the `class-go-recaptcha.php` file to insert your keys.

Once that is set up, you call a reCAPTCHA form using something along the lines of:
```
<fieldset class="recaptcha">
	<?php
		if ( function_exists( 'go_recaptcha' ) )
		{
    		echo go_recaptcha()->get_inputs();
		}//end if
	?>
</fieldset>
```

and validate the recaptcha using something like:
```
if ( function_exists( 'go_recaptcha' ) && ! go_recaptcha()->check_request() )
{
	$return['error'] = go_recaptcha()->get_message_text();
	echo json_encode( $return );
	die;
} // END if
```