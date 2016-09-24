<?php
/**
 * @package AttachmentTaxonomiesFeature
 */

/**
 * Registers the default attachment taxonomies.
 */
function atf_create_initial_taxonomies() {
	register_taxonomy( 'attachment_category', 'attachment', array(
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => false,
		'show_tagcloud'         => false,
		'show_admin_column'     => true,
		'hierarchical'          => true,
		'update_count_callback' => '_update_generic_term_count',
		'query_var'             => 'attachment_category',
		'rewrite'               => false,
		'capabilities'          => array(
			'manage_terms'          => 'upload_files',
			'edit_terms'            => 'upload_files',
			'delete_terms'          => 'upload_files',
			'assign_terms'          => 'upload_files',
		),
	) );

	register_taxonomy( 'attachment_tag', 'attachment', array(
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => false,
		'show_tagcloud'         => false,
		'show_admin_column'     => true,
		'hierarchical'          => false,
		'update_count_callback' => '_update_generic_term_count',
		'query_var'             => 'attachment_tag',
		'rewrite'               => false,
		'capabilities'          => array(
			'manage_terms'          => 'upload_files',
			'edit_terms'            => 'upload_files',
			'delete_terms'          => 'upload_files',
			'assign_terms'          => 'upload_files',
		),
	) );
}

/**
 * Returns the "Filter by" label for a taxonomy.
 *
 * This is an additional taxonomy label. To define it for a custom taxonomy,
 * a 'filter_by_item' key must be added to the labels array.
 *
 * If it is not defined, the default label will be used.
 *
 * @access private
 *
 * @param object $taxonomy The taxonomy object.
 * @return string The "Filter by" label for that taxonomy.
 */
function _atf_get_filter_by_label( $taxonomy ) {
	if ( isset( $taxonomy->labels->filter_by_item ) ) {
		return $taxonomy->labels->filter_by_item;
	} elseif ( $taxonomy->hierarchical ) {
		return __( 'Filter by Category' );
	} else {
		return __( 'Filter by Tag' );
	}
}

/**
 * Formats a taxonomy to be used in JavaScript.
 *
 * Also includes the terms of this taxonomy.
 *
 * @access private
 *
 * @param string $taxonomy_slug The taxonomy slug.
 * @param object $taxonomy      The taxonomy object.
 * @return array An associative array for the taxonomy.
 */
function _atf_prepare_taxonomy_for_js( $taxonomy_slug, $taxonomy ) {
	$js_slug = lcfirst( implode( array_map( 'ucfirst', explode( '_', $taxonomy_slug ) ) ) );

	return array(
		'name'		=> $taxonomy->label,
		'slug'		=> $js_slug,
		'slugId'	=> str_replace( '_', '-', $taxonomy_slug ),
		'queryVar'	=> $taxonomy->query_var,
		'terms'		=> array_map( '_atf_get_term_array', get_terms( array(
			'taxonomy'   => $taxonomy_slug,
			'hide_empty' => false,
		) ) ),
	);
}

/**
 * Transforms a term object into an array.
 *
 * @access private
 *
 * @param object $term A term object.
 * @return array A term array.
 */
function _atf_get_term_array( $term ) {
	if ( ! is_a( $term, 'WP_Term' ) ) {
		return get_object_vars( $term );
	}

	return $term->to_array();
}
