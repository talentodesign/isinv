<?php
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterUsers::instance();

if ($controls->is_action('save')) {

    $controls->data['status'] = 'C';

    $user = $module->save_user($controls->data);
    if ($user === false) {
        $controls->errors = 'This email already exists.';
    } else {
        $controls->js_redirect('admin.php?page=newsletter/users/edit.php&id=' . $user->id);
        return;
    }
}
?>
<div class="wrap">
    <?php $help_url = 'http://www.satollo.net/plugins/newsletter/subscribers-module'; ?>
    <?php include NEWSLETTER_DIR . '/header.php'; ?>
    <?php include NEWSLETTER_DIR . '/users/menu.inc.php'; ?>

    <?php $controls->show(); ?>

    <form method="post" action="">
        <?php $controls->init(); ?>

        <table class="form-table">
            <tr valign="top">
                <th>New email address</th>
                <td>
                    <?php $controls->text('email', 60); ?>
                    <?php $controls->button('save', 'Proceed'); ?>

                </td>
            </tr>
        </table>

    </form>
</div>
