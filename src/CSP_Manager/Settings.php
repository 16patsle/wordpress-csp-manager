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
            'policy' => '',
            'mode' => 'disabled',
        ),
        'loggedin' => array(
            'policy' => '',
            'mode' => 'disabled',
        ),
        'frontend' => array(
            'policy' => '',
            'mode' => 'disabled',
        ),
	);

    /**
	 * Set up actions needed for the plugin's admin interface
     * 
     * @since 1.0.0
     * @param string $pluginfile __FILE__ path to the main plugin file.
	 */
	public function __construct($pluginfile) {
		add_action( 'admin_init', array($this, 'csp_settings_init'));
        add_action( 'admin_menu', array($this, 'csp_admin_menu'));
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
        register_setting('csp', 'csp_manager_admin');
		register_setting('csp', 'csp_manager_loggedin');
		register_setting('csp', 'csp_manager_frontend');

		add_settings_section(
			'csp_admin',
			__('Admin Policy', 'csp-manager'),
			function() {
                esc_html_e('Set the policy to be used in the WordPress admin interface.', 'csp-manager');
            },
			'csp'
		);

		add_settings_field(
			'csp_admin_policy',
			__('Admin Policy Header', 'lazysizes'),
			function() {
		        $this->csp_render_option_policy('admin', __('Enter a CSP header string to use for admin pages.', 'csp-manager'));
            },
			'csp',
			'csp_admin'
        );

        add_settings_field(
			'csp_admin_mode',
			__('Admin Policy Mode', 'lazysizes'),
			function() {
		        $this->csp_render_option_mode('admin');
            },
			'csp',
			'csp_admin'
        );
        
        add_settings_section(
			'csp_loggedin',
			__('Logged-in Policy', 'csp-manager'),
			function() {
                esc_html_e( 'Set the policy to be used in the frontend for logged-in users.', 'csp-manager' );
            },
			'csp'
        );

        add_settings_field(
			'csp_loggedin_policy',
			__('Logged-in Policy Header', 'lazysizes'),
			function() {
                $this->csp_render_option_policy('loggedin', __('Enter a CSP header string to use for logged-in users on the frontend pages.', 'csp-manager'));
            },
			'csp',
			'csp_loggedin'
        );

        add_settings_field(
			'csp_loggedin_mode',
			__('Logged-in Policy Mode', 'lazysizes'),
			function() {
		        $this->csp_render_option_mode('loggedin');
            },
			'csp',
			'csp_loggedin'
        );
        
        add_settings_section(
			'csp_frontend',
			__('Frontend Policy', 'csp-manager'),
			function() {
                esc_html_e('Set the policy to be used in for visitors to the site\'s frontend.', 'csp-manager');
            },
			'csp'
        );
        
        add_settings_field(
			'csp_frontend_policy',
			__('Frontend Policy Header', 'lazysizes'),
			function() {
		        $this->csp_render_option_policy('frontend', __('Enter a CSP header string to use for frontend visitors.', 'csp-manager'));
            },
			'csp',
			'csp_frontend'
        );

        add_settings_field(
			'csp_frontend_mode',
			__('Frontend Policy Mode', 'lazysizes'),
			function() {
		        $this->csp_render_option_mode('frontend');
            },
			'csp',
			'csp_frontend'
        );
    }

    public function csp_render_option_policy($option, $description) {
        ?>
		<label>
            <textarea name="csp_manager_<?php echo $option; ?>[policy]" cols="80" rows="5"><?php echo esc_textarea(get_option('csp_manager_' . $option)['policy']) ?></textarea>
			<p class="description">
			    <?php echo esc_html($description); ?>
		    </p>
		</label>
		<?php
    }

    public function csp_render_option_mode($option) {
        ?>
		<label>
            <input type="radio" name='csp_manager_<?php echo $option; ?>[mode]' <?php if(get_option('csp_manager_' . $option)['mode'] === 'enforce') echo 'checked'; ?> value="enforce">
            <?php esc_html_e('Enforce the Content Security Policy.', 'csp-manager'); ?>
		</label>
        <br>
        <label>
            <input type="radio" name='csp_manager_<?php echo $option; ?>[mode]' <?php if(get_option('csp_manager_' . $option)['mode'] === 'report') echo 'checked'; ?> value="report">
            <?php esc_html_e('Don\'t enforce Policy, run it in Report-Only mode.', 'csp-manager'); ?>
		</label>
        <br>
        <label>
            <input type="radio" name='csp_manager_<?php echo $option; ?>[mode]' <?php if(get_option('csp_manager_' . $option)['mode'] === 'disabled') echo 'checked'; ?> value="disabled">
            <?php esc_html_e('No CSP header is added.', 'csp-manager'); ?>
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
}