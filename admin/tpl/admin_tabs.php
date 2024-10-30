<h2 class="nav-tab-wrapper">
<?php
//tabs are created
foreach ($settings_object->setting_page_fields->tabs as $tab) {
    $activeTab = "";
    if ($tab->active === true) {
        $activeTab = 'nav-tab-active';
    }
    echo '<a href="edit.php?post_type=' . POST_TYPE_NSCS . '&page=' . $settings_object->plugin_slug . '&tab=' . $tab->tab_slug . '" class="nav-tab ' . $activeTab . '" >' . $tab->tabname . '</a>';
}
$active_tab_index = $settings_object->setting_page_fields->active_tab_index;

?>
</h2>

<p><?php echo $settings_object->setting_page_fields->tabs[$active_tab_index]->tab_description ?></p>