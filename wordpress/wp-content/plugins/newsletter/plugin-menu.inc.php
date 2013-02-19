<?php

$level = $this->options['editor'] ? 7 : 10;

add_menu_page('Newsletter', 'Newsletter', $level, 'newsletter/welcome.php', '', '');
//add_submenu_page('newsletter/welcome.php', 'User Guide', 'User Guide', $level, 'newsletter/main.php');

add_submenu_page('newsletter/welcome.php', 'Welcome & Support', 'Welcome & Support', $level, 'newsletter/welcome.php');

add_submenu_page('newsletter/welcome.php', 'Configuration', 'Configuration', $level, 'newsletter/main.php');

add_submenu_page('newsletter/welcome.php', 'Subscription', 'Subscription', $level, 'newsletter/subscription/options.php');
add_submenu_page(null, 'Subscription Form', 'Subscription Form', $level, 'newsletter/subscription/profile.php');
add_submenu_page('newsletter/subscription/options.php', 'Forms', 'Forms', $level, 'newsletter/subscription/forms.php');
add_submenu_page('newsletter/subscription/options.php', 'Forms', 'Forms', $level, 'newsletter/subscription/form-code.php');

add_submenu_page('newsletter/welcome.php', 'Newsletters', 'Newsletters', $level, 'newsletter/emails/index.php');
add_submenu_page(null, 'Email Edit', 'Email Edit', $level, 'newsletter/emails/old-emails.php');
add_submenu_page(null, 'Email Edit', 'Email Edit', $level, 'newsletter/emails/old-edit.php');

add_submenu_page(null, 'Email List', 'Email List', $level, 'newsletter/emails/list.php');
add_submenu_page(null, 'Email New', 'Email New', $level, 'newsletter/emails/new.php');
add_submenu_page(null, 'Email Edit', 'Email Edit', $level, 'newsletter/emails/edit.php');
add_submenu_page(null, 'Email Theme', 'Email Theme', $level, 'newsletter/emails/theme.php');

add_submenu_page(null, 'Email statistics', 'Email statistics', $level, 'newsletter/statistics/statistics-email.php');

add_submenu_page('newsletter/welcome.php', 'Subscribers', 'Subscribers', $level, 'newsletter/users/index.php');
add_submenu_page('newsletter/users/index.php', 'New subscriber', 'New subscriber', $level, 'newsletter/users/new.php');
add_submenu_page('newsletter/users/index.php', 'Subscribers Edit', 'Subscribers Edit', $level, 'newsletter/users/edit.php');
add_submenu_page('newsletter/users/index.php', 'Subscribers Statistics', 'Subscribers Statistics', $level, 'newsletter/users/stats.php');
add_submenu_page('newsletter/users/index.php', 'Massive Management', 'Massive Management', $level, 'newsletter/users/massive.php');
add_submenu_page('newsletter/users/index.php', 'Import', 'Import', $level, 'newsletter/users/import.php');
add_submenu_page('newsletter/users/index.php', 'Export', 'Export', $level, 'newsletter/users/export.php');

// Statistics
//add_submenu_page('newsletter/welcome.php', 'Statistics', 'Statistics', $level, 'newsletter/statistics/statistics-index.php');
//add_submenu_page('newsletter/statistics/statistics-index.php', 'Statistics', 'Statistics', $level, 'newsletter/statistics/statistics-view.php');

// Updates
//add_submenu_page('newsletter/welcome.php', 'Updates', 'Updates', $level, 'newsletter/updates/updates-index.php');
//add_submenu_page('newsletter/updates/updates-index.php', 'Updates', 'Updates', $level, 'newsletter/updates/updates-edit.php');
//add_submenu_page('newsletter/updates/updates-index.php', 'Updates', 'Updates', $level, 'newsletter/updates/updates-emails.php');

?>
