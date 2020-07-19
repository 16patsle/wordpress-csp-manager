<?php
/**
 * The core plugin
 *
 * @package CSP_Manager
 */

declare(strict_types=1);

namespace CSP_Manager;

defined('ABSPATH') || die('No script kiddies please!');

/**
 * The CSP core class
 * @since 1.0.0
 */
class Core {

    /**
	 * Set up actions and hooks
     * 
     * @since 1.0.0
     * @param string $pluginfile __FILE__ path to the main plugin file.
	 */
	public function __construct(string $pluginfile) {
		if(is_admin() && !wp_doing_ajax()) {
            require_once __DIR__ . '/Settings.php';
        
            $settings = new Settings($pluginfile);
        }

        add_action('init',[$this, 'csp_init']);
    }

    /**
     * Output CSP headers in init
     * 
     * @since 1.0.0
     */
    public function csp_init() {
        if (is_admin()) {
            // Admin
            $option = get_option('csp_manager_admin');
        } elseif (is_user_logged_in()) {
            // Logged-in
            $option = get_option('csp_manager_loggedin');
        } else {
            // Frontend
            $option = get_option('csp_manager_frontend');
        }

        if($option['mode'] !== 'disabled') {
            $header = 'Content-Security-Policy';

            if($option['mode'] === 'report') {
                $header .= '-Report-Only';
            }

            $content = '';

            foreach ($option as $directive => $policy) {
                if(strpos($directive, 'enable_') === 0 || $directive === 'mode') {
                    continue;
                }
                $content .= $directive . ' ' . $policy . '; ';
            }

            header(sprintf('%s: %s', $header, $content));
        }
    }

}