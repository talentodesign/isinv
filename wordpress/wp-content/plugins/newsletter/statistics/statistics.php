<?php

require_once NEWSLETTER_INCLUDES_DIR . '/module.php';

class NewsletterStatistics extends NewsletterModule {

    const VERSION = '1.0.2';

    static $instance;

    /**
     * @return NewsletterStatistics
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new NewsletterStatistics();
        }
        return self::$instance;
    }

    function __construct() {
        parent::__construct('statistics', self::VERSION);
    }

    function upgrade() {
        global $wpdb, $charset_collate;


        // This before table creation or update for compatibility
        $this->upgrade_query("alter table {$wpdb->prefix}newsletter_stats change column newsletter_id user_id int not null default 0");
        $this->upgrade_query("alter table {$wpdb->prefix}newsletter_stats change column newsletter_id user_id int not null default 0");
        $this->upgrade_query("alter table {$wpdb->prefix}newsletter_stats change column date created timestamp not null default current_timestamp");

        // Just for test since it will be part of statistics module
        // This table stores clicks and email opens. An open is registered with a empty url.
        $this->upgrade_query("create table if not exists {$wpdb->prefix}newsletter_stats (id int auto_increment, primary key (id)) $charset_collate");

        // References
        $this->upgrade_query("alter table {$wpdb->prefix}newsletter_stats add column user_id int not null default 0");
        $this->upgrade_query("alter table {$wpdb->prefix}newsletter_stats add column email_id int not null default 0");
        // Future... see the links table
        $this->upgrade_query("alter table {$wpdb->prefix}newsletter_stats add column link_id int not null default 0");

        $this->upgrade_query("alter table {$wpdb->prefix}newsletter_stats add column user_id int not null default 0");
        $this->upgrade_query("alter table {$wpdb->prefix}newsletter_stats add column created timestamp not null default current_timestamp");
        $this->upgrade_query("alter table {$wpdb->prefix}newsletter_stats add column url varchar(255) not null default ''");
        $this->upgrade_query("alter table {$wpdb->prefix}newsletter_stats add column anchor varchar(200) not null default ''");

        // Stores the link of every email to create short links
        $this->upgrade_query("create table if not exists {$wpdb->prefix}newsletter_links (id int auto_increment, primary key (id)) $charset_collate");
        $this->upgrade_query("alter table {$wpdb->prefix}newsletter_links add column email_id int not null default 0");
        $this->upgrade_query("alter table {$wpdb->prefix}newsletter_links add column token varchar(10) not null default ''");
        $this->upgrade_query("alter table {$wpdb->prefix}newsletter_links add column text varchar(255) not null default ''");
    }

    function relink($text, $email_id, $user_id) {
        $this->relink_email_id = $email_id;
        $this->relink_user_id = $user_id;
        $text = preg_replace_callback('/(<[aA][^>]+href=["\'])([^>"\']+)(["\'][^>]*>)(.*?)(<\/[Aa]>)/', array($this, 'relink_callback'), $text);

        // TODO: use the WP rewriting
        $text = str_replace('</body>', '<img src="' . NEWSLETTER_URL . '/statistics/open.php?r=' . urlencode(base64_encode($email_id . ';' . $user_id)) . '"/></body>', $text);
        return $text;
    }

    function relink_callback($matches) {
        $href = str_replace('&amp;', '&', $matches[2]);
        // Do not replace the tracking or subscription/unsubscription links.
        if (strpos($href, '/newsletter/') !== false) return $matches[0];
        if (substr($href, 0, 1) == '#') return $matches[0];

        $anchor = '';
        if ($this->options['anchor'] == 1) {
            $anchor = trim(str_replace(';', ' ', $matches[4]));
            $anchor = strip_tags($anchor, '<img>');
            if (stripos($anchor, '<img') === false && strlen($anchor) > 100) {
                $anchor = substr($anchor, 0, 100);
            }
        }

        $url = NEWSLETTER_URL . '/statistics/link.php?r=' .
                urlencode(base64_encode($this->relink_email_id . ';' . $this->relink_user_id . ';' . $href . ';' . $anchor));

        return $matches[1] . $url . $matches[3] . $matches[4] . $matches[5];
    }

    function get_statistics_url($email_id) {
        return 'admin.php?page=newsletter_statistics_view&amp;id=' . $email_id;
    }

    function get_read_count($email_id) {
        global $wpdb;
        $email_id = (int) $email_id;
        return (int) $wpdb->get_var("select count(distinct user_id) from " . NEWSLETTER_STATS_TABLE . " where email_id=" . $email_id);
    }

}

add_action('newsletter_admin_menu', 'newsletter_statistics_admin_menu');

/**
 * Add menu pages for this module.
 * @global Newsletter $newsletter
 */
function newsletter_statistics_admin_menu() {
    global $newsletter;
    $newsletter->add_menu_page('statistics', 'index', 'Statistics');
    $newsletter->add_admin_page('statistics', 'view', 'Statistics');
}

