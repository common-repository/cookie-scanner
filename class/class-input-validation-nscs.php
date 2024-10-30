<?php

class input_validation_nscs
{

    private $admin_error_obj;

    public function __construct()
    {
        $this->admin_error_obj = new admin_messages_nscs;
    }

    public function validate_field_custom_save_nscs($extra_validation_value, $input)
    {
        $return = $this->sanitize_input_nscs($input);
        switch ($extra_validation_value) {
            case "timestamp_to_datetime":
                $return = $this->timestamp_to_datetime($return);
                break;
        }
        return $return;
    }

    public function sanitize_input_nscs($input)
    {
        if (is_array($input)) {
            $sanitizedArray = array();
            foreach ($input as $key => $input_field) {
                $sanitizedkey = sanitize_text_field($key);
                $sanitizedArray[$sanitizedkey] = sanitize_text_field($input_field);
            }
            return $sanitizedArray;
        }
        return sanitize_text_field($input);
    }

    public function check_if_json_is_valid_nscs($input)
    {
        $check = json_encode(json_decode($input));
        if (empty($check) || $check == "null") {
            return false;
        }
        return true;
    }

    public function sanitize_url_nscs($input)
    {
        if (is_array($input)) {
            $sanitizedArray = array();
            foreach ($input as $key => $input_field) {
                $sanitizedArray[$key] = filter_var($input_field, FILTER_SANITIZE_URL);
            }
            return $sanitizedArray;
        }
        return filter_var($input, FILTER_SANITIZE_URL);
    }

    public function php_version_good_nscs($minVersion = '5.4.0')
    {
        if (version_compare(phpversion(), $minVersion, '>=')) {
            return true;
        } else {
            return false;
        }
    }

    private function timestamp_to_datetime($timestamp)
    {
        $plugin_configs = new plugin_configs_nscs;
        return $plugin_configs->unixtimestamp_to_date_nscs($timestamp);
    }

    public function form_field_check_input_url($urls)
    {
        $urls = $this->normalize_line_endings($urls);
        $urls = explode("\n", $urls);
        $urls = array_filter($urls);

        if (!is_array($urls) || empty($urls)) {
            $default_url = get_home_url();
            return $default_url;
        }

        if (count($urls) == 1) {
            return filter_var($urls[0], FILTER_SANITIZE_URL);
        }

        $sanitizedArray = array();
        foreach ($urls as $key => $url) {
            $sanitizedArray[$key] = filter_var($url, FILTER_SANITIZE_URL);
        }
        return implode("\n", $sanitizedArray);
    }

    private function normalize_line_endings($string)
    {
        // Normalize line endings
        // Convert all line-endings to UNIX format
        $string = str_replace("\r\n", "\n", $string);
        $string = str_replace("\r", "\n", $string);
        // Don't allow out-of-control blank lines
        $string = preg_replace("/\n{2,}/", "\n\n", $string);
        return $string;
    }

}
