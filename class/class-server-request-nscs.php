<?php

class server_request_nscs
{
    private $error;

    public function return_error_nscs()
    {
        return $this->error;
    }

    public function request_license_key_creation_nscs($license_config)
    {
        $api_url = "https://api.cookie-scanner.com/v1/createlicensekey/";
        $body = json_encode($license_config);
        $response = $this->post($api_url, $body, "");
        return $response;
    }

    public function request_crawling_request_nscs($crawl_configs, $license_key)
    {
        $api_url = "https://api.cookie-scanner.com/v1/crawlingrequest/";
        $body = json_encode($crawl_configs);
        $response = $this->post($api_url, $body, $license_key);
        if ($response === false) {
            return false;
        }
        return true;
    }

    public function request_crawling_results_nscs($license_key, $urls_to_crawl)
    {
        $api_url = "https://api.cookie-scanner.com/v1/crawlingresult/";
        $body = json_encode($urls_to_crawl);
        $response = $this->post($api_url, $body, $license_key);
        return $response;
    }

    public function request_get_license_data_nscs($license_key)
    {
        $api_url = "https://api.cookie-scanner.com/v1/getlicensedata/";
        $response = $this->get($api_url, $license_key);
        return $response;
    }

    private function get_user_agent()
    {
        global $wp_version;
        $phpversion = phpversion();
        return "NSCS:" . PLUGIN_VERSION_NSCS . " PHP:" . $phpversion . " WP:" . $wp_version;
    }

    private function post($api_url, $body, $license_key)
    {
        $response = wp_remote_post($api_url, array(
            'headers' => array(
                'User-Agent' => $this->get_user_agent(),
                'LicenseKey' => $license_key,
                'Content-Type' => "application/json",
            ),
            'body' => $body,
            'httpversion' => '1.1',
        ));
        return $this->getBody($response);
    }

    private function get($api_url, $license_key)
    {
        $response = wp_remote_get($api_url, array(
            'headers' => array(
                'User-Agent' => $this->get_user_agent(),
                'LicenseKey' => $license_key,
            ),
            'httpversion' => '1.1',
        ));
        return $this->getBody($response);
    }

    private function getBody($response)
    {

        $response_code = wp_remote_retrieve_response_code($response);
        $raw_body = wp_remote_retrieve_body($response);
        if ($response === false || !in_array($response_code, array(200, 204))) {
            $response_body = $raw_body;
            $this->error = $response_code . ": " . $response_body;
            return false;
        }

        $response_body = json_decode($raw_body);
        if ($response_body === null && !empty($raw_body)) {
            $this->error = $response_code . ": COULDNOTPARSESERVERRESPONSE";
            return false;
        }

        return $response_body;
    }
}
