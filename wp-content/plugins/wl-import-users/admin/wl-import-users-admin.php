<?php

require_once __DIR__ . '/class-users.php';
require_once __DIR__ . '/class-numbers.php';
require_once __DIR__ . '/class-rodo.php';

function wl_import_users_page()
{
    add_submenu_page(
        'tools.php',
        'WL Import Users',
        'WL Import Users',
        'manage_options',
        'wl-import-users',
        'wl_import_users_page_html'
    );
}
add_action('admin_menu', 'wl_import_users_page');

function wl_import_numbers_page()
{
    add_submenu_page(
        'tools.php',
        'WL Import Numbers',
        'WL Import Numbers',
        'manage_options',
        'wl-import-numbers',
        'wl_import_numbers_page_html'
    );
}
add_action('admin_menu', 'wl_import_numbers_page');

function wl_rodo_page()
{
    add_submenu_page(
        'tools.php',
        'WL Check RODO Contracts',
        'WL Check RODO Contracts',
        'manage_options',
        'wl-rodo',
        'wl_rodo_page_html'
    );
}
add_action('admin_menu', 'wl_rodo_page');

function plugin_admin_init() {
     register_setting( 'wl_import_users_options_group', 'option_field_name', 'wl_import_users_validation_callback' );
     add_settings_section('wl_import_users_main_id', 'Import user from old CMS', 'wl_import_users_main_settings_cb', 'wl_import_users_page_html');
     register_setting( 'wl_import_numbers_options_group', 'option_field_name', 'wl_import_numbers_validation_callback' );
     add_settings_section('wl_import_numbers_main_id', 'Import numbers to dealers', 'wl_import_numbers_main_settings_cb', 'wl_import_numbers_page_html');
     register_setting( 'wl_rodo_options_group', 'option_field_name' );
     add_settings_section('wl_rodo_main_id', 'Check RODO Contracts', 'wl_rodo_main_settings_cb', 'wl_rodo_page_html');
}
add_action('admin_init', 'plugin_admin_init');
