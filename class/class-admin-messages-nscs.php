<?php

class admin_messages_nscs
{
    public $errors;

    public function __construct()
    {
        $this->errors = array();
    }

    public function display_messages_nscs()
    {
        if (!empty($this->errors)) {
            add_action('admin_notices', array($this, 'add_admin_messages_nscs'));
        }
    }

    public function set_admin_error_nscs($error, $type = "error")
    {
        $this->errors[$type][] = $error;
    }

    public function set_admin_info_nscs($message, $type = "info")
    {
        $this->errors[$type][] = $message;
    }

    public function add_admin_messages_nscs()
    {
        foreach ($this->errors as $message_type => $type) {
            foreach ($type as $error_message) {
                echo '<div class="notice notice-' . $message_type . '">
                       <p>' . __($error_message, "cookie-scanner-nscs") . '</p>
                    </div>';
            }
        }
    }

}
