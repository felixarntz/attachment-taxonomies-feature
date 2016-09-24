<?php
/**
 * @package AttachmentTaxonomiesFeature
 */

/**
 * Sets terms for attachment taxonomies through the `save_attachment` AJAX action.
 *
 * This is a workaround to handle terms through this AJAX action as it normally does not support
 * terms.
 *
 * @param int $attachment_id The attachment ID.
 */
function atf_save_ajax_attachment_taxonomies( $attachment_id ) {
	if ( ! doing_action( 'wp_ajax_save-attachment' ) ) {
		return;
	}

	if ( empty( $_REQUEST['changes'] ) ) {
		return;
	}

	foreach ( get_object_taxonomies( 'attachment', 'objects' ) as $taxonomy ) {
		if ( ! isset( $_REQUEST['changes'][ 'taxonomy-' . $taxonomy->name . '-terms' ] ) ) {
			continue;
		}

		$terms = $_REQUEST['changes'][ 'taxonomy-' . $taxonomy->name . '-terms' ];
		if ( $taxonomy->hierarchical ) {
			$terms = array_filter( array_map( 'trim', explode( ',', $terms ) ) );
		}

		if ( current_user_can( $taxonomy->cap->assign_terms ) ) {
			wp_set_post_terms( $attachment_id, $terms, $taxonomy->name );
		}
	}
}
