<?php

class plugin_configs_base_nscs
{
    private $config_file_path;
    private $configs_as_object;
    private $configs_as_object_without_db;
    private $active_tab;

    public function get_option_nscs($option_slug, $default = false)
    {
        $option_value = $default;
        $found_in_config = false;
        $settings_for_options = $this->return_plugin_configs_without_db_settings_nscs();
        foreach ($settings_for_options->setting_page_fields->tabs as $tab) {
            foreach ($tab->tabfields as $field) {
                if ($field->field_slug == $option_slug) {
                    $option_value = get_option($settings_for_options->plugin_prefix . $option_slug, $field->pre_selected_value);
                    $found_in_config = true;
                    break;
                }
            }
        }

        if ($found_in_config === false) {
            $option_value = get_option($settings_for_options->plugin_prefix . $option_slug, $default);
        }
        return $option_value;
    }

    public function update_option_nscs($option_slug, $value, $autoload = true)
    {
        $settings_for_options = $this->return_plugin_configs_without_db_settings_nscs();
        return update_option($settings_for_options->plugin_prefix . $option_slug, $value, $autoload);
    }

    public function delete_option_nscs($option_name)
    {
        $settings_for_options = $this->return_plugin_configs_without_db_settings_nscs();
        $option_name_with_prefix = $settings_for_options->plugin_prefix . $option_name;
        delete_option($option_name_with_prefix);
    }

    public function plugin_prefix_nscs()
    {
        $this->return_plugin_configs_without_db_settings_nscs();
        return $this->configs_as_object_without_db->plugin_prefix;
    }

    public function plugin_slug_nscs()
    {
        $this->return_plugin_configs_without_db_settings_nscs();
        return $this->configs_as_object_without_db->plugin_slug;
    }

    public function return_plugin_configs_nscs()
    {
        if (empty($this->configs_as_object)) {
            $this->configs_as_object = $this->return_plugin_configs_without_db_settings_nscs();
            $this->add_current_setting_values();
            $this->add_html_description_templates();
        }
        return $this->configs_as_object;
    }

    public function unixtimestamp_to_date_nscs($unix_timestamp, $dateformat = null, $timeformat = null)
    {
        if (empty($dateformat)) {
            $dateformat = get_option('date_format');
        }

        if (empty($timeformat)) {
            $timeformat = get_option('time_format');
        }
        return date_i18n($dateformat . " " . $timeformat, $unix_timestamp + get_option('gmt_offset') * 3600);
    }

    //returns settings without db only as fallback. needed e.g. for var replacement in configfile.
    public function return_plugin_configs_without_db_settings_nscs()
    {
        if (!empty($this->configs_as_object)) {
            return $this->configs_as_object;
        }

        if (empty($this->configs_as_object_without_db)) {
            $this->configs_as_object_without_db = $this->read_config_file();
            if (empty($this->configs_as_object_without_db)) {
                throw new Exception($this->config_file_path . " was not readable. Make sure it contains valid json.");
            }
        }
        return $this->configs_as_object_without_db;
    }

    public function return_settings_field_nscs($searched_field_slug)
    {
        $this->return_plugin_configs_nscs();
        foreach ($this->configs_as_object->setting_page_fields->tabs as $tab) {
            $number_of_fields = count($tab->tabfields);
            for ($i = 0; $i < $number_of_fields; $i++) {
                if ($tab->tabfields[$i]->field_slug == $searched_field_slug) {
                    return $tab->tabfields[$i];
                }
            }
        }
    }

    public function return_plugin_upload_base_dir_nscs()
    {
        $uploadDirArray = wp_upload_dir();

        $defaultUploadDirPath = realpath($uploadDirArray['basedir']);

        $resultToReturn = $defaultUploadDirPath . "/" . $this->plugin_slug_nscs() . "/";
        if (!is_dir($resultToReturn)) {
            mkdir($resultToReturn);
        }
        return $resultToReturn;
    }

    public function return_settings_field_default_value_nscs($searched_field_slug)
    {
        $settings_field = $this->return_settings_field($searched_field_slug);
        return $settings_field->pre_selected_value;
    }

    public function replace_variables_in_config_nscs($varname, $replace_value)
    {
        $configs = $this->return_plugin_configs_nscs();
        $configs_string = json_encode($configs);
        $configs_string = str_replace("{{" . $varname . "}}", $replace_value, $configs_string);
        $this->configs_as_object = json_decode($configs_string);
    }

    private function read_config_file()
    {
        $this->config_file_path = PLUGIN_CONFIGS_PATH_NSCS;
        $settings = file_get_contents($this->config_file_path);
        $settings = json_decode($settings);
        if (empty($settings)) {
            throw new Exception($this->config_file_path . " was not readable. Make sure it contains valid json.");
        }
        return $settings;
    }

    private function add_html_description_templates()
    {
        $number_of_tabs = count($this->configs_as_object->setting_page_fields->tabs);
        if (strpos($this->configs_as_object->settings_page_configs->description, ".html") !== false &&
            file_exists(PLUGIN_PATH_NSCS . "/admin/tpl/" . $this->configs_as_object->settings_page_configs->description)) {
            $desc = file_get_contents(PLUGIN_PATH_NSCS . "/admin/tpl/" . $this->configs_as_object->settings_page_configs->description);
            $this->configs_as_object->settings_page_configs->description = $desc;
        }
        for ($t = 0; $t < $number_of_tabs; $t++) {
            $this->configs_as_object->setting_page_fields->tabs[$t]->tab_description = $this->get_tab_description_template($t);
        }
    }

    private function get_tab_description_template($t)
    {
        if (strpos($this->configs_as_object->setting_page_fields->tabs[$t]->tab_description, ".html") === false ||
            !file_exists(PLUGIN_PATH_NSCS . "/admin/tpl/" . $this->configs_as_object->setting_page_fields->tabs[$t]->tab_description)) {
            return $this->configs_as_object->setting_page_fields->tabs[$t]->tab_description;
        }
        $tab_description = file_get_contents(PLUGIN_PATH_NSCS . "/admin/tpl/" . $this->configs_as_object->setting_page_fields->tabs[$t]->tab_description);
        return $tab_description;
    }

    private function get_active_tab()
    {
        $this->active_tab = "";
        if (isset($_GET["tab"])) {
            $this->active_tab = sanitize_text_field($_GET["tab"]);
        } else {
            $this->active_tab = $this->configs_as_object->setting_page_fields->tabs[0]->tab_slug;
        }
    }

    // this fuctions gets the value saved in wordpress db using get_option
    // and adds it to the config object in the pre_selected_value field.
    // if no value is set it sets the default value from config file.
    private function add_current_setting_values()
    {
        $this->get_active_tab();
        $this->configs_as_object->setting_page_fields->active_tab_slug = $this->active_tab;
        $numper_of_tabs = count($this->configs_as_object->setting_page_fields->tabs);
        for ($t = 0; $t < $numper_of_tabs; $t++) {
            $number_of_fields_in_this_tab = count($this->configs_as_object->setting_page_fields->tabs[$t]->tabfields);
            if ($this->active_tab == $this->configs_as_object->setting_page_fields->tabs[$t]->tab_slug) {
                $this->configs_as_object->setting_page_fields->tabs[$t]->active = true;
                $this->configs_as_object->setting_page_fields->active_tab_index = $t;
            }
            for ($f = 0; $f < $number_of_fields_in_this_tab; $f++) {
                $option_slug = $this->configs_as_object->plugin_prefix . $this->configs_as_object->setting_page_fields->tabs[$t]->tabfields[$f]->field_slug;
                $default_value = $this->configs_as_object->setting_page_fields->tabs[$t]->tabfields[$f]->pre_selected_value;
                $wp_option_value = get_option($option_slug, $default_value);
                $this->configs_as_object->setting_page_fields->tabs[$t]->tabfields[$f]->pre_selected_value = $wp_option_value;
            }
        }
    }
}
