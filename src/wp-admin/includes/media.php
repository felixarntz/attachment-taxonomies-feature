<?php
/**
 * @package AttachmentTaxonomiesFeature
 */

/**
 * Removes all taxonomies from the attachment compat fields.
 *
 * This is done since the plugin actually handles taxonomies through its dedicated
 * dropdowns in Backbone. No need for ugly compat fields here.
 *
 * @param array $form_fields The original form fields array.
 * @return array The modified form fields array.
 */
function atf_remove_taxonomies_from_attachment_compat( $form_fields ) {
	foreach ( get_object_taxonomies( 'attachment', 'names' ) as $taxonomy_slug ) {
		if ( isset( $form_fields[ $taxonomy_slug ] ) ) {
			unset( $form_fields[ $taxonomy_slug ] );
		}
	}

	return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'atf_remove_taxonomies_from_attachment_compat' );
