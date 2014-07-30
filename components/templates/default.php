<?php
$recaptcha_url = esc_url( $this->api_server . '/challenge?k=' . $this->config()->public_key . $message );
?>
<script type="text/javascript" src="<?php echo $recaptcha_url; // escaped above ?>">
</script>
<noscript>
	<iframe src="<?php echo $recaptcha_url; // escaped above ?>" height="300" width="500" frameborder="0"></iframe><br>
	<textarea name="recaptcha_challenge_field" rows="3" cols="40">
	</textarea>
	<input type="hidden" name="recaptcha_response_field" value="manual_challenge">
</noscript>