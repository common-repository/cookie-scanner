<form action="<?php echo $settings_object->setting_page_fields->tabs[$active_tab_index]->form_action ?>" method="post">
<?php
settings_fields($settings_object->plugin_slug . $settings_object->setting_page_fields->tabs[$active_tab_index]->tab_slug);
?>

<?php if ($settings_object->setting_page_fields->tabs[$active_tab_index]->form_action != "display_only") {?>
<?php submit_button();}?>

<table class="form-table">
<?php foreach ($settings_object->setting_page_fields->tabs[$active_tab_index]->tabfields as $field_configs) {?>
 <tr>
  <th scope="row">
    <?php echo $field_configs->name ?>
  </th>
  <td>
    <fieldset>
     <label>
      <?php echo $form_fields->return_form_field_nscs($field_configs, $settings_object->plugin_prefix); ?>
     </label>
     <p class="description"><?php echo esc_html($field_configs->helpertext) ?></p>
    </fieldset>
  </td>
 </tr>
<?php }?>
</table>
</form>

