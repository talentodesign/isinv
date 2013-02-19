<?php
// Those default options are used ONLY on FIRST setup and on plugin updates but limited to
// new options that may have been added between your and new version.
//
// This is the main language file, too, which is always loaded by Newsletter. Other language
// files are loaded according the WPLANG constant defined in wp-config.php file. Those language
// specific files are "merged" with this one and the language specific configuration
// keys override the ones in this file.
//
// Language specific files only need to override configurations containing texts
// langiage dependant.

$options = array();
$options['profile_text'] = '{profile_form}<p>If you want to cancel your subscription, <a href="{unsubscription_confirm_url}">click here</a></p>';

// Subscription page introductory text (befor the subscription form)
$options['subscription_text'] =
"{subscription_form}";

// Message show after a subbscription request has made.
$options['confirmation_text'] =
"<p>You successfully subscribed to my newsletter.
You'll receive in few minutes a confirmation email. Follow the link
in it to confirm the subscription. If the email takes more than 15
minutes to appear in your mailbox, check the spam folder.</p>";

// Confirmation email subject (double opt-in)
$options['confirmation_subject'] =
"Confirm now your subscription to {blog_title}";

// Confirmation email body (double opt-in)
$options['confirmation_message'] =
"<p>Hi {name},</p>
<p>I received a subscription request for this email address. You can confirm it
<a href=\"{subscription_confirm_url}\"><strong>clicking here</strong></a>.
If you cannot click the link, use the following link:</p>
<p>{subscription_confirm_url}</p>
<p>If this subscription request has not been made from you, just ignore this message.</p>
<p>Thank you.</p>";


// Subscription confirmed text (after a user clicked the confirmation link
// on the email he received
$options['confirmed_text'] =
"<p>Your subscription has been confirmed!
Thank you {name}!</p>";

$options['confirmed_subject'] =
"Welcome aboard, {name}";

$options['confirmed_message'] =
"<p>The message confirm your subscription to {blog_title} newsletter.</p>
<p>Thank you!</p>
<p>If you want to cancel your unsubscription, <a href=\"{unsubscription_url}\">click here</a>, if you want to change your
subscription data, <a href=\"{profile_url}\">click here</a>.</p>";

// Unsubscription request introductory text
$options['unsubscription_text'] =
"<p>Please confirm you want to unsubscribe my newsletter
<a href=\"{unsubscription_confirm_url}\">clicking here</a>.";

// When you finally loosed your subscriber
$options['unsubscribed_text'] =
"<p>That make me cry, but I have removed your subscription...</p>";

$options['unsubscribed_subject'] =
"Goodbye, {name}";

$options['unsubscribed_message'] =
"<p>The message confirm your unsubscription to {blog_title} newsletter.</p>
<p>Good bye!</p>";
