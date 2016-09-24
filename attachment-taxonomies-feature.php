<?php
/*
Plugin Name: Attachment Taxonomies (Feature Plugin)
Plugin URI:  https://github.com/felixarntz/attachment-taxonomies-feature
Description: Feature plugin to improve attachment taxonomy support in WordPress.
Version:     1.0.0
Author:      The WordPress Team
Author URI:  https://wordpress.org
License:     GNU General Public License v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Tags:        feature, attachment, media, taxonomy, categories, tags
*/
/**
 * @package AttachmentTaxonomiesFeature
 */

/**
 * Initializes the plugin functionality.
 */
function atf_init() {
	// Bail if the original plugin is active to prevent conflicts.
	if ( class_exists( 'Attachment_Taxonomies' ) ) {
		add_action( 'admin_notices', 'atf_conflict_notice' );
		return;
	}

	define( 'ATF_ABSPATH', plugin_dir_path( __FILE__ ) . 'src/' );
	define( 'ATF_URL', plugin_dir_url( __FILE__ ) . 'src/' );

	require_once ATF_ABSPATH . WPINC . '/default-filters.php';
	require_once ATF_ABSPATH . WPINC . '/post.php';
	require_once ATF_ABSPATH . WPINC . '/script-loader.php';
	require_once ATF_ABSPATH . WPINC . '/taxonomy.php';
	require_once ATF_ABSPATH . WPINC . '/media.php';

	if ( is_admin() ) {
		require_once ATF_ABSPATH . 'wp-admin/includes/class-wp-media-list-table.php';
		require_once ATF_ABSPATH . 'wp-admin/includes/media.php';
	}
}

/**
 * Renders an admin notice when the WordPress version is not supported.
 */
function atf_requirements_notice() {
	$plugin_file = plugin_basename( __FILE__ );

	?>
	<div class="notice notice-warning is-dismissible">
		<p>
			<?php printf(
				__( 'Please note: The Attachment Taxonomies feature plugin requires WordPress 4.6 or higher. <a href="%s">Deactivate plugin</a>.' ),
				wp_nonce_url(
					add_query_arg(
						array(
							'action'        => 'deactivate',
							'plugin'        => $plugin_file,
							'plugin_status' => 'all',
						),
						admin_url( 'plugins.php' )
					),
					'deactivate-plugin_' . $plugin_file
				)
			); ?>
		</p>
	</div>
	<?php
}

/**
 * Renders an admin notice when the original plugin is active.
 */
function atf_conflict_notice() {
	$plugin_file = plugin_basename( __FILE__ );

	?>
	<div class="notice notice-warning is-dismissible">
		<p>
			<?php printf(
				__( 'Please note: The Attachment Taxonomies feature plugin is not compatible with the original plugin. <a href="%s">Deactivate plugin</a>.' ),
				wp_nonce_url(
					add_query_arg(
						array(
							'action'        => 'deactivate',
							'plugin'        => $plugin_file,
							'plugin_status' => 'all',
						),
						admin_url( 'plugins.php' )
					),
					'deactivate-plugin_' . $plugin_file
				)
			); ?>
		</p>
	</div>
	<?php
}

if ( version_compare( $GLOBALS['wp_version'], '4.6', '<' ) ) {
	add_action( 'admin_notices', 'atf_requirements_notice' );
} else {
	add_action( 'plugins_loaded', 'atf_init' );
}
