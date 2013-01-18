<?php
/**
 * Genesis Simple Sidebars Exporter
 * 
 * @package GaryJones\GenesisSimpleSidebarsExporter
 * @author Gary Jones <gary@garyjones.co.uk>
 * @license GPL-2.0+
 * 
 * @wordpress-plugin
 * Plugin Name: Genesis Simple Sidebars Exporter
 * Plugin URI: http://github.com/GaryJones/genesis-simple-sidebars-exporter
 * Description: Adds an exporter for Genesis Simple Sidebars plugin.
 * Version: 1.0.0
 * Author: Gary Jones, Gamajo Tech
 * Author URI: http://gamajo.com/
 * Text Domain: genesis-simple-sidebars-exporter
 * Domain Path: /languages/
 * License: GPL-2.0+
 * License URI: http://www.opensource.org/licenses/gpl-license.php
*/

register_activation_hook( __FILE__, 'gsse_check_requirements' );
/**
 * Activation hook callback.
 *
 * This functions runs when the plugin is activated. It checks to make sure the
 * user is running Genesis Simple Sidebars, so there are no conflicts or fatal
 * errors. That plugin has its own checks for minimum versions of WordPress and
 * Genesis, so no need to duplicate them here.
 *
 * @since 1.0.0
 */
function gsse_check_requirements() {
	$plugin = plugin_basename( __FILE__ );
	if ( ! function_exists( 'deactivate_plugins' ) ) {
		require_once admin_url( 'includes/plugin.php' );
	}
	if ( ! defined( 'SS_SETTINGS_FIELD' ) && is_plugin_active( $plugin ) ) {
		deactivate_plugins( $plugin );
		add_action( 'admin_notices', 'gsse_admin_notice' );
	}	
}

/*
 * Check plugin is still fine to use - captures when GSS was activated, then
 * GSSE, then GSS deactivated. Run at priority 13, as GSS does some requirement
 * checks at priority 12.
 */
add_action( 'admin_init', 'gsse_check_requirements', 13 );

/**
 * Display admin notice, if on the plugins admin page.
 * 
 * @since 1.0.0
 * 
 * @global $pagenow The current admin page filename.
 */
function gsse_admin_notice(){
    global $pagenow;
    if ( 'plugins.php' == $pagenow ) {
		printf(
			'<div class="error"><p>%s</p></div>',
			__( 'Genesis Simple Sidebars Exporter plugin requires Genesis Simple Sidebars plugin to be activated. Genesis Simple Sidebars Exporter plugin has been deactivated.', 'genesis-simple-sidebars-exporter' )
		);
    }
}

add_action( 'init', 'gsse_localization' );
/**
 * Support localization for plugin.
 *
 * @see http://www.geertdedeckere.be/article/loading-wordpress-language-files-the-right-way
 *
 * @since 1.0.0
 */
function gsse_localization() {
	$domain = 'genesis-simple-sidebars-exporter';
	// The "plugin_locale" filter is also used in load_plugin_textdomain()
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	load_textdomain( $domain, WP_LANG_DIR . '/genesis-simple-sidebars-exporter/' . $domain . '-' . $locale . '.mo' );
	load_plugin_textdomain( $domain, false, plugin_dir_path( __FILE__ ) . 'languages/' );
}

add_filter( 'genesis_export_options', 'gsse_export_options' );
/**
 * Hook Genesis Simple Sidebars Exporter into Genesis Exporter, allowing
 * Genesis Simple Sidebars Settings to be exported.
 *
 * @since 1.0.0
 *
 * @param array $options Exporter options.
 *
 * @return array
 */
function gsse_export_options( array $options ) {
	$options['gss'] = array(
		'label' => __( 'Simple Sidebars', 'genesis-simple-sidebars-exporter' ),
		'settings-field' => SS_SETTINGS_FIELD
	);
	return $options;
}
