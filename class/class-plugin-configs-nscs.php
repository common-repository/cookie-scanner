<?php

class plugin_configs_nscs extends plugin_configs_base_nscs
{
    public function run_on_deactivate_nscs()
    {
        $cron = new wp_cron_nscs;
        $cron->unschedule_crawlrequest_cron_nscs();
    }

    public function get_running_crawlrequest_timestamp_nscs()
    {
        $settings_for_options = $this->return_plugin_configs_without_db_settings_nscs();
        return get_option($settings_for_options->plugin_prefix . "running_crawl_request", false);
    }

    public function get_cookie_scanner_license_key_nscs()
    {
        $settings_for_options = $this->return_plugin_configs_without_db_settings_nscs();
        return get_option($settings_for_options->plugin_prefix . "cookie_scanner_license_key", false);
    }

    public function save_cookie_scanner_license_key_nscs($license_key)
    {
        $settings_for_options = $this->return_plugin_configs_without_db_settings_nscs();
        return update_option($settings_for_options->plugin_prefix . "cookie_scanner_license_key", $license_key, true);
    }

}
