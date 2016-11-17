<?php
/**
 * This example shows settings to use when sending via Google's Gmail servers.
 */

require __DIR__.'/../env.php';
require __DIR__.'/../vendor/autoload.php';

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

//Create a new PHPMailer instance
$mail = new PHPMailerOAuth;

//Tell PHPMailer to use SMTP
$mail->isSMTP();

//Enable SMTP debugging
$mail->SMTPDebug = 0;

//How to handle debug output
$mail->Debugoutput = 'html';

//Set the hostname of the mail server
$mail->Host = 'smtp.gmail.com';

//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 587;

//Set the encryption system to use - ssl (deprecated) or tls
$mail->SMTPSecure = 'tls';

//Whether to use SMTP authentication
$mail->SMTPAuth = true;

//Set AuthType
$mail->AuthType = 'XOAUTH2';

//User Email to use for SMTP authentication - Use the same email used in Google Developer Console
$mail->oauthUserEmail = OAUTH_USER_EMAIL;

//Obtained From Google Developer Console
$mail->oauthClientId = OAUTH_CLIENT_ID;

//Obtained From Google Developer Console
$mail->oauthClientSecret = OAUTH_CLIENT_SECRET;

//Obtained after setting up APP in Google Developer Console
$mail->oauthRefreshToken = OAUTH_REFRESH_TOKEN;

//Set who the message is to be sent from
//For Gmail, this generally needs to be the same as the user you logged in as
$mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);

//Set who the message is to be sent to
$mail->addAddress(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);

//Set to who the message is to be reply to
$mail->addReplyTo(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);

foreach (MAIL_RECIPIENTS as $recipient) {
    $mail->addBCC($recipient['address'], $recipient['name']);
}

//Set the subject line
$mail->Subject = MAIL_SUBJECT;

//Set the message encoding
$mail->CharSet = 'UTF-8';

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML(file_get_contents('../resources/templates/'. MAIL_TEMPLATE .'/index.html'), dirname(__FILE__));

//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
