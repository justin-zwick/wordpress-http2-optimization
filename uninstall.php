<?php
namespace O10n;

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://github.com/o10n-x/
 * @package    optimization
 */

if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$plugin_path = dirname(__FILE__);

// load uninstall controller
if (!class_exists('\O10n\Uninstall', false)) {
    require $plugin_path . '/core/controllers/uninstall.class.php';
}

// start uninstaller
$uninstaller = new Uninstall('http2');

// delete settings
$uninstaller->delete_settings('http2');

// delete cache tables
$uninstaller->delete_tables();
