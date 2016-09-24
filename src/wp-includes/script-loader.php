<?php
/**
 * @package AttachmentTaxonomiesFeature
 */

/**
 * Registers the default plugin scripts.
 *
 * @param WP_Scripts $scripts WP_Scripts object.
 */
function atf_default_scripts( &$scripts ) {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	$scripts->add( 'attachment-taxonomies', ATF_URL . WPINC . '/js/library' . $suffix . '.js', array( 'jquery', 'media-views' ), false, 1 );

	if ( did_action( 'init' ) ) {
		$taxonomies = array();
		$all_items = array();
		$filter_by_item = array();

		foreach ( get_object_taxonomies( 'attachment', 'objects' ) as $taxonomy_slug => $taxonomy ) {
			if ( ! $taxonomy->query_var ) {
				continue;
			}

			$js_taxonomy = _atf_prepare_taxonomy_for_js( $taxonomy_slug, $taxonomy );

			$taxonomies[] = $js_taxonomy;
			$all_items[ $js_taxonomy['slug'] ] = $taxonomy->labels->all_items;
			$filter_by_item[ $js_taxonomy['slug'] ] = _atf_get_filter_by_label( $taxonomy );
		}

		$scripts->localize( 'attachment-taxonomies', '_attachment_taxonomies', array(
			'data'			=> $taxonomies,
			'l10n'			=> array(
				'all'			=> $all_items,
				'filterBy'		=> $filter_by_item,
			),
		) );
	}
}
