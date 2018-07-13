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

    const DEFAULT_CONFIG = '{
        "customPurposeListLocation": null,
        "globalVendorListLocation": "https://vendorlist.consensu.org/vendorlist.json",
        "globalConsentLocation": "https://cdn.digitrust.mgr.consensu.org/1/portal.html",
        "storeConsentGlobally": true,
        "storePublisherData": true,
        "logging": false,
        "localization": {},
        "forceLocale": null,
        "gdprAppliesGlobally": false,
        "repromptOptions": {
            "fullConsentGiven": 360,
            "someConsentGiven": 30,
            "noConsentGiven": 30
        },
        "geoIPVendor": "https://cmp.digitru.st/1/geoip.json",
        "digitrustRedirectUrl": "https://cdn.digitru.st/prod/1.5.10/redirect.html?redirect=",
        "testingMode": "normal",
        "blockBrowsing": true,
        "layout": null,
        "showFooterAfterSubmit": true,
        "logoUrl": null,
        "css": {
            "color-primary": "#0a82be",
            "color-secondary": "#eaeaea",
            "color-border": "#eaeaea",
            "color-background": "#ffffff",
            "color-text-primary": "#333333",
            "color-text-secondary": "#0a82be",
            "color-linkColor": "#0a82be",
            "color-table-background": "#f7f7f7",
            "font-family": "\'Helvetica Neue\', Helvetica, Arial, sans-serif",
            "custom-font-url": null
        },
        "digitrust": {
            "redirects": false
        }
    }';


    protected $config = self::DEFAULT_CONFIG;

    public function __construct()
    {
        register_activation_hook(__FILE__, array($this, 'install'));
        add_action('wp_enqueue_scripts',array($this, 'digitrust_init'));
        add_action('admin_menu', array($this, 'digitrust_setting_page'));
        register_uninstall_hook(__FILE__, array('Digitrus_CMP', 'uninstall'));
        add_action('wp_loaded', array($this, 'update_config'));
    }

    function digitrust_init() {
        wp_enqueue_script( 'cmp-config-js', plugins_url( '/js/cmp_config.js', __FILE__ ));
        wp_localize_script( 'cmp-config-js', 'defaultConfig', json_decode($this->getConfig(), true));
        wp_enqueue_script( 'cmp-js', plugins_url( '/js/cmp.js', __FILE__ ));
    }

    function digitrust_setting_page_html()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        echo '<div class="wrap">
            <h1>'. esc_html(get_admin_page_title()).'</h1>
            <form action="" method="post">
                <textarea id="digitrust_cmp_json_config" name="digitrust_cmp_json_config" rows="10" cols="50">'. $this->getConfig() .'</textarea>
                <input type="submit" />
            </form>
        </div>';
    }

    public function update_config()
    {
        if (isset($_POST['digitrust_cmp_json_config']) && !empty($_POST['digitrust_cmp_json_config'])) {
            $this->setConfig(str_replace(['\"', "\'"], ['"', "'"], $_POST['digitrust_cmp_json_config']));
        }
    }

    function digitrust_setting_page()
    {
        add_menu_page('DigiTrust config', 'DigiTrust', 'manage_options', 'digitrust', array($this, 'digitrust_setting_page_html'));
    }

    public function install()
    {
        global $wpdb;
        $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}digitrust_config (id INT AUTO_INCREMENT PRIMARY KEY, config LONGTEXT NOT NULL);");
        $row = $wpdb->get_row("SELECT config FROM {$wpdb->prefix}digitrust_config WHERE id = 1");
        if (is_null($row)) {
            $wpdb->insert("{$wpdb->prefix}digitrust_config", array('config' => $this->config));
        } elseif(!empty($row)) {
            $this->config = $row->config;
        }
    }

    public static function uninstall()
    {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}digitrust_config;");
    }

    public function getConfig()
    {
        global $wpdb;
        $row = $wpdb->get_row("SELECT config FROM {$wpdb->prefix}digitrust_config WHERE id = 1");
        return ($row) ? $row->config : self::DEFAULT_CONFIG;
    }

    public function setConfig($config)
    {
        global $wpdb;
        $table = $wpdb->prefix.'digitrust_config';
        $wpdb->update($table, array('config' => $config), array('id' => 1));
        $this->config = $config;
    }

}

new Digitrus_CMP();
