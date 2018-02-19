<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://github.com/o10n-x/
 * @since      2.5.0
 *
 * @package    o10n
 */

if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// get O10N config
$options = get_option('o10n', false);

// remove http/2 config
if ($options) {
    $param = 'http2.';

    foreach ($options as $key => $value) {
        if (strpos($key, $param) === 0) {
            unset($options[$key]);
        }
    }

    // remove empty options
    if (empty($options)) {
        delete_option('o10n');
    }
}
