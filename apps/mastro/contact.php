<?php
/**
 +----------------------------------------------------------------------+
 | A digital tale (C) 2009 Enrico Possenti :: dCTL                      |
 +----------------------------------------------------------------------+
 | Author:  NoveOPiu di Enrico Possenti <info@noveopiu.com>             |
 | License: Creative Commons License v3.0 (Attr-NonComm-ShareAlike      |
 |          http://creativecommons.org/licenses/by-nc-sa/3.0/           |
 +----------------------------------------------------------------------+
 | A main file for "mastro"                                          |
 +----------------------------------------------------------------------+
*/

 if (!defined('_INCLUDE')) define('_INCLUDE', true);

/* INITIALIZE */
require_once(str_replace('//','/',dirname(__FILE__).'/').'../_shared/config.inc.php');
require_once(str_replace(SYS_PATH_SEP_DOUBLE,SYS_PATH_SEP,dirname(__FILE__).SYS_PATH_SEP).'./config.inc.php');

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
 * Revision: $Id: contact-dist.php 164 2008-12-02 23:31:54Z emartin24 $
 *
 */

// User settings
$to = MAIL_TO;
$subject = "dCTL - Progetto \"Orlando Furioso\" - Segnalazione";

// Include extra form fields and/or submitter data?
// false = do not include
$extra = array(
	"form_subject"	=> true,
	"form_cc"		=> true,
	"ip"				=> true,
	"user_agent"	=> true
);

// Process
$action = isset($_REQUEST["action"]) ? $_REQUEST["action"] : "";
if (empty($action)) {
	// Send back the contact form HTML
	$output = "<div style='display:none'>
	<a href='#' title='Close' class='modalCloseX simplemodal-close'>x</a>
	<div class='contact-top'></div>
	<div class='contact-content'>
		<h1 class='contact-title'>Segnala un problema:</h1>
		<div class='contact-loading' style='display:none'></div>
		<div class='contact-message' style='display:none'></div>
		<form action='#' style='display:none'>
			<label for='contact-name'>*Nome:</label>
			<input type='text' id='contact-name' class='contact-input' name='name' tabindex='1001' />
			<label for='contact-email'>*Email:</label>
			<input type='text' id='contact-email' class='contact-input' name='email' tabindex='1002' />";

	if ($extra["form_subject"]) {
		$output .= "
			<label for='contact-subject'>*Problema:</label>
			<input type='text' id='contact-subject' class='contact-input' name='subject' value='' tabindex='1003' />";
	}

	$output .= "
			<label for='contact-message'>*Descrizione:</label>
			<textarea id='contact-message' class='contact-input' name='message' cols='40' rows='4' tabindex='1004'></textarea>
			<br/>";

	if ($extra["form_cc"]) {
		$output .= "
			<label>&#160;</label>
			<input type='checkbox' id='contact-cc' name='cc' value='1' tabindex='1005' /> <span class='contact-cc'>Invia una copia a me</span>
			<br/>";
	}

	$output .= "
			<label>&#160;</label>
			<button type='submit' class='contact-cancel contact-button simplemodal-close' tabindex='1007'>Chiudi</button>
			<button type='submit' class='contact-send contact-button' tabindex='1006'>Invia</button>
			<br/>
			<input type='hidden' name='token' value='" . smcf_token($to) . "'/>
		</form>
	</div>
	<div class='contact-bottom'> </div>
</div>";

	echo $output;
}
else if ($action == "send") {
	// Send the email
	$name = isset($_REQUEST["name"]) ? $_REQUEST["name"] : "";
	$email = isset($_REQUEST["email"]) ? $_REQUEST["email"] : "";
	$subject = isset($_REQUEST["subject"]) ? $_REQUEST["subject"] : $subject;
	$message = isset($_REQUEST["message"]) ? $_REQUEST["message"] : "";
	$cc = isset($_REQUEST["cc"]) ? $_REQUEST["cc"] : "";
	$token = isset($_REQUEST["token"]) ? $_REQUEST["token"] : "";

	// make sure the token matches
	if ($token === smcf_token($to)) {
		smcf_send($name, $email, $subject, $message, $cc);
		echo "Your message was successfully sent.";
	}
	else {
		echo "Unfortunately, your message could not be verified.";
	}
}

function smcf_token($s) {
	return md5("smcf-" . $s . date("WY"));
}

// Validate and send email
function smcf_send($name, $email, $subject, $message, $cc) {
	global $to, $extra;

	// Filter and validate fields
	$name = smcf_filter($name);
	$subject = smcf_filter($subject);
	$email = smcf_filter($email);
	if (!smcf_validate_email($email)) {
		$subject .= " - invalid email";
		$message .= "\n\nBad email: $email";
		$email = $to;
		$cc = 0; // do not CC "sender"
	}

	if (true) {

			$to = 'info@noveopiu.com';
			$host = '127.0.0.1';
			$port = '25';
			$auth = false;

			$subject = 'dCTL : Orlando Furioso';
		// Add additional info to the message
			if ($extra["ip"]) {
				$message .= "\n\nIP: " . $_SERVER["REMOTE_ADDR"];
			}
			if ($extra["user_agent"]) {
				$message .= "\n\nUSER AGENT: " . $_SERVER["HTTP_USER_AGENT"];
			}
			// Set and wordwrap message body
			$body = "From: $name\n\n";
			$body .= "Message: $message";
			$body = wordwrap($body, 70);
			// UTF-8
			if (function_exists('mb_encode_mimeheader')) {
				$subject = mb_encode_mimeheader($subject, "UTF-8", "B", "\n");
			}
			else {
				// you need to enable mb_encode_mimeheader or risk
				// getting emails that are not UTF-8 encoded
			}
 		include("../_shared/Net/Mail.php");
			$headers["From"]    = $email;
			if ($cc == 1) {
				$headers["Cc"]    = $email;
			}
			$headers["Reply-To"]    = $email;
			$headers["To"]      = MAIL_TO;
			$headers["Subject"] = $subject;
			$headers["X-Mailer"] = 'PHP/'.phpversion();
			$headers["MIME-Version"] = '1.0';
// 			$headers["Content-Type"] = 'multipart/alternative; charset=utf-8';
  	$headers["Content-Type"] = 'text/plain; charset=utf-8';
			$headers["Content-Transfer-Encoding"] = 'quoted-printable';// '8bit';
			$params["host"] = $host;
			$params["port"] = $port;
			$params["auth"] = $auth;
			// Create the mail object using the Mail::factory method
			$params["debug"] = false;
			$params["body"] = $body;
			$mail_object =& Mail::factory("smtp", $params);
			if (PEAR::isError($e = $mail_object->send($to, $headers, $body))) {
				die('Messaggio inviato, grazie');
			} else {
				die('Errore nell\'invio, scrivere direttamente a info@ctl.sns.it');
			};

	} else {

	// Add additional info to the message
		if ($extra["ip"]) {
			$message .= "\n\nIP: " . $_SERVER["REMOTE_ADDR"];
		}
		if ($extra["user_agent"]) {
			$message .= "\n\nUSER AGENT: " . $_SERVER["HTTP_USER_AGENT"];
		}

		// Set and wordwrap message body
		$body = "From: $name\n\n";
		$body .= "Message: $message";
		$body = wordwrap($body, 70);

		// Build header
		$headers = "From: $email\n";
		if ($cc == 1) {
			$headers .= "Cc: $email\n";
		}
		$headers .= "X-Mailer: PHP/SimpleModalContactForm";

		// UTF-8
		if (function_exists('mb_encode_mimeheader')) {
			$subject = mb_encode_mimeheader($subject, "UTF-8", "B", "\n");
		}
		else {
			// you need to enable mb_encode_mimeheader or risk
			// getting emails that are not UTF-8 encoded
		}
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/plain; charset=utf-8\n";
		$headers .= "Content-Transfer-Encoding: quoted-printable\n";

		// Send email
		@mail($to, $subject, $body, $headers) or
			die("Unfortunately, a server issue prevented delivery of your message.");
		};
}

// Remove any un-safe values to prevent email injection
function smcf_filter($value) {
	$pattern = array("/\n/","/\r/","/content-type:/i","/to:/i", "/from:/i", "/cc:/i");
	$value = preg_replace($pattern, "", $value);
	return $value;
}

// Validate email address format in case client-side validation "fails"
function smcf_validate_email($email) {
	$at = strrpos($email, "@");

	// Make sure the at (@) sybmol exists and
	// it is not the first or last character
	if ($at && ($at < 1 || ($at + 1) == strlen($email)))
		return false;

	// Make sure there aren't multiple periods together
	if (preg_match("/(\.{2,})/", $email))
		return false;

	// Break up the local and domain portions
	$local = substr($email, 0, $at);
	$domain = substr($email, $at + 1);


	// Check lengths
	$locLen = strlen($local);
	$domLen = strlen($domain);
	if ($locLen < 1 || $locLen > 64 || $domLen < 4 || $domLen > 255)
		return false;

	// Make sure local and domain don't start with or end with a period
	if (preg_match("/(^\.|\.$)/", $local) || preg_match("/(^\.|\.$)/", $domain))
		return false;

	// Check for quoted-string addresses
	// Since almost anything is allowed in a quoted-string address,
	// we're just going to let them go through
	if (!preg_match('/^"(.+)"$/', $local)) {
		// It's a dot-string address...check for valid characters
		if (!preg_match('/^[-a-zA-Z0-9!#$%*\/?|^{}`~&\'+=_\.]*$/', $local))
			return false;
	}

	// Make sure domain contains only valid characters and at least one period
	if (!preg_match("/^[-a-zA-Z0-9\.]*$/", $domain) || !strpos($domain, "."))
		return false;

	return true;
}

exit;

?>
