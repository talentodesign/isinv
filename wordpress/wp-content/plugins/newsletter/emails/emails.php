<?php

require_once NEWSLETTER_INCLUDES_DIR . '/themes.php';
require_once NEWSLETTER_INCLUDES_DIR . '/module.php';

class NewsletterEmails extends NewsletterModule {

    const VERSION = '1.0.7';

    /**
     * @var NewsletterThemes
     */
    var $themes;

    static $instance;

    /**
     *
     * @return NewsletterEmails
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new NewsletterEmails();
        }
        return self::$instance;
    }

    function __construct() {
        parent::__construct('emails', self::VERSION);
        $this->themes = new NewsletterThemes('emails');
    }

    function upgrade() {
        global $wpdb, $charset_collate;
        $this->upgrade_query("alter table " . NEWSLETTER_EMAILS_TABLE . " change column `type` `type` varchar(50) not null default ''");
        $this->upgrade_query("alter table " . NEWSLETTER_EMAILS_TABLE . " add column token varchar(10) not null default ''");
        $this->upgrade_query("alter table " . NEWSLETTER_EMAILS_TABLE . " drop column visibility");
        $this->upgrade_query("update " . NEWSLETTER_EMAILS_TABLE . " set type='message' where type=''");

        // Force a token to email without one already set.
        $token = self::get_token();
        $wpdb->query("update " . NEWSLETTER_EMAILS_TABLE . " set token='" . $token . "' where token=''");

        return true;
    }

    function save_options($options) {
        $this->options = $options;
        parent::save_options($options);
        // This separately save the theme options
        $this->themes->save_options($options['theme'], $options);
    }

    /**
     * Returns the current selected theme.
     */
    function get_current_theme() {
       $theme = $this->options['theme'];
       if (empty($theme)) return 'blank';
       else return $theme;
    }

    function get_current_theme_options() {
        return $this->themes->get_options($this->get_current_theme());
    }

    /**
     * Returns the file path to a theme using the theme overriding rules.
     * @param type $theme
     * @param type $file
     */
    function get_theme_file_path($theme, $file) {
        return $this->themes->get_file_path($theme);
    }

    function get_current_theme_file_path($file) {
        return $this->themes->get_file_path($this->get_current_theme(), $file);
    }

    function get_current_theme_url() {
        return $this->themes->get_theme_url($this->get_current_theme());
    }

    /**
     * Returns true if the emails database still contain old 2.5 format emails.
     *
     * @return boolean
     */
    function has_old_emails() {
        return $this->store->get_count(NEWSLETTER_EMAILS_TABLE, "where type='email'") > 0;
    }

    function convert_old_emails() {
        global $newsletter;
        $list = $newsletter->get_emails('email', ARRAY_A);
        foreach ($list as &$email) {
            $email['type'] = 'message';
            $query = "select * from " . NEWSLETTER_USERS_TABLE . " where status='C'";

            if ($email['list'] != 0) $query .= " and list_" . $email['list'] . "=1";
            $email['preferences'] = $email['list'];

            if (!empty($email['sex'])) {
                $query .= " and sex='" . $email['sex'] . "'";
            }
            $email['query'] = $query;

            $newsletter->save_email($email);
        }
    }

}
