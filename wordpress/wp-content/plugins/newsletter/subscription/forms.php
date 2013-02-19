<?php
@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();

if (!$controls->is_action()) {
    $controls->data = get_option('newsletter_forms');
}

if ($controls->is_action('save')) {
    update_option('newsletter_forms', $controls->data);
    $controls->messages = 'Saved';
}
?>

<div class="wrap">
    <?php $help_url = 'http://www.satollo.net/plugins/newsletter/newsletter-forms'; ?>
    <?php include NEWSLETTER_DIR . '/header.php'; ?>

    <?php include NEWSLETTER_DIR . '/subscription/menu.inc.php'; ?>

    <?php $controls->show(); ?>

    <div class="preamble">
        <p>
            Here you can store your hand coded forms to recall them from short codes.
            <a href="http://www.satollo.net/plugins/newsletter/newsletter-forms" target="_blank">Read more about forms</a>.
        </p>
    </div>

    <form method="post" action="">
        <?php $controls->init(); ?>

        <div id="tabs">

            <ul>
                <li><a href="#tabs-1">Forms</a></li>
            </ul>

            <div id="tabs-1">
                <table class="form-table">
                    <?php for ($i = 1; $i <= 10; $i++) { ?>
                        <tr valign="top">
                            <th>Form <?php echo $i; ?></th>
                            <td>
                                <?php $controls->textarea('form_' . $i); ?>
                                <br />
                                <?php $controls->button('save', 'Save'); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

        </div>
    </form>

</div>