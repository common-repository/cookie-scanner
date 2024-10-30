<?php

class admin_custom_post_nscs
{
    private $metafields;

    public function __construct()
    {
        $this->metafields = json_decode(file_get_contents(PLUGIN_PATH_NSCS . "plugin-cpt-metafields.json"));
    }

    public function execute_wordpress_actions_nscs()
    {
        add_action("init", array($this, "register_custom_post_type_nscs"));
        add_action("admin_init", array($this, "add_meta_box_nscs"));
        add_action("save_post", array($this, "save_custom_metaboxes_nscs"));
        add_action("manage_edit-" . POST_TYPE_NSCS . "_columns", array($this, "manage_edit_columns_nscs"));
        add_action('manage_posts_custom_column', array($this, "manage_posts_custom_columns_nscs"));
    }

    public function register_custom_post_type_nscs()
    {
        $labels = array(
            "name" => __("Cookie", "cookie-scanner-nscs"),
            "all_items" => __("Cookie List", "cookie-scanner-nscs"),
            "singular_name" => __("Cookie", "cookie-scanner-nscs"),
            "add_new" => __("Add New", "cookie-scanner-nscs"),
            "add_new_item" => __("Add New Cookie", "cookie-scanner-nscs"),
            "edit_item" => __("Edit Cookie", "cookie-scanner-nscs"),
            "new_item" => __("New Cookie", "cookie-scanner-nscs"),
            "view_item" => __("View Cookie", "cookie-scanner-nscs"),
            "search_items" => __("Search Cookies", "cookie-scanner-nscs"),
            "not_found" => __("Nothing found", "cookie-scanner-nscs"),
            "not_found_in_trash" => __("Nothing found in Trash", "cookie-scanner-nscs"),
            "parent_item_colon" => "",
            "menu_name" => "Cookie Scanner",
            "item_published" => "Cookie now live and visisble with shortcode [cookie_list_nscs].",
        );
        $args = array(
            "labels" => $labels,
            "public" => false,
            "publicly_queryable" => false,
            "exclude_from_search" => true,
            "show_ui" => true,
            "query_var" => true,
            "rewrite" => true,
            "capabilities" => array(
                "publish_posts" => "manage_options",
                "edit_posts" => "manage_options",
                "edit_others_posts" => "manage_options",
                "delete_posts" => "manage_options",
                "delete_others_posts" => "manage_options",
                "read_private_posts" => "manage_options",
                "edit_post" => "manage_options",
                "delete_post" => "manage_options",
                "read_post" => "manage_options",
            ),
            /** done editing */
            "menu_icon" => "dashicons-code-standards",
            "hierarchical" => false,
            "menu_position" => null,
            "supports" => array("title", "editor"),
        );
        register_post_type(POST_TYPE_NSCS, $args);
    }

    public function add_meta_box_nscs()
    {
        foreach ($this->metafields->post_meta_fields as $meta_field) {
            if ($meta_field->editable === false) {
                continue;
            }
            add_meta_box($meta_field->slug, __($meta_field->nice_name, "cookie-scanner-nscs"), array($this, "create_metabox_nscs"), POST_TYPE_NSCS, "side", "default", array("meta_field" => $meta_field));
        }
    }

    public function create_metabox_nscs($o, $boxarray)
    {

        $meta_field_slug = $boxarray["args"]["meta_field"]->slug;
        $validation_method = $boxarray["args"]["meta_field"]->extra_validation_name;
        $explanation = $boxarray["args"]["meta_field"]->explanation;
        global $post;
        $custom_post = get_post_custom($post->ID);
        $meta_value = (isset($custom_post[$meta_field_slug][0])) ? $custom_post[$meta_field_slug][0] : '';
        $input_validation = new input_validation_nscs;
        $meta_value = $input_validation->validate_field_custom_save_nscs($validation_method, $meta_value);

        echo '<input name="' . $meta_field_slug . '" value="' . $meta_value . '" style="width:95%;" /><label>' . $explanation . '</label>';
    }

    /** Saves all form data from custom post meta boxes, including saitisation of input */
    public function save_custom_metaboxes_nscs()
    {
        global $post;
        foreach ($this->metafields->post_meta_fields as $meta_field) {
            $slug = $meta_field->slug;
            if (isset($_POST[$meta_field->slug]) && $meta_field->editable === true) {
                $input_validation = new input_validation_nscs;
                $insert_value = $input_validation->validate_field_custom_save_nscs($meta_field->extra_validation_name, $_POST[$meta_field->slug]);
                update_post_meta($post->ID, $meta_field->slug, $insert_value);
            }
        }
    }

    /** Apply column names to the custom post type table */
    public function manage_edit_columns_nscs($columns)
    {
        $columns = array(
            "cb" => "<input type=\"checkbox\" />",
            "title" => "Cookie Name",
        );
        foreach ($this->metafields->post_meta_fields as $meta_field) {
            if ($meta_field->show_as_table_column) {
                $columns[$meta_field->slug] = $meta_field->nice_name;
            }
        }
        $columns["description"] = "Description";
        return $columns;
    }

    /** Add column data to custom post type table columns */
    public function manage_posts_custom_columns_nscs($column, $post_id = 0)
    {
        global $post;
        $input_validation = new input_validation_nscs;

        foreach ($this->metafields->post_meta_fields as $meta_field) {
            if ($column == $meta_field->slug) {
                $custom_post = get_post_custom();
                if (isset($custom_post[$meta_field->slug][0])) {
                    echo $input_validation->validate_field_custom_save_nscs($meta_field->extra_validation_name, $custom_post[$meta_field->slug][0]);
                }
            }
        }
    }

    public function insert_new_custom_post_nscs($title, $post_meta)
    {
        $post_data = array(
            "post_title" => $title,
            'post_status' => "draft",
            'post_type' => POST_TYPE_NSCS,
            'post_category' => array(),
            'post_content' => '',
            'page_template' => 'default',
            'meta_input' => $post_meta,
        );

        $post_id = wp_insert_post($post_data, false);
        return true;
    }

    public function get_all_cookies_nscs($status = "draft, publish, pending")
    {
        $all_cookies = get_posts(
            array(
                "numberposts" => -1,
                "post_type" => POST_TYPE_NSCS,
                "post_status" => $status,
            )
        );
        return $all_cookies;
    }

    public function get_post_meta_nscs($post_id, $slug = "")
    {
        return get_post_meta($post_id, $slug);
    }
}
