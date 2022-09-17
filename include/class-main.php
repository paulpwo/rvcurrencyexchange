<?php

// Block direct access to file
defined('ABSPATH') or die('Not Authorized!');

class Avcurrency_Exchange
{

	private $settings;

	private $metaboxes = array();

	private $widgets = array();

	private $shortcodes = array();

	private $toolbars = array();

	private $taxonomies = array();

	public function __construct()
	{

		// Plugin uninstall hook
		register_uninstall_hook(RVCURRENCY_FILE, array(__CLASS__, 'plugin_uninstall'));

		// Plugin activation/deactivation hooks
		register_activation_hook(RVCURRENCY_FILE, array($this, 'plugin_activate'));
		register_deactivation_hook(RVCURRENCY_FILE, array($this, 'plugin_deactivate'));

		// Plugin Actions
		add_action('plugins_loaded', array($this, 'plugin_init'));

		// User
		add_action('wp_enqueue_scripts', array($this, 'plugin_enqueue_scripts'));

		// Admin
		add_filter('mce_css', array($this, 'plugin_add_editor_style'));
		add_action('admin_enqueue_scripts', array($this, 'plugin_enqueue_admin_scripts'));
		add_action('admin_init', array($this, 'plugin_register_settings'));
		add_action('admin_menu', array($this, 'plugin_add_settings_pages'));

		// Register plugin widgets
		add_action('widgets_init', function () {
			foreach ($this->widgets as $widgetName => $widgetPath) {
				include_once(RVCURRENCY_INCLUDE_DIR . $widgetPath);
				register_widget($widgetName);
			}
		});

		// Init plugin shortcodes
		foreach ($this->shortcodes as $className => $path) {
			include_once(RVCURRENCY_INCLUDE_DIR . $path);
			new $className();
		}

		// Init plugin metaboxes
		foreach ($this->metaboxes as $className => $path) {
			include_once(RVCURRENCY_INCLUDE_DIR . $path);
			new $className();
		}

		// Init plugin taxonomies
		foreach ($this->taxonomies as $className => $path) {
			include_once(RVCURRENCY_INCLUDE_DIR . $path);
			new $className();
		}

		// Init plugin toolbars
		foreach ($this->toolbars as $className => $path) {
			include_once(RVCURRENCY_INCLUDE_DIR . $path);
			new $className();
		}
	}

	/**
	 * Plugin uninstall function
	 * called when the plugin is uninstalled
	 * @method plugin_uninstall
	 */
	public static function plugin_uninstall()
	{
	}

	/**
	 * Plugin activation function
	 * called when the plugin is activated
	 * @method plugin_activate
	 */
	public function plugin_activate()
	{
	}

	/**
	 * Plugin deactivate function
	 * is called during plugin deactivation
	 * @method plugin_deactivate
	 */
	public function plugin_deactivate()
	{
	}

	/**
	 * Plugin init function
	 * init the polugin textDomain
	 * @method plugin_init
	 */
	function plugin_init()
	{

		load_plugin_textDomain('rvcurrencyexchange', false, dirname(RVCURRENCY_DIR_BASENAME) . '/languages');
		add_action('wp_head', array($this, 'plugin_add_custom_css'));
		add_action('wp_footer', array($this, 'loadBotton')); //cargar en el foofte
	}
	function plugin_add_custom_css()
	{
		$this->settings = get_option('rvcurrencyexchange_main_options');
		$custom_css = $this->settings['special_options'];
		if (!empty($custom_css)) {
			echo '<style type="text/css"> /* Developer: paulpwo */' . $custom_css . '</style>';
		}
	}
	function loadBotton()
	{
		$this->settings = get_option('rvcurrencyexchange_main_options');

		if ($this->settings) {
?>
			<script>
				jQuery(function($) {
					setTimeout(function() {
						jQuery('.count').each(function() {
							var duration = parseInt($(this).data('duration'));
							var amount = $(this).data('amount')
							var isDecimal = false;
							try {
								amount = amount.replace(',', '.');
								isDecimal = true;
							} catch (e) {}
							var amount = parseFloat(amount);

							$(this).fadeIn(200);
							$(this).prop('Counter', 0).animate({
								Counter: amount
							}, {
								duration: duration,
								easing: 'swing',
								step: function(now) {
									if (isDecimal) {
										var rs = Math.round(now * 100) / 100;
										rs = rs.toString().replace('.', ',');
									} else {
										var rs = Math.round(now);
										rs = rs.toString();
									}
									$(this).text(rs);
								}
							});
						});
					}, 500);
				});
			</script>
		<?php
		}
	}

	/**
	 * Add the plugin menu page(s)
	 * @method plugin_add_settings_pages
	 */
	function plugin_add_settings_pages()
	{

		add_menu_page(
			__('Rvcurrency Exchange', 'rvcurrencyexchange'),
			__('Rvcurrency Exchange', 'rvcurrencyexchange'),
			'administrator', // Menu page capabilities
			'rvcurrencyexchange-settings', // Page ID
			array($this, 'plugin_settings_page'), // Callback
			'dashicons-admin-generic',
			null
		);
	}

	/**
	 * Register the main Plugin Settings
	 * @method plugin_register_settings
	 */
	function plugin_register_settings()
	{

		register_setting('rvcurrencyexchange-settings-group', 'rvcurrencyexchange_main_options', array($this, 'plugin_sanitize_settings'));

		add_settings_section('main', __('USA', 'rvcurrencyexchange'), array($this, 'main_section_callback'), 'rvcurrencyexchange-settings');
		add_settings_field('usa_purchase', 'USA PURCHASE', array($this, 'usa_purchase_callback'), 'rvcurrencyexchange-settings', 'main');
		add_settings_field('usa_sale', 'USA SALE', array($this, 'usa_sale_callback'), 'rvcurrencyexchange-settings', 'main');

		add_settings_section('eu_main', __('EUROPEAN UNION', 'rvcurrencyexchange'), array($this, 'main_section_callback'), 'rvcurrencyexchange-settings');
		add_settings_field('eu_purchase', 'EU PURCHASE', array($this, 'eu_purchase_callback'), 'rvcurrencyexchange-settings', 'eu_main');
		add_settings_field('eu_sale', 'EU SALE', array($this, 'eu_sale_callback'), 'rvcurrencyexchange-settings', 'eu_main');


		add_settings_section('bri_main', __('BRITISH', 'rvcurrencyexchange'), array($this, 'main_section_callback'), 'rvcurrencyexchange-settings');
		add_settings_field('bri_purchase', 'BRITISH PURCHASE', array($this, 'bri_purchase_callback'), 'rvcurrencyexchange-settings', 'bri_main');
		add_settings_field('bri_sale', 'BRITISH SALE', array($this, 'bri_sale_callback'), 'rvcurrencyexchange-settings', 'bri_main');

		register_setting('rvcurrencyexchange-settings-group2', 'rvcurrencyexchange_main_options2', array($this, 'plugin_sanitize_settings2'));
		add_settings_section('special_options', __('Styles (CAUTION ON MODIFY THIS CODE)', 'rvcurrencyexchange'), array($this, 'main_section_callback2'), 'rvcurrencyexchange-settings2');
	}

	/**
	 * The text to display as description for the main section
	 * @method main_section_callback
	 */
	function main_section_callback()
	{
		return _e('', 'rvcurrencyexchange');
	}

	/**
	 * Create the option html input
	 * @return html
	 */
	function usa_purchase_callback()
	{
		return printf(
			'<input type="text" id="usa_purchases" name="rvcurrencyexchange_main_options[usa_purchase]"  value="%s" style="width: 100px; text-align: center;"/>',
			isset($this->settings['usa_purchase']) ? esc_attr($this->settings['usa_purchase']) : ''
		);
	}

	/**
	 * Create the option html input
	 * @return html
	 */
	function usa_sale_callback()
	{
		return printf(
			'<input type="text" id="usa_sale" name="rvcurrencyexchange_main_options[usa_sale]"  value="%s" style="width: 100px; text-align: center;"/>',
			isset($this->settings['usa_sale']) ? esc_attr($this->settings['usa_sale']) : ''
		);
	}

	/**
	 * Create the option html input
	 * @return html
	 */
	function eu_purchase_callback()
	{
		return printf(
			'<input type="text" id="eu_purchase" name="rvcurrencyexchange_main_options[eu_purchase]"  value="%s" style="width: 100px; text-align: center;"/>',
			isset($this->settings['eu_purchase']) ? esc_attr($this->settings['eu_purchase']) : ''
		);
	}

	/**
	 * Create the option html input
	 * @return html
	 */
	function eu_sale_callback()
	{
		return printf(
			'<input type="text" id="eu_sale" name="rvcurrencyexchange_main_options[eu_sale]"  value="%s" style="width: 100px; text-align: center;"/>',
			isset($this->settings['eu_sale']) ? esc_attr($this->settings['eu_sale']) : ''
		);
	}


	/**
	 * Create the option html input
	 * @return html
	 */
	function bri_purchase_callback()
	{
		return printf(
			'<input type="text" id="bri_purchase" name="rvcurrencyexchange_main_options[bri_purchase]"  value="%s" style="width: 100px; text-align: center;"/>',
			isset($this->settings['bri_purchase']) ? esc_attr($this->settings['bri_purchase']) : ''
		);
	}

	/**
	 * Create the option html input
	 * @return html
	 */
	function bri_sale_callback()
	{
		return printf(
			'<input type="text" id="bri_sale" name="rvcurrencyexchange_main_options[bri_sale]"  value="%s" style="width: 100px; text-align: center;"/>',
			isset($this->settings['bri_sale']) ? esc_attr($this->settings['bri_sale']) : ''
		);
	}

	function main_section_callback2()
	{
		// return textarea with code colors and styles
		return printf(
			'<textarea id="special_options" name="rvcurrencyexchange_main_options[special_options]"  
									value="%s" style="width: 510px; height: 150px; display:none">%s</textarea>',
			isset($this->settings['special_options']) ? esc_attr($this->settings['special_options']) : '',
			isset($this->settings['special_options']) ? esc_attr($this->settings['special_options']) : ''
		);
	}

	/**
	 * Sanitize the settings values before saving it
	 * @param  mixed $input The settings value
	 * @return mixed        The sanitized value
	 */
	function plugin_sanitize_settings($input)
	{
		return $input;
	}

	/**
	 * Enqueue the main Plugin admin scripts and styles
	 * @method plugin_enqueue_scripts
	 */
	function plugin_enqueue_admin_scripts()
	{

		wp_register_style(
			'rvcurrencyexchange_admin_style',
			RVCURRENCY_DIR_URL . '/assets/dist/admin.css',
			array(),
			null
		);

		wp_register_script(
			'rvcurrencyexchange_admin_script',
			RVCURRENCY_DIR_URL . "/assets/dist/admin.js",
			array('jquery'),
			null,
			true
		);

		wp_enqueue_style('rvcurrencyexchange_admin_style');
		wp_enqueue_script('rvcurrencyexchange_admin_script');
	}

	/**
	 * Enqueue the main Plugin user scripts and styles
	 * @method plugin_enqueue_scripts
	 */
	function plugin_enqueue_scripts()
	{

		wp_register_style(
			"rvcurrencyexchange_user_style",
			RVCURRENCY_DIR_URL . "/assets/dist/user.css",
			array(),
			null
		);

		wp_register_script(
			"rvcurrencyexchange_user_script",
			RVCURRENCY_DIR_URL . "/assets/dist/user.js",
			array('jquery'),
			null,
			true
		);

		wp_enqueue_style('rvcurrencyexchange_user_style');
		wp_enqueue_script('rvcurrencyexchange_user_script');
	}

	/**
	 * Add the plugin style to tinymce editor
	 * @method plugin_add_editor_style
	 */
	function plugin_add_editor_style($styles)
	{
		if (!empty($styles)) {
			$styles .= ',';
		}
		$styles .= RVCURRENCY_DIR_URL . '/assets/dist/editor-style.css';
		return $styles;
	}

	/**
	 * Plugin main settings page
	 * @method plugin_settings_page
	 */
	function plugin_settings_page()
	{

		ob_start(); ?>


		<script src="https://pagecdn.io/lib/ace/1.9.6/ace.js" crossorigin="anonymous" integrity="sha256-Df0y/Q99ekLl+f6XctYp2tUMNP0QrIfxg417zUfU57M="></script>


		<div class="wrap" style="max-width: 860px;">

			<div class="card" style="max-width: 860px;">

				<h1><?php _e('Rvcurrency Exchange', 'rvcurrencyexchange'); ?> </h1>
				<small>power Devpwo Paul Osinga</small>
				<p>
					Options of your currencies.
				</p>
				<ul>
					<li>
						example of use simple default options: <b>[rvcurrencyexchange]</b>
					</li>
					<li>
						example of use with options: <b> [rvcurrencyexchange type="usa_purchase" symbol="$" duration="5000"]</b>
					</li>
				</ul>
				<p>
					<b>Options:</b>
				<ul>
					<li>type: usa_purchase, usa_sale, eu_purchase, eu_sale, bri_purchase, bri_sale (usa_purchase is default)</li>
					<li>symbol: $, €, ¢</li>
					<li>duration: 5000, 10000, 15000, 20000 (0 for disable) (20000 is default)</li>
				</ul>

				</p>

			</div>

			<div class="card" style="max-width: 860px;">

				<?php
				$this->settings = get_option('rvcurrencyexchange_main_options');
				?>

				<form method="post" action="options.php">

					<?php settings_fields('rvcurrencyexchange-settings-group'); ?>
					<?php do_settings_sections('rvcurrencyexchange-settings'); ?>
					<?php do_settings_sections('rvcurrencyexchange-settings2'); ?>

					<style>
						#editor {
							position: relative;
							height: 120px;
							width: 510px
						}
					</style>
					<div id="editor">.rvcurrencyexchange {
						position: relative;
						}
						.rvcurrencyexchange.amount{
						font-size: 20px;
						}
					</div>
					<hr>
					<?php submit_button(); ?>

				</form>

			</div>

		</div>


		<script>
			var editor = ace.edit("editor");
			editor.setTheme("ace/theme/monokai");
			editor.session.setMode("ace/mode/css");

			var fromSetValue = false;
			editor.on("change", function() {
				if (!fromSetValue) {
					var value = editor.getValue();
					jQuery('#special_options').val(value);
				}
			})
			//jquery wordpress ready
			jQuery(document).ready(function($) {
				var value = $('#special_options').val();
				debugger
				if (value != '') {
					fromSetValue = true;
					editor.setValue(value);
					fromSetValue = false;
				}

			});
		</script>

<?php

		return print(ob_get_clean());
	}
}

new Avcurrency_Exchange;
