<?php

class wp_cron_nscs
{

    private $download_crawl_results_cron_name;

    public function __construct()
    {
        $this->download_crawl_results_cron_name = "download_crawl_results_cron_nscs";
        $this->auto_crawl_cron_name = "auto_crawl_request_cron_nscs";
    }

    public function add_cron_schedules_nscs()
    {
        add_filter('cron_schedules', array($this, 'add_cron_interval_nscs'));
    }

    public function set_crons_nscs()
    {
        $this->set_download_crawl_results_cron();
        $this->set_auto_crawling_cron();
    }

    public function get_next_run_crawl_request_nscs()
    {
        $current_schedule = wp_next_scheduled($this->auto_crawl_cron_name);
        return $current_schedule;
    }

    private function set_download_crawl_results_cron()
    {
        $plugin_configs = new plugin_configs_nscs;

        if ($this->shall_i_unschedule_download_crawl_results_cron()) {
            $this->remove_cron($this->download_crawl_results_cron_name);
            return true;
        }

        if ($plugin_configs->get_running_crawlrequest_timestamp_nscs() !== false) {
            $cookie_scanner = new cookie_scanner_nscs;
            $this->set_cron($this->download_crawl_results_cron_name, array($cookie_scanner, "get_crawling_results_cron_nscs"), "every_minute");
            return true;
        }
    }

    private function set_auto_crawling_cron()
    {
        $plugin_configs = new plugin_configs_nscs;
        $crawling_intervall = $plugin_configs->get_option_nscs("auto_crawling_intervall");
        if ($crawling_intervall === "disable") {
            $this->remove_cron($this->auto_crawl_cron_name);
            return false;
        }

        $availableSchedules = wp_get_schedules();
        if (!isset($availableSchedules[$crawling_intervall])) {
            echo $crawling_intervall;
            throw new Exception("Custom: needed cron schedule not found.");
        }

        $current_schedule = wp_get_schedule($this->auto_crawl_cron_name);
        if ($current_schedule != $crawling_intervall) {
            $this->remove_cron($this->auto_crawl_cron_name);
        }
        $cookie_scanner = new cookie_scanner_nscs;
        $this->set_cron($this->auto_crawl_cron_name, array($cookie_scanner, "set_crawling_request_cron_nscs"), $crawling_intervall);
        return true;
    }

    public function add_cron_interval_nscs($schedules)
    {
        $schedules['every_minute'] = array(
            'interval' => 60,
            'display' => esc_html__('Every Minute'),
        );

        $schedules['every_week'] = array(
            'interval' => 604800,
            'display' => esc_html__('Every Week'),
        );

        $schedules['every_second_week'] = array(
            'interval' => 1209600,
            'display' => esc_html__('Every two Weeks'),
        );

        $schedules['every_month'] = array(
            'interval' => 2628000,
            'display' => esc_html__('Every Month'),
        );

        return $schedules;
    }

    private function shall_i_unschedule_download_crawl_results_cron()
    {
        $plugin_configs = new plugin_configs_nscs;

        if ($plugin_configs->get_running_crawlrequest_timestamp_nscs() === false &&
            wp_next_scheduled($this->download_crawl_results_cron_name) !== false) {
            return true;
        }

        return false;
    }

    private function set_cron($cron_hook_name, $method, $intervall)
    {
        add_action($cron_hook_name, $method);
        if (!wp_next_scheduled($cron_hook_name)) {
            wp_schedule_event(time(), $intervall, $cron_hook_name);
        }
    }

    private function remove_cron($cron_hook_name)
    {
        wp_clear_scheduled_hook($cron_hook_name);
    }
}
