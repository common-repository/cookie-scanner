<?php

class admin_html_formfields_nscs
{
    private $field;
    private $prefix;

    public function return_form_field_nscs($field, $prefix)
    {
        $this->field = $field;
        $this->prefix = $prefix;
        switch ($this->field->type) {
            case "checkbox":
                return $this->create_checkbox();
                break;
            case "textarea":
                return $this->create_textarea();
                break;
            case "text":
                return $this->create_text();
                break;
            case "longtext":
                return $this->create_text("long");
                break;
            case "select":
                return $this->create_select();
                break;
            case "radio":
                return $this->create_radio();
                break;
            case "hidden":
                return $this->create_hidden_field();
                break;
            case "button":
                return $this->create_button();
                break;
            default:
                return $this->field->pre_selected_value;
                break;
        }
    }

    private function create_checkbox()
    {
        $checkbox = '<input id="ff_' . $this->prefix . $this->field->field_slug . '" type="checkbox" name="' . $this->prefix . $this->field->field_slug . '" id="' . $this->prefix . $this->field->field_slug . '" value="1" ' . checked(1, $this->field->pre_selected_value, false) . '>';
        $checkbox = '<input type="hidden" name="' . $this->prefix . $this->field->field_slug . '_hidden" value="0"/>' . $checkbox;
        return $checkbox;
    }

    private function create_textarea()
    {
        return '<textarea id="ff_' . $this->prefix . $this->field->field_slug . '" cols="120"  id="' . $this->prefix . $this->field->field_slug . '" name="' . $this->prefix . $this->field->field_slug . '" rows="20" class="large-text code" type="textarea">' . $this->convert_to_string($this->field->pre_selected_value) . '</textarea>';
    }

    private function create_hidden_field()
    {
        return "<input type='hidden'  id='ff_" . $this->prefix . $this->field->field_slug . "' name='" . $this->prefix . $this->field->field_slug . "_hidden' value='" . $this->convert_to_string($this->field->pre_selected_value) . "'/>";
    }

    private function create_text($length = "short")
    {
        $size = 20;
        if ($length == "long") {
            $size = 50;
        }
        return '<input id="ff_' . $this->prefix . $this->field->field_slug . '" type="text"  id="' . $this->prefix . $this->field->field_slug . '" name="' . $this->prefix . $this->field->field_slug . '" size="' . $size . '" maxlength="200" value="' . $this->field->pre_selected_value . '">';
    }

    private function create_select()
    {

        $html = '<select id="ff_' . $this->prefix . $this->field->field_slug . '"  name="' . $this->prefix . $this->field->field_slug . '" id="' . $this->prefix . $this->field->field_slug . '">';
        foreach ($this->field->selectable_values as $selectable_value) {
            $select = "";
            if ($selectable_value->value == $this->field->pre_selected_value) {$select = "selected";}
            $html .= '<option value="' . $selectable_value->value . '" ' . $select . '>' . $selectable_value->name . '</option>';
        }
        $html .= "</select>";
        return $html;
    }

    private function create_radio()
    {
        $html = "";
        foreach ($this->field->selectable_values as $selectable_value) {
            $select = "";
            if ($selectable_value->value == $this->field->pre_selected_value) {$select = "checked";}
            $html .= '<input id="ff_' . $this->prefix . $this->field->field_slug . '"  type="radio" name="' . $this->prefix . $this->field->field_slug . '" value="' . $selectable_value->value . '" ' . $select . ' > ' . $selectable_value->name . ' ';
        }
        return $html;
    }

    private function create_button()
    {
        return '<input type="submit" formaction="' . $this->field->button_action . '" name="submit" id="ff_' . $this->prefix . $this->field->field_slug . '" class="button button-primary" value="' . $this->field->name . '">';
    }

    private function convert_to_string($input)
    {
        if (!is_string($input)) {
            return json_encode($input);
        }
        return $input;
    }

}
