<?php

class mail_sender_nscs
{
    public function send_new_cookie_mail_nscs($new_cookies)
    {
        if (empty($new_cookies)) {
            return false;
        }
        $plugin_configs = new plugin_configs_nscs;
        $notification_active = $plugin_configs->get_option_nscs("new_cookie_notification");
        if ($notification_active == false) {
            return false;
        }

        $to = $plugin_configs->get_option_nscs("new_cookie_notification_email");
        $subject = $this->niceSubject(count($new_cookies));
        $admin_url = get_admin_url(null, "edit.php?post_type=" . POST_TYPE_NSCS);
        $blog_url = get_bloginfo("wpurl");
        $body = "Hallo,\n\n the following cookies were newly found on " . $blog_url . ".\n For more details please log in: " . $admin_url . "\n\n";
        foreach ($new_cookies as $cookie) {
            $body .= $cookie->name . "\n";
        }
        $result = wp_mail($to, $subject, $body);
        //file_put_contents(PLUGIN_PATH_NSCS . "/log.txt", date("Y-m-d H:i:s", time()) . ": mail send result:" . var_dump($result) . "\n", FILE_APPEND);
    }

    public function niceSubject($numberCookies)
    {
        $subject = array();
        $subject[1] = "One new cookie found.";
        $subject[2] = "Two new cookies found.";
        $subject[3] = "Three new cookies found.";
        $subject[4] = "Four new cookies found.";
        $subject[5] = "Five new cookies found.";
        $subject[6] = "Six new cookies found.";
        $subject[7] = "Seven new cookies found.";
        $subject[8] = "Eight new cookies found.";
        $subject[9] = "Nine new cookies found.";
        $subject[10] = "Ten new cookies found.";
        $subject[11] = "Eleven new cookies found.";
        $subject[12] = "Twelve new cookies found.";

        if (isset($subject[$numberCookies])) {
            return $subject[$numberCookies];
        }
        return $numberCookies . " new cookies found.";
    }
}
