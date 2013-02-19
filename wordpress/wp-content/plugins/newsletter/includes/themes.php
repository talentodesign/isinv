<?php

class NewsletterThemes {

    var $module;

    function __construct($module) {
        $this->module = $module;
    }

    /** Loads all themes of a module (actually only "emails" module makes sense). Themes are located inside the subfolder
     * named as the module on plugin folder and on a subfolder named as the module on wp-content/newsletter folder (which
     * must be manually created).
     *
     * @param type $module
     * @return type
     */
    function get_all() {
        $list = array();

        $dir = NEWSLETTER_DIR . '/' . $this->module . '/themes';
        $handle = @opendir($dir);

        if ($handle !== false) {
            while ($file = readdir($handle)) {
                if ($file == '.' || $file == '..') continue;
                if (!is_file($dir . '/' . $file . '/theme.php')) continue;

                $list[$file] = $file;
            }
            closedir($handle);
        }

        $dir = WP_CONTENT_DIR . '/extensions/newsletter/' . $this->module . '/themes';
        $handle = @opendir($dir);

        if ($handle !== false) {
            while ($file = readdir($handle)) {
                if ($file == '.' || $file == '..') continue;
                if (isset($list[$file])) continue;
                if (!is_file($dir . '/' . $file . '/theme.php')) continue;
                $list[$file] = $file;
            }
            closedir($handle);
        }
        return $list;
    }

    /**
     *
     * @param type $theme
     * @param type $options
     * @param type $module
     */
    function save_options($theme, &$options) {
        add_option('newsletter_' . $this->module . '_theme_' . $theme, array(), null, 'no');
        $theme_options = array();
        foreach ($options as $key => &$value) {
            if (substr($key, 0, 6) != 'theme_') continue;
            $theme_options[$key] = $value;
        }
        update_option('newsletter_' . $this->module . '_theme_' . $theme, $theme_options);
    }

    function get_options($theme) {
        $options = get_option('newsletter_' . $this->module . '_theme_' . $theme);
        // To avoid merge problems.
        if (!is_array($options)) return array();
        return $options;
    }

    function get_file_path($theme, $file) {
        $path = WP_CONTENT_DIR . '/extensions/newsletter/' . $this->module . '/themes/' . $theme . '/' . $file;
        if (is_file($path)) return $path;
        else return NEWSLETTER_DIR . '/' . $this->module . '/themes/' . $theme . '/' . $file;
    }

    function get_theme_url($theme) {
        $path = NEWSLETTER_DIR . '/' . $this->module . '/themes/' . $theme;
        if (is_dir($path)) {
            return NEWSLETTER_URL . '/' . $this->module . '/themes/' . $theme;
        }
        else {
            return WP_CONTENT_URL . '/extensions/newsletter/' . $this->module . '/themes/' . $theme;
        }
    }

    function get_default_options() {
        $path1 = NEWSLETTER_DIR . '/' . $this->module . '/themes/' . $theme . '/languages';
        $path2 = WP_CONTENT_DIR . '/extensions/newsletter/' . $this->module . '/themes/' . $theme . '/languages';
        @include $path1 . '/en_US.php';
        @include $path2 . '/en_US.php';
        @include $path1 . '/' . WPLANG . '.php';
        @include $path2 . '/' . WPLANG . '.php';

        if (!is_array($options)) return array();
        return $options;
    }

}

function nt_option($name, $def = null) {
    $options = get_option('newsletter_email');
    $option = $options['theme_' . $name];
    if (!isset($option)) return $def;
    else return $option;
}
