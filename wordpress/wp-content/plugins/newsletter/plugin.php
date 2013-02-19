<?php

/*
  Plugin Name: Newsletter
  Plugin URI: http://www.satollo.net/plugins/newsletter
  Description: Newsletter is a cool plugin to create your own subscriber list, to send newsletters, to build your business. Please, do not update while the plugin is delivering a newsletter.
  Version: 3.0.7
  Author: Stefano Lissa
  Author URI: http://www.satollo.net
  Disclaimer: Use at your own risk. No warranty expressed or implied is provided.

  Copyright 2011 Stefano Lissa (email: info@satollo.net, web: http://www.satollo.net)
 */

// Useed as dummy parameter on css and js links
define('NEWSLETTER_VERSION', '3.0.7');

global $wpdb, $newsletter;

//@include_once WP_CONTENT_DIR . '/extensions/newsletter/config.php';

define('NEWSLETTER_EMAILS_TABLE', $wpdb->prefix . 'newsletter_emails');
define('NEWSLETTER_USERS_TABLE', $wpdb->prefix . 'newsletter');
define('NEWSLETTER_STATS_TABLE', $wpdb->prefix . 'newsletter_stats');

// Do not use basename(dirname()) since on activation the plugin is sandboxed inside a function
define('NEWSLETTER_SLUG', 'newsletter');

define('NEWSLETTER_DIR', WP_PLUGIN_DIR . '/' . NEWSLETTER_SLUG);
define('NEWSLETTER_INCLUDES_DIR', WP_PLUGIN_DIR . '/' . NEWSLETTER_SLUG . '/includes');
define('NEWSLETTER_URL', WP_PLUGIN_URL . '/' . NEWSLETTER_SLUG);
define('NEWSLETTER_SUBSCRIPTION_POPUP_URL', NEWSLETTER_URL . '/do/subscription-popup.php');
define('NEWSLETTER_SUBSCRIBE_URL', NEWSLETTER_URL . '/do/subscribe.php');
define('NEWSLETTER_SUBSCRIBE_POPUP_URL', NEWSLETTER_URL . '/do/subscribe-popup.php');
define('NEWSLETTER_PROFILE_URL', NEWSLETTER_URL . '/do/profile.php');
define('NEWSLETTER_SAVE_URL', NEWSLETTER_URL . '/do/save.php');
define('NEWSLETTER_CONFIRM_URL', NEWSLETTER_URL . '/do/confirm.php');
define('NEWSLETTER_CHANGE_URL', NEWSLETTER_URL . '/do/change.php');
define('NEWSLETTER_UNLOCK_URL', NEWSLETTER_URL . '/do/unlock.php');
define('NEWSLETTER_UNSUBSCRIBE_URL', NEWSLETTER_URL . '/do/unsubscribe.php');
define('NEWSLETTER_UNSUBSCRIPTION_URL', NEWSLETTER_URL . '/do/unsubscription.php');

define('NEWSLETTER_EMAIL_URL', NEWSLETTER_URL . '/do/view.php');

if (!defined('NEWSLETTER_LIST_MAX')) define('NEWSLETTER_LIST_MAX', 20);
if (!defined('NEWSLETTER_PROFILE_MAX')) define('NEWSLETTER_PROFILE_MAX', 20);
if (!defined('NEWSLETTER_FORMS_MAX')) define('NEWSLETTER_FORMS_MAX', 10);

// Force the whole system log level to this value
//define('NEWSLETTER_LOG_LEVEL', 4);

require_once NEWSLETTER_INCLUDES_DIR . '/logger.php';
require_once NEWSLETTER_INCLUDES_DIR . '/store.php';
require_once NEWSLETTER_INCLUDES_DIR . '/module.php';
require_once NEWSLETTER_INCLUDES_DIR . '/themes.php';

class Newsletter extends NewsletterModule {

    const VERSION = '1.1.0';

    // Limits to respect to avoid memory, time or provider limits
    var $time_limit;
    var $email_limit = 10; // Per run, every 5 minutes
    var $limits_set = false;
    var $max_emails = 20;

    /**
     * @var PHPMailer
     */
    var $mailer;
    // Message shown when the interaction is inside a WordPress page
    var $message;
    var $user;
    var $error;
    var $theme;
    // Theme autocomposer variables
    var $theme_max_posts;
    var $theme_excluded_categories; // comma separated ids (eventually negative to exclude)
    var $theme_posts; // WP_Query object
    // Secret key to create a unique log file name (and may be other)
    static $instance;

    /**
     * @return Newsletter
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new Newsletter();
        }
        return self::$instance;
    }

    function __construct() {
        // Early possible
        $max_time = (int) (@ini_get('max_execution_time') * 0.9);
        if ($max_time == 0) $max_time = 600;
        $this->time_limit = time() + $max_time;

        // Here because the upgrade is called by the parent constructor and uses the scheduler
        add_filter('cron_schedules', array(&$this, 'hook_cron_schedules'), 1000);

        parent::__construct('main', self::VERSION);

        $max = $this->options['scheduler_max'];
        if (!is_numeric($max)) $max = 100;
        $this->max_emails = max(floor($max / 12), 1);

        add_action('init', array(&$this, 'hook_init'));
        add_action('newsletter', array(&$this, 'hook_newsletter'), 1);

        // This specific event is created by "Feed by mail" panel on configuration
        add_action('shutdown', array(&$this, 'hook_shutdown'));

        if (defined('DOING_CRON') && DOING_CRON) return;

        // TODO: Meditation on how to use those ones...
        //register_activation_hook(__FILE__, array(&$this, 'hook_activate'));
        //register_deactivation_hook(__FILE__, array(&$this, 'hook_deactivate'));

        add_action('admin_init', array(&$this, 'hook_admin_init'));

        add_action('wp_head', array(&$this, 'hook_wp_head'));

        add_shortcode('newsletter_lock', array(&$this, 'shortcode_newsletter_lock'));
        add_shortcode('newsletter_profile', array(&$this, 'shortcode_newsletter_profile'));

        if (is_admin()) {
            add_action('admin_menu', array(&$this, 'hook_admin_menu'));
            add_action('admin_head', array(&$this, 'hook_admin_head'));
        }
    }

    function upgrade() {
        global $wpdb, $charset_collate;

        $this->upgrade_query("create table if not exists " . $wpdb->prefix . "newsletter_emails (id int auto_increment, primary key (id)) $charset_collate");
        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails add column message longtext");
        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails add column message_text longtext");
        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails add column subject varchar(255) not null default ''");
        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails add column type varchar(50) not null default ''");
        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails add column created timestamp not null default current_timestamp");

        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails add column status enum('new','sending','sent','paused') not null default 'new'");

        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails add column total int not null default 0");
        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails add column last_id int not null default 0");
        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails add column sent int not null default 0");
        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails add column send_on int not null default 0");
        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails add column track tinyint not null default 0");
        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails add column editor tinyint not null default 0");
        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails add column sex varchar(10) not null default ''");
        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails change column sex sex varchar(10) not null default ''");

        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails add column query text");
        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails add column preferences text");

        // Cleans up old installations
        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails drop column name");
        $this->upgrade_query("drop table if exists " . $wpdb->prefix . "newsletter_work");
        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter_emails convert to character set utf8");

        // TODO: To be moved on users module.
        $this->upgrade_query("alter table " . $wpdb->prefix . "newsletter convert to character set utf8");

        // Some setting check to avoid the common support request for mis-configurations
        $options = $this->get_options();

        if (empty($options['sender_email'])) {
            // That code was taken from WordPress
            $sitename = strtolower($_SERVER['SERVER_NAME']);
            if (substr($sitename, 0, 4) == 'www.') $sitename = substr($sitename, 4);
            // WordPress build an address in the same way using wordpress@...
            $options['sender_email'] = 'newsletter@' . $sitename;
            $this->save_options($options);
        }

        if (empty($options['api_key'])) {
            $options['api_key'] = self::get_token();
            $this->save_options($options);
        }

        if (empty($options['scheduler_max'])) {
            $options['scheduler_max'] = 100;
            $this->save_options($options);
        }

        wp_clear_scheduled_hook('newsletter');
        wp_schedule_event(time() + 30, 'newsletter', 'newsletter');

//        $sql = 'create table if not exists ' . $wpdb->prefix . 'newsletter_profiles (
//        `newsletter_id` int NOT NULL,
//        `name` varchar (100) NOT NULL DEFAULT \'\',
//        `value` text,
//        primary key (newsletter_id, name)
//        ) DEFAULT charset=utf8';

        wp_mkdir_p(WP_CONTENT_DIR . '/extensions/newsletter');
        wp_mkdir_p(WP_CONTENT_DIR . '/cache/newsletter');

        return true;
    }

    /**
     * Returns a set of warnings about this installtion the suser should be aware of. Return an empty string
     * if there are no warnings.
     */
    function warnings() {
        $warnings = '';
        $x = wp_next_scheduled('newsletter');
        if ($x === false) {
            $warnings .= 'The delivery engine is off (it should never be off). See the System Check below to reactivate it.<br>';
        }
        else if (time() - $x > 900) {
            $warnings .= 'The cron system seems not running correctly. See <a href="http://www.satollo.net/how-to-make-the-wordpress-cron-work" target="_blank">this page</a> for more information.<br>';
        }

        if (!empty($warnings)) {
            echo '<div id="#newsletter-warnings">';
            echo $warnings;
            echo '</div>';
        }
    }

    // TODO: Remove almost anything...
    function hook_init() {
        global $cache_stop, $hyper_cache_stop, $wpdb;


        $action = $_REQUEST['na'];
        if (empty($action) || is_admin()) return;

        $cache_stop = true;
        $hyper_cache_stop = true;

        if ($action == 'of') {
            echo $this->subscription_form('os');
            die();
        }

        // Actions below need a user. This code loads the user checking parameter or cookies.
        $user = $this->check_user();
        if ($user == null) die('No user');

        if ($action == 'fu') {
            $wpdb->query("update " . $wpdb->prefix . "newsletter set followup=2 where id=" . $user->id);
            $options_followup = get_option('newsletter_followup');
            $this->message = $options_followup['unsubscribed_text'];
            return;
        }
    }

    function is_admin_page() {
        // TODO: Use the module list to detect that...
        return strpos($_GET['page'], 'newsletter_') === 0 || strpos($_GET['page'], 'newsletter-statistics/') === 0 || strpos($_GET['page'], 'newsletter/') === 0 ||
                strpos($_GET['page'], 'newsletter-updates/') === 0 || strpos($_GET['page'], 'newsletter-flows/') === 0;
    }

    function hook_admin_init() {
        if ($this->is_admin_page()) {
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            wp_enqueue_style('thickbox');
        }
    }

    function hook_admin_head() {
        if ($this->is_admin_page()) {
            echo '<link type="text/css" rel="stylesheet" href="' . NEWSLETTER_URL . '/admin.css?' . NEWSLETTER_VERSION . '"/>';
            echo '<script src="' . NEWSLETTER_URL . '/admin.js?' . NEWSLETTER_VERSION . '"></script>';
        }
    }

    function hook_admin_menu() {
        include 'plugin-menu.inc.php';

        do_action('newsletter_admin_menu');

        add_submenu_page('newsletter/welcome.php', 'Diagnostic', 'Diagnostic', $level, 'newsletter/diagnostic.php');

    }

    function hook_wp_head() {
        if (!empty($this->options['css'])) {
            echo "<style type='text/css'>\n";
            echo $this->options['css'];
            echo "</style>";
        }

        // TODO: move on subscription module
        $profile_options = get_option('newsletter_profile');
        if ($profile_options['style'] != '') {
            echo '<link href="' . NewsletterSubscription::instance()->get_style_url($profile_options['style']) . '" type="text/css" rel="stylesheet">';
        }
        if ($profile_options['widget_style'] != '') {
            echo '<link href="' . NewsletterSubscription::instance()->get_style_url($profile_options['widget_style']) . '" type="text/css" rel="stylesheet">';
        }
    }

    function relink($text, $email_id, $user_id) {
        return NewsletterStatistics::instance()->relink($text, $email_id, $user_id);
    }

    /**
     * Runs every 5 minutes and look for emails that need to be processed.
     */
    function hook_newsletter() {
        global $wpdb;

        $this->logger->debug('hook_newsletter> Starting');

        // Do not accept job activation before at least 4 minutes are elapsed from the last run.
        if (!$this->check_transient('engine', 240)) return;

        // Retrieve all email in "sending" status
        $emails = $wpdb->get_results("select * from " . NEWSLETTER_EMAILS_TABLE . " where status='sending' and send_on<" . time() . " order by id asc");
        $this->logger->debug('hook_newsletter> Emails found in sending status: ' . count($emails));
        foreach ($emails as &$email) {
            $this->logger->debug('hook_newsletter> Sending email ' . $email->id);
            if (!$this->send($email)) return;
        }

        // TODO: Manage the follow-up with Newsletter 3.0.
        if (defined('NEWSLETTER_FOLLOWUP_VERSION')) {
            //global $newsletter_followup;
            //$newsletter_followup->send();
        }
    }

    /**
     * Sends an email to targeted users ot to users passed on. If a list of users is given (usually a list of test users)
     * the query inside the email to retrieve users is not used.
     *
     * @global type $wpdb
     * @global type $newsletter_feed
     * @param type $email
     * @param array $users
     * @return boolean True if the proccess completed, false if limits was reached. On false the caller should no continue to call it with other emails.
     */
    function send($email, $users = null) {
        global $wpdb;

        if (is_array($email)) $email = (object) $email;

        // This stops the update of last_id and sent fields since it's not a scheduled delivery but a test.
        $test = $users != null;

        if ($users == null) {
            if (empty($email->query)) $email->query = "select * from " . NEWSLETTER_EMAILS_TABLE . " where status='C'";
            $query = $email->query . " and id>" . $email->last_id . " order by id limit " . $this->max_emails;
            $users = $wpdb->get_results($query);

            // If there was a database error, do nothing
            if ($wpdb->last_error) {
                $this->logger->fatal($wpdb->last_error);
                return;
            }

            if (empty($users)) {
                $this->logger->info('No more users');
                $wpdb->query("update " . NEWSLETTER_EMAILS_TABLE . " set status='sent' where id=" . $email->id . " limit 1");
                return true;
            }
        }

        foreach ($users as &$user) {

            // Before try to send, check the limits.
            if (!$test && $this->limits_exceeded()) return false;

            $headers = array('List-Unsubscribe' => '<' . NEWSLETTER_UNSUBSCRIBE_URL . '?nk=' . $user->id . '-' . $user->token . '>');

            if (!$test) {
                $wpdb->query("update " . NEWSLETTER_EMAILS_TABLE . " set sent=sent+1, last_id=" . $user->id . " where id=" . $email->id . " limit 1");
            }

            $m = $this->replace($email->message, $user, $email->id);
            $mt = $this->replace($email->message_text, $user, $email->id);


            if ($email->track == 1) $m = $this->relink($m, $email->id, $user->id);

            $s = $this->replace($email->subject, $user);

            $this->mail($user->email, $s, array('html' => $m, 'text' => $mt), $headers);

            $this->email_limit--;
        }

        // TODO: Integrate the feed by mail
        if (defined('NEWSLETTER_FEED_VERSION')) {
            //global $newsletter_feed;
            //if ($email->type == 'feed') return $newsletter_feed->feed_send($email, $users);
        }

        return true;
    }

    function execute($text, $user = null) {
        global $wpdb;
        ob_start();
        $r = eval('?' . '>' . $text);
        if ($r === false) {
            $this->error = 'Error while executing a PHP expression in a message body. See log file.';
            $this->log('Error on execution of ' . $text, 1);
            ob_end_clean();
            return false;
        }

        return ob_get_clean();
    }

    /**
     * This function checks is, during processing, we are getting to near to system limits and should stop any further
     * work (when returns true).
     */
    function limits_exceeded() {
        global $wpdb;

        if (!$this->limits_set) {
            $this->logger->debug('limits_exceeded> Setting the limits for the first time');
            $max = $this->options['scheduler_max'];
            if (!is_numeric($max)) $max = 100;
            $this->email_limit = max(floor($max / 12), 1);
            $this->logger->debug('limits_exceeded> Max number of emails can send: ' . $this->email_limit);

            $wpdb->query("set session wait_timeout=300");
            // From default-constants.php
            if (function_exists('memory_get_usage') && ( (int) @ini_get('memory_limit') < 128 ))
                    @ini_set('memory_limit', '256M');

            $this->limits_set = true;
        }

        // The time limit is set on constructor, since it has to be set as early as possible
        if (time() > $this->time_limit) {
            $this->logger->info('limits_exceeded> Max execution time limit reached');
            return true;
        }

        if ($this->email_limit <= 0) {
            $this->logger->info('limits_exceeded> Max emails limit reached');
            return true;
        }
        return false;
    }

    /**
     *
     * @param string $to
     * @param string $subject
     * @param string|array $message
     * @param type $headers
     * @return boolean
     */
    function mail($to, $subject, $message, $headers = null) {

        $this->logger->debug('mail> To: ' . $to);
        $this->logger->debug('mail> Subject: ' . $subject);
        if (empty($subject)) {
            $this->logger->debug('mail> Subject empty, skipped');
            return true;
        }

        if ($this->mailer == null) $this->mailer_init();

        // Simple message is asumed to be html
        if (!is_array($message)) {
            $this->mailer->IsHTML(true);
            $this->mailer->Body = $message;
        }
        else {
            // Only html is present?
            if (empty($message['text'])) {
                $this->mailer->IsHTML(true);
                $this->mailer->Body = $message['html'];
            }
            // Only text is present?
            else if (empty($message['html'])) {
                $this->mailer->IsHTML(false);
                $this->mailer->Body = $message['text'];
            }
            else {
                $this->mailer->IsHTML(true);

                $this->mailer->Body = $message['html'];
                $this->mailer->AltBody = $message['text'];
            }
        }

        $this->mailer->Subject = $subject;

        $this->mailer->ClearCustomHeaders();
        if (!empty($headers)) {
            foreach ($headers as $key => $value) {
                $this->mailer->AddCustomHeader($key . ': ' . $value);
            }
        }

        $this->mailer->ClearAddresses();
        $this->mailer->AddAddress($to);
        $this->mailer->Send();

        if ($this->mailer->IsError()) {
            $this->logger->error('mail> ' . $this->mailer->ErrorInfo);
            // If the error is due to SMTP connection, the mailer cannot be reused since it does not clean up the connection
            // on error.
            $this->mailer = null;
            return false;
        }
        return true;
    }

    function mailer_init() {
        require_once ABSPATH . WPINC . '/class-phpmailer.php';
        require_once ABSPATH . WPINC . '/class-smtp.php';
        $this->mailer = new PHPMailer();

        if ($this->options['smtp_enabled'] == 1) {
            $this->mailer->IsSMTP();
            $this->mailer->Host = $this->options['smtp_host'];
            if (!empty($this->options['smtp_port'])) $this->mailer->Port = (int) $this->options['smtp_port'];

            if (!empty($this->options['smtp_user'])) {
                $this->mailer->SMTPAuth = true;
                $this->mailer->Username = $this->options['smtp_user'];
                $this->mailer->Password = $this->options['smtp_pass'];
            }
            $this->mailer->SMTPKeepAlive = true;
            $this->mailer->SMTPSecure = $this->options['smtp_secure'];
        }
        else $this->mailer->IsMail();

        if (!empty($this->options['content_transfer_encoding']))
                $this->mailer->Encoding = $this->options['content_transfer_encoding'];

        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->From = $this->options['sender_email'];
        if (!empty($this->options['return_path'])) $this->mailer->Sender = $this->options['return_path'];
        if (!empty($this->options['reply_to'])) $this->mailer->AddReplyTo($this->options['reply_to']);

        $this->mailer->FromName = $this->options['sender_name'];
    }

    function hook_deactivate() {
        wp_clear_scheduled_hook('newsletter');
        wp_clear_scheduled_hook('newsletter_feed');
    }

    function hook_cron_schedules($schedules) {
        $schedules['newsletter'] = array(
            'interval' => 300, // seconds
            'display' => 'Newsletter'
        );
        return $schedules;
    }

    function shortcode_newsletter_form($attrs, $content) {
        return $this->form($attrs['form']);
    }

    function form($number = null) {
        if ($number == null) return $this->subscription_form();
        $options = get_option('newsletter_forms');

        $form = $options['form_' . $number];

        if (stripos($form, '<form') !== false) {
            $form = str_replace('{newsletter_url}', NEWSLETTER_SUBSCRIBE_URL, $form);
        } else {
            $form = '<form method="post" action="' . NEWSLETTER_SUBSCRIBE_URL . '" onsubmit="return newsletter_check(this)">' .
                    $form . '</form>';
        }

        $form = $this->replace_lists($form);

        return $form;
    }

    function find_file($file1, $file2) {
        if (is_file($file1)) return $file1;
        return $file2;
    }

    /**
     * Return a user if there are request parameters or cookie with identification data otherwise null.
     */
    function check_user() {
        global $wpdb, $current_user;

        if (isset($_REQUEST['nk'])) {
            list($id, $token) = @explode('-', $_REQUEST['nk'], 2);
        } else if (isset($_REQUEST['ni'])) {
            $id = (int) $_REQUEST['ni'];
            $token = $_REQUEST['nt'];
        } else if (isset($_COOKIE['newsletter'])) {
            list ($id, $token) = @explode('-', $_COOKIE['newsletter'], 2);
        }

        if (is_numeric($id) && !empty($token)) {
            return $wpdb->get_row($wpdb->prepare("select * from " . $wpdb->prefix . "newsletter where id=%d and token=%s limit 1", $id, $token));
        }

        if ($this->options_main['wp_integration'] != 1) {
            return null;
        }

        get_currentuserinfo();

        // Retrieve the related newsletter user
        $user = $wpdb->get_row("select * from " . NEWSLETTER_USERS_TABLE . " where wp_user_id=" . $current_user->ID . " limit 1");
        // There is an email matching?
        if (empty($user)) {
            $user = $wpdb->get_row($wpdb->prepare("select * from " . NEWSLETTER_USERS_TABLE . " where email=%s limit 1", strtolower($current_user->user_email)));
            // If not found, create a new Newsletter user, else update the wp_user_id since this email must be linked
            // to the WP user email.
            if (empty($user)) {
                return null;
                //echo 'WP user not found';
                $user = array();
                $user['status'] = 'C';
                $user['wp_user_id'] = $current_user->ID;
                $user['token'] = $this->get_token();
                $user['email'] = strtolower($current_user->user_email);

                $id = $wpdb->insert(NEWSLETTER_USERS_TABLE, $user);
                $user = NewsletterUsers::instance()->get_user($id);
            } else {
                //echo 'WP user found via email';
                $wpdb->query($wpdb->prepare("update " . NEWSLETTER_USERS_TABLE . " set wp_user_id=" . $current_user->ID . ", email=%s", $current_user->user_email));
            }
        } else {
            //echo 'WP user found via id';
        }

        return $user;
    }

    function replace_date($text) {
        // Date processing
        $x = 0;
        while (($x = strpos($text, '{date_', $x)) !== false) {
            $y = strpos($text, '}', $x);
            if ($y === false) continue;
            $f = substr($text, $x + 6, $y - $x - 6);
            $text = substr($text, 0, $x) . date($f) . substr($text, $y + 1);
        }
        return $text;
    }

    /**
     * Replace any kind of newsletter placeholder in a text.
     */
    function replace($text, $user = null, $email_id = null) {
        global $wpdb;

        if (is_array($user)) $user = NewsletterUsers::instance()->get_user($user['id']);
        $text = str_replace('{home_url}', get_option('home'), $text);
        $text = str_replace('{blog_title}', get_option('blogname'), $text);
        $text = str_replace('{blog_description}', get_option('blogdescription'), $text);
        $text = str_replace('{date}', date_i18n(get_option('date_format')), $text);

        $text = $this->replace_date($text);

        if ($user != null) {
            $text = str_replace('{email}', $user->email, $text);
            if (empty($user->name)) {
                $text = str_replace(' {name}', '', $text);
                $text = str_replace('{name}', '', $text);
            } else {
                $text = str_replace('{name}', $user->name, $text);
            }
            $text = str_replace('{surname}', $user->surname, $text);
            $text = str_replace('{token}', $user->token, $text);
            $text = str_replace('%7Btoken%7D', $user->token, $text);
            $text = str_replace('{id}', $user->id, $text);
            $text = str_replace('%7Bid%7D', $user->id, $text);
            $text = str_replace('{ip}', $user->ip, $text);
            $text = str_replace('{key}', $user->id . '-' . $user->token, $text);
            $text = str_replace('%7Bkey%7D', $user->id . '-' . $user->token, $text);

            if (strpos($text, '{profile_form}') !== false)
                    $text = str_replace('{profile_form}', NewsletterSubscription::instance()->get_profile_form($user), $text);

            for ($i = 1; $i < NEWSLETTER_PROFILE_MAX; $i++) {
                $p = 'profile_' . $i;
                $text = str_replace('{profile_' . $i . '}', $user->$p, $text);
            }

            $profile = $wpdb->get_results("select name,value from " . $wpdb->prefix . "newsletter_profiles where newsletter_id=" . $user->id);
            foreach ($profile as $field) {
                $text = str_ireplace('{np_' . $field->name . '}', htmlspecialchars($field->value), $text);
            }

            $text = preg_replace('/\\{np_.+\}/i', '', $text);

            $base = (empty($this->options_main['url']) ? get_option('home') : $this->options_main['url']);
            $id_token = '&amp;ni=' . $user->id . '&amp;nt=' . $user->token;
            $nk = $user->id . '-' . $user->token;

            //$text = $this->replace_url($text, 'SUBSCRIPTION_CONFIRM_URL', self::add_qs(plugins_url('do.php', __FILE__), 'a=c' . $id_token));
            $text = $this->replace_url($text, 'SUBSCRIPTION_CONFIRM_URL', NEWSLETTER_CONFIRM_URL . '?nk=' . $nk);
            $text = $this->replace_url($text, 'UNSUBSCRIPTION_CONFIRM_URL', NEWSLETTER_UNSUBSCRIBE_URL . '?nk=' . $nk);
            //$text = $this->replace_url($text, 'UNSUBSCRIPTION_CONFIRM_URL', NEWSLETTER_URL . '/do/unsubscribe.php?nk=' . $nk);
            $text = $this->replace_url($text, 'UNSUBSCRIPTION_URL', NEWSLETTER_UNSUBSCRIPTION_URL . '?nk=' . $nk);
            $text = $this->replace_url($text, 'CHANGE_URL', NEWSLETTER_CHANGE_URL);

            // Obsolete.
            $text = $this->replace_url($text, 'FOLLOWUP_SUBSCRIPTION_URL', self::add_qs($base, 'nm=fs' . $id_token));
            $text = $this->replace_url($text, 'FOLLOWUP_UNSUBSCRIPTION_URL', self::add_qs($base, 'nm=fu' . $id_token));
            $text = $this->replace_url($text, 'FEED_SUBSCRIPTION_URL', self::add_qs($base, 'nm=es' . $id_token));
            $text = $this->replace_url($text, 'FEED_UNSUBSCRIPTION_URL', self::add_qs($base, 'nm=eu' . $id_token));

            $options_profile = get_option('newsletter_profile');
            if (empty($options_profile['profile_url']))
                    $text = $this->replace_url($text, 'PROFILE_URL', NEWSLETTER_PROFILE_URL . '?nk=' . $nk);
            else
                    $text = $this->replace_url($text, 'PROFILE_URL', self::add_qs($options_profile['profile_url'], 'ni=' . $user->id . '&amp;nt=' . $user->token));

            //$text = $this->replace_url($text, 'UNLOCK_URL', self::add_qs($this->options_main['lock_url'], 'nm=m' . $id_token));
            $text = $this->replace_url($text, 'UNLOCK_URL', NEWSLETTER_UNLOCK_URL . '?nk=' . $nk);
            if (!empty($email_id)) {
                $text = $this->replace_url($text, 'EMAIL_URL', NEWSLETTER_EMAIL_URL . '?id=' . $email_id . '&amp;nk=' . $nk);
            }

            for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
                $text = $this->replace_url($text, 'LIST_' . $i . '_SUBSCRIPTION_URL', self::add_qs($base, 'nm=ls&amp;nl=' . $i . $id_token));
                $text = $this->replace_url($text, 'LIST_' . $i . '_UNSUBSCRIPTION_URL', self::add_qs($base, 'nm=lu&amp;nl=' . $i . $id_token));
            }

            // Profile fields change links
            $text = $this->replace_url($text, 'SET_SEX_MALE', NEWSLETTER_CHANGE_URL . '?nk=' . $nk . '&nf=sex&nv=m');
            $text = $this->replace_url($text, 'SET_SEX_FEMALE', NEWSLETTER_CHANGE_URL . '?nk=' . $nk . '&nf=sex&nv=f');
            $text = $this->replace_url($text, 'SET_FEED', NEWSLETTER_CHANGE_URL . '?nk=' . $nk . '&nf=feed');
            for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
                $text = $this->replace_url($text, 'SET_PREFERENCE_' . $i, NEWSLETTER_CHANGE_URL . '?nk=' . $nk . '&nf=preference_' . $i);
            }

        }
        return $text;
    }

    function replace_url($text, $tag, $url) {
        $home = get_option('home') . '/';
        $tag_lower = strtolower($tag);
        $text = str_replace($home . '{' . $tag_lower . '}', $url, $text);
        $text = str_replace($home . '%7B' . $tag_lower . '%7D', $url, $text);
        $text = str_replace('{' . $tag_lower . '}', $url, $text);
        $text = str_replace('%7B' . $tag_lower . '%7D', $url, $text);

        // for compatibility
        $text = str_replace($home . $tag, $url, $text);

        return $text;
    }

    function hook_shutdown() {
        if ($this->mailer != null) $this->mailer->SmtpClose();
    }

    function shortcode_newsletter_lock($attrs, $content = null) {
        global $hyper_cache_stop, $cache_stop;

        $hyper_cache_stop = true;
        $cache_stop = true;

        $user = $this->check_user();
        if ($user != null && $user->status == 'C') {
            return do_shortcode($content);
        }

        $buffer = $this->options_main['lock_message'];
        ob_start();
        eval('?>' . $buffer . "\n");
        $buffer = ob_get_clean();
        // TODO: add the newsletter check on submit
        $buffer = str_ireplace('<form', '<form method="post" action="' . NEWSLETTER_SUBSCRIBE_URL . '"', $buffer);
        return do_shortcode($buffer);
    }

    function shortcode_newsletter_profile($attrs, $content) {
        global $wpdb, $current_user;

        $user = $this->check_user();

        if ($user == null) {
            return 'No user found.';
        }

        return $this->profile_form($user);
    }

    /**
     * Exceutes a query and log it.
     */
    function query($query) {
        global $wpdb;

        $this->log($query, 3);
        return $wpdb->query($query);
    }

    function notify_admin($user, $subject) {
        if ($this->options['notify'] != 1) return;
        $message = "Subscriber details:\n\n" .
                "email: " . $user->email . "\n" .
                "first name: " . $user->name . "\n" .
                "last name: " . $user->surname . "\n" .
                "gender: " . $user->sex . "\n";

        $options_profile = get_option('newsletter_profile');

        for ($i = 0; $i < NEWSLETTER_PROFILE_MAX; $i++) {
            if ($options_profile['profile_' . $i] == '') continue;
            $field = 'profile_' . $i;
            $message .= $options_profile['profile_' . $i] . ': ' . $user->$field . "\n";
        }

        for ($i = 0; $i < NEWSLETTER_LIST_MAX; $i++) {
            if ($options_profile['list_' . $i] == '') continue;
            $field = 'list_' . $i;
            $message .= $options_profile['list_' . $i] . ': ' . $user->$field . "\n";
        }

        $message .= "token: " . $user->token . "\n" .
                "status: " . $user->status . "\n" .
                "\nYours, Newsletter Pro.";

        wp_mail(get_option('admin_email'), '[' . get_option('blogname') . '] ' . $subject, $message, "Content-type: text/plain; charset=UTF-8\n");
    }

    function get_user_from_request($required = false) {
        if (isset($_REQUEST['nk'])) {
            list($id, $token) = @explode('-', $_REQUEST['nk'], 2);
        } else if (isset($_REQUEST['ni'])) {
            $id = (int) $_REQUEST['ni'];
            $token = $_REQUEST['nt'];
        }
        $user = NewsletterUsers::instance()->get_user($id);

        if ($user == null || $token != $user->token) {
            if ($required) die('No subscriber found.');
            else return null;
        }
        return $user;
    }

    function add_menu_page($module, $page, $title) {
        //var_dump($this);
        //$this->menu_pages[] = array($module, $page, $title);
        $file = WP_CONTENT_DIR . '/extensions/newsletter/' . $module . '/' . $page . '.php';
        if (!is_file($file)) {
            $file = NEWSLETTER_DIR . '/' . $module . '/' . $page . '.php';
        }
        $name = 'newsletter_' . $module . '_' . $page;
        eval('function ' . $name . '(){global $newsletter;require \'' . $file . '\';}');
        add_submenu_page('newsletter/welcome.php', $title, $title, $this->options['editor'] ? 7 : 10, $name, $name);
    }

    function add_admin_page($module, $page, $title) {

        $file = WP_CONTENT_DIR . '/extensions/newsletter/' . $module . '/' . $page . '.php';
        if (!is_file($file)) {
            $file = NEWSLETTER_DIR . '/' . $module . '/' . $page . '.php';
        }

        $name = 'newsletter_' . $module . '_' . $page;
        eval('function ' . $name . '(){global $newsletter;require \'' . $file . '\';}');
        add_submenu_page(null, $title, $title, $this->options['editor'] ? 7 : 10, $name, $name);
    }

    function get_emails($type = null, $format = OBJECT) {
        global $wpdb;
        if ($type == null) {
            $list = $wpdb->get_results("select * from " . NEWSLETTER_EMAILS_TABLE . " order by id desc", $format);
        } else {
            $list = $wpdb->get_results($wpdb->prepare("select * from " . NEWSLETTER_EMAILS_TABLE . " where type=%s order by id desc", $type), $format);
        }
        if ($wpdb->last_error) {
            $this->logger->error($wpdb->last_error);
            return false;
        }
        if (empty($list)) return array();
        return $list;
    }

    function save_email($email) {
        return $this->store->save(NEWSLETTER_EMAILS_TABLE, $email);
    }

    function delete_email($id) {
        return $this->store->delete(NEWSLETTER_EMAILS_TABLE, $id);
    }

    function get_email($id, $format = OBJECT) {
        return $this->store->get_single(NEWSLETTER_EMAILS_TABLE, $id, $format);
    }

    function get_email_field($id, $field_name) {
        return $this->store->get_field(NEWSLETTER_EMAILS_TABLE, $id, $field_name);
    }

    /**
     * NEVER CHANGE THIS METHOD SIGNATURE, USER BY THIRD PARTY PLUGINS.
     *
     * Saves a new user on the database. Return false if the email (that must be unique) is already
     * there. For a new users set the token and creation time if not passed.
     *
     * @param type $user
     * @return type
     */
    function save_user($user, $return_format=OBJECT) {
        if (is_object($user)) $user = (array) $user;
        if (empty($user['id'])) {
            if (empty($user['token'])) $user['token'] = NewsletterModule::get_token();
            //if (empty($user['created'])) $user['created'] = time();
            // Database default
            //if (empty($user['status'])) $user['status'] = 'S';
        }
        // Due to the unique index on email field, this can fail.
        return $this->store->save(NEWSLETTER_USERS_TABLE, $user, $return_format);
    }

    /**
     * Returns a list of users marked as "test user".
     * @return array
     */
    function get_test_users() {
        return $this->store->get_all(NEWSLETTER_USERS_TABLE, "where test=1");
    }

    /** Returns the user identify by an id or an email. If $id_or_email is an object or an array, it is assumed it contains
     * the "id" attribute or key and that is used to load the user.
     *
     * @global type $wpdb
     * @param string|int|object|array $id_or_email
     * @param type $format
     * @return boolean
     */
    function get_user($id_or_email, $format=OBJECT) {
        global $wpdb;

        // To simplify the reaload of a user passing the user it self.
        if (is_object($id_or_email)) $id_or_email = $id_or_email->id;
        else if (is_array($id_or_email)) $id_or_email = $id_or_email['id'];

        $id_or_email = strtolower(trim($id_or_email));

        if (is_numeric($id_or_email)) {
            $r = $wpdb->get_row($wpdb->prepare("select * from " . NEWSLETTER_USERS_TABLE . " where id=%d limit 1", $id_or_email), $format);
        } else {
            $r = $wpdb->get_row($wpdb->prepare("select * from " . NEWSLETTER_USERS_TABLE . " where email=%s limit 1", $id_or_email), $format);
        }

        if ($wpdb->last_error) {
            $this->logger->error($wpdb->last_error);
            return false;
        }
        return $r;
    }

    function delete_user($id) {
        global $wpdb;
        $r = $this->store->delete(NEWSLETTER_USERS_TABLE, $id);
        if ($r !== false) {
            $wpdb->delete(NEWSLETTER_STATS_TABLE, array('user_id'=>$id));
        }
    }

    function set_user_status($id_or_email, $status) {
        global $wpdb;

        $id_or_email = strtolower(trim($id_or_email));
        if (is_numeric($id_or_email)) {
            $r = $wpdb->query($wpdb->prepare("update " . NEWSLETTER_USERS_TABLE . " set status=%s where id=%d limit 1", $status, $id_or_email));
        } else {
            $r = $wpdb->query($wpdb->prepare("update " . NEWSLETTER_USERS_TABLE . " set status=%s where email=%s limit 1", $status, $id_or_email));
        }

        if ($wpdb->last_error) {
            $this->logger->error($wpdb->last_error);
            return false;
        }
        return $r;
    }


}

// Newsletter will be always instantiated.
$newsletter = Newsletter::instance();

// Module loading
require_once NEWSLETTER_DIR . '/users/users.php';
require_once NEWSLETTER_DIR . '/subscription/subscription.php';
require_once NEWSLETTER_DIR . '/emails/emails.php';
require_once NEWSLETTER_DIR . '/statistics/statistics.php';
//require_once NEWSLETTER_DIR . '/bounce/bounce.php';
// Extended module loading
//if (is_file(WP_CONTENT_DIR . '/newsletter/updates/updates.php')) {
//    require_once WP_CONTENT_DIR . '/newsletter/updates/updates.php';
//}
//if (is_file(WP_CONTENT_DIR . '/newsletter/followup/followup.php')) {
//    require_once WP_CONTENT_DIR . '/newsletter/followup/followup.php';
//}
//
if (is_file(WP_CONTENT_DIR . '/extensions/newsletter/feed/feed.php')) {
    require_once WP_CONTENT_DIR . '/extensions/newsletter/feed/feed.php';
}

require_once(dirname(__FILE__) . '/widget.php');

register_activation_hook(__FILE__, 'newsletter_activate');

function newsletter_activate() {
    // TODO: May be it's better to declare an "activate" method?
    Newsletter::instance()->upgrade();

    // Modules
    NewsletterUsers::instance()->upgrade();
    NewsletterEmails::instance()->upgrade();
    NewsletterSubscription::instance()->upgrade();
    NewsletterStatistics::instance()->upgrade();

    // Extended modules
    //NewsletterUpdates::instance()->upgrade();
    //NewsletterFollowup::instance()->upgrade();
    // TODO: Scan for other modules or use the found modules above
}

register_activation_hook(__FILE__, 'newsletter_deactivate');

function newsletter_deactivate() {
//    Newsletter::instance()->deactivate();
//    NewsletterUsers::instance()->deactivate();
//    NewsletterEmails::instance()->deactivate();
//    NewsletterSubscription::instance()->deactivate();
//    NewsletterStatistics::instance()->deactivate();
//    NewsletterUpdates::instance()->deactivate();
    // TODO: Scan for other modules or use the found modules above
}
