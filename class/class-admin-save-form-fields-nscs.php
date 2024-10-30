<?php

class admin_save_form_fields_nscs
{
    private $plugin_settings;
    private $plugin_configs;
    private $admin_messages_obj;

    public function __construct()
    {
        $this->plugin_configs = new plugin_configs_nscs();
        $this->admin_messages_obj = new admin_messages_nscs;
        $this->plugin_settings = $this->plugin_configs->return_plugin_configs_without_db_settings_nscs();
    }

    public function save_submitted_form_fields_nscs()
    {

        if (isset($_POST['submit']) === false) {
            return false;
        }

        if (current_user_can($this->plugin_settings->settings_page_configs->capability) === false) {
            return false;
        }

        $tabs = $this->plugin_settings->setting_page_fields->tabs;
        $plugin_prefix = $this->plugin_settings->plugin_prefix;

        foreach ($tabs as $tab_index => $tab) {
            if ($tab->form_action == "options.php" || $tab->form_action == "display_only") {
                //will be handled by wp options api
                continue;
            }
            foreach ($tab->tabfields as $tabfield_index => $tabfield) {
                $tabfield_slug = $plugin_prefix . $tabfield->field_slug;

                if ($tabfield->save_in_db === true && (isset($_POST[$tabfield_slug]) || isset($_POST[$tabfield_slug . "_hidden"]))) {
                    $newvalue = $this->return_post_value_from_tabfield_slug($tabfield_slug, $tabfield->extra_validation_name);
                    $this->plugin_configs->update_option_nscs($tabfield->field_slug, $newvalue);
                }
            }
        }
        $this->save_cookie_scanner_terms();
        $this->do_crawl_nscs();
        $this->admin_messages_obj->display_messages_nscs();
    }

    private function return_post_value_from_tabfield_slug($tabfield_slug, $extra_validation_name)
    {
        $validate = new input_validation_nscs;
        if (isset($_POST[$tabfield_slug])) {
            return $validate->validate_field_custom_save_nscs($extra_validation_name, $_POST[$tabfield_slug]);
        }
        if (isset($_POST[$tabfield_slug . "_hidden"])) {
            return $validate->validate_field_custom_save_nscs($extra_validation_name, $_POST[$tabfield_slug . "_hidden"]);
        }
    }

    private function save_cookie_scanner_terms()
    {
        $prefix = $this->plugin_configs->plugin_prefix_nscs();
        if (isset($_POST[$prefix . "acceptTermsCookieScanner"]) && $_POST[$prefix . "acceptTermsCookieScanner"] == "1") {
            $cs_license = new cookie_scanner_license_nscs;
            $license_key = $cs_license->create_license_key_nscs();
            if ($license_key !== true) {
                $this->admin_messages_obj->set_admin_error_nscs($license_key);
            }
        }
    }

    private function do_crawl_nscs()
    {
        $response = true;
        if (isset($_POST["crawl_now"])) {
            $cookie_scanner_nscs = new cookie_scanner_nscs;
            $response = $cookie_scanner_nscs->send_crawling_request();
            if ($response !== true) {
                $admin_error = new admin_messages_nscs;
                $admin_error->set_admin_error_nscs($response);
                $admin_error->display_messages_nscs();
            }
        }
        return $response;
    }
}
