<form id="acfp-ajax-contact" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>" class="acfp-ajax-contact">							
	<div class="form-group">
		<input type="email" class="form-control" id="acfp-email" placeholder="Email" name="acfp-email" required>
	</div>
	<div class="form-group">
		<textarea id="acfp-message" class="form-control" rows="3" placeholder="Message" name="acfp-message" required></textarea>
	</div>
	<div id="acfp-form-messages"></div>
	<button type="submit" class="btn" name="submit" value="submit">Send</button>
</form>