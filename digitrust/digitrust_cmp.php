<?php
/*
Plugin Name: DigiTrust CMP
Description: DigiTrust CMP javascript injection for GDPR
Version: 0.0.1
Author: Purch && DigiTrust Working Group
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
        "askForConset": null,
        "repromptOptions": {
            "fullConsentGiven": 360,
            "someConsentGiven": 30,
            "noConsentGiven": 30
        },
        "geoIPVendor": "https://cmp.digitru.st/1/geoip.json",
        "digitrustRedirectUrl": "https://cdn.digitru.st/prod/1.5.10/redirect.html?redirect=",
        "testingMode": "normal",
        "blockBrowsing": false,
        "layout": "thin",
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

    /**
     * Digitrus_CMP constructor.
     */
    public function __construct()
    {
        register_activation_hook(__FILE__, array($this, 'install'));
        add_action('wp_enqueue_scripts',array($this, 'digitrust_init'));
        add_action('admin_menu', array($this, 'digitrust_setting_page'));
        register_uninstall_hook(__FILE__, array('Digitrus_CMP', 'uninstall'));
        add_action('wp_loaded', array($this, 'update_config'));
    }

    /**
     * Init and Load CMP javascript
     */
    function digitrust_init() {
        wp_enqueue_script( 'cmp-config-js', plugins_url( '/js/cmp_config.js', __FILE__ ));
        wp_localize_script( 'cmp-config-js', 'defaultConfig', json_decode($this->getConfig(), true));
        wp_enqueue_script( 'cmp-js', plugins_url( '/js/cmp.js', __FILE__ ));
    }

    /**
     * Install Digitrust plugin
     */
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

    /**
     * Uninstall Digitrust plugin
     */
    public static function uninstall()
    {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}digitrust_config;");
    }

    /**
     * Add DigiTrust Menu
     */
    function digitrust_setting_page()
    {
        add_menu_page('DigiTrust config', 'DigiTrust', 'manage_options', 'digitrust', array($this, 'digitrust_setting_page_html'));
    }

    /**
     * Set HTML Digitrust Config page
     */
    function digitrust_setting_page_html()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        $content = [];
	    $content[] = '<script>var config_digitrust_cmp = ' . $this->getConfig() . '</script>';
        $content[] = '<div class="wrap"><h1>'. esc_html(get_admin_page_title()).'</h1>';
        echo join('', $content);
        require_once('digitrust_setting_page_html.html');
    }

    /**
     * Update config
     */
    public function update_config()
    {
        if (!empty($_POST['digitrust_cmp_reset'])) {
        	$this->resetConfig();
        } elseif (!empty($_POST['digitrust_cmp_save'])) {
	        $this->saveConfig();
        }
    }

    /**
     * Get $config
     * @return string
     */
    public function getConfig()
    {
        global $wpdb;
        $row = $wpdb->get_row("SELECT config FROM {$wpdb->prefix}digitrust_config WHERE id = 1");
        return ($row) ? $row->config : self::DEFAULT_CONFIG;
    }

    /**
     * Set $config
     * @param $config
     */
    public function setConfig($config)
    {
        global $wpdb;
        $table = $wpdb->prefix.'digitrust_config';
        $wpdb->update($table, array('config' => $config), array('id' => 1));
        $this->config = $config;
    }

    protected function resetConfig()
    {
	    $this->setConfig(self::DEFAULT_CONFIG);
    }

    protected function saveConfig()
    {
	    $config = json_decode($this->getConfig(), true);
	    if (!empty($_POST['digitrust_cmp_layout'])) {
		    $config['layout'] = $_POST['digitrust_cmp_layout'];
		    if ($config['layout'] == 'modal') {
			    $config['blockBrowsing'] = true;
		    }
	    }
	    if (isset($_POST['digitrust_cmp_block_browsing'])) {
	    	if ($config['layout'] != 'modal') {
			    $config['blockBrowsing'] = boolval($_POST['digitrust_cmp_block_browsing']);
		    }
	    }

	    if (isset($_POST['digitrust_cmp_force_local'])) {
	    	if ($_POST['digitrust_cmp_force_local'] == 'Autodetect') {
			    $config['forceLocale'] = get_locale();
		    } else {
			    $config['forceLocale'] = $_POST['digitrust_cmp_force_local'];
		    }
	    }

	    if (isset($_POST['digitrust_cmp_ask_for_conset'])) {
		    $config['askForConset'] = $_POST['digitrust_cmp_ask_for_conset'];
		    if ($config['askForConset'] == 0) {
			    $config['gdprAppliesGlobally'] = false;
			    $config['testingMode'] = 'normal';
		    } elseif ($config['askForConset'] == 1) {
			    $config['gdprAppliesGlobally'] = true;
			    $config['testingMode'] = 'normal';
		    } else {
			    $config['gdprAppliesGlobally'] = false;
			    $config['testingMode'] = 'newer show';
		    }
	    }

	    if (isset($_POST['digitrust_cmp_store_consent_globally'])) {
		    $config['storeConsentGlobally'] = boolval($_POST['digitrust_cmp_store_consent_globally']);
	    }

	    if (!empty($_FILES['digitrust_cmp_logo_url'])) {
		    if ( ! function_exists( 'wp_handle_upload' ) ) {
			    require_once( ABSPATH . 'wp-admin/includes/file.php' );
		    }
		    $moveFile = wp_handle_upload($_FILES['digitrust_cmp_logo_url'], ['test_form' => false]);
	    }

	    if (isset($moveFile['url'])) {
		    $config['logoUrl'] = $moveFile['url'];
	    	if (empty($config['logoUrl'])) {
			    $config['logoUrl'] = null;
		    }
	    }

	    $this->setConfig(json_encode($config));
    }

}

new Digitrus_CMP();
