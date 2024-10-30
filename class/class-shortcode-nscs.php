<?php

class shortcode_nscs
{

    public function add_short_code_nscs()
    {
        add_action('wp_enqueue_scripts', array($this, 'add_styles_nscs'));
        add_shortcode('cookie_list_nscs', array($this, 'shortcode_cookie_list_nscs'));
    }

    public function shortcode_cookie_list_nscs($attr = "")
    {
        $cookie_string = $this->get_cookie_list_grouped_none();

        return $cookie_string;
    }

    public function add_styles_nscs()
    {
        wp_enqueue_style('cookie_list_nscs-style', PLUGIN_URL_NSCS . "/templates/css/style.css");
    }

    private function get_cookie_list_grouped_none()
    {

        $plugin_configs = new plugin_configs_nscs;
        $cookie_table_th_cookie_name = $plugin_configs->get_option_nscs("cookie_table_th_cookie_name");
        $cookie_table_th_details = $plugin_configs->get_option_nscs("cookie_table_th_details");
        $cookie_table_th_description = $plugin_configs->get_option_nscs("cookie_table_th_description");
        $cookie_table_th_domain = $plugin_configs->get_option_nscs("cookie_table_th_domain");
        $cookie_table_th_type = $plugin_configs->get_option_nscs("cookie_table_th_type");
        $cookie_table_th_duration = $plugin_configs->get_option_nscs("cookie_table_th_duration");
        $cookie_table_th_category = $plugin_configs->get_option_nscs("cookie_table_th_category");

        $custom_post = new admin_custom_post_nscs;
        $cookie_list_raw = $custom_post->get_all_cookies_nscs("publish");
        $cookie_string = "<table id='cookie_list_nscs' class='cookie_list_nscs'><thead><tr>";
        $cookie_string .= "<th>" . $cookie_table_th_cookie_name . "</th>";
        $cookie_string .= "<th>" . $cookie_table_th_details . "</th>";
        $cookie_string .= "<th>" . $cookie_table_th_description . "</th>";
        $cookie_string .= "</tr></thead><tbody>";
        foreach ($cookie_list_raw as $cookie) {
            $cookie_meta = $custom_post->get_post_meta_nscs($cookie->ID);
            $cookie_string .= "<tr>";
            $cookie_string .= "<td>" . esc_attr($cookie->post_title) . "</td>";
            $cookie_string .= "<td><strong>" . $cookie_table_th_domain . ":</strong> " . esc_attr($cookie_meta["_cookie_domain_nscs"][0]) . " <br>";
            $cookie_string .= "<strong>" . $cookie_table_th_type . ":</strong> " . esc_attr($cookie_meta["_cookie_type_nscs"][0]) . "<br>";
            if (!empty($cookie_meta["_cookie_category_nscs"][0])) {
                $cookie_string .= "<strong>" . $cookie_table_th_category . ":</strong> " . esc_attr($cookie_meta["_cookie_category_nscs"][0]) . "<br>";
            }
            $cookie_string .= "<strong>" . $cookie_table_th_duration . ":</strong> " . esc_attr($cookie_meta["_cookie_duration_nscs"][0]) . "</td>";
            $cookie_string .= "<td>" . esc_attr($cookie->post_content) . "</td>";
            $cookie_string .= "</tr>";
        }
        $cookie_string .= "</tbody></table>";

        return $cookie_string;
    }
}
