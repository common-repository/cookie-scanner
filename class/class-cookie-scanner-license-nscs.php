<?php

class cookie_scanner_license_nscs
{
    private $plugin_configs;

    public function __construct()
    {
        $this->plugin_configs = new plugin_configs_nscs;
    }

    public function create_license_key_nscs()
    {
        if (!function_exists('wp_get_current_user')) {
            include_once ABSPATH . "wp-includes/pluggable.php";
        }

        $body["url"] = get_home_url();
        $body["owner"] = wp_get_current_user()->user_email;
        $body["accepted_terms_version"] = "v1";
        $body["accepted_data_privacy_version"] = "v1";
        $body["license_id"] = rand(1000000000, 9999999999);

        $server_request = new server_request_nscs;
        $license_key = $server_request->request_license_key_creation_nscs($body);
        if ($license_key === false) {
            return $server_request->return_error_nscs();
        }
        $this->plugin_configs->save_cookie_scanner_license_key_nscs($license_key->license_key);
        $this->update_license_data_nscs();
        return true;
    }

    public function update_license_data_nscs()
    {
        $data_to_store = $this->return_license_data_keys_nscs();
        $license_data = $this->get_license_data();
        foreach ($license_data as $key => $license_property_value) {
            if (in_array($key, $data_to_store)) {
                $this->plugin_configs->update_option_nscs($key, $license_property_value);
            }
        }
    }

    public function return_license_data_keys_nscs()
    {
        $license_data_keys = array(
            "name",
            "allowed_crawls",
            "crawls_allowed_within_days",
            "additional_crawls_budget",
            "crawls_left",
            "crawls_done_in_timeframe",
        );
        return $license_data_keys;
    }

    private function get_license_data()
    {
        $server_request = new server_request_nscs;

        $license_key = $this->plugin_configs->get_cookie_scanner_license_key_nscs();
        $license_data = $server_request->request_get_license_data_nscs($license_key);
        return $license_data;
    }
}
