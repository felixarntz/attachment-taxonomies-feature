<?php
/**
 * @package AttachmentTaxonomiesFeature
 */

/**
 * Renders attachment taxonomy filter dropdowns.
 *
 * This is only used for the PHP-based part of the media library (i.e. the regular list).
 * The JavaScript implementation using Backbone happens elsewhere.
 *
 * @param string $post_type The current post type.
 */
function atf_render_taxonomy_filters( $post_type ) {
	if ( 'attachment' !== $post_type && 'upload' !== get_current_screen()->base ) {
		return;
	}

	if ( isset( $_REQUEST['attachment-filter'] ) && 'trash' === $_REQUEST['attachment-filter'] ) {
		return;
	}

	foreach ( get_object_taxonomies( 'attachment', 'objects' ) as $taxonomy_slug => $taxonomy ) {
		if ( ! $taxonomy->query_var ) {
			continue;
		}

		$value = isset( $_REQUEST[ $taxonomy->query_var ] ) ? $_REQUEST[ $taxonomy->query_var ] : '';
		$terms = get_terms( array(
			'taxonomy'   => $taxonomy_slug,
			'hide_empty' => false,
		) );

		?>
		<label for="attachment-<?php echo sanitize_html_class( $taxonomy_slug ); ?>-filter" class="screen-reader-text"><?php echo esc_html( _atf_get_filter_by_label( $taxonomy ) ); ?></label>
		<select class="attachment-filters" name="<?php echo esc_attr( $taxonomy->query_var ); ?>" id="attachment-<?php echo sanitize_html_class( $taxonomy_slug ); ?>-filter">
			<option value="" <?php selected( '', $value ); ?>><?php echo esc_html( $taxonomy->labels->all_items ); ?></option>
			<?php foreach ( $terms as $term ) : ?>
				<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $term->slug, $value ); ?>><?php echo esc_html( $term->name ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}
}
add_action( 'restrict_manage_posts', 'atf_render_taxonomy_filters' );
