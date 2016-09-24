<?php
/**
 * @package AttachmentTaxonomiesFeature
 * @subpackage Tests
 */

// disable xdebug backtrace
if ( function_exists( 'xdebug_disable' ) ) {
	xdebug_disable();
}

if ( false !== getenv( 'WP_PLUGIN_DIR' ) ) {
	define( 'WP_PLUGIN_DIR', getenv( 'WP_PLUGIN_DIR' ) );
}

$GLOBALS['wp_tests_options'] = array(
	'active_plugins' => array(
		'attachment-taxonomies-feature/attachment-taxonomies-feature.php',
	),
);

if ( false !== getenv( 'WP_DEVELOP_DIR' ) ) {
	$test_root = getenv( 'WP_DEVELOP_DIR' ) . '/tests/phpunit';
} elseif ( file_exists( '/tmp/wordpress-tests-lib/includes/bootstrap.php' ) ) {
	$test_root = '/tmp/wordpress-tests-lib';
} else {
	$test_root = '../../../../../../tests/phpunit';
}

require $test_root . '/includes/bootstrap.php';

echo "Installing Attachment Taxonomies Feature...\n";

activate_plugin( 'attachment-taxonomies-feature/attachment-taxonomies-feature.php' );
