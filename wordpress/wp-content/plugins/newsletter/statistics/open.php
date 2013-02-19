<?php

require_once '../../../../wp-load.php';

list($email_id, $user_id) = explode(';', base64_decode($_GET['r']), 2);

// TODO: Create a service inside store o newsletter statistics
$wpdb->insert(NEWSLETTER_STATS_TABLE, array(
    'email_id' => $email_id,
    'user_id' => $user_id,
        )
);

header('Content-Type: image/gif');
readfile(dirname(__FILE__) . '/1x1.gif');
die();

