<?php

class uninstaller_nscs
{
    private $plugin_configs;

    public function __construct()
    {
        $this->plugin_configs = new plugin_configs_nscs;
    }

    public function delete_plugin_data_nscs()
    {
        $settings = $this->plugin_configs->return_plugin_configs_without_db_settings_nscs();
        $prefix = $settings->plugin_prefix;
        foreach ($settings->setting_page_fields->tabs as $tab) {
            foreach ($tab->tabfields as $fields) {
                delete_option($prefix . $fields->field_slug);
            }
        }
        $cs_license = new cookie_scanner_license_nscs;
        $license_data_fields = $cs_license->return_license_data_keys_nscs();
        foreach ($license_data_fields as $license_data_field) {
            delete_option($prefix . $license_data_field);
        }
        delete_option($prefix . "cron_result_crawling_result");
        delete_option($prefix . "cron_crawling_request_result");

        $this->delete_posts();
    }

    private function delete_posts()
    {
        global $wpdb;
        // delete custom post type posts
        $custom_posts = get_posts([
            'post_type' => POST_TYPE_NSCS,
            'numberposts' => -1,
            'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
        ]);
        foreach ($custom_posts as $post) {
            wp_delete_post($post->ID, true);
        }
    }
}
