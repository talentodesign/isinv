<?php
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$module = NewsletterStatistics::instance();
$controls = new NewsletterControls();
$emails = Newsletter::instance()->get_emails();

if ($controls->is_action('save')) {
    $module->save_options($controls->data);
    $controls->messages = 'Saved.';
}
?>

<div class="wrap">
    <?php $help_url = 'http://www.satollo.net/plugins/newsletter/statistics-module'; ?>
    <?php include NEWSLETTER_DIR . '/header.php'; ?>
    <h2>Statistics Module</h2>
    <p><em>This is a brief introduction waiting for an official page.</em></p>
    <p>
        The Newsletter Statistics Module adds to Newsletter the ability to collect messages opening and link clicks on
        those messages.
    </p>
    <p>
        Statistic data is collected per email, so on panels listing emals there should always be a "statistics" button
        which leads to the analytics panel for that email.
    </p>

    <h3>Summary of collected data</h3>
    <p><em>To do...</em></p>
    <table class="widefat" style="width: auto">
        <thead>
            <tr>
            <th>Parameter</th>
            <th>Value</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>Total email sent</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Total clicks</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Total unique users which read or clicked</td>
                <td>-</td>
            </tr>
        </tbody>
    </table>


</div>
