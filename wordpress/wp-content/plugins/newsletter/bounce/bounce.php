<?php

class NewsletterBounce extends NewsletterModule {

    const VERSION = '1.0.0';

    static $instance;

    /**
     * @return NewsletterBounce
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new NewsletterBounce();
        }
        return self::$instance;
    }

    function __construct() {
        parent::__construct('bounce', self::VERSION);
    }

    function upgrade() {
        global $wpdb, $charset_collate;
        parent::upgrade();
    }

    /**
     * Run based on scheduled daily hour and generate, if needed, an email that will be then sent by
     * Newsletter delivery engine.
     *
     * @global Newsletter $newsetter
     */
    function run($force = false) {
        global $wpdb, $newsletter, $post;

        if (!$force && !$this->check_transient('run', 3600)) return;

        $this->save_last_run(time());
    }

}

add_action('newsletter_admin_menu', 'newsletter_bounce_admin_menu');

/**
 * Add menu pages for this module.
 * @global Newsletter $newsletter
 */
function newsletter_bounce_admin_menu() {
    global $newsletter;
    $newsletter->add_menu_page('bounce', 'index', 'Bounce');
}

add_action('newsletter_bounce', 'newsletter_bounce_run');

function newsletter_bounce_run() {
    NewsletterBounce::instance()->run();
}

