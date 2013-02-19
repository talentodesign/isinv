<?php
@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$controls = new NewsletterControls();

if ($controls->is_action('save')) {
        update_option('newsletter_log_level', $controls->data['log_level']);
        update_option('newsletter_diagnostic', $controls->data);
    $controls->messages = 'Loggin levels saved.';
}

if ($controls->is_action('trigger')) {
    $newsletter->hook_newsletter();
    $controls->messages = 'Delivery engine triggered.';
}

if ($controls->is_action('undismiss')) {
    update_option('newsletter_dismissed', array());
    $controls->messages = 'Notices restored.';
}

if ($controls->is_action('trigger_followup')) {
    NewsletterFollowup::instance()->send();
    $controls->messages = 'Follow up delivery engine triggered.';
}

if ($controls->is_action('engine_on')) {
    wp_clear_scheduled_hook('newsletter');
    wp_schedule_event(time() + 30, 'newsletter', 'newsletter');
    $controls->messages = 'Delivery engine reactivated.';
}

if ($controls->is_action('upgrade')) {
    // TODO: Compact them in a call to Newsletter which should be able to manage the installed modules
    Newsletter::instance()->upgrade();
    NewsletterUsers::instance()->upgrade();
    NewsletterSubscription::instance()->upgrade();
    NewsletterEmails::instance()->upgrade();
    NewsletterStatistics::instance()->upgrade();
    $controls->messages = 'Upgrade forced!';
}

if ($controls->is_action('delete_transient')) {
    delete_transient($_POST['btn']);
    $controls->messages = 'Deleted.';
}

if ($controls->is_action('test_wp')) {

    if ($controls->data['test_email'] == $newsletter->options['sender_email']) {
        $controls->messages .= 'You are using as test email the same configured as sender email. Test can fail because that.<br />';
    }

    $text = 'This is a simple test email sent directly with the WordPress mailing functionality' . "\r\n" .
            'in the same way WordPress sends notifications of new comment or registered users.' . "\r\n\r\n" .
            'This email is in pure text format and the sender should be wordpress@youdomain.tld (but it can be forced to be different with specific plugins.';

    $r = wp_mail($controls->data['test_email'], 'Newsletter: direct WordPress email test', $text);

    if ($r) {
        $controls->messages .= 'Direct WordPress email sent<br />';
    } else {
        $controls->errors .= 'Direct WordPress email NOT sent: ask your provider if your web space is enabled to send emails.<br />';
    }
}

if ($controls->is_action('send_test')) {

    if ($controls->data['test_email'] == $controls->data['sender_email']) {
        $controls->messages .= 'You are using as test email the same configured as sender email. Test can fail because that.<br />';
    }

    $text = 'This is a pure textual email sent using the sender data set on basic Newsletter settings.' . "\r\n" .
            'You should see it to come from the email address you set on basic Newsletter plugin setting.';
    $r = $newsletter->mail($controls->data['test_email'], 'Newsletter: pure text email', array('text' => $text));


    if ($r) $controls->messages .= 'Newsletter TEXT test email sent.<br />';
    else
            $controls->errors .= 'Newsletter TEXT test email NOT sent: try to change the sender data, remove the return path and the reply to settings.<br />';

    $text = '<p>This is a <strong>html</strong> email sent using the <i>sender data</i> set on Newsletter main setting.</p>';
    $text .= '<p>You should see some "mark up", like bold and italic characters.</p>';
    $text .= '<p>You should see it to come from the email address you set on basic Newsletter plugin setting.</p>';
    $r = $newsletter->mail($controls->data['test_email'], 'Newsletter: pure html email', $text);
    if ($r) $controls->messages .= 'Newsletter HTML test email sent.<br />';
    else
            $controls->errors .= 'Newsletter HTML test email NOT sent: try to change the sender data, remove the return path and the reply to settings.<br />';


    $text = array();
    $text['html'] = '<p>This is an <b>HTML</b> test email part sent using the sender data set on Newsletter main setting.</p>';
    $text['text'] = 'This is a textual test email part sent using the sender data set on Newsletter main setting.';
    $r = $newsletter->mail($controls->data['test_email'], 'Newsletter: both textual and html email', $text);
    if ($r) $controls->messages .= 'Newsletter: both textual and html test email sent.<br />';
    else
            $controls->errors .= 'Newsletter both TEXT and HTML test email NOT sent: try to change the sender data, remove the return path and the reply to settings.<br />';
}

if (empty($controls->data)) $controls->data = get_option('newsletter_diagnostic');

?>
<div class="wrap">
    <?php $help_url = 'http://www.satollo.net/plugins/newsletter/newsletter-diagnostic'; ?>
    <?php include NEWSLETTER_DIR . '/header.php'; ?>

    <h2>Newsletter Diagnostic</h2>

    <?php $controls->show(); ?>

    <p>
        If something is not working, here there are test procedures and diagnostic data. But before start take a little time to
        write down a list of modifications you coulf have made recently. Sender email or name? Return path? Reply to?
    </p>

    <form method="post" action="">
        <?php $controls->init(); ?>

        <h3>Test</h3>
        <table class="form-table">
            <tr>
                <th>Email test</th>
                <td>
                    <?php $controls->text('test_email'); ?>
                    <?php $controls->button('test_wp', 'Send an email with WordPress'); ?>
                    <?php $controls->button('send_test', 'Send test emails to this address'); ?>
                    <div class="hints">
                        Some test emails will be sent to the specified address:<br />
                        1. One with the native mail functionality of WordPress as is, so the email should come fro wordpress@yourdomain.tld<br />
                        2. One with sender data/reply to/return path as configured on Newsletter main settings in TEXT format (some time those values can break the mail system)<br />
                        3. One with sender data/reply to/return path as configured on Newsletter main settings in HTML format (some time those values can break the mail system)<br />
                        4. One in multipart format (with html and text parts) managed directly by Newsletter
                    </div>
                </td>
            </tr>
        </table>

        <h3>System Check and Upgrade</h3>
        <p>
            Tables below contain some system parameter that can affect Newsletter plugin working mode. When asking for support consider to
            report those values.
        </p>

        <div id="tabs">

            <ul>
                <li><a href="#tabs-1">Modules and logging</a></li>
                <li><a href="#tabs-2">Sempahores and Crons</a></li>
                <li><a href="#tabs-4">System</a></li>
                <li><a href="#tabs-upgrade">Maintainance</a></li>
            </ul>

            <div id="tabs-1">
                <h4>Modules</h4>

                <p>Logs are store on wp-content/logs folder.</p>

                <?php $controls->log_level('log_level'); ?>

                <table class="widefat" style="width: auto">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th>Version</th>
                        </tr>
                    </thead>
                    <!-- TODO: Should be a cicle of installed modules -->
                    <tbody>
                        <tr>
                            <td>Main</td>
                            <td><?php echo Newsletter::VERSION; ?></td>
                        </tr>
                        <tr>
                            <td>Users</td>
                            <td><?php echo NewsletterUsers::VERSION; ?></td>
                        </tr>
                        <tr>
                            <td>Subscription</td>
                            <td><?php echo NewsletterSubscription::VERSION; ?></td>
                        </tr>
                        <tr>
                            <td>Newsletters</td>
                            <td><?php echo NewsletterEmails::VERSION; ?></td>
                        </tr>
                        <tr>
                            <td>Statistics</td>
                            <td><?php echo NewsletterStatistics::VERSION; ?></td>
                        </tr>
                    </tbody>
                </table>
                <?php $controls->button('save', 'Save'); ?>
            </div>
            <div id="tabs-2">
                <h4>Semaphores</h4>
                <table class="widefat" style="width: auto">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Active since</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                Newsletter delivery
                            </td>
                            <td>
                                <?php
                                $value = get_transient('newsletter_main_engine');
                                if ($value) echo (time() - $value) . ' seconds';
                                else echo 'Not set';
                                ?>
                                <?php $controls->button('delete_transient', 'Delete', null, 'newsletter_main_engine'); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h4>Crons</h4>
                <table class="widefat" style="width: auto">
                    <thead>
                        <tr>
                            <th>Function</th>
                            <th>Runs in (seconds)</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                WordPress Cron System
                            </td>
                            <td>
                                <?php
                                if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON)
                                        echo 'DISABLED. (really bad, see <a href="http://www.satollo.net/?p=2015" target="_tab">this page)</a>';
                                else echo "ENABLED. (it's ok)";
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Delivery Engine
                            </td>
                            <td>
                                <?php
                                $x = wp_next_scheduled('newsletter');
                                if ($x === false) {
                                    echo 'Error! The delivery engine is off (it should never be off),';
                                    $controls->button('engine_on', 'Reactivate now');
                                }
                                echo 'next run on ';
                                if ($x > 0) echo $x - time();
                                echo ' seconds';
                                if ($x < -1000)
                                        echo ' (not good, see <a href="http://www.satollo.net/?p=2015" target="_tab">this page)</a>)';
                                ?>
                                <?php $controls->button('trigger', 'Trigger now'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Feed by Mail
                            </td>
                            <td>
                                <?php
                                $x = wp_next_scheduled('newsletter_feed');
                                if ($x === false) {
                                    echo 'Not active';
                                    //$controls->button('engine_on', 'Reactivate now');
                                } else {
                                    if ($x > 0) {
                                        echo 'Next run on ' . ($x - time()) . ' seconds';
                                    } else {
                                        echo 'Running now';
                                    }
                                }
                                ?>
                                <?php //$controls->button('trigger_followup', 'Trigger now'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Follow Up
                            </td>
                            <td>
                                <?php
                                $x = wp_next_scheduled('newsletter_followup');
                                if ($x === false) {
                                    echo 'Not active';
                                    //$controls->button('engine_on', 'Reactivate now');
                                } else {
                                    if ($x > 0) {
                                        echo 'Next run on ' . ($x - time()) . ' seconds';
                                    } else {
                                        echo 'Running now';
                                    }
                                }
                                ?>
                                <?php $controls->button('trigger_followup', 'Trigger now'); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="tabs-4">
                <h4>System parameters</h4>

                <table class="widefat" style="width: auto">
                    <thead>
                        <tr>
                            <th>Parameter</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Database Wait Timeout</td>
                            <td>
                                <?php $wait_timeout = $wpdb->get_var("select @@wait_timeout"); ?>
                                <?php echo $wait_timeout; ?> (seconds)
                            </td>
                        </tr>
                        <tr>
                            <td>PHP Execution Time</td>
                            <td>
                                <?php echo ini_get('max_execution_time'); ?> (seconds)
                            </td>
                        </tr>
                        <tr>
                            <td>PHP Memory Limit</td>
                            <td>
                                <?php echo @ini_get('memory_limit'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>WordPress Memory limit</td>
                            <td>
                                <?php echo WP_MEMORY_LIMIT; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Absolute path</td>
                            <td>
                                <?php echo ABSPATH; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Tables Prefix</td>
                            <td>
                                <?php echo $table_prefix; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Database Charset and Collate</td>
                            <td>
                                <?php echo DB_CHARSET; ?> <?php echo DB_COLLATE; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Hook "phpmailer_init"</td>
                            <td>
                                Obsolete.<br>
                                <?php
                                $filters = $wp_filter['phpmailer_init'];
                                if (!is_array($filters)) echo 'No actions attached';
                                else {
                                    foreach ($filters as &$filter) {
                                        foreach ($filter as &$entry) {
                                            if (is_array($entry['function']))
                                                    echo get_class($entry['function'][0]) . '->' . $entry['function'][1];
                                            else echo $entry['function'];
                                            echo '<br />';
                                        }
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>File permissions</td>
                            <td>
                                <?php
                                $index_permissions = fileperms(ABSPATH . '/index.php');
                                $subscribe_permissions = fileperms(NEWSLETTER_DIR . '/do/subscribe.php');
                                if ($index_permissions != $subscribe_permissions) {
                                    echo 'Plugin file permissions differ from blog index.php permissions, that may compromise the subscription process';
                                }else {
                                    echo 'OK';
                                }
                                ?>
                            </td>
                        </tr>                        
                    </tbody>
                </table>

            </div>
            <div id="tabs-upgrade">
                <p>
                    Plugin and modules are able to upgrade them self when needed. If you urgently need to try to force an upgrade, press the
                    button below.
                </p>
                <p>
                    <?php $controls->button('upgrade', 'Force an upgrade'); ?>
                </p>
                
                <p>
                    Restore al dismissed messages
                </p>
                <p>
                    <?php $controls->button('undismiss', 'Restore'); ?>
                </p>
            </div>
        </div>

    </form>

</div>
