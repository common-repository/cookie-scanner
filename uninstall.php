<?php

if (defined('WP_UNINSTALL_PLUGIN') === false) {
    echo "no way";
    exit;
}

define('PLUGIN_PATH_NSCS', plugin_dir_path(__FILE__));
define('PLUGIN_CONFIGS_PATH_NSCS', PLUGIN_PATH_NSCS . "/plugin-config.json");
define('PLUGIN_URL_NSCS', plugin_dir_url(__FILE__));
define("POST_TYPE_NSCS", "cookielist_nscs");

require dirname(__FILE__) . "/class/class-plugin-configs-base-nscs.php";
require dirname(__FILE__) . "/class/class-plugin-configs-nscs.php";
require dirname(__FILE__) . "/class/class-cookie-scanner-license-nscs.php";
require dirname(__FILE__) . "/class/class-uninstall-nscs.php";

$uninstaller = new uninstaller_nscs();
$uninstaller->delete_plugin_data_nscs();
