<?php
/**
 * The plugin settings file
 *
 * @package CSP_Manager
 */

declare(strict_types=1);

namespace CSP_Manager;

defined('ABSPATH') || die('No script kiddies please!');

/**
 * The CSP settings class
 * @since 1.0.0
 */
class Settings {

    /**
	 * The default settings.
	 *
     * @since 1.0.0
	 * @var array[]
	 */
	protected $defaults = [
		'admin' => [
            'mode' => 'report',
            'enable_default-src' => 1,
            'default-src' => '\'self\'',
        ],
        'loggedin' => [
            'mode' => 'disabled',
            'enable_default-src' => 1,
            'default-src' => '\'self\'',
        ],
        'frontend' => [
            'mode' => 'disabled',
            'enable_default-src' => 1,
            'default-src' => '\'self\'',
        ],
    ];
    
    /**
     * CSP directives and descriptions.
     * 
     * @since 1.0.0
     * @var string[]
     */
    protected $directives;

    /**
     * Store options in memory.
     * 
     * @since 1.0.0
     * @var array[]
     */
    private $options;

    /**
	 * Set up actions needed for the plugin's admin interface
     * 
     * @since 1.0.0
     * @param string $pluginfile __FILE__ path to the main plugin file.
	 */
	public function __construct(string $pluginfile) {
        $this->directives = [
            'base-uri' => [
                'description' => esc_html__('Allowed URLs for the base element, which sets the base URL used to resolve relative URLs.', 'csp-manager'),
            ],
            'frame-ancestors' => [
                'description' => esc_html__('Which sources can embed the page in a frame. Restricting this can prevent clickjacking attacks.', 'csp-manager'),
            ],
            'upgrade-insecure-requests' => [
                'description' => esc_html__('Force the browser to use HTTPS for all resources, even regular HTTP URLs. Site must support HTTPS.', 'csp-manager'),
            ],
            'form-action' => [
                'description' => esc_html__('Allowed targets for form submission.', 'csp-manager'),
            ],
            'trusted-types' => [
                'description' => esc_html__('Restrict creation of Trusted Types policies. Used together with require-trusted-types-for.', 'csp-manager'),
            ],
            'require-trusted-types-for' => [
                /* translators: %s: <code>'script'</code> */
                'description' => sprintf(esc_html__('When used with the value %s, Trusted Types will be required for various DOM functions. Helps prevent XSS vulnerabilities.', 'csp-manager'), '<code>\'script\'</code>'),
            ],
            'default-src' => [
                'description' => esc_html__('Fallback for the src directives.', 'csp-manager'),
            ],
            'connect-src' => [
                'description' => esc_html__('Allowed URLs for fetch/XMLHttpRequest, WebSocket etc.', 'csp-manager'),
            ],
            'script-src' => [
                /* translators: 1: <code>'unsafe-eval'</code> 2: <code>'unsafe-inline'</code> */
                'description' => sprintf(esc_html__('Allowed JavaScript sources. %1$s allows usage of eval, while %2$s allows inline scripts.', 'csp-manager'), '<code>\'unsafe-eval\'</code>', '<code>\'unsafe-inline\'</code>'),
            ],
            'style-src' => [
                /* translators: 1: <code>'unsafe-eval'</code> 2: <code>'unsafe-inline'</code> */
                'description' => sprintf(esc_html__('Allowed style sources. %1$s allows usage of eval, while %2$s allows inline styles.', 'csp-manager'), '<code>\'unsafe-eval\'</code>', '<code>\'unsafe-inline\'</code>'),
            ],
            'img-src' => [
                'description' => esc_html__('Allowed sources for images (including favicons).', 'csp-manager'),
            ],
            'media-src' => [
                'description' => esc_html__('Allowed audio/video sources.', 'csp-manager'),
            ],
            'font-src' => [
                'description' => esc_html__('Allowed web font file sources.', 'csp-manager'),
            ],
            'frame-src' => [
                'description' => esc_html__('Allowed sources for frame elements.', 'csp-manager'),
            ],
            'manifest-src' => [
                'description' => esc_html__('Allowed sources for web app manifests.', 'csp-manager'),
            ],
            'object-src' => [
                /* translators: %s: <code>'none'</code> */
                'description' => sprintf(esc_html__('Allowed sources for Flash content, Java applets or other content loaded using object, embed or applet tags. Recommended to set to %s if you\'re not using these types of content.', 'csp-manager'), '<code>\'none\'</code>'),
            ],
            'prefetch-src' => [
                /* translators: 1: <code>&lt;link rel="prefetch"&gt;</code> 2: <code>&lt;link rel="prerender"&gt;</code> */
                'description' => sprintf(esc_html__('Allowed sources in %1$s and %2$s elements.', 'csp-manager'), '<code>&lt;link rel="prefetch"&gt;</code>', '<code>&lt;link rel="prerender"&gt;</code>'),
            ],
            'script-src-elem' => [
                'description' => esc_html__('Allowed sources for script elements, falls back to script-src if missing.', 'csp-manager'),
            ],
            'script-src-attr' => [
                'description' => esc_html__('Allowed inline event handler sources, falls back to script-src if missing.', 'csp-manager'),
            ],
            'style-src-elem' => [
                'description' => esc_html__('Allowed sources for style and stylesheet link elements, falls back to style-src if missing.', 'csp-manager'),
            ],
            'style-src-attr' => [
                'description' => esc_html__('Allowed inline style sources, falls back to style-src if missing.', 'csp-manager'),
            ],
            'worker-src' => [
                'description' => esc_html__('Allowed sources for web workers and service workers.', 'csp-manager'),
            ],
            'report-uri' => [
                'description' => esc_html__('URL to send a report to when policy violations happen. Prefer usage of report-to instead, this directive should only be used for compatibility purposes.', 'csp-manager'),
            ],
            'report-to' => [
                'description' => esc_html__('Reporting group name to send violation reports to. Used together with the Report-To header, which defines these report groups and where to send the reports.', 'csp-manager'),
            ],
        ];

        $this->categories = [
            'general' => [
                'title' => 'General directives',
                'description' => esc_html__('TODO', 'csp-manager')
            ],
            'resources' => [
                'title' => 'Resource directives',
                'description' => esc_html__('TODO', 'csp-manager')
            ],
        ];

        $this->options = [
            'admin' => get_option('csp_manager_admin'),
            'loggedin' => get_option('csp_manager_loggedin'),
            'frontend' => get_option('csp_manager_frontend')
        ];

		add_action('admin_init', [$this, 'csp_settings_init']);
        add_action('admin_menu', [$this, 'csp_admin_menu']);
        // If this is the first time we've enabled the plugin, setup default settings.
		register_activation_hook($pluginfile, [$this, 'first_time_activation']);
    }

    /**
	 * Runs on first activation, sets default settings
	 *
	 * @since 1.0.0
	 */
	public function first_time_activation(): void {
		$defaults = $this->defaults;
		foreach ($defaults as $key => $val) {
			if (get_option('csp_manager_' . $key, false) === false) {
				update_option('csp_manager_' . $key, $val);
			}
		}
	}
    
    /**
	 * Registers the settings with WordPress
	 *
	 * @since 1.0.0
	 */
	public function csp_settings_init(): void {
        $this->csp_add_settings(
            'admin',
            __('Admin Policy', 'csp-manager'),
            __('Set the policy to be used in the WordPress admin interface.', 'csp-manager')
        );

        $this->csp_add_settings(
            'loggedin',
            __('Logged-in Policy', 'csp-manager'),
            __('Set the policy to be used for logged-in users on the frontend pages.', 'csp-manager')
        );

        $this->csp_add_settings(
            'frontend',
            __('Frontend Policy', 'csp-manager'),
            __('Set the policy to be used for visitors to the site\'s frontend.', 'csp-manager')
        );
    }

    /**
     * Display all settings for the internal option called $name.
     * 
     * @since 1.0.0
     * @param string $name Current internal option, either 'admin', 'loggedin' or 'frontend'.
     * @param string $title The title to use for the settings section.
     * @param string $description The description to use for the settings section.
     */
    public function csp_add_settings(string $name, string $title, string $description): void {
        register_setting('csp', 'csp_manager_' . $name);

        add_settings_section(
			'csp_' . $name,
			$title,
			function() use($description) {
                echo esc_html($description);
            },
			'csp'
        );
        
        add_settings_field(
            'csp_' . $name . '_mode',
            /* translators: %s: Translated version of either 'Admin Policy', 'Logged-in Policy' or 'Frontend Policy' */
			sprintf(__('%s Mode', 'csp-manager'), $title),
			function() use($name) {
		        $this->csp_render_option_mode($name);
            },
			'csp',
			'csp_' . $name . '_general',
        );

        foreach ($this->categories as $category => $category_object) {
            add_settings_section(
                'csp_' . $name . '_' . $category,
                $category_object['title'],
                function() use($category_object) {
                    echo esc_html($category_object['description']);
                },
                'csp'
            );
        }

        foreach ($this->directives as $directive => $directive_object) {
            $this->csp_add_directive_setting($name, $directive, $directive_object);
        }

        add_settings_field(
            'csp_' . $name . '_reportto',
            /* translators: %s: Translated version of either 'Admin Policy', 'Logged-in Policy' or 'Frontend Policy' */
			sprintf(__('%s Report-To Header', 'csp-manager'), $title),
			function() use($name) {
                ?>
		            <label>
                        <textarea name="csp_manager_<?php echo $name; ?>[header_reportto]" cols="80" rows="2"><?php echo $this->get_textarea_option($name, 'header_reportto'); ?></textarea>
		            	<p class="description">
		            	    <?php esc_html_e('Set the value of the Report-To header, used together with the report-to directive. The header will only be sent if a value is set.', 'csp-manager'); ?>
		                </p>
		            </label>
                <?php
            },
			'csp',
			'csp_' . $name . '_general',
        );
    }

    /**
     * Display the policy text box for $option's $directive setting.
     * 
     * @since 1.0.0
     * @param string $option Current internal option, either 'admin', 'loggedin' or 'frontend'.
     * @param string $directive The CSP directive to create textbox for.
     * @param array $directive_object Array with related data such as description for the directive's text area, HTML escaped if necessary.
     */
    public function csp_add_directive_setting(string $option, string $directive, array $directive_object): void {
        /* translators: %s: A CSP directive like 'default-src' */
        $policy_string = __('Policy: %s', 'csp-manager');

        $description = $directive_object['description'];
        $category = !empty($directive_object['category']) ? $directive_object['category'] : 'general';

        add_settings_field(
			'csp_' . $option . '_' . $directive,
			sprintf($policy_string, $directive),
			function() use($option, $directive, $description) {
		        ?>
		        <fieldset>
                    <label>
		        		<input type="checkbox" name="csp_manager_<?php echo $option; ?>[enable_<?php echo $directive; ?>]" <?php checked($this->get_directive_enabled_option($option, $directive), 1, true); ?> value="1">
		        		<?php esc_html_e( 'Enable', 'csp-manager' ); ?>
		        	</label>
                    <br>
                    <label>
                        <textarea name="csp_manager_<?php echo $option; ?>[<?php echo $directive; ?>]" cols="80" rows="2"><?php echo $this->get_textarea_option($option, $directive); ?></textarea>
		            	<p class="description">
		            	    <?php echo $description; ?>
		                </p>
		            </label>
                </fieldset>
		        <?php
            },
			'csp',
			'csp_' . $option . '_' . $category
        );
    }

    /**
     * Display the mode radio buttons for $option
     * 
     * @since 1.0.0
     * @param string $option Current option, either 'admin', 'loggedin' or 'frontend'.
     */
    public function csp_render_option_mode(string $option): void {
        ?>
		<label>
            <input type="radio" name='csp_manager_<?php echo $option; ?>[mode]' <?php checked(get_option('csp_manager_' . $option)['mode'], 'enforce', true); ?> value="enforce">
            <?php esc_html_e('Enforce', 'csp-manager'); ?>
            <p class="description">
			    <?php esc_html_e('Enforce the Content Security Policy.', 'csp-manager'); ?>
		    </p>
		</label>
        <br>
        <label>
            <input type="radio" name='csp_manager_<?php echo $option; ?>[mode]' <?php checked(get_option('csp_manager_' . $option)['mode'], 'report', true); ?> value="report">
            <?php esc_html_e('Report-Only', 'csp-manager'); ?>
            <p class="description">
            <?php esc_html_e('Don\'t enforce the policy, run it in Report-Only mode.', 'csp-manager'); ?>
		    </p>
		</label>
        <br>
        <label>
            <input type="radio" name='csp_manager_<?php echo $option; ?>[mode]' <?php checked(get_option('csp_manager_' . $option)['mode'], 'disabled', true); ?> value="disabled">
            <?php esc_html_e('Disabled.', 'csp-manager'); ?>
            <p class="description">
            <?php esc_html_e('Don\'t add a CSP header.', 'csp-manager'); ?>
		    </p>
		</label>
		<?php
    }

    /**
	 * Adds an entry in the sidebar
	 *
	 * @since 1.0.0
	 */
    public function csp_admin_menu(): void {
		$admin_page = add_options_page(
            'Content Security Policy Manager',
            'CSP Manager',
            'manage_options',
            'csp-manager',
            function() {
            ?>
		    <div class="wrap">
			    <h2><?php esc_html_e('Content Security Policy Manager', 'csp-manager'); ?></h2>
			    <form id="csp_settings" action='options.php' method='post' style='clear:both;'>
			    	<?php
                    settings_fields('csp');
                    submit_button();

                    global $wp_settings_sections;
                    
                    foreach ( (array) $wp_settings_sections[ 'csp' ] as $section ) {
                        // Matches the section csp_manager_admin, but not csp_manager_admin_general.
                        if (preg_match('/csp_[a-z]+$/', $section['id'])) {
                            ?>
                            <h2 style="font-size: 1.6em;"><?php echo $section['title'] ?></h2>
                            <?php
                            call_user_func( $section['callback'], $section );
                        } else {
                            ?>
                            <details style="margin: 15px 0;">
                                <summary>
                                    <h3 style="display: inline;"><?php echo $section['title'] ?></h3>
                                </summary>
                                <?php
                                call_user_func( $section['callback'], $section );
                                ?>
                                <table class="form-table" role="presentation">
                                <?php
                                do_settings_fields( 'csp', $section['id'] );
                                ?>
                                </table>
                                <?php
                                submit_button();
                                ?>
                            </details>
                            <?php
                        }
                        
                    }
			    	?>
			    </form>
		    </div>
		    <?php
        } );
    }

    /**
     * Get the value of a CSP directive policy option, for use in a text area
     * 
     * @since 1.0.0
     * @param string $option Either 'admin', 'loggedin' or 'frontend'.
     * @param string $directive A CSP directive to get the policy for.
     * @return string Value of directive, or empty string.
     */
    public function get_textarea_option(string $option, string $directive): string {
        if(isset($this->options[$option][$directive])) {
            return esc_textarea($this->options[$option][$directive]);
        } else {
            return '';
        }
    }

    /**
     * Get the enabled value of a CSP directive option, for use in a checkbox
     * 
     * @since 1.0.0
     * @param string $option Either 'admin', 'loggedin' or 'frontend'.
     * @param string $directive A CSP directive to check if enabled.
     * @return int Enabled status of the directive, else 0.
     */
    public function get_directive_enabled_option(string $option, string $directive): int {
        if(isset($this->options[$option]['enable_' . $directive])) {
            return (int)$this->options[$option]['enable_' . $directive];
        } else {
            return 0;
        }
    }
}
