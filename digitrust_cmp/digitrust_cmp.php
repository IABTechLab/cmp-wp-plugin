<?php
/*
Plugin Name: DigiTrust CMP
Description: DigiTrust CMP allows these publishers to install the CMP in their site and without having to write code
Version: 4.0.3
Author: Abderrahmen Tabbakh
License: GPLv2 or later
Text Domain: digitrust
*/

class Digitrus_CMP
{
    public function __construct()
    {
        register_activation_hook(__FILE__, array('Digitrus_CMP', 'install'));
        add_action('wp_enqueue_scripts',array($this, 'ava_test_init'));
        add_action('admin_menu', array($this, 'digitrust_setting_page'));
        register_uninstall_hook(__FILE__, array('Digitrus_CMP', 'uninstall'));
    }

    function ava_test_init() {
        wp_enqueue_script( 'ava-test-js', plugins_url( '/js/cmp.js', __FILE__ ));
    }

    function digitrust_setting_page_html()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div class="wrap">
            <h1>'. esc_html(get_admin_page_title()).'</h1>
            <form action="'. plugins_url().'/digitrust_cmp/digitrust_config.php" method="post">
                <textarea id="digitrust[json]" name="digitrust[json]" rows="10" cols="50"></textarea>'.
                submit_button("Save Settings")
            .'</form>
        </div>';
    }

    function digitrust_setting_page()
    {
        add_menu_page('DigiTrust config', 'DigiTrust', 'manage_options', 'digitrust', array($this, 'digitrust_setting_page_html'));
    }

    public static function install()
    {
        global $wpdb;
        $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}digitrust_config (id INT AUTO_INCREMENT PRIMARY KEY, config LONGTEXT NOT NULL);");
    }

    public static function uninstall()
    {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}digitrust_config;");
    }
}

new Digitrus_CMP();
