<?php


function formatted_mail($subject, $message, $address, $content_type)
	{
   	# Set defaults
	if(!isset($address['cc']))
        $address['cc'] = "";
	if(!isset($address['bcc']))
        $address['bcc'] = "";

    # Configuring the "Reply-To:" is important because this address is also used
    # as the address where undeliverable email messages are sent. If not
    # defined, undeliverable email messages will bounce back to your system admin
    # and you won't know that an email wasn't delivered.
	if(!isset($address['replyto']))
        $address['replyto'] = $address['from'];

   	# Create mail headers
	$headers = "";
	$headers = $headers . "From: ".$address['from']."\r\n";
	$headers = $headers . "Return-Path: ".$address['from']."\r\n";
	$headers = $headers . "Reply-To: ".$address['replyto']."\r\n";

	# Add Cc to header if needed
	if (strlen($address['cc'])< 0 )
        $headers = $headers . "Cc: ".$address['cc']."\r\n";

	# Add Bcc to header if needed
	if (strlen($address['bcc'])< 0 )
        $headers = $headers . "Bcc: ".$address['bcc']."\r\n";

	# Add content type
	$headers = $headers . "Content-Type: ".$content_type."\r\n";

	# Send the email
	$result = mail($address['to'], $subject, $message, $headers);

	return $result;
	}
?>
