<?php
/**
 * The plugin settings file
 *
 * @package CSP_Manager
 */

namespace CSP_Manager;

defined('ABSPATH') || die('No script kiddies please!');

/**
 * The CSP settings class
 * @since 1.0.0
 */
class Settings {

    /**
	 * The default settings
	 *
     * @since 1.0.0
	 * @var array[]
	 */
	protected $defaults = array(
		'admin' => array(
            'mode' => 'disabled',
            'enable_default-src' => 1,
            'enable_script-src' => 1,
            'enable_style-src' => 1,
            'enable_img-src' => 1,
        ),
        'loggedin' => array(
            'mode' => 'disabled',
            'enable_default-src' => 1,
            'enable_script-src' => 1,
            'enable_style-src' => 1,
            'enable_img-src' => 1,
        ),
        'frontend' => array(
            'mode' => 'disabled',
            'enable_default-src' => 1,
            'enable_script-src' => 1,
            'enable_style-src' => 1,
            'enable_img-src' => 1,
        ),
    );
    
    protected const DIRECTIVES = [
        'default-src',
        'script-src',
        'style-src',
        'img-src',
        'media-src',
        'font-src',
        'connect-src',
        'frame-src',
        'manifest-src',
        'object-src',
        'prefetch-src',
        'script-src-elem',
        'script-src-attr',
        'style-src-elem',
        'style-src-attr',
        'worker-src',
    ];

    /**
	 * Set up actions needed for the plugin's admin interface
     * 
     * @since 1.0.0
     * @param string $pluginfile __FILE__ path to the main plugin file.
	 */
	public function __construct($pluginfile) {
		add_action('admin_init', array($this, 'csp_settings_init'));
        add_action('admin_menu', array($this, 'csp_admin_menu'));
        // If this is the first time we've enabled the plugin, setup default settings.
		register_activation_hook($pluginfile, array($this, 'first_time_activation'));
    }

    /**
	 * Runs on first activation, sets default settings
	 *
	 * @since 1.0.0
	 */
	public function first_time_activation() {
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
	public function csp_settings_init() {
        $this->options = [
            'admin' => get_option('csp_manager_admin'),
            'loggedin' => get_option('csp_manager_loggedin'),
            'frontend' => get_option('csp_manager_frontend')
        ];

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

    public function csp_add_settings($name, $title, $description) {
        register_setting('csp', 'csp_manager_' . $name);

        add_settings_section(
			'csp_' . $name,
			$title,
			function() use($description) {
                echo esc_html($description);
            },
			'csp'
		);

        $this->csp_add_directive_setting($name, 'default-src');

        add_settings_field(
			'csp_' . $name . '_mode',
			sprintf(__('%s Mode', 'csp-manager'), $title),
			function() use($name) {
		        $this->csp_render_option_mode($name);
            },
			'csp',
			'csp_' . $name
        );
    }

    public function csp_add_directive_setting($name, $directive) {
        add_settings_field(
			'csp_' . $name . '_' . $directive,
			sprintf(__('Policy: %s', 'csp-manager'), $directive),
			function() use($name, $directive) {
		        $this->csp_render_option_policy($name, $directive, 'Temp description');
            },
			'csp',
			'csp_' . $name
        );
    }

    /**
     * Display the policy text box for $option
     * 
     * @since 1.0.0
     * @param string $option Current internal option, either 'admin', 'loggedin' or 'frontend'.
     * @param string $directive The CSP directive to create textbox for.
     * @param string $description Description for the text area.
     */
    public function csp_render_option_policy($option, $directive, $description) {
        ?>
		<fieldset>
            <label>
				<input type="checkbox" name="csp_manager_<?php echo $option; ?>[enable_<?php echo $directive; ?>]" <?php checked($this->get_checkbox_option($option, 'enable_' . $directive), 1, true); ?> value="1">
				<?php esc_html_e( 'Enable', 'csp-manager' ); ?>
			</label>
            <br>
            <label>
                <textarea name="csp_manager_<?php echo $option; ?>[<?php echo $directive; ?>]" cols="80" rows="5"><?php echo $this->get_textarea_option($option, $directive); ?></textarea>
		    	<p class="description">
		    	    <?php echo esc_html($description); ?>
		        </p>
		    </label>
        </fieldset>
		<?php
    }

    /**
     * Display the mode radio buttons for $option
     * 
     * @since 1.0.0
     * @param string $option Current option, either 'admin', 'loggedin' or 'frontend'.
     */
    public function csp_render_option_mode($option) {
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
	public function csp_admin_menu() {
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
			    	do_settings_sections('csp');
			    	submit_button();
			    	?>
			    </form>
		    </div>
		    <?php
        } );
    }

    public function get_textarea_option($option, $directive) {
        if(isset($this->options[$option][$directive])) {
            return esc_textarea($this->options[$option][$directive]);
        } else {
            return '';
        }
    }

    public function get_checkbox_option($option, $directive) {
        if(isset($this->options[$option][$directive])) {
            return $this->options[$option][$directive];
        } else {
            return 0;
        }
    }
}