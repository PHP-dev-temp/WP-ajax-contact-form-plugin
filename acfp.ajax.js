jQuery(document).ready(function($) {	

    // Ajax contact form
    // Get the form.
    var form = $('#acfp-ajax-contact');
    // Get the messages div.
    var formMessages = $('#acfp-form-messages');
    // Set up an event listener for the contact form.
    $(form).submit(function(event) {
        // Stop the browser from submitting the form.
        event.preventDefault();
		email = $('#acfp-email').val();
		message = $('#acfp-message').val();
        // Submit the form using AJAX.
        $.ajax({
            type: 'POST',
            url: $(form).attr('action'),
            data: {
				action : 'acfp_contact_form',
				email : email,
				message : message
			}
        })
		.done(function(response) {
			// Make sure that the formMessages div has the 'success' class.
			$(formMessages).removeClass('alert alert-warning');
			$(formMessages).addClass('alert alert-success');
			// Set the message text.
			$(formMessages).text(response);
			// Clear the form.
			$('#acfp-email').val('');
			$('#acfp-message').val('');
		})
		.fail(function(data) {
			// Make sure that the formMessages div has the 'error' class.
			$(formMessages).removeClass('alert alert-success');
			$(formMessages).addClass('alert alert-warning');
			// Set the message text.
			if (data.responseText !== '') {
			$(formMessages).text(data.responseText);
			} else {
			$(formMessages).text('Oops! An error occured and your message could not be sent.');
			}
		});
    });
});