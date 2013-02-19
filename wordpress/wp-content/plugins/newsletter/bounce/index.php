<?php
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$module = NewsletterBounce::instance();
$controls = new NewsletterControls();

if ($controls->is_action('save')) {
    $module->save_options($controls->data);
    $controls->messages = 'Saved.';
}
?>

<div class="wrap">
    <h2>Bounce (not working)</h2>
    <p>
        In this panel you can configure the bounce detection system. Remember the mailbox you select as trap for bounce messages
        must accept all address in the form [prefix]+[something]@domain.tld.
    </p>

</div>
