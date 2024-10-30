<?php

class cookie_scanner_nscs
{
    public function get_crawling_results_cron_nscs()
    {
        //file_put_contents(PLUGIN_PATH_NSCS . "/log.txt", date("Y-m-d H:i:s", time()) . ": cron save results\n", FILE_APPEND);
        $response = $this->save_crawling_results_nscs();
        $plugin_configs = new plugin_configs_nscs;
        if ($response !== true && $response !== null) {
            $plugin_configs->update_option_nscs("cron_result_crawling_result", $response);
            return;
        }
        $plugin_configs->delete_option_nscs("cron_result_crawling_result");
    }

    public function set_crawling_request_cron_nscs()
    {
        //file_put_contents(PLUGIN_PATH_NSCS . "/log.txt", date("Y-m-d H:i:s", time()) . ": set_crawling_request_cron_nscs\n", FILE_APPEND);
        $response = $this->send_crawling_request();
        $plugin_configs = new plugin_configs_nscs;
        if ($response !== true) {
            $plugin_configs->update_option_nscs("cron_crawling_request_result", $response);
            return;
        }
        $plugin_configs->delete_option_nscs("cron_crawling_request_result");
    }

    public function send_crawling_request()
    {
        $crawl_request_allowed = $this->new_crawl_request_allowed();
        if ($crawl_request_allowed !== true) {
            return $crawl_request_allowed;
        }

        $plugin_configs = new plugin_configs_nscs;
        $crawl_request_timestamp = time();
        $body["urls"] = $this->get_pages_to_crawl();
        $body["consent_cookie"]["name"] = $plugin_configs->get_option_nscs("consent_cookie_name");
        $body["consent_cookie"]["value_allow"] = $plugin_configs->get_option_nscs("cookie_value_allow");
        $body["consent_cookie"]["value_deny"] = $plugin_configs->get_option_nscs("cookie_value_deny");
        $body["consent_cookie"]["value_dismiss"] = "";
        $body["crawl_request_timestamp"] = $crawl_request_timestamp;
        $license_key = $plugin_configs->get_cookie_scanner_license_key_nscs();
        $server_request = new server_request_nscs;
        $response = $server_request->request_crawling_request_nscs($body, $license_key);
        if ($response === false) {
            return $server_request->return_error_nscs();
        }
        $plugin_configs->update_option_nscs("running_crawl_request", $crawl_request_timestamp);
        return true;
    }

    public function calculate_crawl_credit_consumption()
    {
        $pages_to_crawl = $this->get_pages_to_crawl();
        return count($pages_to_crawl);
    }

    private function get_pages_to_crawl()
    {
        $plugin_configs = new plugin_configs_nscs;
        $input_validation = new input_validation_nscs;
        $plugin_configs->replace_variables_in_config_nscs("home_url", get_home_url());
        $pages_to_crawl = $plugin_configs->get_option_nscs("pages_to_crawl");
        $pages_to_crawl = explode("\n", $pages_to_crawl);
        $pages_to_crawl = array_filter($pages_to_crawl);
        $pages_to_crawl = $input_validation->sanitize_url_nscs($pages_to_crawl);
        if (!is_array($pages_to_crawl)) {
            $admin_error = new admin_messages_nscs;
            $admin_error->set_admin_error_nscs("URLS_TO_CRAWL_MALFORMED_OR_MISSING");
            $admin_error->display_messages_nscs();
            return array();
        }
        return $pages_to_crawl;
    }

    private function new_crawl_request_allowed()
    {
        $plugin_configs = new plugin_configs_nscs;
        $crawl_request_date = $plugin_configs->get_running_crawlrequest_timestamp_nscs();
        if ($crawl_request_date !== false) {
            return "A crawl is already running. It can take a couple of minutes until it is finished.";
        }
        return true;
    }

    public function remove_too_long_running_request_nscs()
    {
        $plugin_configs = new plugin_configs_nscs;
        $current_crawlrequest_timestamp = $plugin_configs->get_running_crawlrequest_timestamp_nscs();

        if ($current_crawlrequest_timestamp === false) {
            return true;
        }

        $running_duration = time() - $current_crawlrequest_timestamp;
        $one_minute = 60;
        $max_running_time_sec = $one_minute * 10;
        if ($running_duration > $max_running_time_sec) {
            $plugin_configs->delete_option_nscs("running_crawl_request");
        }

    }

    private function get_latest_entry($cookie_field_array)
    {
        // the first entry is always the newest, unless the order is changed on api.
        //TODO: make more secure.
        if (!isset($cookie_field_array[0])) {
            return "not set.";
        }
        return $cookie_field_array[0];

    }

    public function save_crawling_results_nscs()
    {
        $crawling_results = $this->return_crawling_results();
        $plugin_configs = new plugin_configs_nscs;

        if (empty($crawling_results->cookies)) {
            return null;
        }
        $new_cookies = array();

        $custom_post_nscs = new admin_custom_post_nscs;
        foreach ($crawling_results->cookies as $cookie) {
            $crawled_at = strtotime($this->get_latest_entry($cookie->last_crawled));
            $remote_crawl_request_timestamp = $this->get_latest_entry($cookie->crawl_request_timestamp);

            $local_crawl_request_timestamp = $plugin_configs->get_running_crawlrequest_timestamp_nscs();
            //check if the newest crawl result is on server.
            if ($local_crawl_request_timestamp !== false && $remote_crawl_request_timestamp < $local_crawl_request_timestamp) {
                //file_put_contents(PLUGIN_PATH_NSCS . "/log.txt", date("Y-m-d H:i:s", time()) . ": no new " . $cookie->name . " - local: " . $local_crawl_request_timestamp . " remote: " . $remote_crawl_request_timestamp . "\n", FILE_APPEND);
                // if for first cookie local crawl request not fit with remote: abort.
                return null;
            }

            $cookie_db_id = $this->get_cookie_post_id($crawled_at, $cookie);
            if ($cookie_db_id !== false) {
                update_post_meta($cookie_db_id, '_crawled_at_nscs', $crawled_at);
                update_post_meta($cookie_db_id, '_crawl_request_timestamp_nscs', $remote_crawl_request_timestamp);
                update_post_meta($cookie_db_id, '_urls_with_this_cookie_nscs', json_encode($cookie->crawled_urls));
                //file_put_contents(PLUGIN_PATH_NSCS . "/log.txt", date("Y-m-d H:i:s", time()) . ": update " . $cookie->name . "\n", FILE_APPEND);
                continue;
            }

            $new_cookies[] = $cookie;
            $post_meta_value = array(
                "_cookie_category_nscs" => "",
                "_cookie_duration_nscs" => $this->cookie_duration($cookie, $crawled_at),
                "_cookie_type_nscs" => $this->cookie_type($cookie, $crawled_at),
                "_crawled_at_nscs" => $crawled_at,
                "_technology_nscs" => $this->get_latest_entry($cookie->technology),
                "_consent_status_nscs" => $this->get_latest_entry($cookie->consent_setting),
                "_cookie_domain_nscs" => $this->get_latest_entry($cookie->domains),
                "_cookie_sameSite_nscs" => $this->get_latest_entry($cookie->sameSite),
                "_cookie_source_nscs" => "crawler",
                "_crawl_request_timestamp_nscs" => $remote_crawl_request_timestamp,
                "_urls_with_this_cookie_nscs" => json_encode($cookie->crawled_urls),
            );
            $custom_post_nscs->insert_new_custom_post_nscs($cookie->name, $post_meta_value);
            //file_put_contents(PLUGIN_PATH_NSCS . "/log.txt", date("Y-m-d H:i:s", time()) . " insert " . $cookie->name . "\n", FILE_APPEND);

        }

        $plugin_configs->update_option_nscs("last_crawled", $crawled_at);
        $plugin_configs->delete_option_nscs("running_crawl_request");
        $mail_sender = new mail_sender_nscs;
        $mail_sender->send_new_cookie_mail_nscs($new_cookies);
        $cs_license = new cookie_scanner_license_nscs;
        $cs_license->update_license_data_nscs();
        return true;
    }

    private function cookie_type($cookie, $crawled_at)
    {
        $home_url = get_home_url();
        $home_domain = parse_url($home_url, PHP_URL_HOST);

        if (strripos("." . $home_domain, $this->get_latest_entry($cookie->domains)) === false) {
            return "3rd party";
        }

        if ($this->get_latest_entry($cookie->session) == true || $this->cookie_duration($cookie, $crawled_at) <= 0) {
            return "session";
        }

        return "persistent";

    }

    private function cookie_duration($cookie, $crawled_at)
    {
        $one_day_sec = 86400;
        $one_month_sec = $one_day_sec * 30;
        $one_year_sec = $one_day_sec * 365;
        $two_year_sec = $one_year_sec * 2;

        $duration = $this->get_latest_entry($cookie->expires) - $crawled_at;
        switch (true) {
            case $duration <= 0:
                return 0;
                break;
            case $duration <= $one_month_sec:
                $days = round($duration / $one_day_sec, 1);
                return $days . " days";
                break;
            case $duration <= $one_year_sec:
                $months = round($duration / $one_month_sec, 1);
                return $months . " months";
                break;
            default:
                $years = round($duration / $one_year_sec, 1);
                return $years . " years";
        }
    }

    private function return_crawling_results()
    {
        $plugin_configs = new plugin_configs_nscs;
        $license_key = $plugin_configs->get_cookie_scanner_license_key_nscs();
        $urls_to_check = $this->get_pages_to_crawl();

        $server_request = new server_request_nscs;
        $crawling_result = $server_request->request_crawling_results_nscs($license_key, $urls_to_check);
        if ($crawling_result === false) {
            return $server_request->return_error_nscs();
        }
        return $crawling_result;
    }

    private function get_cookie_post_id($crawled_at, $cookie)
    {
        $custom_post = new admin_custom_post_nscs;

        $cookies_already_saved = $custom_post->get_all_cookies_nscs("draft, publish, pending");

        foreach ($cookies_already_saved as $cookie_in_db) {
            $post_metadata = $custom_post->get_post_meta_nscs($cookie_in_db->ID);
            if ($cookie->name == $cookie_in_db->post_title &&
                $this->get_latest_entry($cookie->domains) == $post_metadata["_cookie_domain_nscs"][0]
            ) {
                return $cookie_in_db->ID;
            }
        }
        return false;
    }
}
