<?php

class admin_settings_nscs
{
    private $settings;
    private $prefix;
    private $plugin_dir;

    public function __construct()
    {
        $this->plugin_dir = PLUGIN_PATH_NSCS;
        $this->plugin_configs = new plugin_configs_nscs();
        $this->settings = $this->plugin_configs->return_plugin_configs_without_db_settings_nscs();
        $this->prefix = $this->settings->plugin_prefix;
    }

    public function execute_wordpress_actions_nscs()
    {
        add_action("admin_init", array($this, "register_settings_nscs"));
        add_action("admin_menu", array($this, "add_admin_menu_nscs"));
        add_action("admin_enqueue_scripts", array($this, "enqueue_admin_script_on_admin_page_nscs"));
        add_filter("plugin_action_links_" . PLUGIN_BASENAME_NSCS, array($this, 'add_settings_link_nscs'));
        $this->show_currently_running_request_nscs();
    }

    public function enqueue_admin_script_on_admin_page_nscs($hook)
    {
        if ($hook == 'settings_page_settings_nscs') {
            wp_enqueue_script('settings_page_settings_nscs_scripts', PLUGIN_URL_NSCS . '/admin/js/admin.js');
        }
    }

    public function show_currently_running_request_nscs()
    {
        if ($this->plugin_configs->get_option_nscs("running_crawl_request") != false &&
            isset($_GET["post_type"]) &&
            $_GET["post_type"] == "cookielist_nscs") {
            $display_message = new admin_messages_nscs;
            $display_message->set_admin_info_nscs("A cookie crawl request is currently running.");
            $display_message->display_messages_nscs();
        }
    }

    public function add_admin_menu_nscs()
    {
        add_submenu_page(
            "edit.php?post_type=" . POST_TYPE_NSCS,
            $this->settings->settings_page_configs->page_title,
            $this->settings->settings_page_configs->menu_title,
            $this->settings->settings_page_configs->capability,
            $this->settings->plugin_slug,
            array($this, "create_admin_page_nscs")
        );

    }

    public function create_admin_page_nscs()
    {
        $has_license = $this->plugin_configs->get_cookie_scanner_license_key_nscs();
        $hint_no_license = "";
        if ($has_license === false) {
            $hint_no_license = "<div style='text-align:center; line-height:2em;'><p>To get the cookies crawled, you must get a license first. No worries: it is free.</p><p>The Reason behind it: The crawler runs on an external server provided by the author. And every single crawl cost server resources which need to be paid.</p></div>";}
        $this->plugin_configs->replace_variables_in_config_nscs("hint_no_license", $hint_no_license);

        $this->replace_variables_in_plugin_configs_for_admin_page();

        $settings_object = $this->plugin_configs->return_plugin_configs_nscs();
        $form_fields = new admin_html_formfields_nscs;
        if ($has_license === false) {
            require $this->plugin_dir . "/admin/tpl/admin_head_no_license.php";
        } else {
            require $this->plugin_dir . "/admin/tpl/admin_head.php";
        }
        require $this->plugin_dir . "/admin/tpl/admin_tabs.php";
        require $this->plugin_dir . "/admin/tpl/admin.php";

    }

    private function replace_variables_in_plugin_configs_for_admin_page()
    {
        $last_crawled = $this->plugin_configs->get_option_nscs("last_crawled");
        if (empty($last_crawled)) {
            $last_crawled = "never";
        } else {
            $last_crawled = $this->plugin_configs->unixtimestamp_to_date_nscs($last_crawled);
        }
        $this->plugin_configs->replace_variables_in_config_nscs("last_crawled", $last_crawled);
        $this->plugin_configs->replace_variables_in_config_nscs("home_url", get_home_url());

        $current_user = wp_get_current_user();
        $this->plugin_configs->replace_variables_in_config_nscs("user_email", $current_user->user_email);

        $cs_license = new cookie_scanner_license_nscs;
        $license_data_fields = $cs_license->return_license_data_keys_nscs();
        foreach ($license_data_fields as $license_data_field) {
            $license_data_field_value = $this->plugin_configs->get_option_nscs($license_data_field, "not available");
            $this->plugin_configs->replace_variables_in_config_nscs($license_data_field, $license_data_field_value);
        }
        $cookie_scanner = new cookie_scanner_nscs;
        $page_consumption = $cookie_scanner->calculate_crawl_credit_consumption();
        $this->plugin_configs->replace_variables_in_config_nscs("page_consumption", $page_consumption);

        $cron_crawling_request_result = $this->plugin_configs->get_option_nscs("cron_crawling_request_result", null);
        $cron_result_crawling_result = $this->plugin_configs->get_option_nscs("cron_result_crawling_result", null);
        $cron_request_error = "";
        $cron_crawl_result_error = "";
        if (!empty($cron_crawling_request_result)) {
            $cron_request_error = "<div class='notice notice-error'><p>Last cron error when requesting a crawl: " . $cron_crawling_request_result . "</p></div>";
        }
        if (!empty($cron_result_crawling_result)) {
            $cron_crawl_result_error = "<div class='notice notice-error'><p>Error in downloading a crawl result: " . $cron_result_crawling_result . "</p></div>";
        }
        $this->plugin_configs->replace_variables_in_config_nscs("cron_crawling_request_result_error", $cron_request_error);
        $this->plugin_configs->replace_variables_in_config_nscs("cron_result_crawling_result_error", $cron_crawl_result_error);

        $cron = new wp_cron_nscs;
        $next_run = $cron->get_next_run_crawl_request_nscs();
        $next_run = (empty($next_run) ? "never." : $this->plugin_configs->unixtimestamp_to_date_nscs($next_run));

        $this->plugin_configs->replace_variables_in_config_nscs("next_scheduled_crawl_request", $next_run);

    }

    public function register_settings_nscs()
    {
        //settings werden mit db values angereichert
        $this->settings = $this->plugin_configs->return_plugin_configs_nscs();
        $validate = new input_validation_nscs();

        foreach ($this->settings->setting_page_fields->tabs as $tab) {
            foreach ($tab->tabfields as $field) {
                $functionForValidation = "sanitize_input_nscs";
                if ($field->extra_validation_name !== false) {
                    $functionForValidation = $field->extra_validation_name;
                }
                if ($field->save_in_db === true) {
                    register_setting($this->settings->plugin_slug . $tab->tab_slug, $this->prefix . $field->field_slug, array($validate, $functionForValidation));
                }
            }
        }
    }

    public function add_settings_link_nscs($links)
    {
        $settings_link = '<a href="edit.php?post_type=' . POST_TYPE_NSCS . '&page=' . $this->settings->plugin_slug . '">' . __('Settings') . '</a>';
        array_push($links, $settings_link);
        return $links;
    }

}
