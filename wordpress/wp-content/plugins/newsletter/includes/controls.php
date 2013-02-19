<?php

class NewsletterControls {

    var $data;
    var $action = false;
    var $button_data = '';

    function __construct($options = null) {
        if ($options == null) $this->data = stripslashes_deep($_POST['options']);
        else $this->data = $options;

        $this->action = $_REQUEST['act'];

        if (isset($_REQUEST['btn'])) $this->button_data = $_REQUEST['btn'];

        // Fields analysis
        $fields = $_REQUEST['fields'];
        if (is_array($fields)) {
            foreach ($fields as $name=>$type) {
                if ($type == 'datetime') {
                    // Ex. The user insert 01/07/2012 14:30 and it set the time zone to +2. We cannot use the
                    // mktime, since it uses the time zone of the machine. We create the time as if we are on
                    // GMT 0 and then we subtract the GMT offset (the example date and time on GMT+2 happens
                    // "before").

                    $time = gmmktime($_REQUEST[$name . '_hour'], 0, 0,
                            $_REQUEST[$name . '_month'], $_REQUEST[$name . '_day'], $_REQUEST[$name . '_year']);
                    $time -= get_option('gmt_offset') * 3600;
                    $this->data[$name] = $time;
                }
            }
        }
    }

  function merge($options) {
      if (!is_array($options)) return;
      if ($this->data == null) $this->data = array();
      $this->data = array_merge($this->data, $options);
  }

    function merge_defaults($defaults) {
        if ($this->data == null) $this->data = $defaults;
        else $this->data = array_merge($defaults, $this->data);
    }

    /**
     * Return true is there in an asked action is no action name is specified or
     * true is the requested action matches the passed action.
     * Dies if it is not a safe call.
     */
    function is_action($action = null) {
        if ($action == null) return $this->action != null;
        if ($this->action == null) return false;
        if ($this->action != $action) return false;
        if (check_admin_referer()) return true;
        die('Invalid call');
    }

    /**
     * Show the errors and messages.
     */
    function show() {
        if (!empty($this->errors)) {
            echo '<div class="newsletter-error">';
            echo $this->errors;
            echo '</div>';
        }
        if (!empty($this->messages)) {
            echo '<div class="newsletter-message">';
            echo $this->messages;
            echo '</div>';
        }
    }

    function yesno($name) {
        $value = isset($this->data[$name]) ? (int) $this->data[$name] : 0;

        echo '<select style="width: 60px" name="options[' . $name . ']">';
        echo '<option value="0"';
        if ($value == 0) echo ' selected';
        echo '>No</option>';
        echo '<option value="1"';
        if ($value == 1) echo ' selected';
        echo '>Yes</option>';
        echo '</select>&nbsp;&nbsp;&nbsp;';
    }

    function enabled($name) {
        $value = isset($this->data[$name]) ? (int) $this->data[$name] : 0;

        echo '<select style="width: 100px" name="options[' . $name . ']">';
        echo '<option value="0"';
        if ($value == 0) echo ' selected';
        echo '>Disabled</option>';
        echo '<option value="1"';
        if ($value == 1) echo ' selected';
        echo '>Enabled</option>';
        echo '</select>';
    }

    function checkbox_group($name, $value, $label = '') {
        echo '<input type="checkbox" id="' . $name . '" name="options[' . $name . '][]" value="' . $value . '"';
        if (is_array($this->data[$name]) && array_search($value, $this->data[$name]) !== false)
                echo ' checked="checked"';
        echo '/>';
        if ($label != '') echo ' <label for="' . $name . '">' . $label . '</label>';
    }

    function select_group($name, $options) {
        echo '<select name="options[' . $name . '][]">';

        foreach ($options as $key => $label) {
            echo '<option value="' . $key . '"';
            if (is_array($this->data[$name]) && array_search($value, $this->data[$name]) !== false) echo ' selected';
            echo '>' . htmlspecialchars($label) . '</option>';
        }

        echo '</select>';
    }

    function select($name, $options, $first = null) {
        $value = $this->data[$name];

        echo '<select id="options-' . $name . '" name="options[' . $name . ']">';
        if (!empty($first)) {
            echo '<option value="">' . htmlspecialchars($first) . '</option>';
        }
        foreach ($options as $key => $label) {
            echo '<option value="' . $key . '"';
            if ($value == $key) echo ' selected';
            echo '>' . htmlspecialchars($label) . '</option>';
        }
        echo '</select>';
    }

    function select_grouped($name, $groups) {
        $value = $this->data[$name];

        echo '<select name="options[' . $name . ']">';

        foreach ($groups as $group) {
            echo '<optgroup label="' . htmlspecialchars($group['']) . '">';
            if (!empty($group)) {
                foreach ($group as $key => $label) {
                    if ($key == '') continue;
                    echo '<option value="' . $key . '"';
                    if ($value == $key) echo ' selected';
                    echo '>' . htmlspecialchars($label) . '</option>';
                }
            }
            echo '</optgroup>';
        }
        echo '</select>';
    }

    /**
     * Generated a select control with all available templates. From version 3 there are
     * only on kind of templates, they are no more separated by type.
     */
    function themes($name, $theme_dir, $theme_dir2 = null) {
        $list = array();

        $handle = @opendir($theme_dir);

        while ($file = readdir($handle)) {
            if ($file == '.' || $file == '..') continue;
            // TODO: optimize the string concatenation
            if (!is_dir($theme_dir . '/' . $file)) continue;
            if (!is_file($theme_dir . '/' . $file . '/theme.php')) continue;
            $list[$theme_dir . '/' . $file] = $file;
        }
        closedir($handle);

        if ($theme_dir2 != null && is_dir($theme_dir2)) {
            $handle = @opendir($theme_dir2);
            $list = array();
            while ($file = readdir($handle)) {
                if ($file == '.' || $file == '..') continue;
                // TODO: optimize the string concatenation
                if (!is_dir($theme_dir2 . '/' . $file)) continue;
                if (!is_file($theme_dir2 . '/' . $file . '/theme.php')) continue;
                $list[$theme_dir2 . '/' . $file] = $file;
            }
            closedir($handle);
        }

        $this->select($name, $list);
    }

    function value($name) {
        echo htmlspecialchars($this->data[$name]);
    }

    function value_date($name) {
        $time = $this->data[$name];
        echo gmdate(get_option('date_format') . ' ' . get_option('time_format'), $time + get_option('gmt_offset') * 3600);
    }

    function text($name, $size = 20) {
        echo '<input name="options[' . $name . ']" type="text" size="' . $size . '" value="';
        echo htmlspecialchars($this->data[$name]);
        echo '"/>';
    }

    function text_email($name, $size = 40) {
        echo '<input name="options[' . $name . ']" type="email" placeholder="Valid email address" size="' . $size . '" value="';
        echo htmlspecialchars($this->data[$name]);
        echo '"/>';
    }

    function hidden($name) {
        echo '<input name="options[' . $name . ']" type="hidden" value="';
        echo htmlspecialchars($this->data[$name]);
        echo '"/>';
    }

    function button($action, $label, $function = null) {
        if ($function != null) {
            echo '<input class="button-secondary" type="button" value="' . $label . '" onclick="this.form.act.value=\'' . $action . '\';' . htmlspecialchars($function) . '"/>';
        } else {
            echo '<input class="button-secondary" type="button" value="' . $label . '" onclick="this.form.act.value=\'' . $action . '\';this.form.submit()"/>';
        }
    }

    function button_confirm($action, $label, $message, $data = '') {
        echo '<input class="button-secondary" type="button" value="' . $label . '" onclick="this.form.btn.value=\'' . $data . '\';this.form.act.value=\'' . $action . '\';if (confirm(\'' .
        htmlspecialchars($message) . '\')) this.form.submit()"/>';
    }

    function editor($name, $rows = 5, $cols = 75) {
        echo '<textarea class="visual" name="options[' . $name . ']" style="width: 100%" wrap="off" rows="' . $rows . '">';
        echo htmlspecialchars($this->data[$name]);
        echo '</textarea>';
    }

    function textarea($name, $width = '100%', $height = '50') {
        echo '<textarea class="dynamic" name="options[' . $name . ']" wrap="off" style="width:' . $width . ';height:' . $height . '">';
        echo htmlspecialchars($this->data[$name]);
        echo '</textarea>';
    }

    function textarea_fixed($name, $width = '100%', $height = '200') {
        echo '<textarea name="options[' . $name . ']" wrap="off" style="width:' . $width . ';height:' . $height . 'px">';
        echo htmlspecialchars($this->data[$name]);
        echo '</textarea>';
    }

    function email($prefix) {
        echo 'Subject:<br />';
        $this->text($prefix . '_subject', 70);
        echo '<br />Message:<br />';
        $this->editor($prefix . '_message');
    }

    function checkbox($name, $label = '') {
        echo '<input type="checkbox" id="' . $name . '" name="options[' . $name . ']" value="1"';
        if (!empty($this->data[$name])) echo ' checked="checked"';
        echo '/>';
        if ($label != '') echo '&nbsp;<label for="' . $name . '">' . $label . '</label>';
    }

    function color($name) {
        echo $this->text($name, 10);
    }

    /**
     * Creates a set of checkbox to activate the profile preferences. Every checkbox has a DIV around to
     * be formatted.
     */
    function preferences_group($name = 'preferences', $skip_empty = false) {
        $options_profile = get_option('newsletter_profile');

        echo '<div class="newsletter-preferences-group">';
        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            if (empty($options_profile['list_' . $i])) continue;
            echo '<div class="newsletter-preferences-item">';
            $this->checkbox_group($name, $i, '(' . $i . ') ' . htmlspecialchars($options_profile['list_' . $i]));
            echo '</div>';
        }
        echo '<div style="clear: both"></div>';
        echo '<a href="http://www.satollo.net/plugins/newsletter/newsletter-preferences" target="_blank">Click here know more about preferences.</a> They can be configured on Subscription/Form field panel.';
        echo '</div>';
    }

    function preferences($name = 'preferences', $skip_empty = false) {
        $options_profile = get_option('newsletter_profile');
        echo '<div class="newsletter-preferences-group">';

        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            if (empty($options_profile['list_' . $i])) continue;
            echo '<div class="newsletter-preferences-item">';
            $this->checkbox($name . '_' . $i, '(' . $i . ') ' . htmlspecialchars($options_profile['list_' . $i]));
            echo '</div>';
        }
        echo '<div style="clear: both"></div>';
        echo '<a href="http://www.satollo.net/plugins/newsletter/newsletter-preferences" target="_blank">Click here know more about preferences.</a> They can be configured on Subscription/Form field panel.';
        echo '</div>';

    }

    function date($name) {
        $this->hidden($name);
        $year = date('Y', $this->data[$name]);
        $day = date('j', $this->data[$name]);
        $month = date('m', $this->data[$name]);
        $onchange = "this.form.elements['options[" . $name . "]'].value = new Date(document.getElementById('" . $name . "_year').value, document.getElementById('" . $name . "_month').value, document.getElementById('" . $name . "_day').value, 12, 0, 0).getTime()/1000";
        echo '<select id="' . $name . '_month" onchange="' . $onchange . '">';
        for ($i = 0; $i < 12; $i++) {
            echo '<option value="' . $i . '"';
            if ($month - 1 == $i) echo ' selected';
            echo '>' . date('F', mktime(0, 0, 0, $i + 1, 1, 2000)) . '</option>';
        }
        echo '</select>';

        echo '<select id="' . $name . '_day" onchange="' . $onchange . '">';
        for ($i = 1; $i <= 31; $i++) {
            echo '<option value="' . $i . '"';
            if ($day == $i) echo ' selected';
            echo '>' . $i . '</option>';
        }
        echo '</select>';

        echo '<select id="' . $name . '_year" onchange="' . $onchange . '">';
        for ($i = 2011; $i <= 2021; $i++) {
            echo '<option value="' . $i . '"';
            if ($year == $i) echo ' selected';
            echo '>' . $i . '</option>';
        }
        echo '</select>';
    }

    function datetime($name) {
        echo '<input type="hidden" name="fields[' . $name . ']" value="datetime">';
        $time = $this->data[$name] + get_option('gmt_offset') * 3600;
        $year = gmdate('Y', $time);
        $day = gmdate('j', $time);
        $month = gmdate('m', $time);
        $hour = gmdate('H', $time);

        echo '<select name="' . $name . '_month">';
        for ($i = 1; $i <= 12; $i++) {
            echo '<option value="' . $i . '"';
            if ($month == $i) echo ' selected';
            echo '>' . date('F', mktime(0, 0, 0, $i, 1, 2000)) . '</option>';
        }
        echo '</select>';

        echo '<select name="' . $name . '_day">';
        for ($i = 1; $i <= 31; $i++) {
            echo '<option value="' . $i . '"';
            if ($day == $i) echo ' selected';
            echo '>' . $i . '</option>';
        }
        echo '</select>';

        echo '<select name="' . $name . '_year">';
        for ($i = 2011; $i <= 2021; $i++) {
            echo '<option value="' . $i . '"';
            if ($year == $i) echo ' selected';
            echo '>' . $i . '</option>';
        }
        echo '</select>';

        echo '<select name="' . $name . '_hour">';
        for ($i = 0; $i <= 23; $i++) {
            echo '<option value="' . $i . '"';
            if ($hour == $i) echo ' selected';
            echo '>' . $i . ':00</option>';
        }
        echo '</select>';
    }

    function hours($name) {
        $hours = array();
        for ($i = 0; $i < 24; $i++) {
            $hours['' . $i] = '' . $i;
        }
        $this->select($name, $hours);
    }

    function days($name) {
        $days = array(0 => 'Every day', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday');
        $this->select($name, $days);
    }

    function init() {
        echo '<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("textarea.dynamic").focus(function() {
            jQuery("textarea.dynamic").css("height", "50px");
            jQuery(this).css("height", "400px");
        });
      tabs = jQuery("#tabs").tabs({ cookie: { expires: 30 } });
    });
</script>
';
        echo '<input name="act" type="hidden" value=""/>';
        echo '<input name="btn" type="hidden" value=""/>';
        wp_nonce_field();
    }

    function log_level($name = 'log_level') {
        $this->select($name, array(0 => 'None', 2 => 'Error', 3 => 'Normal', 4 => 'Debug'));
    }

    function update_option($name, $data = null) {
        if ($data == null) $data = $this->data;
        update_option($name, $data);
        if (isset($data['log_level'])) {
            update_option($name . '_log_level', $data['log_level']);
        }
    }

//  function button_link($action, $url, $anchor) {
//    if (strpos($url, '?') !== false) $url .= $url . '&';
//    else $url .= $url . '?';
//    $url .= 'act=' . $action;
//
//    $url .= '&_wpnonce=' . wp_create_nonce();
//
//    echo '<a class="button" href="' . $url . '">' . $anchor . '</a>';
//  }

    function js_redirect($url) {
        echo '<script>';
        echo 'location.href="' . $url . '"';
        echo '</script>';
    }

    /**
     * @deprecated
     */
    function save_user($subscriber) {
        return NewsletterUsers::instance()->save_user($user);
    }

    /**
     * @deprecated
     */
    function get_test_subscribers() {
        return NewsletterUsers::instance()->get_test_users();
    }

}

?>
