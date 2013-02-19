<?php
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterEmails::instance();


// Always required
$email_id = $_GET['id'];
$email = Newsletter::instance()->get_email($email_id, ARRAY_A);

// If there is no action we assume we are enter the first time so we populate the
// $nc->data with the editable email fields
if (!$controls->is_action()) {
    $controls->data = $email;
    if (!empty($email['preferences'])) $controls->data['preferences'] = explode(',', $email['preferences']);
    if (!empty($email['sex'])) $controls->data['sex'] = explode(',', $email['sex']);
}

if ($controls->is_action('test') || $controls->is_action('save') || $controls->is_action('send') || $controls->is_action('editor')) {

    // If we were editing with visual editor (==0), we must read the extra <body> content
    if ($email['editor'] == 0) {
        $x = strpos($email['message'], '<body');
        if ($x !== false) {
            $x = strpos($email['message'], '>', $x);
            $email['message'] = substr($email['message'], 0, $x + 1) . $controls->data['message'] . '</body></html>';
        } else {
            $email['message'] = '<html><body>' . $controls->data['message'] . '</body></html>';
        }
    } else {
        $email['message'] = $controls->data['message'];
    }
    $email['message_text'] = $controls->data['message_text'];
    $email['subject'] = $controls->data['subject'];
    $email['track'] = $controls->data['track'];

    if (is_array($controls->data['preferences'])) $email['preferences'] = implode(',', $controls->data['preferences']);
    else $email['preferences'] = '';

    if (is_array($controls->data['sex'])) $email['sex'] = implode(',', $controls->data['sex']);
    else $email['sex'] = '';

    // Before send, we build the query to extract subscriber, so the delivery engine does not
    // have to worry about the email parameters
    $query = "select * from " . $wpdb->prefix . "newsletter where status='C'";

    $preferences = $controls->data['preferences'];
    if (is_array($preferences)) {
        $query .= " and (";
        foreach ($preferences as $x) {
            $query .= "list_" . $x . "=1 or ";
        }
        $query = substr($query, 0, -4);
        $query .= ")";
    }

    $sex = $controls->data['sex'];
    if (is_array($sex)) {
        $query .= " and sex in (";
        foreach ($sex as $x) {
            $query .= "'" . $x . "', ";
        }
        $query = substr($query, 0, -2);
        $query .= ")";
    }

    $email['query'] = $query;
    if ($controls->is_action('test')) {
        $email['total'] = 0;
    } else {
        $email['total'] = $wpdb->get_var(str_replace('*', 'count(*)', $query));
    }
    $email['sent'] = 0;
    $email['last_id'] = 0;
    $email['send_on'] = $controls->data['send_on'];
    
    if ($controls->is_action('editor')) {
        $email['editor'] = $email['editor'] == 0?1:0;
    }

    Newsletter::instance()->save_email($email);
    
    $controls->data['message'] = $email['message'];
}

if ($controls->is_action('send')) {

    $wpdb->update($wpdb->prefix . 'newsletter_emails', array('status' => 'sending'), array('id' => $email_id));
    $email['status'] = 'sending';
    $controls->messages = "Email added to the queue.";
}

if ($controls->is_action('pause')) {
    $wpdb->update($wpdb->prefix . 'newsletter_emails', array('status' => 'paused'), array('id' => $email_id));
    $email['status'] = 'paused';
}

if ($controls->is_action('continue')) {
    $wpdb->update($wpdb->prefix . 'newsletter_emails', array('status' => 'sending'), array('id' => $email_id));
    $email['status'] = 'sending';
}

if ($controls->is_action('abort')) {
    $wpdb->query("update " . $wpdb->prefix . "newsletter_emails set last_id=0, total=0, sent=0, status='new' where id=" . $email_id);
    $email['status'] = 'new';
    $email['total'] = 0;
    $email['sent'] = 0;
    $email['last_id'] = 0;
    $controls->messages = "Sending aborted.";
}

if ($controls->is_action('test')) {
    $users = NewsletterUsers::instance()->get_test_users();
    if (count($users) == 0) {
        $controls->errors = 'There is no test subscribers to who send this email. Mark some subscribers as test subscriber from the Subscriber panel.';
    } else {
        Newsletter::instance()->send(Newsletter::instance()->get_email($email_id), $users);
        $controls->messages = 'Test emails sent to ' . count($users) . ' test users.';
    }
}


if ($email['editor'] == 0) {
    $x = strpos($controls->data['message'], '<body');
    // Some time the message in $nc->data is already cleaned up, it depends on action called
    if ($x !== false) {
        $x = strpos($controls->data['message'], '>', $x);
        $y = strpos($controls->data['message'], '</body>');

        $controls->data['message'] = substr($controls->data['message'], $x + 1, $y - $x - 1);
    }
}
?>

<script type="text/javascript" src="<?php echo NEWSLETTER_URL; ?>/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
    tinyMCE.init({
        mode : "specific_textareas",
        editor_selector : "visual",
        theme : "advanced",
        plugins: "table,fullscreen,legacyoutput",
        theme_advanced_disable : "styleselect",
        theme_advanced_buttons1_add: "forecolor,blockquote,code",
        theme_advanced_buttons3 : "tablecontrols,fullscreen",
        relative_urls : false,
        theme_advanced_statusbar_location: "bottom",
        remove_script_host : false,
        theme_advanced_resizing : true,
        theme_advanced_toolbar_location : "top",
        document_base_url : "<?php echo get_option('home'); ?>/",
        content_css: "<?php echo NEWSLETTER_URL . '/emails/css.php?id=' . $email_id . '&' . time(); ?>"
    });

    jQuery(document).ready(function() {
        jQuery('#upload_image_button').click(function() {
            tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
            return false;
        });

        window.send_to_editor = function(html) {
            imgurl = jQuery('img',html).attr('src');
            //jQuery('#upload_image').val(imgurl);
            tinyMCE.execCommand('mceInsertContent',false,'<img src="' + imgurl + '" />');
            tb_remove();
        }
    });
</script>

<div class="wrap">

    <?php $help_url = 'http://www.satollo.net/plugins/newsletter/newsletters-module'; ?>
    <?php include NEWSLETTER_DIR . '/header.php'; ?>

    <h2>Newsletters Module</h2>

    <?php $controls->show(); ?>

    <form method="post" action="" id="newsletter-form">
        <?php $controls->init(); ?>

        <p class="submit">
            <?php if ($email['status'] != 'sending') $controls->button('save', 'Save'); ?>
            <?php if ($email['status'] != 'sending') $controls->button_confirm('test', 'Save and test', 'Save and send test emails to test addresses?'); ?>

            <?php if ($email['status'] == 'new') $controls->button_confirm('send', 'Send', 'Start a real delivery?'); ?>
            <?php if ($email['status'] == 'sending') $controls->button_confirm('pause', 'Pause', 'Pause the delivery?'); ?>
            <?php if ($email['status'] == 'paused') $controls->button_confirm('continue', 'Continue', 'Continue the delivery?'); ?>
            <?php if ($email['status'] != 'new') $controls->button_confirm('abort', 'Abort', 'Abort the delivery?'); ?>
            <?php $controls->button_confirm('editor', 'Save and switch to ' . ($email['editor'] == 0 ? 'HTML source' : 'visual') . ' editor', 'Sure?'); ?>
        </p>

        <div id="tabs">
            <ul>
                <li><a href="#tabs-1">Message</a></li>
                <li><a href="#tabs-2">Message (textual)</a></li>
                <li><a href="#tabs-3">Who will receive it</a></li>
                <li><a href="#tabs-4">Status</a></li>
            </ul>


            <div id="tabs-1">
                <table class="form-table">
                    <tr valign="top">
                        <th>Subject</th>
                        <td>
                            <?php $controls->text('subject', 70); ?>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th>Message</th>
                        <td>
                            <input id="upload_image_button" type="button" value="Choose or upload an image" />
                            <?php $email['editor'] == 0 ? $controls->editor('message', 30) : $controls->textarea_fixed('message', '100%', '400'); ?>
                            <div class="hints">
                                Tags: <strong>{name}</strong> receiver name;
                                <strong>{unsubscription_url}</strong> unsubscription URL;
                                <strong>{token}</strong> the subscriber token; <strong>{profile_url}</strong> link to user subscription options page;
                                <strong>{np_aaa}</strong> user profile data named "aaa".
                            </div>
                        </td>
                    </tr>
                </table>
            </div>


            <div id="tabs-2">
                <p>
                    This is the textual version of your newsletter. If you empty it, only an HTML version will be sent but
                    is an anti-spam best practice to include a text only version.
                </p>
                <table class="form-table">
                    <tr valign="top">
                        <th>Message</th>
                        <td>
                            <?php $controls->textarea_fixed('message_text', '100%', '250'); ?>
                        </td>
                    </tr>
                </table>
            </div>


            <div id="tabs-3">
                <table class="form-table">
                    <tr valign="top">
                        <th>Approximative number of receivers</th>
                        <td>
                            <?php
                            // Compute the receivers ids that should  receive that email
                            $query = "select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='C'";

                            if (is_array($controls->data['preferences'])) {
                                $query .= " and (";
                                foreach ($controls->data['preferences'] as $x) {
                                    $query .= "list_" . $x . "=1 or ";
                                }
                                $query = substr($query, 0, -4);
                                $query .= ")";
                            }

                            if (is_array($controls->data['sex'])) {
                                $query .= " and sex in (";
                                foreach ($controls->data['sex'] as $x) {
                                    $query .= "'" . $x . "', ";
                                }
                                $query = substr($query, 0, -2);
                                $query .= ")";
                            }
                            echo $wpdb->get_var($query);
                            ?>
                            <div class="hints">
                            If you change selections below, save the email to update this values.
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Sex</th>
                        <td>
                            <div class="nl-checkbox-group"><?php $controls->checkbox_group('sex', 'f', 'Women'); ?></div>
                            <div class="nl-checkbox-group"><?php $controls->checkbox_group('sex', 'm', 'Men'); ?></div>
                            <div class="nl-checkbox-group"><?php $controls->checkbox_group('sex', 'n', 'Not specified'); ?></div>
                            <div style="clear: both"></div>
                            <div class="hints">
                                Leaving all sex options unselected means to NOT filter by sex.
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Preferences</th>
                        <td>
                            <?php $controls->preferences_group('preferences', true); ?>
                            <div style="clear: both"></div>
                            <div class="hints">
                                Leaving all preferences unselected means to NOT filter by preference.
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Track message links?</th>
                        <td>
                            <?php $controls->yesno('track'); ?>
                            <div class="hints">
                                When this option is enabled, each link in the email text will be rewritten and clicks
                                on them intercepted.
                            </div>
                        </td>
                    </tr>
                </table>
            </div>


            <div id="tabs-4">
                <table class="form-table">
                    <tr valign="top">
                        <th>Send on</th>
                        <td>
                            <?php $controls->datetime('send_on'); ?> (<?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format')); ?> )
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Email status</th>
                        <td><?php echo $email['status']; ?></td>
                    </tr>
                    <tr valign="top">
                        <th>Email sent</th>
                        <td><?php echo $email['sent']; ?> of <?php echo $email['total']; ?></td>
                    </tr>
                </table>
            </div>


        </div>

    </form>
</div>
