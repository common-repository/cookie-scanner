<?php
/*
Plugin Name: Cookie Scanner
Description: Get a cookie list automatically and display it anywhere at your page.
Author: Cookie Scanner - Nikel Schubert
Version: 1.1
Author URI: https://cookie-scanner.com
Text Domain: cookie-scanner
License: GPL3

Cookie Scanner is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

Cookie Scanner is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Cookie Scanner. If not, see {License URI}.
 */
if (!defined('ABSPATH')) {
    exit;
}

define('PLUGIN_BASENAME_NSCS', plugin_basename(__FILE__));
define('PLUGIN_PATH_NSCS', plugin_dir_path(__FILE__));
define('PLUGIN_CONFIGS_PATH_NSCS', PLUGIN_PATH_NSCS . "/plugin-config.json");
define('PLUGIN_URL_NSCS', plugin_dir_url(__FILE__));
define("PLUGIN_VERSION_NSCS", "1.1");
define("POST_TYPE_NSCS", "cookielist_nscs");

require dirname(__FILE__) . "/class/class-admin-messages-nscs.php";
require dirname(__FILE__) . "/class/class-plugin-configs-base-nscs.php";
require dirname(__FILE__) . "/class/class-plugin-configs-nscs.php";
require dirname(__FILE__) . "/class/class-mail-sender-nscs.php";
require dirname(__FILE__) . "/class/class-input-validation-nscs.php";
require dirname(__FILE__) . "/class/class-wp-cron-nscs.php";
require dirname(__FILE__) . "/class/class-server-request-nscs.php";
require dirname(__FILE__) . "/class/class-cookie-scanner-license-nscs.php";
require dirname(__FILE__) . "/class/class-admin-custom-post-nscs.php";
require dirname(__FILE__) . "/class/class-cookie-scanner-nscs.php";
require dirname(__FILE__) . "/class/class-admin-html-formfields-nscs.php";
require dirname(__FILE__) . "/class/class-admin-save-form-fields-nscs.php";
require dirname(__FILE__) . "/class/class-admin-settings-nscs.php";
require dirname(__FILE__) . "/class/class-shortcode-nscs.php";

if (is_admin()) {
    //save formfields

    $save_formfields_nscs = new admin_save_form_fields_nscs();
    add_action('plugins_loaded', array($save_formfields_nscs, 'save_submitted_form_fields_nscs'));

    //creates admin page
    $backendpage_nscs = new admin_settings_nscs;
    $backendpage_nscs->execute_wordpress_actions_nscs();
}

$cron_nscs = new wp_cron_nscs();
$cron_nscs->add_cron_schedules_nscs();
$cron_nscs->set_crons_nscs();

$cookie_scanner_nscs = new cookie_scanner_nscs();
$cookie_scanner_nscs->remove_too_long_running_request_nscs();

//creates custom post type
$admin_custom_post_nscs = new admin_custom_post_nscs;
$admin_custom_post_nscs->execute_wordpress_actions_nscs();

//for shortcode
$shortcode_nscs = new shortcode_nscs;
$shortcode_nscs->add_short_code_nscs();
