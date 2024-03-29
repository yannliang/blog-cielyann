<?php

/*
	Support class Mini Mail Dashboard Widget
	Copyright (c) 2009 by Marcel Bokhorst
*/

// http://framework.zend.com/
require_once('Zend/Loader.php');
Zend_Loader::loadFile('Zend/Mail.php', dirname(__FILE__), true);
Zend_Loader::loadFile('Zend/Mail/Storage/Pop3.php', dirname(__FILE__), true);
Zend_Loader::loadFile('Zend/Mail/Storage/Imap.php', dirname(__FILE__), true);
Zend_Loader::loadFile('Zend/Mail/Transport/Sendmail.php', dirname(__FILE__), true);
Zend_Loader::loadFile('Zend/Mail/Transport/Smtp.php', dirname(__FILE__), true);

// http://www.chuggnutt.com/html2text.php
if (!class_exists('html2text'))
	require_once('lib/class.html2text.inc');

// http://www.criticaldevelopment.net/xml/doc.php
if (!class_exists('XMLParser'))
	require_once('lib/parser_php5.php');

// Define constants
define('c_wpmm_table_name', 'wpmm_log');
define('c_wpmm_text_domain', 'wp-mini-mail');

define('c_wpmm_meta_known', 'wpmm_known');

// General options
define('c_wpmm_option_version', 'wpmm_version');
define('c_wpmm_option_time_format', 'wpmm_time_format');
define('c_wpmm_option_cache_maxage', 'wpmm_cache_maxage');
define('c_wpmm_option_cron_interval', 'wpmm_cron_interval');
define('c_wpmm_option_http_timeout', 'wpmm_http_timout');
define('c_wpmm_option_sms_maxlen', 'wpmm_sms_maxlen');
define('c_wpmm_option_wp_addr', 'wpmm_wp_addr');
define('c_wpmm_option_debug', 'wpmm_debug');
define('c_wpmm_option_clean', 'wpmm_clean');

// Mail options
define('c_wpmm_meta_mail_name', 'wpmm_mail_name');
define('c_wpmm_meta_mail_addr', 'wpmm_mail_addr');
define('c_wpmm_meta_mail_rx', 'wpmm_mail_rx');
define('c_wpmm_meta_mail_tx', 'wpmm_mail_tx');
define('c_wpmm_meta_mail_book', 'wpmm_mail_book');

// POP3 options
define('c_wpmm_meta_pop3_host', 'wpmm_pop3_host');
define('c_wpmm_meta_pop3_port', 'wpmm_pop3_port');
define('c_wpmm_meta_pop3_user', 'wpmm_pop3_user');
define('c_wpmm_meta_pop3_pwd', 'wpmm_pop3_pwd');
define('c_wpmm_meta_pop3_ssl', 'wpmm_pop3_ssl');

// IMAP options
define('c_wpmm_meta_imap_host', 'wpmm_imap_host');
define('c_wpmm_meta_imap_port', 'wpmm_imap_port');
define('c_wpmm_meta_imap_user', 'wpmm_imap_user');
define('c_wpmm_meta_imap_pwd', 'wpmm_imap_pwd');
define('c_wpmm_meta_imap_ssl', 'wpmm_imap_ssl');
define('c_wpmm_meta_imap_folder', 'wpmm_imap_folder');

// SMTP options
define('c_wpmm_meta_smtp_host', 'wpmm_smtp_host');
define('c_wpmm_meta_smtp_port', 'wpmm_smtp_port');
define('c_wpmm_meta_smtp_auth', 'wpmm_smtp_auth');
define('c_wpmm_meta_smtp_user', 'wpmm_smtp_user');
define('c_wpmm_meta_smtp_pwd', 'wpmm_smtp_pwd');
define('c_wpmm_meta_smtp_ssl', 'wpmm_smtp_ssl');

// SMS options
define('c_wpmm_meta_sms_enable', 'wpmm_sms_enable');
define('c_wpmm_meta_sms_notify', 'wpmm_sms_notify');
define('c_wpmm_meta_sms_separator', 'wpmm_sms_separator');
define('c_wpmm_meta_sms_from_len', 'wpmm_sms_from_length');
define('c_wpmm_meta_sms_subj_len', 'wpmm_sms_subject_length');
define('c_wpmm_meta_sms_text_len', 'wpmm_sms_text_length');
define('c_wpmm_meta_sms_user', 'wpmm_sms_user');
define('c_wpmm_meta_sms_pwd', 'wpmm_sms_pwd');
define('c_wpmm_meta_sms_from', 'wpmm_sms_from');
define('c_wpmm_meta_sms_to', 'wpmm_sms_to');
define('c_wpmm_meta_sms_url', 'wpmm_sms_url');
define('c_wpmm_meta_sms_limit', 'wpmm_sms_limit');
define('c_wpmm_meta_sms_book', 'wpmm_sms_book');
define('c_wpmm_meta_sms_schedule', 'wpmm_sms_schedule');

define('c_wpmm_meta_sms_day', 'wpmm_sms_day');
define('c_wpmm_meta_sms_count', 'wpmm_sms_count');


// Request arguments
define('c_wpmm_action_arg', 'wpmm_action');
define('c_wpmm_action_list', 'list');
define('c_wpmm_action_msg', 'message');
define('c_wpmm_param_msgid', 'msgid');
define('c_wpmm_action_send', 'send');
define('c_wpmm_param_to', 'to');
define('c_wpmm_param_cc', 'cc');
define('c_wpmm_param_subj', 'subject');
define('c_wpmm_param_phone', 'phone');
define('c_wpmm_param_text', 'text');
define('c_wpmm_action_sms', 'sms');
define('c_wpmm_param_cache', 'cache');
define('c_wpmm_action_cron', 'cron');
define('c_wpmm_action_del', 'delete');
define('c_wpmm_param_nonce', 'nonce');
define('c_wpmm_action_log', 'log');
define('c_wpmm_param_clear', 'clear');
define('c_wpmm_action_headers', 'headers');
define('c_wpmm_action_html', 'html');
define('c_wpmm_action_file', 'file');
define('c_wpmm_param_file', 'file');

// Session
define('c_wpmm_session_cache', 'wpmm_cache');
define('c_wpmm_session_age', 'wpmm_age');

define('c_wpmm_nonce_form', 'wpmm-nonce-from');
define('c_wpmm_nonce_ajax', 'wpmm-nonce-ajax');

// Define class
if (!class_exists('WPMiniMail')) {
	class WPMiniMail {
		// Class variables
		var $main_file = null;
		var $plugin_url = null;

		// Constructor
		function WPMiniMail() {
			$bt = debug_backtrace();
			$this->main_file = $bt[0]['file'];
			$this->plugin_url = WP_PLUGIN_URL . '/' . basename(dirname($this->main_file));

			// Register (de)activation hook
			register_activation_hook($this->main_file, array(&$this, 'wpmm_activate'));
			register_deactivation_hook($this->main_file, array(&$this, 'wpmm_deactivate'));

			// Register actions/filters
			add_action('init', array(&$this, 'wpmm_init'));
			if (is_admin()) {
				add_action('wp_dashboard_setup', array(&$this, 'wpmm_dashboard_setup'));
				add_action('admin_menu', array(&$this, 'wpmm_admin_menu'));
				add_filter('wpmm_address_book', array(&$this, 'wpmm_address_book_extra'));
				add_filter('wpmm_phone_book', array(&$this, 'wpmm_phone_book_extra'));
			}
			add_filter('cron_schedules', array(&$this, 'wpmm_cron_schedules'));
			add_action('wpmm_cron', array(&$this, 'wpmm_cron'));

			session_start();
		}

		// Handle plugin activation
		function wpmm_activate() {
			$version = get_option(c_wpmm_option_version);
			if (!$version) {
				// Set default options
				update_option(c_wpmm_option_version, 1);
				update_option(c_wpmm_option_time_format, get_option('date_format') . ' ' . get_option('time_format'));
				update_option(c_wpmm_option_cache_maxage, 15);
				update_option(c_wpmm_option_cron_interval, 30);
				update_option(c_wpmm_option_http_timeout, 30);
				update_option(c_wpmm_option_wp_addr, false);
				update_option(c_wpmm_option_clean, false);

				// Create log table
				global $wpdb;
				$table_name = $wpdb->prefix . c_wpmm_table_name;
				if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
					$sql = "CREATE TABLE " . $table_name . "(
					id BIGINT(20) NOT NULL AUTO_INCREMENT,
					time DATETIME NOT NULL,
					user BIGINT(20) NOT NULL,
					severity VARCHAR(20) NOT NULL,
					function VARCHAR(40) NOT NULL,
					text TEXT NOT NULL,
					UNIQUE KEY id (id));";
					require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
					dbDelta($sql);
				}
			}

			// Upgrade to version 2
			if ($version <= 1) {
				update_option(c_wpmm_option_version, 2);
				update_option(c_wpmm_option_sms_maxlen, 160);
			}
		}

		// Handle plugin deactivation
		function wpmm_deactivate() {
			// Stop cron job
			wp_clear_scheduled_hook('wpmm_cron');

			// Cleanup data
			if (get_option(c_wpmm_option_clean)) {
				global $wpdb;

				// Delete options
				$rows = $wpdb->get_results("SELECT option_name FROM " . $wpdb->options . " WHERE option_name LIKE 'wpmm_%'");
				foreach ($rows as $row)
					delete_option($row->option_name);

				// Delete user meta values
				$rows = $wpdb->get_results("SELECT user_id, meta_key FROM " . $wpdb->usermeta . " WHERE meta_key LIKE 'wpmm_%'");
				foreach ($rows as $row)
					delete_usermeta($row->user_id, $row->meta_key);

				// Delete log table
				$sql = "DROP TABLE " . $wpdb->prefix . c_wpmm_table_name;
				if ($wpdb->query($sql) === false)
					$wpdb->print_error();

				// Clear cache
				$this->wpmm_clear_cache();
			}
		}

		// Handle initialize
		function wpmm_init() {
			if (is_admin()) {
				// I18n
				load_plugin_textdomain(c_wpmm_text_domain, false, basename(dirname($this->main_file)));

				// Load style sheet
				$css_name = $this->change_extension(basename($this->main_file), '.css');
				if (file_exists(TEMPLATEPATH . '/' . $css_name))
					$css_url = get_bloginfo('template_directory') . '/' . $css_name;
				else
					$css_url = $this->plugin_url . '/' . $css_name;
				wp_register_style('wpmm_style', $css_url);
				wp_enqueue_style('wpmm_style');

				// Enqueue jQuery
				wp_enqueue_script('jquery');
			}
		}

		// Handle dashboard setup
		function wpmm_dashboard_setup() {
			// Register widget
			wp_add_dashboard_widget('wpmm_dashboard', __('Mini Mail', c_wpmm_text_domain), array(&$this, 'wpmm_dashboard'));
		}

		// Handle menu setup
		function wpmm_admin_menu() {
			if (function_exists('add_options_page'))
				add_management_page(
					__('Mini Mail', c_wpmm_text_domain),
					__('Mini Mail', c_wpmm_text_domain),
					'edit_posts',
					$this->main_file,
					array(&$this, 'wpmm_configure'));
		}

		function wpmm_render_info_panel() {
?>
			<div id="wpmm-resources">
			<h3><?php _e('Resources', c_wpmm_text_domain); ?></h3>
			<ul>
			<li><a href="http://wordpress.org/extend/plugins/mini-mail-dashboard-widget/other_notes/" target="_blank"><?php _e('Usage instructions', c_wpmm_text_domain); ?></a></li>
			<li><a href="http://wordpress.org/extend/plugins/mini-mail-dashboard-widget/faq/" target="_blank"><?php _e('Frequently asked questions', c_wpmm_text_domain); ?></a></li>
			<li><a href="http://blog.bokhorst.biz/2414/computers-en-internet/wordpress-plugin-mini-mail-dashboard-widget/" target="_blank"><?php _e('Support page', c_wpmm_text_domain); ?></a></li>
			<li><a href="http://blog.bokhorst.biz/about/" target="_blank"><?php _e('About the author', c_wpmm_text_domain); ?></a></li>
			</ul>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----MIIHZwYJKoZIhvcNAQcEoIIHWDCCB1QCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYA4K0hdo9IAvF36+fuAa/Gfr+CFX2LfuG1ShQZyOm9oCmG9h/nYE5jOulQ9hBUtJEUjW/aXa992pVzbsZnM+yWJLvFpPqTIRTKSot0C2IZUf/233quiNbefAzN2Mlu0PWmjoWawKClhZ3YVSdwz6opi7UX1pPWiGEPyjZG65c/F+jELMAkGBSsOAwIaBQAwgeQGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQIPoWoGhjTYdOAgcD6tQshF5WdHiVa9957j/cwFIjEHBBZgQz1UGeqozJ63S9RgukwGoCqK/4inQeoKQVAZffaCufkOy8pw9gWVoDfntUPmmHuVSRqCTm87DLsO9f1AOUZYCaNVuiihK2e/Ku7p1fO7DLx9G2+Ku0FfL+RWfVOJY/Q9wAqKtH2iaqQ7lTOOaZ+8CAqGLZ1+kHpvdzH1N45N5IlF89E1ts4YzueLJ/9D795fPvLgiEBtGbprd5V8dm1mhwQa5LjA8jS87ugggOHMIIDgzCCAuygAwIBAgIBADANBgkqhkiG9w0BAQUFADCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wHhcNMDQwMjEzMTAxMzE1WhcNMzUwMjEzMTAxMzE1WjCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAMFHTt38RMxLXJyO2SmS+Ndl72T7oKJ4u4uw+6awntALWh03PewmIJuzbALScsTS4sZoS1fKciBGoh11gIfHzylvkdNe/hJl66/RGqrj5rFb08sAABNTzDTiqqNpJeBsYs/c2aiGozptX2RlnBktH+SUNpAajW724Nv2Wvhif6sFAgMBAAGjge4wgeswHQYDVR0OBBYEFJaffLvGbxe9WT9S1wob7BDWZJRrMIG7BgNVHSMEgbMwgbCAFJaffLvGbxe9WT9S1wob7BDWZJRroYGUpIGRMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbYIBADAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBBQUAA4GBAIFfOlaagFrl71+jq6OKidbWFSE+Q4FqROvdgIONth+8kSK//Y/4ihuE4Ymvzn5ceE3S/iBSQQMjyvb+s2TWbQYDwcp129OPIbD9epdr4tJOUNiSojw7BHwYRiPh58S1xGlFgHFXwrEBb3dgNbMUa+u4qectsMAXpVHnD9wIyfmHMYIBmjCCAZYCAQEwgZQwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tAgEAMAkGBSsOAwIaBQCgXTAYBgkqhkiG9w0BCQMxCwYJKoZIhvcNAQcBMBwGCSqGSIb3DQEJBTEPFw0wOTA5MTcwNzU3MTdaMCMGCSqGSIb3DQEJBDEWBBRaAQdJf5HselIEf7ScNtmsCzMeCzANBgkqhkiG9w0BAQEFAASBgFA3ImJxzqE4es+sJI/W21/sFzaqqAJBhjQwpye7HC/jXfpUOshdGujB2gchaQIZaquaVQlZu8y9GJ8s0yDVJL11Fj1iVb0ZZUAXWf2lGGE9O6zhyt/8kU3Rt/OMUkcXzpdxolzSpzjEjWUcTpJ5nfiKJWNPrNhTtOWBAuzYAgqq-----END PKCS7-----">
			<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			</form>
			</div>
<?php
		}

		// Handle dashboard configuration
		// http://codex.wordpress.org/Creating_Options_Pages
		function wpmm_configure() {
			// Get current user
			global $user_ID;
			get_currentuserinfo();

			echo '<div class="wrap">';
			
			$this->wpmm_render_info_panel();
			
			echo '<div id="wpmm-options">';
			echo '<h2>' . __('Mini Mail', c_wpmm_text_domain) . '</h2>';
			echo '<form method="post" action="">';

			// Security
			wp_nonce_field(c_wpmm_nonce_form);

			// Check post back
			if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
				// Check security
				check_admin_referer(c_wpmm_nonce_form);

				// General
				if (current_user_can('manage_options')) {
					update_option(c_wpmm_option_time_format, $_POST[c_wpmm_option_time_format]);
					update_option(c_wpmm_option_cache_maxage, intval($_POST[c_wpmm_option_cache_maxage]));
					update_option(c_wpmm_option_cron_interval, intval($_POST[c_wpmm_option_cron_interval]));
					update_option(c_wpmm_option_http_timeout, intval($_POST[c_wpmm_option_http_timeout]));
					update_option(c_wpmm_option_sms_maxlen, intval($_POST[c_wpmm_option_sms_maxlen]));
					update_option(c_wpmm_option_wp_addr, $_POST[c_wpmm_option_wp_addr]);
					update_option(c_wpmm_option_debug, $_POST[c_wpmm_option_debug]);
					update_option(c_wpmm_option_clean, $_POST[c_wpmm_option_clean]);
				}

				// Mail
				update_usermeta($user_ID, c_wpmm_meta_mail_name, $_POST[c_wpmm_meta_mail_name]);
				update_usermeta($user_ID, c_wpmm_meta_mail_addr, $_POST[c_wpmm_meta_mail_addr]);
				update_usermeta($user_ID, c_wpmm_meta_mail_rx, $_POST[c_wpmm_meta_mail_rx]);
				update_usermeta($user_ID, c_wpmm_meta_mail_tx, $_POST[c_wpmm_meta_mail_tx]);
				update_usermeta($user_ID, c_wpmm_meta_mail_book, $_POST[c_wpmm_meta_mail_book]);

				// POP3
				update_usermeta($user_ID, c_wpmm_meta_pop3_host, $_POST[c_wpmm_meta_pop3_host]);
				update_usermeta($user_ID, c_wpmm_meta_pop3_port, intval($_POST[c_wpmm_meta_pop3_port]));
				update_usermeta($user_ID, c_wpmm_meta_pop3_user, $_POST[c_wpmm_meta_pop3_user]);
				update_usermeta($user_ID, c_wpmm_meta_pop3_pwd, $_POST[c_wpmm_meta_pop3_pwd]);
				update_usermeta($user_ID, c_wpmm_meta_pop3_ssl, $_POST[c_wpmm_meta_pop3_ssl]);

				// IMAP
				update_usermeta($user_ID, c_wpmm_meta_imap_host, $_POST[c_wpmm_meta_imap_host]);
				update_usermeta($user_ID, c_wpmm_meta_imap_port, intval($_POST[c_wpmm_meta_imap_port]));
				update_usermeta($user_ID, c_wpmm_meta_imap_user, $_POST[c_wpmm_meta_imap_user]);
				update_usermeta($user_ID, c_wpmm_meta_imap_pwd, $_POST[c_wpmm_meta_imap_pwd]);
				update_usermeta($user_ID, c_wpmm_meta_imap_ssl, $_POST[c_wpmm_meta_imap_ssl]);
				update_usermeta($user_ID, c_wpmm_meta_imap_folder, $_POST[c_wpmm_meta_imap_folder]);

				// SMTP
				update_usermeta($user_ID, c_wpmm_meta_smtp_host, $_POST[c_wpmm_meta_smtp_host]);
				update_usermeta($user_ID, c_wpmm_meta_smtp_port, intval($_POST[c_wpmm_meta_smtp_port]));
				update_usermeta($user_ID, c_wpmm_meta_smtp_auth, $_POST[c_wpmm_meta_smtp_auth]);
				update_usermeta($user_ID, c_wpmm_meta_smtp_user, $_POST[c_wpmm_meta_smtp_user]);
				update_usermeta($user_ID, c_wpmm_meta_smtp_pwd, $_POST[c_wpmm_meta_smtp_pwd]);
				update_usermeta($user_ID, c_wpmm_meta_smtp_ssl, $_POST[c_wpmm_meta_smtp_ssl]);

				// SMS
				update_usermeta($user_ID, c_wpmm_meta_sms_enable, $_POST[c_wpmm_meta_sms_enable]);
				update_usermeta($user_ID, c_wpmm_meta_sms_notify, $_POST[c_wpmm_meta_sms_notify]);
				update_usermeta($user_ID, c_wpmm_meta_sms_separator, $_POST[c_wpmm_meta_sms_separator]);
				update_usermeta($user_ID, c_wpmm_meta_sms_from_len, intval($_POST[c_wpmm_meta_sms_from_len]));
				update_usermeta($user_ID, c_wpmm_meta_sms_subj_len, intval($_POST[c_wpmm_meta_sms_subj_len]));
				update_usermeta($user_ID, c_wpmm_meta_sms_text_len, intval($_POST[c_wpmm_meta_sms_text_len]));
				update_usermeta($user_ID, c_wpmm_meta_sms_user, $_POST[c_wpmm_meta_sms_user]);
				update_usermeta($user_ID, c_wpmm_meta_sms_pwd, $_POST[c_wpmm_meta_sms_pwd]);
				update_usermeta($user_ID, c_wpmm_meta_sms_from, $_POST[c_wpmm_meta_sms_from]);
				update_usermeta($user_ID, c_wpmm_meta_sms_to, $_POST[c_wpmm_meta_sms_to]);
				update_usermeta($user_ID, c_wpmm_meta_sms_url, $_POST[c_wpmm_meta_sms_url]);
				update_usermeta($user_ID, c_wpmm_meta_sms_limit, $_POST[c_wpmm_meta_sms_limit]);
				update_usermeta($user_ID, c_wpmm_meta_sms_book, $_POST[c_wpmm_meta_sms_book]);
				update_usermeta($user_ID, c_wpmm_meta_sms_schedule, $_POST[c_wpmm_meta_sms_schedule]);

				update_usermeta($user_ID, c_wpmm_meta_sms_count, 0);

				$this->wpmm_log($user_ID, 'info', __('Settings updated', c_wpmm_text_domain));
				$this->wpmm_clear_cache();

				echo '<div id="message" class="updated fade"><p><strong>' . __('Settings updated', c_wpmm_text_domain) . '</strong></p></div>';
			}

			// Render options form
?>
<?php		if (current_user_can('manage_options')) { ?>
			<h3><?php _e('General', c_wpmm_text_domain); ?></h3>
			<table class="form-table">

			<tr valign="top"><th scope="row">
				<label for="wpmm_time_format"><?php _e('Time format:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_time_format" class="wpmm-input-medium" name="<?php echo c_wpmm_option_time_format; ?>" type="text" value="<?php echo get_option(c_wpmm_option_time_format); ?>" />
				<a href="http://www.php.net/date" target="_blank"><?php _e('Details', c_wpmm_text_domain); ?></a>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_cache_maxage"><?php _e('Max. cache age:', c_wpmm_text_domain); ?></label>
			</th>
			<td>
				<input id="wpmm_cache_maxage" class="wpmm-input-numeric" name="<?php echo c_wpmm_option_cache_maxage; ?>" type="text" value="<?php echo get_option(c_wpmm_option_cache_maxage); ?>" />
				<span class="wpmm-explanation"><?php _e('Minutes', c_wpmm_text_domain); ?></span>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_opt_cron"><?php _e('Mail check interval:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_opt_cron" class="wpmm-input-numeric" name="<?php echo c_wpmm_option_cron_interval; ?>" type="text" value="<?php echo get_option(c_wpmm_option_cron_interval); ?>" />
				<span class="wpmm-explanation"><?php _e('Minutes', c_wpmm_text_domain); ?></span>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_opt_http"><?php _e('HTTP time-out:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_opt_http" class="wpmm-input-numeric" name="<?php echo c_wpmm_option_http_timeout; ?>" type="text" value="<?php echo get_option(c_wpmm_option_http_timeout); ?>" />
				<span class="wpmm-explanation"><?php _e('Seconds', c_wpmm_text_domain); ?></span>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_sms_maxlen"><?php _e('Max. SMS length:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_sms_maxlen" class="wpmm-input-numeric" name="<?php echo c_wpmm_option_sms_maxlen; ?>" type="text" value="<?php echo get_option(c_wpmm_option_sms_maxlen); ?>" />
				<span class="wpmm-explanation"><?php _e('Characters', c_wpmm_text_domain); ?></span>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_opt_wp_addr"><?php _e('WordPress address book:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_opt_wp_addr" name="<?php echo c_wpmm_option_wp_addr; ?>" type="checkbox"<?php if (get_option(c_wpmm_option_wp_addr)) echo ' checked="checked"'; ?> />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_opt_debug"><?php _e('Debug:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_opt_debug" name="<?php echo c_wpmm_option_debug; ?>" type="checkbox"<?php if (get_option(c_wpmm_option_debug)) echo ' checked="checked"'; ?> />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_opt_clean"><?php _e('Clean on deactivate:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_opt_clean" name="<?php echo c_wpmm_option_clean; ?>" type="checkbox"<?php if (get_option(c_wpmm_option_clean)) echo ' checked="checked"'; ?> />
			</td></tr>

			</table>
			<br />
<?php		} ?>

			<h3><?php _e('Mail', c_wpmm_text_domain); ?></h3>
			<table class="form-table">

			<tr valign="top"><th scope="row">
				<label for="wpmm_mail_name"><?php _e('Your name:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_mail_name" class="wpmm-input-medium" name="<?php echo c_wpmm_meta_mail_name; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_mail_name); ?>" />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_mail_addr"><?php _e('Your e-mail:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_mail_addr" class="wpmm-input-medium" name="<?php echo c_wpmm_meta_mail_addr; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_mail_addr); ?>" />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_mail_rx"><?php _e('Receive method:', c_wpmm_text_domain); ?></label>
			</th><td>
				<?php $rx = get_usermeta($user_ID, c_wpmm_meta_mail_rx); ?>
				<select id="wpmm_mail_rx" name="<?php echo c_wpmm_meta_mail_rx; ?>">
				<option value=""<?php if (!$rx) echo ' selected'; ?>><?php _e('Off', c_wpmm_text_domain); ?></option>
				<option value="POP3"<?php if ($rx == 'POP3') echo ' selected'; ?>>POP3</option>
				<option value="IMAP"<?php if ($rx == 'IMAP') echo ' selected'; ?>>IMAP</option>
				</select>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_mail_tx"><?php _e('Send method:', c_wpmm_text_domain); ?></label>
			</th><td>
				<?php $tx = get_usermeta($user_ID, c_wpmm_meta_mail_tx); ?>
				<select id="wpmm_mail_tx" name="<?php echo c_wpmm_meta_mail_tx; ?>">
				<option value=""<?php if (!$tx) echo ' selected'; ?>><?php _e('Off', c_wpmm_text_domain); ?></option>
				<option value="Sendmail"<?php if ($tx == 'Sendmail') echo ' selected'; ?>>PHP mail</option>
				<option value="SMTP"<?php if ($tx == 'SMTP') echo ' selected'; ?>>SMTP</option>
				</select>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_mail_book"><?php _e('Address book:', c_wpmm_text_domain); ?></label>
			</th><td>
				<textarea id="wpmm_mail_book" name="<?php echo c_wpmm_meta_mail_book; ?>"><?php echo get_usermeta($user_ID, c_wpmm_meta_mail_book); ?></textarea>
			</td></tr>
			<tr><th>&nbsp;</th><td><span class="wpmm-explanation wpmm-explain-below">John Doe &lt;john.doe@example.org&gt;</span></td></tr>
			</table>
			<br />

			<h3><?php _e('POP3', c_wpmm_text_domain); ?></h3>
			<table class="form-table">

			<tr valign="top"><th scope="row">
				<label for="wpmm_pop3_host"><?php _e('Host:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_pop3_host" class="wpmm-input-medium" name="<?php echo c_wpmm_meta_pop3_host; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_pop3_host); ?>" />
				<a href="http://www.emailaddressmanager.com/tips/mail-settings.html" target="_blank"><?php _e('Frequenty used', c_wpmm_text_domain); ?></a>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_pop3_ssl"><?php _e('Secure transfer:', c_wpmm_text_domain); ?></label>
			</th><td>
				<?php $ssl = get_usermeta($user_ID, c_wpmm_meta_pop3_ssl); ?>
				<select id="wpmm_pop3_ssl" name="<?php echo c_wpmm_meta_pop3_ssl; ?>">
				<option value=""<?php if (!$ssl) echo ' selected'; ?>>-</option>
				<option value="SSL"<?php if ($ssl == 'SSL') echo ' selected'; ?>>SSL</option>
				<option value="TLS"<?php if ($ssl == 'TSL') echo ' selected'; ?>>TLS</option>
				</select>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_pop3_port"><?php _e('Port:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_pop3_port" class="wpmm-input-numeric" name="<?php echo c_wpmm_meta_pop3_port; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_pop3_port); ?>" />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_pop3_user"><?php _e('User name:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_pop3_user" class="wpmm-input-medium" name="<?php echo c_wpmm_meta_pop3_user; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_pop3_user); ?>" />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_pop3_pwd"><?php _e('Password:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_pop3_pwd" class="wpmm-input-medium" name="<?php echo c_wpmm_meta_pop3_pwd; ?>" type="password" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_pop3_pwd); ?>" />
			</td></tr>
			</table>
			<br />

			<h3><?php _e('IMAP', c_wpmm_text_domain); ?></h3>
			<table class="form-table">

			<tr valign="top"><th scope="row">
				<label for="wpmm_imap_host"><?php _e('Host:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_imap_host" class="wpmm-input-medium" name="<?php echo c_wpmm_meta_imap_host; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_imap_host); ?>" />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_imap_ssl"><?php _e('Secure transfer:', c_wpmm_text_domain); ?></label>
			</th><td>
				<?php $ssl = get_usermeta($user_ID, c_wpmm_meta_imap_ssl); ?>
				<select id="wpmm_imap_ssl" name="<?php echo c_wpmm_meta_imap_ssl; ?>">
				<option value=""<?php if (!$ssl) echo ' selected'; ?>>-</option>
				<option value="SSL"<?php if ($ssl == 'SSL') echo ' selected'; ?>>SSL</option>
				<option value="TLS"<?php if ($ssl == 'TSL') echo ' selected'; ?>>TLS</option>
				</select>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_imap_port"><?php _e('Port:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_imap_port" class="wpmm-input-numeric" name="<?php echo c_wpmm_meta_imap_port; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_imap_port); ?>" />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_imap_user"><?php _e('User name:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_imap_user" class="wpmm-input-medium" name="<?php echo c_wpmm_meta_imap_user; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_imap_user); ?>" />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_imap_pwd"><?php _e('Password:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_imap_pwd" class="wpmm-input-medium" name="<?php echo c_wpmm_meta_imap_pwd; ?>" type="password" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_imap_pwd); ?>" />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_imap_folder"><?php _e('Folder:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_imap_folder" class="wpmm-input-medium" name="<?php echo c_wpmm_meta_imap_folder; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_imap_folder); ?>" />
			</td></tr>
			</table>
			<br />

			<h3><?php _e('SMTP', c_wpmm_text_domain); ?></h3>
			<table class="form-table">

			<tr valign="top"><th scope="row">
				<label for="wpmm_smtp_host"><?php _e('Host:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_smtp_host" class="wpmm-input-medium" name="<?php echo c_wpmm_meta_smtp_host; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_smtp_host); ?>" />
				<a href="http://www.emailaddressmanager.com/tips/mail-settings.html" target="_blank"><?php _e('Frequenty used', c_wpmm_text_domain); ?></a>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_smtp_ssl"><?php _e('Secure transfer:', c_wpmm_text_domain); ?></label>
			</th><td>
				<?php $ssl = get_usermeta($user_ID, c_wpmm_meta_smtp_ssl); ?>
				<select id="wpmm_smtp_ssl" name="<?php echo c_wpmm_meta_smtp_ssl; ?>">
				<option value=""<?php if (!$ssl) echo ' selected'; ?>>-</option>
				<option value="SSL"<?php if ($ssl == 'SSL') echo ' selected'; ?>>SSL</option>
				<option value="TLS"<?php if ($ssl == 'TLS') echo ' selected'; ?>>TLS</option>
				</select>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_smtp_port"><?php _e('Port:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_smtp_port" class="wpmm-input-numeric" name="<?php echo c_wpmm_meta_smtp_port; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_smtp_port); ?>" />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_smtp_auth"><?php _e('Authentication:', c_wpmm_text_domain); ?></label>
			</th><td>
				<?php $auth = get_usermeta($user_ID, c_wpmm_meta_smtp_auth); ?>
				<select id="wpmm_smtp_auth" name="<?php echo c_wpmm_meta_smtp_auth; ?>">
				<option value=""<?php if (!$auth) echo ' selected'; ?>>-</option>
				<option value="Plain"<?php if ($auth == 'Plain') echo ' selected'; ?>>Plain</option>
				<option value="Login"<?php if ($auth == 'Login') echo ' selected'; ?>>Login</option>
				<option value="Crammd5"<?php if ($auth == 'Crammd5') echo ' selected'; ?>>Crammd5</option>
				</select>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_smtp_user"><?php _e('User name:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_smtp_user" class="wpmm-input-medium" name="<?php echo c_wpmm_meta_smtp_user; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_smtp_user); ?>" />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_smtp_pwd"><?php _e('Password:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_smtp_pwd" class="wpmm-input-medium" name="<?php echo c_wpmm_meta_smtp_pwd; ?>" type="password" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_smtp_pwd); ?>" />
			</td></tr>
			</table>
			<br />

			<h3><?php _e('SMS', c_wpmm_text_domain); ?></h3>

			<a href="http://progx.ch/home-voip-smsbetamax-3-1-1.html" target="_blank">
			<?php _e('Use VoipBuster or one of its clones', c_wpmm_text_domain); ?></a>

			<table class="form-table">

			<tr valign="top"><th scope="row">
				<label for="wpmm_sms_enable"><?php _e('Enable:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_sms_enable" name="<?php echo c_wpmm_meta_sms_enable; ?>" type="checkbox"<?php if (get_usermeta($user_ID,  c_wpmm_meta_sms_enable)) echo ' checked="checked"'; ?> />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_sms_notify"><?php _e('Notifications:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_sms_notify" name="<?php echo c_wpmm_meta_sms_notify; ?>" type="checkbox"<?php if (get_usermeta($user_ID,  c_wpmm_meta_sms_notify)) echo ' checked="checked"'; ?> />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_sms_sepa"><?php _e('Separator:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_sms_sepa" class="wpmm-input-small" name="<?php echo c_wpmm_meta_sms_separator; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_sms_separator); ?>" />
				<span class="wpmm-explanation"><?php _e('Between from/subject/text; default is space', c_wpmm_text_domain); ?></span>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_sms_from_len"><?php _e('Shorten from:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_sms_from_len" class="wpmm-input-numeric" name="<?php echo c_wpmm_meta_sms_from_len; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_sms_from_len); ?>" />
				<span class="wpmm-explanation"><?php _e('-1 is off; empty is maximum', c_wpmm_text_domain); ?></span>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_sms_subj_len"><?php _e('Shorten subject:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_sms_subj_len" class="wpmm-input-numeric" name="<?php echo c_wpmm_meta_sms_subj_len; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_sms_subj_len); ?>" />
				<span class="wpmm-explanation"><?php _e('-1 is off; empty is maximum', c_wpmm_text_domain); ?></span>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_sms_text_len"><?php _e('Shorten text:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_sms_text_len" class="wpmm-input-numeric" name="<?php echo c_wpmm_meta_sms_text_len; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_sms_text_len); ?>" />
				<span class="wpmm-explanation"><?php _e('-1 is off; empty is maximum', c_wpmm_text_domain); ?></span>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_sms_user"><?php _e('User name:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_sms_user" class="wpmm-input-medium" name="<?php echo c_wpmm_meta_sms_user; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_sms_user); ?>" />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_sms_pwd"><?php _e('Password:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_sms_pwd" class="wpmm-input-medium" name="<?php echo c_wpmm_meta_sms_pwd; ?>" type="password" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_sms_pwd); ?>" />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_sms_from"><?php _e('From number:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_sms_from" class="wpmm-input-medium" name="<?php echo c_wpmm_meta_sms_from; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_sms_from); ?>" />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_sms_to"><?php _e('Notifications to:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_sms_to" class="wpmm-input-medium" name="<?php echo c_wpmm_meta_sms_to; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_sms_to); ?>" />
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_sms_url"><?php _e('Url:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_sms_url" class="wpmm-input-large" name="<?php echo c_wpmm_meta_sms_url; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_sms_url); ?>" />
				<a href="#" id="wpmm-sms-url-default"><?php _e('VoipBuster', c_wpmm_text_domain); ?></a>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_sms_limit"><?php _e('Max. SMS/day:', c_wpmm_text_domain); ?></label>
			</th><td>
				<input id="wpmm_sms_limit" class="wpmm-input-numeric" name="<?php echo c_wpmm_meta_sms_limit; ?>" type="text" value="<?php echo get_usermeta($user_ID, c_wpmm_meta_sms_limit); ?>" />
				<span class="wpmm-explanation"><?php _e('0 is unlimited', c_wpmm_text_domain); ?></span>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_sms_phone"><?php _e('Phone book:', c_wpmm_text_domain); ?></label>
			</th><td>
				<textarea id="wpmm_sms_phone" name="<?php echo c_wpmm_meta_sms_book; ?>"><?php echo get_usermeta($user_ID, c_wpmm_meta_sms_book); ?></textarea>
			</td></tr>
			<tr><th>&nbsp;</th><td>
				<span class="wpmm-explanation wpmm-explain-below">John Doe &lt;+012345678&gt;</span>
			</td></tr>

			<tr valign="top"><th scope="row">
				<label for="wpmm_sms_sched"><?php _e('Schedule:', c_wpmm_text_domain); ?></label>
			</th><td>
				<textarea id="wpmm_sms_sched" name="<?php echo c_wpmm_meta_sms_schedule; ?>"><?php echo get_usermeta($user_ID, c_wpmm_meta_sms_schedule); ?></textarea>
			</td></tr>
			<tr><th>&nbsp;</th><td>
				<span class="wpmm-explanation wpmm-explain-below"><?php _e('Works like a mechanical time switch; lines starting with + switches on, with - off.', c_wpmm_text_domain); ?></span>
				<a href="http://www.php.net/strtotime" target="_blank"><?php _e('Details', c_wpmm_text_domain); ?></a>
			</td></tr>
			</table>
			<br />

			<p><span class="wpmm-explanation"><?php _e('All passwords are stored and sent in plain text', c_wpmm_text_domain); ?></span></p>

			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save', c_wpmm_text_domain) ?>" />
			</p>

			</form>
			</div>
			</div>

			<script type="text/javascript">
			/* <![CDATA[ */
			jQuery(document).ready(function($) {
				$('#wpmm_pop3_ssl').change(function() {
					var ssl = $('#wpmm_pop3_ssl option:selected').text();
					$('#wpmm_pop3_port').val(ssl == 'SSL' || ssl == 'TLS' ? '995' : '110');
				});

				$('#wpmm_imap_ssl').change(function() {
					var ssl = $('#wpmm_imap_ssl option:selected').text();
					$('#wpmm_imap_port').val(ssl == 'SSL' || ssl == 'TLS' ? '993' : '143');
				});

				$('#wpmm_smtp_ssl').change(function() {
					var ssl = $('#wpmm_smtp_ssl option:selected').text();
					$('#wpmm_smtp_port').val(ssl == 'SSL' || ssl == 'TLS' ? '465' : '25');
				});

				$('#wpmm-sms-url-default').click(function() {
					$('#wpmm_sms_url').val('https://www.voipbuster.com/myaccount/sendsms.php');
					return false;
				});
			});
			/* ]]> */
			</script>
<?php
		}

		// Render dashboard widget
		function wpmm_dashboard() {
			// Get current user
			global $user_ID;
			get_currentuserinfo();

			// Security
			$nonce = wp_create_nonce(c_wpmm_nonce_ajax);
?>
			<div id="wpmm-container">
			<div id="wpmm-list"></div>
				<div class="wpmm-action">
<?php			if (get_usermeta($user_ID, c_wpmm_meta_mail_rx)) { ?>
				<a id="wpmm-docheck" href="#"><?php _e('Check', c_wpmm_text_domain); ?></a>
<?php			} ?>
<?php			if (get_usermeta($user_ID, c_wpmm_meta_mail_tx)) { ?>
				<a id="wpmm-docompose" href="#"><?php _e('Compose', c_wpmm_text_domain); ?></a>
				<a id="wpmm-doannounce" href="#"><?php _e('Announce', c_wpmm_text_domain); ?></a>
<?php			} ?>
<?php			if (get_usermeta($user_ID,  c_wpmm_meta_sms_enable)) { ?>
				<a id="wpmm-dosms" href="#"><?php _e('SMS', c_wpmm_text_domain); ?></a>
<?php			} ?>
				<a id="wpmm-dolog" href="#"><?php _e('Log', c_wpmm_text_domain); ?></a>
				<a id="wpmm-doclear" href="#"><?php _e('Clear', c_wpmm_text_domain); ?></a>
<?php			if (get_option(c_wpmm_option_debug)) { ?>
				<a id="wpmm-docron" href="#"><?php _e('Cron', c_wpmm_text_domain); ?></a>
<?php			} ?>
				<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=AJSBB7DGNA3MJ&amp;lc=USamp;&amp;item_name=Mini%20Mail%20Dashboard%20Widget%20WordPress%20Plugin&amp;item_number=Marcel%20Bokhorst&amp;currency_code=USD&amp;bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted" target="_blank"><?php _e('Donate', c_wpmm_text_domain); ?></a>
			</div>

			<div id="wpmm-body" style="display:none;">
				<table>
				<tr><td><span class="wpmm-body-title"><?php _e('Date:', c_wpmm_text_domain); ?></span></td>
				<td><span id="wpmm-body-date"></span></td></tr>
				<tr><td><span class="wpmm-body-title"><?php _e('From:', c_wpmm_text_domain); ?></span></td>
				<td><span id="wpmm-body-from"></span></td></tr>
				<tr><td><span class="wpmm-body-title"><?php _e('Subject:', c_wpmm_text_domain); ?></span></td>
				<td><span id="wpmm-body-subj"></span></td></tr>
				<tr id="wpmm-body-attach" style="display:none;"><td><span class="wpmm-body-title"><?php _e('Attachments:', c_wpmm_text_domain); ?></span></td>
				<td id="wpmm-body-files">&nbps;</td></tr>
				</table>
				<div id="wpmm-body-text"></div>
				<div id="wpmm-body-action" class="wpmm-action">
<?php			if (get_usermeta($user_ID, c_wpmm_meta_mail_tx)) { ?>
				<a id="wpmm-doreply" href="#"><?php _e('Reply', c_wpmm_text_domain); ?></a>
				<a id="wpmm-doforward" href="#"><?php _e('Forward', c_wpmm_text_domain); ?></a>
<?php			} ?>
				<a id="wpmm-dohtml" href="#" target="_blank" style="display:none;"><?php _e('HTML', c_wpmm_text_domain); ?></a>
				<a id="wpmm-doclosebody" href="#"><?php _e('Close', c_wpmm_text_domain); ?></a>
				</div>
			</div>

			<div id="wpmm-compose" style="display:none">
				<form action="#" method="post">
				<table>
				<tr class="wpmm-row-msg"><td class="wpmm-form-title"><?php _e('To:', c_wpmm_text_domain); ?></td>
				<td>
				<input type="text" name="wpmm-to" class="wpmm-msgenable wpmm-msgval" />
				<select id="wpmm-addr-book-to">
				<?php $this->wpmm_output_addr_book($user_ID); ?>
				</select>
				</td></tr>
				<tr class="wpmm-row-msg"><td class="wpmm-form-title"><?php _e('CC:', c_wpmm_text_domain); ?></td>
				<td>
				<input type="text" name="wpmm-cc" class="wpmm-msgenable wpmm-msgval" />
				<select id="wpmm-addr-book-cc">
				<?php $this->wpmm_output_addr_book($user_ID); ?>
				</select>
				</td></tr>
				<tr class="wpmm-row-msg"><td class="wpmm-form-title"><?php _e('Subject:', c_wpmm_text_domain); ?></td>
				<td><input type="text" name="wpmm-subj" class="wpmm-msgenable wpmm-msgval" /></td></tr>
				<tr class="wpmm-row-sms"><td class="wpmm-form-title"><?php _e('Phone:', c_wpmm_text_domain); ?></td>
				<td>
				<input type="text" name="wpmm-phone" class="wpmm-msgenable wpmm-msgval" />
				<select id="wpmm-phone-book">
				<?php $this->wpmm_output_phone_book($user_ID); ?>
				</select>
				</td></tr>
				</table>
				<textarea name="wpmm-msgtext" class="wpmm-msgenable wpmm-msgval"></textarea><br />
				<input type="submit" name="wpmm-send-msg" class="wpmm-msgenable button-primary" value="<?php _e('Send', c_wpmm_text_domain); ?>" />
				<input type="button" name="wpmm-cancel-msg" class="wpmm-msgenable button" value="<?php _e('Cancel', c_wpmm_text_domain); ?>" />
				</form>
				<div id="wpmm-msgstat" class="wpmm-notice"></div>
			</div>

			<div id="wpmm-log" style="display:none;">
				<div id="wpmm-log-text"></div>
				<div class="wpmm-action">
					<a id="wpmm-docloselog" href="#"><?php _e('Close', c_wpmm_text_domain); ?></a>
				</div>
			</div>
			</div>

			<script type="text/javascript">
			/* <![CDATA[ */
			jQuery(document).ready(function($) {
				/* Get mail list */
				$('#wpmm-list').ready(function() {
					var load = $('<img src="<?php echo $this->plugin_url  . '/img/ajax-loader.gif'; ?>" alt="wait" />');
					$('#wpmm-list').html(load);
					$.ajax({
						url: '<?php echo $this->plugin_url  . '/' . basename($this->main_file); ?>',
						type: 'GET',
						data:
						{
							<?php echo c_wpmm_param_nonce; ?>: '<?php echo $nonce; ?>',
							<?php echo c_wpmm_action_arg; ?>: '<?php echo c_wpmm_action_list ?>',
							<?php echo c_wpmm_param_cache; ?>: 'true'
						},
						dataType: 'text',
						cache: false,
						success: function(result) {
							$('#wpmm-list').html(result);
						},
						error: function(x, stat, e) {
							$('#wpmm-list').html('<span class="wpmm-notice">Error ' + x.status + '<\/span>');
						}
					});
				});

				/* Get or delete message */
				$('#wpmm-list').click(function(e) {
					var a = $(e.target);
					if (a.is('img'))
						a = a.parent();
					if (a.is('a')) {
						var cell = a.parent();
						var row = cell.parent();
						var load = $('<img src="<?php echo $this->plugin_url  . '/img/ajax-loader.gif'; ?>" alt="wait" />');

						/* Get message */
						if (a.get(0).className == 'wpmm-subj') {
							/* Copy meta data */
							$('#wpmm-body').attr('data-from', row.attr('data-from'));
							$('#wpmm-body').attr('data-subj', row.attr('data-subj'));
							$('#wpmm-body').attr('data-to',	row.attr('data-to'));
							$('#wpmm-body').attr('data-date', row.attr('data-date'));
							$('#wpmm-body').attr('data-reply', row.attr('data-reply'));

							/* Visuals */
							$('#wpmm-body-date').text(row.attr('data-date'));
							$('#wpmm-body-from').text(row.attr('data-from'));
							$('#wpmm-body-subj').text(row.attr('data-subj'));
							if (row.attr('data-files') == '')
								$('#wpmm-body-attach').hide();
							else {
								var f = '';
								var files = row.attr('data-files').split(',');
								for (i = 0; i < files.length; i++) {
									var url = '<?php echo $this->plugin_url  . '/' . basename($this->main_file); ?>';
									url = url + '?<?php echo c_wpmm_action_arg; ?>=<?php echo c_wpmm_action_file; ?>';
									url = url + '&<?php echo c_wpmm_param_nonce; ?>=<?php echo $nonce; ?>';
									url = url + '&<?php echo c_wpmm_param_msgid; ?>=' + row.attr('data-id');
									url = url + '&<?php echo c_wpmm_param_file; ?>=' + i;
									f = f + '<a href="' + url + '" target="_blank" class="wpmm-file-link">' + files[i] + '</a>';
								}
								$('#wpmm-body-files').html(f);
								$('#wpmm-body-attach').show();
							}
							$('#wpmm-body-text').html(load);
							$('#wpmm-body-action').hide();
							$('#wpmm-dohtml').hide();
							if (row.attr('data-html') == 'true') {
								var url = '<?php echo $this->plugin_url  . '/' . basename($this->main_file); ?>';
								url = url + '?<?php echo c_wpmm_action_arg; ?>=<?php echo c_wpmm_action_html; ?>';
								url = url + '&<?php echo c_wpmm_param_nonce; ?>=<?php echo $nonce; ?>';
								url = url + '&<?php echo c_wpmm_param_msgid; ?>=' + row.attr('data-id');
								$('#wpmm-dohtml').attr('href', url);
								$('#wpmm-dohtml').show();
							}
							$('#wpmm-body').show();

							/* Async fetch */
							$.ajax({
								url: '<?php echo $this->plugin_url  . '/' . basename($this->main_file); ?>',
								type: 'GET',
								data: {
									<?php echo c_wpmm_param_nonce; ?>: '<?php echo $nonce; ?>',
									<?php echo c_wpmm_action_arg; ?>: '<?php echo c_wpmm_action_msg ?>',
									<?php echo c_wpmm_param_msgid; ?>: row.attr('data-id')
								},
								dataType: 'text',
								cache: false,
								success: function(result) {
									$('#wpmm-body-text').html(result);
									$('#wpmm-body-action').show();
									row.removeClass('wpmm-new-mail');
								},
								error: function(x, stat, e) {
									$('#wpmm-body-text').html('<span class="wpmm-notice">Error ' + x.status + '<\/span>');
								}
							});
						}

						/* Delete message */
						else if (a.get(0).className == 'wpmm-delete') {
							var q = '<?php _e('Are you sure to delete this message?', c_wpmm_text_domain); ?>';
							q = q + "\n<?php _e('Date:', c_wpmm_text_domain); ?> " + row.attr('data-date')
							q = q + "\n<?php _e('From:', c_wpmm_text_domain); ?> " + row.attr('data-from')
							q = q + "\n<?php _e('Subject:', c_wpmm_text_domain); ?> " + row.attr('data-subj')
							if (confirm(q)) {
								/* Visuals */
								a.hide();
								cell.append(load);

								/* Async delete */
								$.ajax({
									url: '<?php echo $this->plugin_url  . '/' . basename($this->main_file); ?>',
									type: 'POST',
									data: {
										<?php echo c_wpmm_param_nonce; ?>: '<?php echo $nonce; ?>',
										<?php echo c_wpmm_action_arg; ?>: '<?php echo c_wpmm_action_del ?>',
										<?php echo c_wpmm_param_msgid; ?>: row.attr('data-id')
									},
									dataType: 'text',
									cache: false,
									success: function(result) {
										if (result == 'OK')
											row.hide();
										else {
											load.remove();
											a.show();
											cell.append('<span class="wpmm-notice">' + result + '<\/span>');
										}
									},
									error: function(x, stat, e) {
										load.remove();
										a.show();
										cell.append('<span class="wpmm-notice">Error ' + x.status + '<\/span>');
									}
								});
							}
						}

						/* Get headers */
						else if (a.get(0).className == 'wpmm-headers') {
							a.hide();
							cell.append(load);

							/* Async get headers */
							$.ajax({
								url: '<?php echo $this->plugin_url  . '/' . basename($this->main_file); ?>',
								type: 'GET',
								data: {
									<?php echo c_wpmm_param_nonce; ?>: '<?php echo $nonce; ?>',
									<?php echo c_wpmm_action_arg; ?>: '<?php echo c_wpmm_action_headers ?>',
									<?php echo c_wpmm_param_msgid; ?>: row.attr('data-id')
								},
								dataType: 'text',
								cache: false,
								success: function(result) {
									$('#wpmm-log-text').html(result);
									$('#wpmm-log').show();
									load.remove();
									a.show();
								},
								error: function(x, stat, e) {
									$('#wpmm-log-text').html('<span class="wpmm-notice">Error ' + x.status + '<\/span>');
									$('#wpmm-log').show();
									load.remove();
									a.show();
								}
							});
						}
					}
					return false;
				});

				/* Check mail / cron */
				$('#wpmm-docheck,#wpmm-docron').click(function() {
					var check = (this.id == 'wpmm-docheck');
					var load = $('<img src="<?php echo $this->plugin_url  . '/img/ajax-loader.gif'; ?>" alt="wait" />');

					/* Visuals */
					if (check)
						$('#wpmm-list').html(load);
					else {
						$('#wpmm-log-text').html(load);
						$('#wpmm-log').show();
					}

					/* Async fetch */
					$.ajax({
						url: '<?php echo $this->plugin_url  . '/' . basename($this->main_file); ?>',
						type: 'GET',
						data: {
							<?php echo c_wpmm_param_nonce; ?>: '<?php echo $nonce; ?>',
							<?php echo c_wpmm_action_arg; ?>:  (check ? '<?php echo c_wpmm_action_list ?>' : '<?php echo c_wpmm_action_cron ?>'),
							<?php echo c_wpmm_param_cache; ?>: 'false'
						},
						dataType: 'text',
						cache: false,
						success: function(result) {
							$(check ? '#wpmm-list' : '#wpmm-log-text').html(result);
						},
						error: function(x, stat, e) {
							$(check ? '#wpmm-list' : '#wpmm-log-text').html('<span class="wpmm-notice">Error ' + x.status + '<\/span>');
						}
					});
					return false;
				});

				/* Address book to */
				$('#wpmm-addr-book-to').change(function() {
					var addr = $(this).val();
					if (addr != '') {
						var cur = $('[name=wpmm-to]').val();
						if (cur != '')
							cur = cur + ', ';
						$('[name=wpmm-to]').val(cur + addr);
					}
					return false;
				});

				/* Address book cc */
				$('#wpmm-addr-book-cc').change(function() {
					var addr = $(this).val();
					if (addr != '') {
						var cur = $('[name=wpmm-cc]').val();
						if (cur != '')
							cur = cur + ', ';
						$('[name=wpmm-cc]').val(cur + addr);
					}
					return false;
				});

				/* Phone book */
				$('#wpmm-phone-book').change(function() {
					$('[name=wpmm-phone]').val($(this).val());
					return false;
				});

				/* Compose mail, announce / sms */
				$('#wpmm-docompose,#wpmm-doannounce,#wpmm-dosms').click(function() {
					var sms = (this.id == 'wpmm-dosms');
					$('#wpmm-compose').attr('data-sms', sms);
					$('#wpmm-msgstat').html('');
					$('.wpmm-msgval').val('');
					$("#wpmm-addr-book-to option[value='']").attr('selected', 'selected');
					$("#wpmm-addr-book-cc option[value='']").attr('selected', 'selected');
					$("#wpmm-phone-book option[value='']").attr('selected', 'selected');
					$(sms ? '.wpmm-row-sms' : '.wpmm-row-msg').show();
					$(sms ? '.wpmm-row-msg' : '.wpmm-row-sms').hide();
					$('#wpmm-compose').show();
					if (this.id == 'wpmm-doannounce') {
						$('[name=wpmm-to]').val('*');
						$('[name=wpmm-subj]').focus();
					}
					else
						$(sms ? '[name=wpmm-phone]' : '[name=wpmm-to]').focus();
					return false;
				});

				/* Show log */
				$('#wpmm-dolog,#wpmm-doclear').click(function() {
					var clear = (this.id == 'wpmm-doclear');
					if (!clear || confirm('<?php _e('Are you sure to clear the log?', c_wpmm_text_domain); ?>')) {
						/* Visuals */
						var load = $('<img src="<?php echo $this->plugin_url  . '/img/ajax-loader.gif'; ?>" alt="wait" />');
						$('#wpmm-log-text').html(load);
						$('#wpmm-log').show();

						/* Async fetch */
						$.ajax({
							url: '<?php echo $this->plugin_url  . '/' . basename($this->main_file); ?>',
							type: 'GET',
							data: {
								<?php echo c_wpmm_param_nonce; ?>: '<?php echo $nonce; ?>',
								<?php echo c_wpmm_action_arg; ?>:  '<?php echo c_wpmm_action_log ?>',
								<?php echo c_wpmm_param_clear; ?>: (clear ? 'true' : 'false')
							},
							dataType: 'text',
							cache: false,
							success: function(result) {
								$('#wpmm-log-text').html(result);
							},
							error: function(x, stat, e) {
								$('#wpmm-log-text').html('<span class="wpmm-notice">Error ' + x.status + '<\/span>');
							}
						});
					}
					return false;
				});

				/* Reply to / forward message */
				$('#wpmm-doreply,#wpmm-doforward').click(function() {
					var b = $('#wpmm-body');
					var reply = (this.id == 'wpmm-doreply');
					$('wpmm-compose').attr('data-sms', 'false');

					/* Visuals */
					$('#wpmm-msgstat').html('');
					$('.wpmm-msgval').val('');
					$("#wpmm-addr-book-to option[value='']").attr('selected', 'selected');
					$("#wpmm-addr-book-cc option[value='']").attr('selected', 'selected');
					if (reply) {
						$('[name=wpmm-to]').val(b.attr('data-reply'));
						$('[name=wpmm-subj]').val('<?php _e('Re: ', c_wpmm_text_domain); ?>' + b.attr('data-subj'));
					}
					else
						$('[name=wpmm-subj]').val('<?php _e('Fwd: ', c_wpmm_text_domain); ?>' + b.attr('data-subj'));
					$('.wpmm-row-msg').show();
					$('.wpmm-row-sms').hide();
					$('#wpmm-compose').show();

					/* Body text */
					var text = '\n\n--- ' + '<?php _e('On', c_wpmm_text_domain); ?>' + ' ' + b.attr('data-date');
					text = text + ' ' + b.attr('data-from') + ' <?php _e('wrote:', c_wpmm_text_domain); ?>\n\n';
					text = text + '<?php _e('From:', c_wpmm_text_domain); ?>' + ' ' + b.attr('data-from') + '\n';
					text = text + '<?php _e('Subject:', c_wpmm_text_domain); ?>' + ' ' + b.attr('data-subj') + '\n';
					text = text + '<?php _e('To:', c_wpmm_text_domain); ?>' + ' ' + b.attr('data-to') + '\n';
					text = text + '<?php _e('Date:', c_wpmm_text_domain); ?>' + ' ' + b.attr('data-date') + '\n';
					text = text + '\n' + $('#wpmm-body-text').text();
					$('[name=wpmm-msgtext]').val(text);

					/* Focus */
					if (reply) {
						var text = $('[name=wpmm-msgtext]');
						if (text.get(0).setSelectionRange)
							text.get(0).setSelectionRange(0, 0);
						else if (text.get(0).createTextRange) {
							var range = text.get(0).createTextRange();
							range.collapse(true);
							range.moveEnd('character', 0);
							range.moveStart('character', 0);
							range.select();
						}
						text.focus();
					}
					else
						$('[name=wpmm-to]').focus();
					return false;
				});

				/* Show html */
				$('#wpmm-dohtml').click(function() {
				});

				/* Send mail / sms */
				$('[name=wpmm-send-msg]').click(function() {
					var sms = ($('#wpmm-compose').attr('data-sms') == 'true');
					var load = $('<img src="<?php echo $this->plugin_url  . '/img/ajax-loader.gif'; ?>" alt="wait" />');

					/* Visuals */
					$('#wpmm-msgstat').html(load);
					$('.wpmm-msgenable').attr('disabled', 'disabled');

					/* Post data */
					$.ajax({
						url: '<?php echo $this->plugin_url  . '/' . basename($this->main_file); ?>',
						type: 'POST',
						data: {
							<?php echo c_wpmm_param_nonce; ?>: '<?php echo $nonce; ?>',
							<?php echo c_wpmm_action_arg; ?>: (sms ? '<?php echo c_wpmm_action_sms ?>' : '<?php echo c_wpmm_action_send ?>'),
							<?php echo c_wpmm_param_to; ?>: $('[name=wpmm-to]').val(),
							<?php echo c_wpmm_param_cc; ?>: $('[name=wpmm-cc]').val(),
							<?php echo c_wpmm_param_subj; ?>: $('[name=wpmm-subj]').val(),
							<?php echo c_wpmm_param_phone; ?>: $('[name=wpmm-phone]').val(),
							<?php echo c_wpmm_param_text; ?>: $('[name=wpmm-msgtext]').val()
						},
						dataType: 'text',
						cache: false,
						success: function(result) {
							$('.wpmm-msgenable').removeAttr('disabled');
							$('#wpmm-msgstat').html(result);
						},
						error: function(x, stat, e) {
							$('.wpmm-msgenable').removeAttr('disabled');
							$('#wpmm-msgstat').html('Error ' + x.status);
						}
					});
					return false;
				});

				/* Count characters */
				$('[name=wpmm-msgtext]').keyup(function() {
					if (($('#wpmm-compose').attr('data-sms') == 'true')) {
						var max = <?php echo get_option(c_wpmm_option_sms_maxlen); ?>;
						var text = $(this).val();
						if (text.length > max)
							$(this).val(text.substring(0, max));
						$('#wpmm-msgstat').html(max - $(this).val().length);
					}
				});

				/* Close message */
				$('#wpmm-doclosebody').click(function() {
					$('#wpmm-body').hide();
					return false;
				});

				/* Cancel compose */
				$('[name=wpmm-cancel-msg]').click(function() {
					$('#wpmm-compose').hide();
					return false;
				});

				/* Close log */
				$('#wpmm-docloselog').click(function() {
					$('#wpmm-log').hide();
					return false;
				});
			});
			/* ]]> */
			</script>
<?php
		}

		// Helper filter add wordpress users to address book
		function wpmm_address_book_extra($book) {
			global $wpdb;
			$sql = "SELECT ID FROM " . $wpdb->users;
			$rows = $wpdb->get_results($sql);
			foreach ($rows as $row) {
				$user = get_userdata($row->ID);
				if ($user->first_name || $user->last_name)
					$name = $user->first_name . ' ' .$user->last_name;
				else
					$name = $user->user_nicename;
				$book[] = $name . ' <' . $user->user_email . '>';
			}
			return $book;
		}

		function wpmm_phone_book_extra($book) {
			return $book;
		}

		// Helper render address book
		function wpmm_output_addr_book($user_ID) {
				$book = explode("\n", get_usermeta($user_ID, c_wpmm_meta_mail_book));
				if (get_option(c_wpmm_option_wp_addr))
					$book = apply_filters('wpmm_address_book', $book);
				$this->wpmm_output_book($book);
		}

		// Helper render phone book
		function wpmm_output_phone_book($user_ID) {
				$book = explode("\n", get_usermeta($user_ID, c_wpmm_meta_sms_book));
				$book = apply_filters('wpmm_phone_book', $book);
				$this->wpmm_output_book($book);
		}

		// Helper render address/phone book
		function wpmm_output_book($book) {
				natcasesort($book);
				echo '<option value="">' . __('Select', c_wpmm_text_domain) . '</option>';
				foreach ($book as $line)
					if (trim($line) != '') {
						$addr = $this->wpmm_parse_addr($line);
						echo '<option value="' . $this->wpmm_format_data($this->wpmm_format_addr($addr)) . '">';
						echo htmlspecialchars($this->wpmm_format_addr($addr, true)) . '</option>' . PHP_EOL;
					}
		}

		// Add cron schedule
		function wpmm_cron_schedules($schedules) {
			$interval = get_option(c_wpmm_option_cron_interval);
			if ($interval <= 0)
				$interval = 30;
			$schedules['wpmm_schedule'] = array(
				'interval' => $interval * 60,
				'display' => __('Mini Mail', c_wpmm_text_domain));
			return $schedules;
		}

		function wpmm_cron_limit($user_ID) {
			$limit = get_usermeta($user_ID, c_wpmm_meta_sms_limit);
			if ($limit <= 0)
				return false;
			else {
				$day = get_usermeta($user_ID, c_wpmm_meta_sms_day);
				$cday = intval(time() / (24 * 60 * 60));
				if ($day == $cday)
					$count = get_usermeta($user_ID, c_wpmm_meta_sms_count);
				else {
					$count = 0;
					update_usermeta($user_ID, c_wpmm_meta_sms_day, $cday);
				}
				$count++;
				update_usermeta($user_ID, c_wpmm_meta_sms_count, $count);
				return ($count > $limit);
			}
		}

		// Handle cron event
		function wpmm_cron($debug = false) {
			// For each user
			global $wpdb;
			$rows = $wpdb->get_results("SELECT ID FROM " . $wpdb->users);
			foreach ($rows as $row) {
				try {
					$count = 0;
					$skip = 0;
					$limited = 0;
					$notify = false;
					$scheduled = false;

					// Check if enabled
					if (get_usermeta($row->ID,  c_wpmm_meta_sms_enable) && get_usermeta($row->ID,  c_wpmm_meta_sms_notify)) {
						$notify = true;

						// Check schedule
						if ($debug || $this->wpmm_is_scheduled($row->ID)) {
							$scheduled = true;

							// Fetch mail
							$list = $this->wpmm_mail_operation($row->ID, 'CRON');
							$count = count($list);

							// Process mail
							for ($i = 0; $i < count($list); $i++) {
								// Process message
								if (!$list[$i]['bulk'] && $list[$i]['id'] && $list[$i]['new']) {
									// Check message limit
									if (!$this->wpmm_cron_limit($row->ID)) {
										// Get data
										$from = $this->wpmm_format_addr($list[$i]['from'], true);
										$subj = $this->wpmm_trimall($list[$i]['subject']);
										$text = '';
										if (isset($list[$i]['text']))
											$text = $this->wpmm_trimall($list[$i]['text']);

										// Limit length
										$from_len = get_usermeta($row->ID, c_wpmm_meta_sms_from_len);
										$subj_len = get_usermeta($row->ID, c_wpmm_meta_sms_subj_len);
										$text_len = get_usermeta($row->ID, c_wpmm_meta_sms_text_len);
										$from = $this->wpmm_limit_sms_text($from, $from_len);
										$subj = $this->wpmm_limit_sms_text($subj, $subj_len);
										$text = $this->wpmm_limit_sms_text($text, $text_len);

										// Build SMS
										$sepa = get_usermeta($row->ID, c_wpmm_meta_sms_separator);
										if (!$sepa)
											$sepa = ' ';
										$to = get_usermeta($row->ID, c_wpmm_meta_sms_to);
										$msg = '';
										if ($from_len >= 0)
											$msg .= $from;
										if ($from_len >= 0 && $subj_len >= 0)
											$msg .= $sepa;
										if ($subj_len >= 0)
											$msg .= $subj;
										if (($from_len >= 0 || $subj_len >= 0) && $text_len >= 0)
											$msg .= $sepa;
										if ($text_len >= 0)
											$msg .= $text;

										// Send SMS
										if ($debug)
											$this->wpmm_log($row->ID, 'verbose', 'SMS to ' . $to . ': ' . $msg);
										else
											$this->wpmm_send_sms($row->ID, $to, $msg);
									}
									else
										$skip++;
								}
								else
									$limited++;
							}
						}
					}

					// Build summary
					$summary = ($notify ? '' : 'no ') . 'notify';
					$summary .= ($scheduled ? ' ' : ' un') . 'scheduled';
					if ($scheduled) {
						$count = get_usermeta($row->ID, c_wpmm_meta_sms_count);
						$summary .= ' ' . $count . ' messages';
						$summary .= ' ' . $skip . ' skipped';
						$summary .= ' ' . $limited . ' limited';
						$summary .= ' ' . $count . ' sent today';
					}
					$this->wpmm_log($row->ID, 'info', $summary);
				}
				catch (Exception $e) {
					$this->wpmm_log($row->ID, 'error', $e->getMessage());
				}
			}
		}

		function wpmm_limit_sms_text($text, $maxlen) {
			if ($maxlen > 0 && strlen($text) > $maxlen)
				$text = substr($text, 0, $maxlen - 1) . '>';
			return $text;
		}

		// Helper check SMS schedule
		function wpmm_is_scheduled($user_ID) {
			$result = false;
			$now = time();
			$schedule = get_usermeta($user_ID, c_wpmm_meta_sms_schedule);
			foreach (explode("\n", $schedule) as $line) {
				$entry = trim($line);
				if ($entry) {
					$on = true;
					$hit = false;
					if (substr($entry, 0, 1) == '-') {
						$on = false;
						$entry = substr($entry, 1);
					}
					else if (substr($entry, 0, 1) == '+')
						$entry = substr($entry, 1);
					$time = strtotime($entry);
					if ($now >= $time) {
						$hit = true;
						$result = $on;
					}

					$msg = 'Schedule ' . ($on ? '+' : '-') . ' ' . date('r', $time);
					$msg .= ' ' . ($hit ? 'hit' : 'skip');
					$msg .= ' ' . ($result ? 'on' : 'off');
					$this->wpmm_log($user_ID, 'verbose', $msg);
				}
			}
			return $result;
		}

		// Handle ajax calls
		function wpmm_check_ajax() {
			if (isset($_REQUEST[c_wpmm_action_arg])) {
				// Security check
				$nonce = $_REQUEST[c_wpmm_param_nonce];
				if (!wp_verify_nonce($nonce, c_wpmm_nonce_ajax))
					die('Unauthorized');

				// Get current user
				global $user_ID;
				get_currentuserinfo();

				// Attachment
				if ($_GET[c_wpmm_action_arg] == c_wpmm_action_file) {
					$this->wpmm_ajax_get_file($user_ID);
					exit();
				}

				// Send header
				header('Content-Type: text/html; charset=' . get_option('blog_charset'));

				// Load text domain
				load_plugin_textdomain(c_wpmm_text_domain, false, basename(dirname($this->main_file)));

				// Get message list
				if($_GET[c_wpmm_action_arg] == c_wpmm_action_list)
					$this->wpmm_ajax_list_messages($user_ID);

				// Get message
				else if ($_GET[c_wpmm_action_arg] == c_wpmm_action_msg)
					$this->wpmm_ajax_get_message($user_ID);

				// Send message
				else if ($_POST[c_wpmm_action_arg] == c_wpmm_action_send)
					$this->wpmm_ajax_send_message($user_ID);

				// Send SMS
				else if ($_POST[c_wpmm_action_arg] == c_wpmm_action_sms) {
					$this->wpmm_ajax_send_sms($user_ID);
					exit();
				}

				// Manual cron
				else if ($_GET[c_wpmm_action_arg] == c_wpmm_action_cron) {
					$this->wpmm_cron(true);
					$this->wpmm_ajax_show_log($user_ID);
				}

				// Delete message
				else if ($_POST[c_wpmm_action_arg] == c_wpmm_action_del)
					$this->wpmm_ajax_delete_message($user_ID);

				// Log
				else if ($_GET[c_wpmm_action_arg] == c_wpmm_action_log)
					$this->wpmm_ajax_show_log($user_ID);

				// Headers
				else if ($_GET[c_wpmm_action_arg] == c_wpmm_action_headers)
					$this->wpmm_ajax_get_headers($user_ID);

				// Html
				else if ($_GET[c_wpmm_action_arg] == c_wpmm_action_html)
					$this->wpmm_ajax_get_message($user_ID, true);

				// Otherwise
				else
					die('Unknown request');

				exit();
			}
		}

		// Helper ajax fetch message list
		function wpmm_ajax_list_messages($user_ID) {
			try {
				// Check mail
				$cache = ($_GET[c_wpmm_param_cache] == 'true');
				$list = $this->wpmm_mail_operation($user_ID, $cache ? 'LIST' : 'CHECK');

				// Print header
				echo '<table><tr>';
				echo '<td>&nbsp;</td>';	// Headers
				echo '<td>&nbsp;</td>'; // Delete
				echo '<td><span class="wpmm-list-title">' . __('Date', c_wpmm_text_domain) . '</span></td>';
				echo '<td><span class="wpmm-list-title">' . __('From', c_wpmm_text_domain) . '</span></td>';
				echo '<td><span class="wpmm-list-title">' . __('Subject', c_wpmm_text_domain) . '</span></td></tr>';

				// Print message list
				$p = 0;
				for ($i = 0; $i < count($list); $i++) {
					// List message
					echo '<tr';
					$classes = array();
					if ($list[$i]['bulk'])
						$classes[] = 'wpmm-bulk';
					if ($list[$i]['new'])
						$classes[] = 'wpmm-new-mail';
					if (count($classes))
						echo ' class="' . implode(' ', $classes) . '"';
					echo ' data-id="' . $this->wpmm_format_data($list[$i]['id']) . '"';
					echo ' data-from="' . $this->wpmm_format_data($this->wpmm_format_addr($list[$i]['from'])) . '"';
					echo ' data-subj="' . $this->wpmm_format_data($list[$i]['subject']) . '"';
					echo ' data-to="' . $this->wpmm_format_data($this->wpmm_format_addr($list[$i]['to'])) . '"';
					echo ' data-date="' . ($list[$i]['date'] ? $this->wpmm_format_data($list[$i]['date']) : '') . '"';
					echo ' data-reply="' . $this->wpmm_format_data($this->wpmm_format_addr($list[$i]['reply'] ? $list[$i]['reply'] : $list[$i]['from'])) . '"';
					echo ' data-html="' . ($list[$i]['has_html'] ? 'true' : 'false') . '"';
					echo ' data-files="' . $this->wpmm_format_data(implode(',', $list[$i]['files'])) . '">';
					echo '<td><a class="wpmm-headers" href="#">';
					echo '<img src="' . $this->plugin_url . '/img/Info_Simple_bw.png" height="10px" alt="info"></img>';
					echo '</a></td>';
					echo '<td><a class="wpmm-delete" href="#">';
					echo '<img src="' . $this->plugin_url . '/img/Pictogram_voting_delete.png" height="10px" alt="info"></img>';
					echo '</a></td>';
					echo '<td>' . ($list[$i]['date'] ? date(get_option(c_wpmm_option_time_format), strtotime($list[$i]['date'])) : '') . '</td>';
					echo '<td>' . htmlspecialchars($this->wpmm_format_addr($list[$i]['from'], true)) . '</td>';
					echo '<td><a class="wpmm-subj" href="#">';
					if (count($list[$i]['files']))
						echo '<img src="' . $this->plugin_url . '/img/Gnome-mail-attachment.png" height="10px" alt="info"></img>';
					echo htmlspecialchars($list[$i]['subject']);
					echo '</a></td>';
					echo '</tr>';
				}
				echo '</table>';
			}
			catch (Exception $e) {
				$this->wpmm_log($user_ID, 'error', $e->getMessage());
				echo '<span class="wpmm-notice">' . htmlspecialchars($e->getMessage()) . '</span>';
			}
		}

		// Helper ajax fetch single message
		function wpmm_ajax_get_message($user_ID, $html = false) {
			try {
				// Receive message
				$msgid = $_GET[c_wpmm_param_msgid];
				$list = $this->wpmm_mail_operation($user_ID, $html ? 'HTML' : 'GET', $msgid);
				if (count($list))
					if (!$html && isset($list[0]['text']))
						echo '<pre>' . $list[0]['text'] . '</pre>';
					else if ($html && isset($list[0]['html']))
						echo $list[0]['html'];
					else
						echo '<span class="wpmm-notice">' . __('n/a', c_wpmm_text_domain) . '</span>';
				else
					echo '<span class="wpmm-notice">' . __('Gone', c_wpmm_text_domain) . '</span>';
			}
			catch (Exception $e) {
				$this->wpmm_log($user_ID, 'error', $e->getMessage());
				echo '<span class="wpmm-notice">' . htmlspecialchars($e->getMessage()) . '</span>';
			}
		}

		// Helper ajax send message
		function wpmm_ajax_send_message($user_ID) {
			try {
				// Get paramaters
				if ($_POST[c_wpmm_param_to] == '*') {
					$book = array();
					$book = $this->wpmm_address_book_extra($book);
					$to = $this->wpmm_parse_addr(implode(',', $book));
				}
				else
					$to = $this->wpmm_parse_addr(stripslashes($_POST[c_wpmm_param_to]));
				$cc = $this->wpmm_parse_addr(stripslashes($_POST[c_wpmm_param_cc]));
				$subj = stripslashes($_POST[c_wpmm_param_subj]);
				$text = stripslashes($_POST[c_wpmm_param_text]);

				if (count($to) + count($cc) == 0)
					throw new Exception(__('E-mail address missing', c_wpmm_text_domain));

				// Send email
				$result = $this->wpmm_send_email($user_ID, $to, $cc, $subj, $text);
				if ($result == 'OK')
					echo __('Sent to', c_wpmm_text_domain) . ' ' . htmlspecialchars($this->wpmm_format_addr($to, true));
				else
					echo htmlspecialchars($result);
			}
			catch (Exception $e) {
				$this->wpmm_log($user_ID, 'error', $e->getMessage());
				echo htmlspecialchars($e->getMessage());
			}
		}

		// Helper ajax send SMS
		function wpmm_ajax_send_sms($user_ID) {
			$phone = $this->wpmm_parse_addr(stripslashes($_POST[c_wpmm_param_phone]));
			$text = $this->wpmm_trimall(stripslashes($_POST[c_wpmm_param_text]));
			if (count($phone) == 0)
				echo __('Phone number missing', c_wpmm_text_domain);
			else {
				$result = $this->wpmm_send_sms($user_ID, $phone[0]['email'], $text);
				if ($result == 'OK')
					echo __('Sent to', c_wpmm_text_domain) . ' ' . htmlspecialchars($phone[0]['email']);
				else
					echo $result;
			}
		}

		// Helper ajax delete message
		function wpmm_ajax_delete_message($user_ID) {
			try {
				// Search message
				$found = false;
				$msgid = $_POST[c_wpmm_param_msgid];
				$list = $this->wpmm_mail_operation($user_ID, 'DELETE', $msgid);
				$found = (count($list) > 0);
				echo ($found ? __('OK', c_wpmm_text_domain) : __('Gone', c_wpmm_text_domain));
			}
			catch (Exception $e) {
				$this->wpmm_log($user_ID, 'error', $e->getMessage());
				echo htmlspecialchars($e->getMessage());
			}
		}

		// Helper ajax show log
		function wpmm_ajax_show_log($user_ID) {
			global $wpdb;

			// Clear log
			$clear = $_GET[c_wpmm_param_clear];
			$clear = isset($clear) && $clear == 'true';
			if ($clear) {
				$sql = "DELETE FROM " . $wpdb->prefix . c_wpmm_table_name;
				$sql .= " WHERE user=" . $user_ID;
				if ($wpdb->query($sql) === false)
					$wpdb->print_error();
				$this->wpmm_log($user_ID, 'info', 'Log cleared');
			}

			// Show header
			global $user_login;
			get_currentuserinfo();
			echo 'User: ' . $user_login . '<br />';
			echo 'Last check: ' . date('r', $_SESSION[c_wpmm_session_age]) . '<br />';
			echo 'Next check: ' . date('r', wp_next_scheduled('wpmm_cron')) . '<br />';
			echo '<br />';

			// Show log
			$sql = "SELECT time, severity, function, text FROM " . $wpdb->prefix . c_wpmm_table_name;
			$sql .= " WHERE user=" . $user_ID . " ORDER BY id DESC";
			$rows = $wpdb->get_results($sql);
			foreach ($rows as $row)
				echo $row->time . ' ' . $row->severity . ' ' . htmlspecialchars($row->function) . ' ' . htmlspecialchars($row->text) . '<br />';
		}

		// Helper ajax get headers
		function wpmm_ajax_get_headers($user_ID) {
			try {
				// Receive message
				$msgid = $_GET[c_wpmm_param_msgid];
				$list = $this->wpmm_mail_operation($user_ID, 'HEADERS', $msgid);
				if (count($list))
					if (isset($list[0]['headers'])) {
						echo '<table>';
						foreach ($list[0]['headers'] as $header) {
							echo '<tr><td colspan="2"><span class="wpmm-header-type">';
							echo htmlspecialchars(strtok($header['type'], ';')) . '</span></td></tr>';
							foreach ($header['entries'] as $entry) {
								echo '<tr><td><span class="wpmm-header-name">' . htmlspecialchars($entry['name']) . ':</span></td>';
								echo '<td><span class="wpmm-header-value">' . htmlspecialchars($entry['value']) . '</span></td></tr>';
							}
						}
						echo '</table>';
					}
					else
						echo '<span class="wpmm-notice">' . __('n/a', c_wpmm_text_domain) . '</span>';
				else
					echo '<span class="wpmm-notice">' . __('Gone', c_wpmm_text_domain) . '</span>';
			}
			catch (Exception $e) {
				$this->wpmm_log($user_ID, 'error', $e->getMessage());
				echo '<span class="wpmm-notice">' . htmlspecialchars($e->getMessage()) . '</span>';
			}
		}

		// Helper download file
		// http://www.longtailvideo.com/support/forum/JavaScript-Interaction/18437/Cross-browser-issue-with-file-download
		function wpmm_ajax_get_file($user_ID) {
			try {
				$msgid = $_GET[c_wpmm_param_msgid];
				$fileno = $_GET[c_wpmm_param_file];
				$list = $this->wpmm_mail_operation($user_ID, 'FILE', $msgid, $fileno);
				if (count($list))
					if (isset($list[0]['data'])) {
						if(ini_get('zlib.output_compression'))
							ini_set('zlib.output_compression', 'Off');
						header('Pragma: public');
						header('Expires: 0');
						header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
						header('Cache-Control: private', false);
						header('Content-Type: ' . $list[0]['data']['type']);
						header('Content-Disposition: attachment; filename="' . $list[0]['data']['name'] . '"');
						header('Content-Transfer-Encoding: binary');
						header('Content-Length: '. strlen($list[0]['data']['content']));
						echo $list[0]['data']['content'];
					}
					else {
						header('Content-Type: text/html; charset=' . get_option('blog_charset'));
						echo __('n/a', c_wpmm_text_domain);
					}
				else {
					header('Content-Type: text/html; charset=' . get_option('blog_charset'));
					echo __('Gone', c_wpmm_text_domain);
				}
			}
			catch (Exception $e) {
				$this->wpmm_log($user_ID, 'error', $e->getMessage());
				header('Content-Type: text/html; charset=' . get_option('blog_charset'));
				echo htmlspecialchars($e->getMessage());
			}
		}

		// Helper mail operations
		function wpmm_mail_operation($user_ID, $operation, $msgid = null, $fileno = null) {
			$list = array();
			$lkey = c_wpmm_session_cache . $user_ID;

			// Check cache
			if ($operation == 'LIST') {
				if (isset($_SESSION[$lkey])) {
					$age = $_SESSION[c_wpmm_session_age];
					$maxage = get_option(c_wpmm_option_cache_maxage);
					if ($maxage <= 0)
						$maxage = 15;
					if (time() - $age < $maxage * 60) {
						$this->wpmm_log($user_ID, 'verbose', $operation . ' from cache');
						return $_SESSION[$lkey];
					}
				}
			}
			else if ($operation == 'GET' || $operation == 'HTML' || $operation == 'FILE') {
				$mkey = c_wpmm_session_cache . '_' . $operation . '_' . $msgid . '_' . $fileno;
				if (isset($_SESSION[$mkey])) {
					if ($operation == 'GET')
						$list[0]['text'] = $_SESSION[$mkey];
					else if ($operation == 'HTML')
						$list[0]['html'] = $_SESSION[$mkey];
					else
						$list[0]['data'] = $_SESSION[$mkey];
					$this->wpmm_log($user_ID, 'verbose', $operation . ':' . $fileno . ' from cache');
					return $list;
				}
			}
			else if ($operation == 'HEADERS') {
				if (isset($_SESSION[$lkey])) {
					$slist = $_SESSION[$lkey];
					for ($i = 0 ; $i < count($slist); $i++)
						if ($slist[$i]['id'] == $msgid) {
							$list[0]['id'] = $msgid;
							$list[0]['headers'] = $slist[$i]['headers'];
							break;
						}
				}
				$this->wpmm_log($user_ID, 'verbose', $operation . ' from cache');
				return $list;
			}

			// Log operation
			$this->wpmm_log($user_ID, 'verbose', $operation . ':' . $fileno);

			try {
				// Get known messages
				if ($operation == 'LIST' || $operation == 'CHECK' || $operation == 'CRON') {
					$known = get_usermeta($user_ID, c_wpmm_meta_known);
					if (!$known)
						$known = array();
					$known_new = array();
				}

				// Fetch message list
				$mail = $this->wpmm_mail_connect_storage($user_ID);
				if ($mail) {
					for ($i = $mail->countMessages(); $i > 0; $i--) {
						// Get message data
						$msg_id = $this->wpmm_get_header($mail[$i], 'message-id');
						$msg_date = $this->wpmm_get_header($mail[$i], 'date');
						$msg_from = $this->wpmm_get_header($mail[$i], 'from');
						$msg_to = $this->wpmm_get_header($mail[$i], 'to');
						$msg_subj = $this->wpmm_get_header($mail[$i], 'subject');

						// Generate message id
						$id = md5($msg_id . $msg_date . $msg_from . $msg_to . $msg_subj);

						// Delete message
						if ($operation == 'DELETE') {
							if ($msgid && $msgid == $id) {
								// Delete message
								$mail->removeMessage($i);

								// Update cache
								if (isset($_SESSION[$lkey])) {
									$slist = $_SESSION[$lkey];
									for ($i = 0 ; $i < count($slist); $i++)
										if ($slist[$i]['id'] == $msgid) {
											unset($slist[$i]);
											$_SESSION[$lkey] = array_values($slist);
											break;
										}
								}

								// Prepare result
								$list[0]['id'] = $msgid;
								break;
							}
						}

						// Get message
						else if ($operation == 'GET' || $operation == 'HTML') {
							if ($msgid && $msgid == $id) {
								// Get body
								$html = ($operation == 'HTML');
								$body = $this->wpmm_get_body($mail[$i], $html);
								if ($body) {
									$text = $this->wpmm_get_decoded_text($body);

									// Convert html
									if (!$html) {
										$ctheader = $this->wpmm_get_header($body, 'content-type');
										if (strtolower(strtok($ctheader, ';')) == 'text/html')
											$text = $this->wpmm_html2text($text);
									}

									// Prepare result
									if (!$html)
										$text = htmlspecialchars($text);
									$list[0][$html ? 'html' : 'text'] = $text;
									$_SESSION[$mkey] = $text;
								}

								// Update cache
								if (isset($_SESSION[$lkey])) {
									$slist = $_SESSION[$lkey];
									for ($i = 0 ; $i < count($slist); $i++)
										if ($slist[$i]['id'] == $msgid) {
											$slist[$i]['new'] = false;
											$_SESSION[$lkey] = $slist;
											break;
										}
								}

								// Prepare result
								$list[0]['id'] = $msgid;
								break;
							}
						}

						// Get file
						else if ($operation == 'FILE') {
							if ($msgid && $msgid == $id) {
								// Get headers/parts
								$message = $mail[$i];
								$headers = array();
								$h = $this->wpmm_list_headers($message);
								$h['part'] = $message;
								$headers[] = $h;
								if ($message->isMultipart())
									foreach (new RecursiveIteratorIterator($message) as $part) {
										$h = $this->wpmm_list_headers($part);
										$h['part'] = $part;
										$headers[] = $h;
									}

								// Find file
								$i = 0;
								foreach ($headers as $header) {
									foreach ($header['entries'] as $entry) {
										if (strtolower($entry['name']) == 'content-disposition') {
											foreach (explode(';', $entry['value']) as $nv) {
												$nvs = explode('=', $nv);
												if (strtolower(trim($nvs[0])) == 'filename') {
													$name = trim(str_replace('"', '', $nvs[1]));
													if ($i == $fileno) {
														// Get type
														$ct = $this->wpmm_get_header($header['part'], 'content-type');
														$type = strtolower(strtok($ct, ';'));

														// Get decoded text
														$text = $this->wpmm_get_decoded_text($header['part'], false);

														// Build file data
														$data = array(
															type => $type,
															name => $name,
															content => $text);
														$list[0]['data'] = $data;
														$_SESSION[$mkey] = $data;
													}
													$i++;
												}
											}
										}
									}
								}

								// Prepare result
								$list[0]['id'] = $msgid;
								break;
							}
						}
						else {
							// Get message meta
							$idx = count($list);
							$list[$idx]['id'] = $id;
							$list[$idx]['from'] = $this->wpmm_parse_addr($msg_from);
							$list[$idx]['to'] = $this->wpmm_parse_addr($msg_to);
							$list[$idx]['date'] = $msg_date;
							$list[$idx]['reply'] = $this->wpmm_parse_addr($this->wpmm_get_header($mail[$i], 'reply-to'));

							// Check for bulk
							$bulk = (strpos($msg_subj, '[Bulk]') === 0);
							if ($bulk)
								$msg_subj = substr($msg_subj, 7);

							// Check empty subject
							if (!$msg_subj || $msg_subj == '')
								$msg_subj = '>>';

							// Check if new
							$new = !in_array($id, $known);

							$list[$idx]['bulk'] = $bulk;
							$list[$idx]['subject'] = $msg_subj;
							$list[$idx]['new'] = $new;

							// List headers
							$message = $mail[$i];
							$headers = array();
							$headers[] = $this->wpmm_list_headers($message);
							if ($message->isMultipart())
								foreach (new RecursiveIteratorIterator($message) as $part)
									$headers[] = $this->wpmm_list_headers($part);
							$list[$idx]['headers'] = $headers;

							// Check for html
							$list[$idx]['has_html'] = ($this->wpmm_get_body($message, true) != null);

							// Check attachments
							$files = array();
							foreach ($headers as $header)
								foreach ($header['entries'] as $entry)
									if (strtolower($entry['name']) == 'content-disposition')
										foreach (explode(';', $entry['value']) as $nv) {
											$nvs = explode('=', $nv);
											if (strtolower(trim($nvs[0])) == 'filename')
												$files[] = trim(str_replace(',', '-', str_replace('"', '', $nvs[1])));
										}
							$list[$idx]['files'] = $files;

							// Cron: get message text
							if ($operation == 'CRON') {
								if (!$bulk && $new) {
									// Get body
									$body = $this->wpmm_get_body($mail[$i]);
									if ($body) {
										$text = $this->wpmm_get_decoded_text($body);

										// Convert html
										$ctheader = $this->wpmm_get_header($body, 'content-type');
										if (strtolower(strtok($ctheader, ';')) == 'text/html')
											$text = $this->wpmm_html2text($text);

										// Prepare result
										$list[$idx]['text'] = $text;
									}
								}
							}
						}

						// Update known messages
						if ($operation == 'LIST' || $operation == 'CHECK' || $operation == 'CRON')
							$known_new[] = $id;
					}
				}

				// Update known messages
				if ($operation == 'LIST' || $operation == 'CHECK' || $operation == 'CRON')
					update_usermeta($user_ID, c_wpmm_meta_known, $known_new);

				// Update cache
				if ($operation == 'LIST' || $operation == 'CHECK') {
					$_SESSION[$lkey] = $list;
					$_SESSION[c_wpmm_session_age] = time();
				}

				$this->wpmm_log($user_ID, 'info', $operation . ' ' . count($list) . ' records');
			}
			catch (Exception $e) {
				$this->wpmm_log($user_ID, 'error', $e->getMessage());
				throw $e;
			}

			return $list;
		}

		// Helper connect to mail storage
		function wpmm_mail_connect_storage($user_ID) {
			$rx = get_usermeta($user_ID, c_wpmm_meta_mail_rx);
			if ($rx == 'POP3') {
				// Get configuration
				$host = get_usermeta($user_ID, c_wpmm_meta_pop3_host);
				$port = get_usermeta($user_ID, c_wpmm_meta_pop3_port);
				$user = get_usermeta($user_ID, c_wpmm_meta_pop3_user);
				$pwd = get_usermeta($user_ID, c_wpmm_meta_pop3_pwd);
				$ssl = get_usermeta($user_ID, c_wpmm_meta_pop3_ssl);

				// Build configuration
				$config = array();
				$config['host'] = $host;
				if ($port)
					$config['port'] = $port;
				$config['user'] = $user;
				$config['password'] = $pwd;
				if ($ssl)
					$config['ssl'] = $ssl;

				// Connect to storage
				return new Zend_Mail_Storage_Pop3($config);
			}
			else if ($rx == 'IMAP') {
				// Get configuration
				$host = get_usermeta($user_ID, c_wpmm_meta_imap_host);
				$port = get_usermeta($user_ID, c_wpmm_meta_imap_port);
				$user = get_usermeta($user_ID, c_wpmm_meta_imap_user);
				$pwd = get_usermeta($user_ID, c_wpmm_meta_imap_pwd);
				$ssl = get_usermeta($user_ID, c_wpmm_meta_imap_ssl);
				$folder = get_usermeta($user_ID, c_wpmm_meta_imap_folder);

				// Build configuration
				$config = array();
				$config['host'] = $host;
				if ($port)
					$config['port'] = $port;
				$config['user'] = $user;
				$config['password'] = $pwd;
				if ($ssl)
					$config['ssl'] = $ssl;
				if ($folder)
					$config['folder'] = $ssl;

				// Connect to storage
				return new Zend_Mail_Storage_Imap($config);
			}
			else
				throw new Exception(__('Not configured', c_wpmm_text_domain));
		}

		// Helper send email
		function wpmm_send_email($user_ID, $to, $cc, $subj, $text) {
			try
			{
				// Select transport
				$tx = get_usermeta($user_ID, c_wpmm_meta_mail_tx);
				if ($tx == 'Sendmail')
					Zend_Mail::setDefaultTransport(new Zend_Mail_Transport_Sendmail());
				else if ($tx == 'SMTP') {
					// Get configuration
					$host = get_usermeta($user_ID, c_wpmm_meta_smtp_host);
					$port = get_usermeta($user_ID, c_wpmm_meta_smtp_port);
					$auth = get_usermeta($user_ID, c_wpmm_meta_smtp_auth);
					$user = get_usermeta($user_ID, c_wpmm_meta_smtp_user);
					$pwd = get_usermeta($user_ID, c_wpmm_meta_smtp_pwd);
					$ssl = get_usermeta($user_ID, c_wpmm_meta_smtp_ssl);

					// Build configuration
					$config = array();
					if ($port)
						$config['port'] = $port;
					if ($auth) {
						$config['auth'] = $auth;
						$config['username'] = $user;
						$config['password'] = $pwd;
					}
					if ($ssl)
						$config['ssl'] = $ssl;

					// Setup transport
					Zend_Mail::setDefaultTransport(new Zend_Mail_Transport_Smtp($host, $config));
				}

				// Get data
				$from_name = get_usermeta($user_ID, c_wpmm_meta_mail_name);
				$from_addr = get_usermeta($user_ID, c_wpmm_meta_mail_addr);

				// Build message
				$mail = new Zend_Mail(get_option('blog_charset'));
				$mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
				$mail->setBodyText($text);
				$mail->setFrom($from_addr, $from_name);
				$mail->setReplyTo($from_addr, $from_name);
				foreach ($to as $topart)
					$mail->addTo($topart['email'], $topart['name']);
				if ($cc)
					foreach ($cc as $ccpart)
					$mail->addCc($ccpart['email'], $ccpart['name']);
				$mail->setSubject($subj);
				$mail->addHeader('X-Mailer', 'WP-Mini-Mail');

				// Send message
				$mail->send();
				$result = 'OK';
			}
			catch (Exception $e) {
				$result = $e->getMessage();
			}

			// Process result
			$this->wpmm_log($user_ID, 'info', $this->wpmm_format_addr($to) . '/' . $this->wpmm_format_addr($cc) . ': ' . htmlspecialchars($result));
			return $result;
		}

		// Helper send SMS
		function wpmm_send_sms($user_ID, $to, $text) {
			$ret = false;

			// Truncate message if necessary
			$maxlen = get_option(c_wpmm_option_sms_maxlen);
			if (strlen($text) > $maxlen)
				$text = substr($text, 0, $maxlen);

			// Replace unsupported characters
			$text = str_replace('€', 'E', $text);
			$text = str_replace('[', '(', $text);
			$text = str_replace(']', ')', $text);
			$text = str_replace('\\', '/', $text);
			$text = str_replace('^', '-', $text);
			$text = str_replace('{', '(', $text);
			$text = str_replace('}', ')', $text);
			$text = str_replace('|', '-', $text);
			$text = str_replace('~', '-', $text);

			// Build query
			$query = http_build_query(array(
				'username' => get_usermeta($user_ID, c_wpmm_meta_sms_user),
				'password' => get_usermeta($user_ID, c_wpmm_meta_sms_pwd),
				'from' => get_usermeta($user_ID, c_wpmm_meta_sms_from),
				'to' => $to,
				'text' => $text
			));

			// Get time out
			$timeout = intval(get_option(c_wpmm_option_http_timeout));
			if (!$timeout || $timeout <= 0)
				$timeout = 30;

			// Build query context
			$context = stream_context_create(array(
				'http' => array(
					'method'  => 'GET',
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'timeout' => $timeout
				)
			));

			// Run query
			$url = get_usermeta($user_ID, c_wpmm_meta_sms_url);
			$xml = @file_get_contents($url . '?' . $query, false, $context);
			if ($xml) {
				// Parse result
				$parser = new XMLParser($xml);
				$parser->Parse();

				// Get result
				$result = false;
				$description = false;
				$endcause = false;
				foreach ($parser->document->tagChildren as $child) {
					if ($child->tagName == 'result')
						$result = $child->tagData;
					else if ($child->tagName == 'description')
						$description = $child->tagData;
					else if ($child->tagName == 'endcause')
						$endcause = $child->tagData;
				}

				// Check result
				$ret = ($result ? 'OK' : $endcause . '/' . $description);
			}
			else {
				$error = error_get_last();
				$ret = $error['message'];
			}

			// Process result
			$this->wpmm_log($user_ID, 'info', $to . ': ' . $ret . ' "' . $text . '"');
			return $ret;
		}

		// Helper get decoded MIME header
		function wpmm_get_header($part, $name) {
			$value = false;
			if ($part->headerExists($name)) {
				$value = $part->getHeader($name, 'string');

				// Workaround Yahoo!
				$bulk = false;
				if ($name == 'subject')
					if (strpos($value, '[Bulk]') === 0) {
						$bulk = true;
						$value = substr($value, 7);
					}

				// "=?charset?encoding?encoded text?="
				$cencoding = 'ISO-8859-1';
				$q = strpos($value, '=?');
				if (($q === 0 || $q > 0) && strpos($value, '?=')) {
					$q = explode('?', $value);
					$cencoding = $q[1];
					$value = mb_decode_mimeheader($value);
				}

				// Convert character encoding
				$value = @mb_convert_encoding($value, get_option('blog_charset'), $cencoding);

				// Restore bulk flag
				if ($bulk)
					$value = '[Bulk] ' . $value;
			}
			else {
				// Workaround if no date
				if ($name == 'date' && $part->headerExists('received')) {
					$received = $part->getHeader('received', 'array');
					if (count($received) > 0) {
						$q = explode(';', $received[0]);
						$value = $q[count($q) - 1];
					}
				}
			}
			return $value;
		}

		// Helper list headers
		function wpmm_list_headers($part) {
			$entries = array();
			foreach ($part->getHeaders() as $name => $value) {
				if (is_string($value))
					$entries[] = array(
						name => $name,
						value => $value);
				else
					foreach ($value as $entry)
						$entries[] = array(
							name => $name,
							value => $entry);
			}
			return array(
				type => $this->wpmm_get_header($part, 'content-type'),
				entries => $entries);
		}

		// Helper parse address
		function wpmm_parse_addr($addr) {
			$result = array();
			$addr = trim($addr);
			while($addr) {
				$quote1 = strpos($addr, '"');
				if ($quote1 === false)
					$comma = strpos($addr, ',');
				else {
					$quote2 = strpos($addr, '"', $quote1 + 1);
					$comma = strpos($addr, ',', $quote2 ? $quote2 + 1 : $quote1 + 1);
				}
				if ($comma === false)
					$next = '';
				else {
					$next = substr($addr, $comma + 1);
					$addr = substr($addr, 0, $comma);
				}

				$lt = strpos($addr, '<');
				if ($lt === false)
					$result[] = array(
						name => '',
						email => trim($addr));
				else
					$result[] = array(
						name => trim(str_replace('"', '', substr($addr, 0, $lt - 1))),
						email => trim(str_replace('>', '', substr($addr, $lt + 1))));

				$addr = trim($next);
			}
			return $result;
		}

		// Helper format address
		function wpmm_format_addr($addr, $readable = false) {
			$result = array();
			foreach($addr as $apart)
				if ($readable)
					$result[] = ($apart['name'] ? $apart['name'] : $apart['email']);
				else
					$result[] = ($apart['name'] ? ('"' . $apart['name'] . '" <' . $apart['email'] . '>') : $apart['email']);
			return implode(', ', $result);
		}

		// Helper format data attributes
		function wpmm_format_data($text) {
			return str_replace('"', '&#34;', $text);
		}

		// Helper get message content
		function wpmm_get_body($message, $html = false) {
			if ($message->isMultipart()) {
				$partText = null;
				$partHtml = null;
				foreach (new RecursiveIteratorIterator($message) as $part) {
					$content_type = strtolower(strtok($this->wpmm_get_header($part, 'content-type'), ';'));
					if ($partText == null && $content_type == 'text/plain')
						$partText = $part;
					else if ($partHtml == null && $content_type == 'text/html')
						$partHtml = $part;
				}
				if ($html)
					return $partHtml;
				else
					if ($partText != null)
						return $partText;
					else if ($partHtml != null)
						return $partHtml;
					else
						return null;
			}
			else {
				$content_type = strtolower(strtok($this->wpmm_get_header($message, 'content-type'), ';'));
				if ($html)
					if ($content_type == 'text/html')
						return $message;
					else
						return null;
				else
					if ($content_type == 'text/plain' || $content_type == 'text/html')
						return $message;
					else
						return null;
			}
		}

		// Helper message decoding
		function wpmm_get_decoded_text($body, $charset = true) {
			$text = $body->getContent();

			// Check transfer encoding
			$tencoding = strtolower($this->wpmm_get_header($body, 'content-transfer-encoding'));
			if ($tencoding == 'base64')
				$text = base64_decode($text);
			if ($tencoding == 'quoted-printable')
				$text = quoted_printable_decode($text);

			// Check character encoding
			if ($charset) {
				$cencoding = 'ISO-8859-1';
				foreach (explode(';', $this->wpmm_get_header($body, 'content-type')) as $nv) {
					$nvs = explode('=', $nv);
					$name = strtolower(trim($nvs[0]));
					if ($name == 'charset') {
						$cencoding = trim(str_replace('"', '', $nvs[1]));
						break;
					}
				}
				$text = @mb_convert_encoding($text, get_option('blog_charset'), $cencoding);
			}

			return $text;
		}

		// Helper check environment
		function wpmm_check_prerequisites() {
			// Check PHP version
			if (version_compare(PHP_VERSION, '5.2.4', '<'))
				die('Mini Mail requires at least PHP 5.2.4, installed version is ' . PHP_VERSION);

			// Check WordPress version
			global $wp_version;
			if (version_compare($wp_version, '2.7') < 0)
				die('Mini Mail requires at least WordPress 2.7, installed version is ' . $wp_version);

			// Check basic prerequisities
			WPMiniMail::wpmm_check_function('register_activation_hook');
			WPMiniMail::wpmm_check_function('register_deactivation_hook');
			WPMiniMail::wpmm_check_function('add_action');
			WPMiniMail::wpmm_check_function('add_filter');
			WPMiniMail::wpmm_check_function('wp_register_style');
			WPMiniMail::wpmm_check_function('wp_enqueue_style');
			WPMiniMail::wpmm_check_function('wp_register_script');
			WPMiniMail::wpmm_check_function('wp_enqueue_script');
			WPMiniMail::wpmm_check_function('wp_next_scheduled');
			WPMiniMail::wpmm_check_function('base64_decode');
			WPMiniMail::wpmm_check_function('quoted_printable_decode');
			WPMiniMail::wpmm_check_function('mb_convert_encoding');
			WPMiniMail::wpmm_check_function('mb_decode_mimeheader');
			WPMiniMail::wpmm_check_function('md5');
		}

		function wpmm_check_function($name) {
			if (!function_exists($name))
				die('Required function "' . $name . '" does not exist');
		}

		// Helper clear cache
		function wpmm_clear_cache() {
			foreach($_SESSION as $key => $value)
				if (strpos($key, c_wpmm_session_cache) === 0)
					unset($_SESSION[$key]);
			unset($_SESSION[c_wpmm_session_age]);
		}

		// Helper log message
		function wpmm_log($user_ID, $severity, $text) {
			if (get_option(c_wpmm_option_debug) || $severity != 'verbose') {
				global $wpdb;
				$bt = debug_backtrace();
				$sql = 'INSERT INTO ' . $wpdb->prefix . c_wpmm_table_name . ' (time, user, severity, function, text)';
				$sql .= ' VALUES (now(), ' . $user_ID . ',"' . $severity . '","' . $wpdb->escape($bt[1]['function']) . '","' .$wpdb->escape($text) . '")';
				if ($wpdb->query($sql) === false)
					$wpdb->print_error();
			}
		}

		// Helper convert html to text
		function wpmm_html2text($text) {
			$h2t = new html2text($text);
			$h2t->width = -1;
			return $h2t->get_text();
		}

		// Helper to remove extra white space
		function wpmm_trimall($str, $charlist = "\t\n\r\0\x0B") {
			return trim(ereg_replace(' +', ' ', str_replace(str_split($charlist), ' ', $str)));
		}

		// Helper change file name extension
		function change_extension($filename, $new_extension) {
			return preg_replace('/\..+$/', $new_extension, $filename);
		}
	}
}

?>
