/*
 * SimpleModal Contact Form
 * http://www.ericmmartin.com/projects/simplemodal/
 * http://code.google.com/p/simplemodal/
 *
 * Copyright (c) 2008 Eric Martin - http://ericmmartin.com
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Revision: $Id: contact.js 170 2008-12-04 19:03:12Z emartin24 $
 *
 */

(function(jQuery) {
jQuery.fn.contact = function(url) {
		contactUrl = url;
		// load the contact form using ajax
		$.get(contactUrl, function(data){
			// create a modal dialog with the data
			$(data).modal({
				close: false,
				position: ["15%",],
				overlayId: 'contact-overlay',
				containerId: 'contact-container',
				onOpen: contact.open,
				onShow: contact.show,
				onClose: contact.close
			});
		});
	};
})(jQuery);

$(document).ready(function () {
	// preload images
	var contactUrl = '';
	var img = ['cancel.png', 'form_bottom.gif', 'form_top.gif', 'spinner.gif', 'send.png'];
	$(img).each(function () {
		var i = new Image();
		i.src = g_CSS_IMG + this;
	});
});
	

var contact = {
	message: null,
	open: function (dialog) {
		// add padding to the buttons in firefox/mozilla
		if ($.browser.mozilla) {
			$('#contact-container .contact-button').css({
				'padding-bottom': '2px'
			});
		}
		// input field font size
		if ($.browser.safari) {
			$('#contact-container .contact-input').css({
				'font-size': '.9em'
			});
		}

		// dynamically determine height
		var h = 280;
		if ($('#contact-subject').length) {
			h += 26;
		}
		if ($('#contact-cc').length) {
			h += 22;
		}

		var title = $('#contact-container .contact-title').html();
		$('#contact-container .contact-title').html('Loading...');
		dialog.overlay.fadeIn(200, function () {
			dialog.container.fadeIn(200, function () {
				dialog.data.fadeIn(200, function () {
					$('#contact-container .contact-content').animate({
						height: h
					}, function () {
						$('#contact-container .contact-title').html(title);
						$('#contact-container form').fadeIn(200, function () {
							$('#contact-container #contact-name').focus();

							$('#contact-container .contact-cc').click(function () {
								var cc = $('#contact-container #contact-cc');
								cc.is(':checked') ? cc.attr('checked', '') : cc.attr('checked', 'checked');
							});

							// fix png's for IE 6
							if ($.browser.msie && $.browser.version < 7) {
								$('#contact-container .contact-button').each(function () {
									if ($(this).css('backgroundImage').match(/^url[("']+(.*\.png)[)"']+$/i)) {
										var src = RegExp.$1;
										$(this).css({
											backgroundImage: 'none',
											filter: 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src="' +  src + '", sizingMethod="crop")'
										});
									}
								});
							}
						});
					});
				});
			});
		});
	},
	show: function (dialog) {
		$('#contact-container .contact-send').click(function (e) {
			e.preventDefault();
			// validate form
			if (contact.validate()) {
				$('#contact-container .contact-message').fadeOut(function () {
					$('#contact-container .contact-message').removeClass('contact-error').empty();
				});
				$('#contact-container .contact-title').html('Sending...');
				$('#contact-container form').fadeOut(200);
				$('#contact-container .contact-content').animate({
					height: '80px'
				}, function () {
					$('#contact-container .contact-loading').fadeIn(200, function () {
						$.ajax({
							url: contactUrl,
							data: $('#contact-container form').serialize() + '&action=send',
							type: 'post',
							cache: false,
							dataType: 'html',
							complete: function (xhr) {
								$('#contact-container .contact-loading').fadeOut(200, function () {
									$('#contact-container .contact-title').html('Thank you!');
									$('#contact-container .contact-message').html(xhr.responseText).fadeIn(200);
								});
							},
							error: contact.error
						});
					});
				});
			}
			else {
				if ($('#contact-container .contact-message:visible').length > 0) {
					var msg = $('#contact-container .contact-message div');
					msg.fadeOut(200, function () {
						msg.empty();
						contact.showError();
						msg.fadeIn(200);
					});
				}
				else {
					$('#contact-container .contact-message').animate({
						height: '30px'
					}, contact.showError);
				}
				
			}
		});
	},
	close: function (dialog) {
		$('#contact-container .contact-message').fadeOut();
		$('#contact-container .contact-title').html('Goodbye...');
		$('#contact-container form').fadeOut(200);
		$('#contact-container .contact-content').animate({
			height: 40
		}, function () {
			dialog.data.fadeOut(200, function () {
				dialog.container.fadeOut(200, function () {
					dialog.overlay.fadeOut(200, function () {
						$.modal.close();
					});
				});
			});
		});
	},
	error: function (xhr) {
		alert(xhr.statusText);
	},
	validate: function () {
		contact.message = '';
		if (!$('#contact-container #contact-name').val()) {
			contact.message += 'Name is required. ';
		}

		if (!$('#contact-container #contact-subject').val()) {
			contact.message += 'Subject is required. ';
		}

		var email = $('#contact-container #contact-email').val();
		if (!email) {
			contact.message += 'Email is required. ';
		}
		else {
			if (!contact.validateEmail(email)) {
				contact.message += 'Email is invalid. ';
			}
		}

		if (!$('#contact-container #contact-message').val()) {
			contact.message += 'Message is required.';
		}

		if (contact.message.length > 0) {
			return false;
		}
		else {
			return true;
		}
	},
	validateEmail: function (email) {
		var at = email.lastIndexOf("@");

		// Make sure the at (@) sybmol exists and  
		// it is not the first or last character
		if (at < 1 || (at + 1) === email.length)
			return false;

		// Make sure there aren't multiple periods together
		if (/(\.{2,})/.test(email))
			return false;

		// Break up the local and domain portions
		var local = email.substring(0, at);
		var domain = email.substring(at + 1);

		// Check lengths
		if (local.length < 1 || local.length > 64 || domain.length < 4 || domain.length > 255)
			return false;

		// Make sure local and domain don't start with or end with a period
		if (/(^\.|\.$)/.test(local) || /(^\.|\.$)/.test(domain))
			return false;

		// Check for quoted-string addresses
		// Since almost anything is allowed in a quoted-string address,
		// we're just going to let them go through
		if (!/^"(.+)"$/.test(local)) {
			// It's a dot-string address...check for valid characters
			if (!/^[-a-zA-Z0-9!#$%*\/?|^{}`~&'+=_\.]*$/.test(local))
				return false;
		}

		// Make sure domain contains only valid characters and at least one period
		if (!/^[-a-zA-Z0-9\.]*$/.test(domain) || domain.indexOf(".") === -1)
			return false;	

		return true;
	},
	showError: function () {
		$('#contact-container .contact-message')
			.html($('<div class="contact-error">').append(contact.message))
			.fadeIn(200);
	}
};

