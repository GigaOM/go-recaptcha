<div id="recaptcha_widget">
	<div class='recap-image'>
		<div id="recaptcha_image"></div>
		<div class="recaptcha_logo"></div>
	</div>
	<div class='recap-fields'>
		<div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>
		
		<ul class="button-group radius even-3">
			<li class="recaptcha_reload"><a class='button' title="Get another CAPTCHA"></a></li>
			<li class="recaptcha_only_if_image"><a class='button' title="Get an audio CAPTCHA"></a></li>
			<li class="recaptcha_only_if_audio"><a class='button' title="Get an image CAPTCHA"></a></li>
			<li class="recaptcha_help"><a class='button' title="Help"></a></li>
		</ul>

		<span class="recaptcha_only_if_image">Enter the words above:</span>
		<span class="recaptcha_only_if_audio">Enter the numbers you hear:</span>

		<input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />
	</div>
</div>
<?php
//escape this here so we dont have to escape it multiple times
$recaptcha_url = esc_url( $this->api_server . '/challenge?k=' . $this->config()->public_key . $message );
?>
<script type="text/javascript"
		src="<?php echo $recaptcha_url; // escaped above ?>">
</script>
<noscript>
	<iframe src="<?php echo $recaptcha_url; // escaped above ?>"
				height="300" width="500" frameborder="0"></iframe><br>
	<textarea name="recaptcha_challenge_field" rows="3" cols="40">
	</textarea>
	<input type="hidden" name="recaptcha_response_field"
				value="manual_challenge">
</noscript>