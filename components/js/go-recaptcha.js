var go_recaptcha = {};
( function($) {
	go_recaptcha.do_captcha = function( div ) {
		// If there's no go-recaptcha div we quit right away
		if ( 0 == div.length ) {
			return;
		}

		// If there's already a live recaptcha input we don't need to do anything
		if ( 0 !== $( div ).find( 'div' ).length ) {
			return;
		}

		// There's no live recaptcha inputs in this form so we destroy any existing ones and recreate a new in the current form
		if ( 'object' == typeof Recaptcha ) {
			$( '.go-recaptcha' ).empty();
			Recaptcha.create( go_recaptcha.public_key, $( div ).attr('id'), go_recaptcha.recaptcha_options );
		}
	};

	go_recaptcha.init = function() {
		$( document ).on( 'focus', 'form input:not(#recaptcha_response_field), form textarea', function( event ) {
			go_recaptcha.do_captcha( $( this ).closest( 'form' ).find( '.go-recaptcha' ) );
			// The following four functions depend on Recaptcha being loaded 
			// if we want a custom help dialog (for intance) we can change the behavior here.
		} ).on( 'click', '.recaptcha_reload .button', function( event ) {
			//refresh image/audio with a new one
			Recaptcha.reload();
		} ).on( 'click', '.recaptcha_only_if_image .button', function( event ) {
			//switch to audio recaptcha
			Recaptcha.switch_type('audio');
		} ).on( 'click', '.recaptcha_only_if_audio .button', function( event ) {
			//switch to image recaptcha
			Recaptcha.switch_type('image');
		} ).on( 'click', '.recaptcha_help .button', function( event ) {
			//show recaptcha help
			Recaptcha.showhelp();
		} );
	}

	$( document ).ready( function() {
		//make sure everything is ready before calling
		go_recaptcha.init();
	} );

} )(jQuery);