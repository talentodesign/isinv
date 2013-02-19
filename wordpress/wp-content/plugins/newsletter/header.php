<?php 
$dismissed = get_option('newsletter_dismissed', array());

if (isset($_REQUEST['dismiss']) && check_admin_referer()) {
    $dismissed[$_REQUEST['dismiss']] = 1;
    update_option('newsletter_dismissed', $dismissed);
}
    
?>
<div id="newsletter-header">
    <a href="<?php echo $help_url?$help_url:'http://www.satollo.net/plugins/newsletter/newsletter-configuration'; ?>" target="_blank">Get Help</a>
    <a href="http://www.satollo.net/plugins/newsletter/newsletter-faq" target="_blank">FAQ</a>
    <a href="http://www.satollo.net/forums" target="_blank">Forum</a>
    <a href="http://www.satollo.net/plugins/newsletter/newsletter-collaboration" target="_blank">Collaboration</a>

    <form style="display: inline; margin: 0;" action="http://www.satollo.net/wp-content/plugins/newsletter/do/subscribe.php" method="post" target="_blank">
        Subscribe to satollo.net <input type="email" name="ne" required placeholder="Your email">
        <input type="submit" value="Go">
    </form>

    <a href="https://www.facebook.com/satollo.net" target="_blank"><img style="vertical-align: bottom" src="<?php echo NEWSLETTER_URL; ?>/images/facebook.png"></a>

    <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5Y6JXSA7BSU2L" target="_blank"><img style="vertical-align: bottom" src="<?php echo NEWSLETTER_URL; ?>/images/donate.png"></a>
    <a href="http://www.satollo.net/donations" target="_blank">Even <b>1$</b> helps: read more</a>

    Engine next run in <?php echo wp_next_scheduled('newsletter') - time(); ?> s
</div>

<?php if ($dismissed['rate'] != 1) { ?>
<div class="newsletter-notice">
    I never asked before and I'm curious: <a href="http://wordpress.org/extend/plugins/newsletter/" target="_blank">would you rate this plugin</a>? 
    (few seconds required). (account on WordPress.org required, every blog owner should have one...). <strong>Really appreciated, Stefano</strong>.
    <div class="newsletter-dismiss"><a href="<?php echo wp_nonce_url($_SERVER['REQUEST_URI'] . '&dismiss=rate')?>">Dismiss</a></div>
    <div style="clear: both"></div>
</div>
<?php } ?>

<?php $newsletter->warnings(); ?>
