<?php
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterEmails::instance();
$store = NewsletterStore::instance();


if ($controls->is_action('change')) {
    // Recover the selected theme options, if any, and use them.
    $controls->merge($module->themes->get_options($controls->data['theme']));
    $module->save_options($controls->data);
}

if ($controls->is_action('save')) {
    $module->save_options($controls->data);
}

if ($controls->is_action('create') || $controls->is_action('test')) {
    $module->save_options($controls->data);

    if ($controls->is_action('test')) {
        $users = $controls->get_test_subscribers();
        $email = new stdClass();
        $email->id = 0;
        $email->message = $controls->data['message'];
        $email->message_text = $controls->data['message_text'];
        $email->subject = 'Test subject';
        $email->track = $controls->data['track'];
        $email->type = 'message';
        $newsletter->send($email, $users);
    }

    if ($controls->is_action('create')) {
        $email = array();
        $email['status'] = 'new';
        $email['subject'] = 'Here the email subject';
        $email['track'] = 1;

        $theme_options = $module->get_current_theme_options();
        $theme_url = $module->get_current_theme_url();

        ob_start();
        include $module->get_current_theme_file_path('theme.php');
        $email['message'] = ob_get_clean();

        ob_start();
        include $module->get_current_theme_file_path('theme-text.php');
        $email['message_text'] = ob_get_clean();

        $email['type'] = 'message';
        $email['send_on'] = time();
        $email = Newsletter::instance()->save_email($email);
    ?>
    <script>
        location.href="admin.php?page=newsletter/emails/edit.php&id=<?php echo $email->id; ?>";
    </script>
    <div class="wrap">
    <p>If you are not automatically redirected to the composer, <a href="admin.php?page=newsletter/emails/edit.php&id=<?php echo $email->id; ?>">click here</a>.</p>
    </div>
    <?php
        return;
    }
}

if ($controls->data == null) {
    $controls->data = NewsletterEmails::instance()->get_options();
}



function newsletter_emails_update_options($options) {
    add_option('newsletter_emails', '', null, 'no');
    update_option('newsletter_emails', $options);
  }

function newsletter_emails_update_theme_options($theme, $options) {
    $x = strrpos($theme, '/');
    if ($x !== false) {
      $theme = substr($theme, $x+1);
    }
    add_option('newsletter_emails_' . $theme, '', null, 'no');
    update_option('newsletter_emails_' . $theme, $options);
  }

function newsletter_emails_get_options() {
    $options = get_option('newsletter_emails', array());
    return $options;
  }

function newsletter_emails_get_theme_options($theme) {
    $x = strrpos($theme, '/');
    if ($x !== false) {
      $theme = substr($theme, $x+1);
    }
    $options = get_option('newsletter_emails_' . $theme, array());
    return $options;
  }
?>

<div class="wrap">

    <?php $help_url = 'http://www.satollo.net/plugins/newsletter/newsletters-module'; ?>
    <?php include NEWSLETTER_DIR . '/header.php'; ?>

    <h2>Newsletters Module</h2>


    <?php $controls->show(); ?>

    <!--
    <p>
      <strong>Select a theme</strong> to compose a precompiled message, tune the theme setting, look at the previews and then preceed to the
      composer.
    </p>
    -->

    <form method="post" action="admin.php?page=newsletter/emails/new.php" id="newsletter-form">
        <?php $controls->init(); ?>
        <div style="padding: .6em; border: 1px solid #ddd; background-color: #f4f4f4; border-radius: 3px;">
            <strong>Choose a theme</strong>
            <?php $controls->select('theme', NewsletterEmails::instance()->themes->get_all()); ?>
            <?php $controls->button('change', 'Change theme'); ?>
            <a href="http://www.satollo.net/plugins/newsletter/newsletter-themes" target="_blank">(more about themes)</a>
        </div>

        <p>
            <?php $controls->button('save', 'Save options and refresh'); ?>
            <?php $controls->button('create', 'Create the email'); ?>
        </p>

        <div id="tabs">
            <ul>
                <li><a href="#tabs-2">Preview</a></li>
                <li><a href="#tabs-3">Preview (textual)</a></li>
                <li><a href="#tabs-1">Theme options</a></li>
                <li><a href="#tabs-4">Help</a></li>
            </ul>

            <div id="tabs-1">
              <?php
                  include NewsletterEmails::instance()->get_current_theme_file_path('theme-options.php');
              ?>
            </div>


            <div id="tabs-2">
                <iframe src="<?php echo wp_nonce_url(NEWSLETTER_URL . '/emails/preview.php?' . time()); ?>" width="100%" height="500"></iframe>
            </div>


            <div id="tabs-3">
                <iframe src="<?php echo wp_nonce_url(NEWSLETTER_URL . '/emails/preview-text.php?' . time()); ?>" width="100%" height="500"></iframe>
            </div>

            <div id="tabs-4">
              <p>
                Custom themes can be created starting from the supplied themes (on <code>wp_content/plugins/newsletter/emails/themes</code>
                each subfolder is a theme)
                and copied inside the <code>wp_content/newsletter/emails/themes</code> folder.
              </p>
            </div>
        </div>

    </form>
</div>
