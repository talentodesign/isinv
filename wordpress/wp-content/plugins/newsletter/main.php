<?php
@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$controls = new NewsletterControls();

if (!$controls->is_action()) {
    $controls->data = get_option('newsletter_main');
} else {
    if ($controls->is_action('remove')) {

        $wpdb->query("delete from " . $wpdb->prefix . "options where option_name like 'newsletter%'");

        $wpdb->query("drop table " . $wpdb->prefix . "newsletter, " . $wpdb->prefix . "newsletter_stats, " .
                $wpdb->prefix . "newsletter_emails, " . $wpdb->prefix . "newsletter_profiles, " .
                $wpdb->prefix . "newsletter_work");

        echo 'Newsletter plugin destroyed. Please, deactivate it now.';
        return;
    }

    if ($controls->is_action('save')) {
        $errors = null;

        // Validation
        $controls->data['sender_email'] = $newsletter->normalize_email($controls->data['sender_email']);
        if (!$newsletter->is_email($controls->data['sender_email'])) {
            $controls->errors .= 'The sender email address is not correct.<br />';
        }

        $controls->data['return_path'] = $newsletter->normalize_email($controls->data['return_path']);
        if (!$newsletter->is_email($controls->data['return_path'], true)) {
            $controls->errors .= 'Return path email is not correct.<br />';
        }

        $controls->data['test_email'] = $newsletter->normalize_email($controls->data['test_email']);
        if (!$newsletter->is_email($controls->data['test_email'], true)) {
            $controls->errors .= 'Test email is not correct.<br />';
        }

        $controls->data['reply_to'] = $newsletter->normalize_email($controls->data['reply_to']);
        if (!$newsletter->is_email($controls->data['reply_to'], true)) {
            $controls->errors .= 'Reply to email is not correct.<br />';
        }

        if (empty($controls->errors)) {
            update_option('newsletter_main', $controls->data);
        }
    }

    if ($controls->is_action('smtp_test')) {

        require_once ABSPATH . WPINC . '/class-phpmailer.php';
        require_once ABSPATH . WPINC . '/class-smtp.php';
        $mail = new PHPMailer();

        $mail->IsSMTP();
        $mail->SMTPDebug = true;
        $mail->CharSet = 'UTF-8';
        $message = 'This Email is sent by PHPMailer of WordPress';
        $mail->IsHTML(false);
        $mail->Body = $message;
        $mail->From = $controls->data['sender_email'];
        $mail->FromName = $controls->data['sender_name'];
        if (!empty($controls->data['return_path'])) $mail->Sender = $options['return_path'];
        if (!empty($controls->data['reply_to'])) $mail->AddReplyTo($controls->data['reply_to']);

        $mail->Subject = '[' . get_option('blogname') . '] SMTP test';

        $mail->Host = $controls->data['smtp_host'];
        if (!empty($controls->data['smtp_port'])) $mail->Port = (int) $controls->data['smtp_port'];

        $mail->SMTPSecure = $controls->data['smtp_secure'];

        if (!empty($controls->data['smtp_user'])) {
            $mail->SMTPAuth = true;
            $mail->Username = $controls->data['smtp_user'];
            $mail->Password = $controls->data['smtp_pass'];
        }

        $mail->SMTPKeepAlive = true;
        $mail->ClearAddresses();
        $mail->AddAddress($controls->data['test_email']);
        ob_start();
        $mail->Send();
        $mail->SmtpClose();
        $debug = htmlspecialchars(ob_get_clean());

        if ($mail->IsError()) $controls->errors = $mail->ErrorInfo;
        else $controls->messages = 'Success.';

        $controls->messages .= '<textarea style="width:100%;height:250px;font-size:10px">';
        $controls->messages .= $debug;
        $controls->messages .= '</textarea>';
    }
}
?>

<div class="wrap">

    <?php $help_url = 'http://www.satollo.net/plugins/newsletter/newsletter-configuration'; ?>
    <?php include NEWSLETTER_DIR . '/header.php'; ?>

    <h2>Newsletter Main Configuration</h2>

    <?php $controls->show(); ?>

    <div class="preamble">
    <p>
        Do not be scared by all those configurations. Only <strong>basic settings</strong> are important and should be reviewed to
        make Newsletter plugin to work correctly. If something seems to now work, run a test reading what you should expect from it.
    </p>
    </div>

    <form method="post" action="">
        <?php $controls->init(); ?>

        <div id="tabs">

            <ul>
                <li><a href="#tabs-1">Basic settings</a></li>
                <li><a href="#tabs-2">Advanced settings</a></li>
                <li><a href="#tabs-5">SMTP</a></li>
                <li><a href="#tabs-3">Content locking</a></li>
            </ul>

            <div id="tabs-1">

                <!-- Main settings -->

                <p class="intro">
                    Configurations on this sub panel can block emails sent by Newsletter Pro. It's not a plugin limit but odd restrictions imposed by
                    hosting providers. It's advisable to careful read the detailed documentation you'll found under every options, specially on the "return path"
                    field. Try different combination of setting below before send a support request and do it in this way: one single change - test - other single
                    change - test, and so on. Thank you for your collaboration.
                </p>

                <table class="form-table">

                    <tr valign="top">
                        <th>Sender name and address</th>
                        <td>
                            email address (required): <?php $controls->text_email('sender_email', 40); ?>
                            name (optional): <?php $controls->text('sender_name', 40); ?>

                            <div class="hints">
                                These are the name and email address a subscriber will see on emails he'll receive.
                                Be aware that hosting providers can block email with a sender address not of the same domain of the blog.<br />
                                For example, if your blog is www.myblog.com, using as sender email "info@myblog.com" or
                                "newsletter@myblog.com" is safer than using "myaccount@gmail.com". The name is optional but is more professional
                                to set it (even if some providers with bugged mail server do not send email with a sender name set as reported by
                                a customer).
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Max emails per hour</th>
                        <td>
                            <?php $controls->text('scheduler_max', 5); ?>
                            <div class="hints">
                                The internal engine of Newsletter Pro sends email with the specified rate to stay under
                                provider limits. The default value is 100 a very low value. The right value for you
                                depends on your provider or server capacity.<br />
                                Some examples. Hostgator: 500. Dreamhost: 100, asking can be raised to 200. Go Daddy: 1000 per day using their SMTP,
                                unknown per hour rate. Gmail: 500 per day using their SMTP, unknown per hour rate.<br />
                                My sites are on Hostgator or Linode VPS.<br />
                                If you have a service with no limits on the number of emails, still PHP have memory and time limits. Newsletter Pro
                                does it's best to detect those limits and to respect them so it can send out less emails per hour than excepted.
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Return path</th>
                        <td>
                            <?php $controls->text_email('return_path', 40); ?> (valid email address)
                            <div class="hints">
                                This is the email address where delivery error messages are sent. Error message are sent back from mail systems when
                                an email cannot be delivered to the receiver (full mailbox, unrecognized user and invalid address are the most common
                                errors).<br />
                                <strong>Some providers do not accept this field and block emails is present or if the email address has a
                                    different domain of the blog</strong> (see above the sender field notes). If you experience problem sending emails
                                (just do some tests), try to leave it blank.
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Reply to</th>
                        <td>
                            <?php $controls->text_email('reply_to', 40); ?> (valid email address)
                            <div class="hints">
                                This is the email address where subscribers will reply (eg. if they want to reply to a newsletter). Leave it blank if
                                you don't want to specify a different address from the sender email above. As for return path, come provider do not like this
                                setting active.
                            </div>
                        </td>
                    </tr>

                </table>
            </div>

            <div id="tabs-2">

                <!-- General parameters -->

                <table class="form-table">

                    <tr valign="top">
                        <th>Enable access to editors?</th>
                        <td>
                            <?php $controls->yesno('editor'); ?>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th>API key</th>
                        <td>
                            <?php $controls->text('api_key', 40); ?>
                            <div class="hints">
                                When non-empty can be used to directly call the API for external integration. See API documentation on
                                documentation panel.
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th>Styling</th>
                        <td>
                            <?php $controls->textarea('css'); ?>
                            <div class="hints">
                                Add here your own css to style the forms. The whole form is enclosed in a div with class
                                "newsletter" and it's made with a table (guys, I know about your table less design
                                mission, don't blame me too much!)
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Email body content encoding</th>
                        <td>
                            <?php $controls->select('content_transfer_encoding', array('' => 'Default', '8bit' => '8 bit', 'base64' => 'Base 64')); ?>
                            <div class="hints">
                                Used only by some modules. Choose base64 to have chunked email body when server reports too long email line.
                            </div>
                        </td>
                    </tr>

                </table>

            </div>


            <div id="tabs-5">
                <div class="tab-preamble">
                <p>
                    To use an external SMTP (mail sending service), fill in the SMTP data and activate it. SMTP will be used for any
                    messages sent by Newsletter (subscription messages and newsletters). SMTP is required to send email with Gmail or
                    GoDaddy hosting account.
                    Read more <a href="http://www.satollo.net/godaddy-using-smtp-external-server-on-shared-hosting" target="_blank">here</a>.
                    Test button below sends an email to the first test address configured above and works even if SMTP is not enabled. If you get a "connection refused"
                    message, check the SMTP settings if they are correct and then contact your hosting provider. If you get a "relay denied" contact your
                    SMTP service provider.
                </p>
                <p>
                    Consider <a href="http://sendgrid.tellapal.com/a/clk/3ZVbH7" target="_blank">SendGrid</a> for a serious and reliable SMTP service.
                </p>
                </div>

                <table class="form-table">
                    <tr>
                        <th>Enable external SMTP?</th>
                        <td><?php $controls->yesno('smtp_enabled'); ?></td>
                    </tr>
                    <tr>
                        <th>SMTP host/port</th>
                        <td>
                            host: <?php $controls->text('smtp_host', 30); ?>
                            port: <?php $controls->text('smtp_port', 6); ?>
                            <?php $controls->select('smtp_secure', array('' => 'No secure protocol', 'tls' => 'TLS protocol', 'ssl' => 'SSL protocol')); ?>
                            <div class="hints">
                                Leave port empty for default value (25). To use Gmail try host "smtp.gmail.com" and port "465" and SSL protocol (without quotes).
                                For GoDaddy use "relay-hosting.secureserver.net".
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Authentication</th>
                        <td>
                            user: <?php $controls->text('smtp_user', 30); ?>
                            password: <?php $controls->text('smtp_pass', 30); ?>
                            <div class="hints">
                                If authentication is not required, leave "user" field blank.
                            </div>
                        </td>
                    </tr>
                </table>
                <?php $controls->button('smtp_test', 'Test'); ?>

            </div>


            <div id="tabs-3">
                <!-- Content locking -->
                <div class="tab-preamble">
                    <p><a href="http://www.satollo.net/plugins/newsletter/newsletter-locked-content" target="_blank">Read more about locked content</a>.</p>
                <p>
                    Content locking is a special feature that permits to "lock out" pieces of post content hiding them and unveiling
                    them only to newsletter subscribers. I use it to hide special content on some post inviting the reader to subscribe the newsletter
                    to read them.<br />
                    Content on post can be hidden surrounding it with [newsletter_lock] and [/newsletter_lock] short codes.<br />
                    A subscribe can see the hidden content after sign up or following a link on newsletters and welcome email generated by
                    {unlock_url} tag. That link bring the user to the URL below that should be a single premium post/page where there is the hidden
                    content or a list of premium posts with hidden content. The latter option can be implemented tagging all premium posts with a
                    WordPress tag or adding them to a specific WordPress category.
                </p>
                </div>
                <table class="form-table">
                    <tr valign="top">
                        <th>Unlock destination URL</th>
                        <td>
                            <?php $controls->text('lock_url', 70); ?>
                            <div class="hints">
                                This is a web address (URL) where users are redirect when they click on unlocking URL ({unlock_url})
                                inserted in newsletters and welcome message. Usually you will redirect the user on a URL with with locked content
                                (that will become visible) or in a place with a list of link to premium content. I send them on a tag page
                                (http://www.satollo.net/tag/reserved) since I tag every premium content with "reserved".
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Denied content message</th>
                        <td>
                            <?php $controls->textarea('lock_message'); ?>
                            <div class="hints">
                                This message is shown in place of protected post or page content which is surrounded with
                                [newsletter_lock] and [/newsletter_lock] short codes.<br />
                                Use HTML to format the message. PHP code is accepted and executed. WordPress short codes provided
                                by other plugins work as well. It's a good
                                practice to add the short code [newsletter_embed] to show a subscription form so readers can sign
                                up the newsletter directly.<br />
                                You can also add a subscription HTML form right here, like:<br />
                                <br />
                                &lt;form&gt;<br />
                                Your email: &lt;input type="text" name="ne"/&gt;<br />
                                &lt;input type="submit" value="Subscribe now!"/&gt;<br />
                                &lt;/form&gt;<br />
                                <br />
                                There is no need to specify a form method or action, Newsletter Pro will take care of. To give more evidence of your
                                alternative content you can style it:<br />
                                <br />
                                &lt;div style="margin: 15px; padding: 15px; background-color: #ff9; border-color: 1px solid #000"&gt;<br />
                                blah, blah, blah...<br />
                                &lt;/div&gt;
                            </div>
                        </td>
                    </tr>
                </table>

            </div>


        </div> <!-- tabs -->

        <p class="submit">
            <?php $controls->button('save', 'Save'); ?>
            <?php $controls->button_confirm('remove', 'Totally remove this plugin', 'Really sure to totally remove this plugin. All data will be lost!'); ?>
        </p>

    </form>
    <p></p>
</div>
