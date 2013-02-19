<?php
@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$controls = new NewsletterControls();

if ($controls->is_action('trigger')) {
    $newsletter->hook_newsletter();
    $controls->messages = 'Delivery engine triggered.';
}

if ($controls->is_action('trigger_followup')) {
    NewsletterFollowup::instance()->send();
    $controls->messages = 'Follow up delivery engine triggered.';
}

if ($controls->is_action('engine_on')) {
    wp_clear_scheduled_hook('newsletter');
    wp_schedule_event(time() + 30, 'newsletter', 'newsletter');
    $controls->messages = 'Delivery engine reactivated.';
}

if ($controls->is_action('upgrade')) {
    // TODO: Compact them in a call to Newsletter which should be able to manage the installed modules
    Newsletter::instance()->upgrade();
    NewsletterUsers::instance()->upgrade();
    NewsletterSubscription::instance()->upgrade();
    NewsletterEmails::instance()->upgrade();
    $controls->messages = 'Upgrade forced!';
}

if ($controls->is_action('delete_transient')) {
    delete_transient($_POST['btn']);
    $controls->messages = 'Deleted.';
}

$x = wp_next_scheduled('newsletter');
if ($x === false) {
    $controls->errors = 'The delivery engine is off (it should never be off). See the System Check below to reactivate it.';
}
?>
<div class="wrap">

    <?php $help_url = 'http://www.satollo.net/plugins/newsletter'; ?>
    <?php include NEWSLETTER_DIR . '/header.php'; ?>

    <h2>Welcome and Support</h2>

    <?php $controls->show(); ?>

    <form method="post" action="">
        <?php $controls->init(); ?>

        <p>
            Thank you to use Newsletter plugin. Newsletter counts more than
            <a href="http://wordpress.org/extend/plugins/newsletter/stats/" target="_blank">400.000 downloads</a> and
            that number is a clear sign of how much useful it is to many bloggers. Or at least I hope that.
        </p>

        <p>
            <strong>Old 2.5.2.7 version is still available <a href="http://www.satollo.net/wp-content/uploads/newsletter-2.5.2.7.zip">here</a>.</strong>
        </p>

        <h3>Version 3.0</h3>
        <p>
            Many reasons lead me to create a totally new version: Newsletter needed more modularity,
            more compatibility, more functions. As for version 2.5, this is a big step forward on both
            configuration organization and functionalities and improvements.
        </p>
        <p>
            The great amount of rework, even if made will all possible attentions, could generate new bugs,
            little incompatibilities and other kinds of issues. I would ask you to be patince and to notify me
            every anomality you encounter, using the support options below.
        </p>

        <h3>First steps</h3>
        <p>
            <strong>Newsletter works out of box</strong>. You don't need to create lists or evenly configure it. Just use your WordPress
            appearance panel, enter the widgets panel and ass the Newsletter widget.
        </p>
        <p>
            To get the most out of Newsletter, to translate messages and so on, it's important to understand the single panels of Newsletter:
        </p>
        <ol>
            <li>
                <strong>Configuration</strong>: is where you find the main setting, like the SMTP, the sender address,
                the delivery engine speed and so on.
            </li>
            <li>
                <strong>Subscription</strong>: is where you configure the subscription process and it's one of the most important panel
                to explore and to understand. Subscription is not limited to collect email addresses! There you define the fields of the
                subscription box, optionally a dedicated page for subscription and profile edit and so on.
            </li>
            <li>
                <strong>Newsletters</strong>: is where you create and send messages to your subscribers. You choose a theme,
                set some parameters, preview the message and finally compose it.
            </li>
            <li>
                <strong>Subscribers</strong>: is where you manage your subscribers like edit, create, export/import and so on.
            </li>
            <li>
                <strong>Statistics</strong>: is where you configure the statistic system; statistics of single email (open, clicks)
                are accessible directly from email lists.
            </li>
        </ol>


        <h3>Support</h3>
        <p>
            There are some options to find or ask for support. Users with Newsletter Pro or Newsletter Pro Extensions can
            use the <a href="http://www.satollo.net/support-form" target="_blank">support form</a> even if the resources below are the first option.
        </p>
        <ul>
            <li><a href="http://www.satollo.net/plugins/newsletter" target="_blank">The official Newsletter page</a> contains information and links extended documentationand FAQ</li>
            <li><a href="http://www.satollo.net/forums/forum/newsletter-plugin" target="_blank">The official Newsletter forum</a> where to find solutions or create new requests</li>
            <li><a href="http://www.satollo.net/tag/newsletter" target="_blank">Newsletter articles and comments</a> are a source of solutions</li>
            <li>Write directly to me at stefano@satollo.net</li>
        </ul>

        <h3>Collaboration</h3>
        <p>
            Any kind of collaboration for this free plugin is welcome (of course). I set up a
            <a href="http://www.satollo.net/plugins/newsletter/newsletter-collaboration" target="_blank">How to collaborate</a>
            page.
        </p>

        <h3>Documentation</h3>
        <p>
            Below are the pages on www.satollo.net which document Newsletter. Since the site evolves, more page can be available and
            the full list is always up-to-date on main Newsletter page.
        </p>

        <ul>
            <li><a href="http://www.satollo.net/plugins/newsletter" target="_blank">Official Newsletter page</a></li>
            <li><a href="http://www.satollo.net/plugins/newsletter/newsletter-configuration" target="_blank">Main configuration</a></li>
            <li><a href="http://www.satollo.net/plugins/newsletter/newsletter-diagnostic" target="_blank">Diagnostic</a></li>
            <li><a href="http://www.satollo.net/plugins/newsletter/newsletter-faq" target="_blank">FAQ</a></li>
            <li><a href="http://www.satollo.net/plugins/newsletter/newsletter-delivery-engine" target="_blank">Delivery Engine</a></li>


            <li><a href="http://www.satollo.net/plugins/newsletter/subscription-module" target="_blank">Subscription Module</a></li>
            <li><a href="http://www.satollo.net/plugins/newsletter/newsletter-forms" target="_blank">Subscription Forms</a></li>
            <li><a href="http://www.satollo.net/plugins/newsletter/newsletter-preferences" target="_blank">Subscriber's preferences</a></li>

            <li><a href="http://www.satollo.net/plugins/newsletter/newsletters-module" target="_blank">Newsletters Module</a></li>
            <li><a href="http://www.satollo.net/plugins/newsletter/newsletter-themes" target="_blank">Themes</a></li>

            <li><a href="http://www.satollo.net/plugins/newsletter/subscribers-module" target="_blank">Subscribers Module</a></li>
            <li><a href="http://www.satollo.net/plugins/newsletter/statistics-module" target="_blank">Statistics Module</a></li>
            <!--
            <li><a href="http://www.satollo.net/plugins/newsletter/feed-by-mail-module" target="_blank">Feed by Mail Module</a></li>
            <li><a href="http://www.satollo.net/plugins/newsletter/follow-up-module" target="_blank">Follow Up Module</a></li>
            -->
        </ul>


    </form>

</div>
