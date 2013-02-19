<?php

@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$options_profile = get_option('newsletter_profile');

$controls = new NewsletterControls();

$lists = array('0' => 'All');
for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
    $lists['' . $i] = '(' . $i . ') ' . $options_profile['list_' . $i];
}
?>

<div class="wrap">
  <?php include NEWSLETTER_DIR . '/users/menu.inc.php'; ?>

  <form method="post" action="<?php echo NEWSLETTER_URL; ?>/users/csv.php">
    <?php $controls->init(); ?>
    <table class="form-table">
      <tr>
        <td>
          <?php $controls->select('list', $lists); ?>
          <?php $controls->button('export', 'Export'); ?>
        </td>
      </tr>
    </table>
  </form>

</div>
