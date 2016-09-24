<?php
/**
 * @package AttachmentTaxonomiesFeature
 */

/**
 * Enqueues the plugin's JavaScript file.
 *
 * The script handles attachment taxonomies through Backbone, allowing filtering by and managing
 * these taxonomies through the media library and media modal.
 */
function atf_media_enqueue_script() {
	$taxonomies = get_object_taxonomies( 'attachment', 'names' );
	if ( empty( $taxonomies ) ) {
		return;
	}

	wp_enqueue_script( 'attachment-taxonomies' );
}

/**
 * Prints some inline styles for the taxonomy filters and term dropdowns.
 *
 * The styles are only printed from within the `admin_footer` action.
 * Otherwise the function will hook itself into this action and bail.
 */
function atf_media_print_styles() {
	$taxonomies = get_object_taxonomies( 'attachment', 'names' );
	if ( empty( $taxonomies ) ) {
		return;
	}

	if ( ! doing_action( 'admin_footer' ) ) {
		add_action( 'admin_footer', 'atf_media_print_styles' );
		return;
	}

	$count = 2 + count( $taxonomies );

	$percentage = intval( round( 84 / $count ) );
	$percentage_calc = intval( round( 96 / $count ) );

	?>
	<style type="text/css">
		.media-modal-content .media-frame .media-toolbar-secondary > select {
			width: <?php echo esc_attr( $percentage ); ?>% !important;
			width: -webkit-calc(<?php echo esc_attr( $percentage_calc ); ?>% - 12px) !important;
			width: calc(<?php echo esc_attr( $percentage_calc ); ?>% - 12px) !important;
		}

		.attachment-taxonomy-input {
			display: none;
		}

		.attachment-details .setting.attachment-taxonomy-select select,
		.media-sidebar .setting.attachment-taxonomy-select select {
			-webkit-box-sizing: border-box;
			   -moz-box-sizing: border-box;
			        box-sizing: border-box;
			margin: 1px;
			width: 65%;
			float: right;
		}
	</style>
	<?php
}

/**
 * Replaces the media templates output action of WordPress Core to be able to modify this output.
 */
function atf_media_adjust_templates() {
	$taxonomies = get_object_taxonomies( 'attachment', 'names' );
	if ( empty( $taxonomies ) ) {
		return;
	}

	remove_action( 'admin_footer', 'wp_print_media_templates' );
	remove_action( 'wp_footer', 'wp_print_media_templates' );
	remove_action( 'customize_controls_print_footer_scripts', 'wp_print_media_templates' );

	add_action( 'admin_footer', 'atf_print_media_templates' );
	add_action( 'wp_footer', 'atf_print_media_templates' );
	add_action( 'customize_controls_print_footer_scripts', 'atf_print_media_templates' );
}

/**
 * Modifies the media templates for Backbone to include attachment taxonomy term dropdowns.
 *
 * This approach is kind of hacky, but there is no other way to adjust this output
 * (Core implementation will look way nicer).
 */
function atf_print_media_templates() {
	ob_start();
	wp_print_media_templates();
	$output = ob_get_clean();

	ob_start();
	foreach ( get_object_taxonomies( 'attachment', 'objects' ) as $taxonomy ) {
		$terms = get_terms( array(
			'taxonomy'   => $taxonomy->name,
			'hide_empty' => false,
		) );
		?>
		<label class="setting attachment-taxonomy-input" data-setting="taxonomy-<?php echo sanitize_html_class( $taxonomy->name ); ?>-terms">
			<input type="hidden" value="{{ data.taxonomies ? Object.keys(data.taxonomies.<?php echo esc_attr( $taxonomy->name ); ?>).join(',') : '' }}" />
		</label>
		<label class="setting attachment-taxonomy-select">
			<span class="name"><?php echo esc_html( $taxonomy->labels->name ); ?></span>
			<select multiple="multiple">
				<?php if ( $taxonomy->hierarchical ) : ?>
					<?php foreach ( $terms as $term ) : ?>
						<option value="<?php echo esc_attr( $term->term_id ); ?>" {{ ( data.taxonomies && data.taxonomies.<?php echo esc_attr( $taxonomy->name ); ?>[<?php echo esc_attr( $term->term_id ); ?>] ) ? 'selected' : '' }}><?php echo esc_html( $term->name ); ?></option>
					<?php endforeach; ?>
				<?php else : ?>
					<?php foreach ( $terms as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>" {{ ( data.taxonomies && data.taxonomies.<?php echo esc_attr( $taxonomy->name ); ?>['<?php echo esc_attr( $term->slug ); ?>'] ) ? 'selected' : '' }}><?php echo esc_html( $term->name ); ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</label>
		<?php
	}
	$taxonomy_output = ob_get_clean();

	$output = preg_replace( '#<script type="text/html" id="tmpl-attachment-details">(.+)</script>#Us', '<script type="text/html" id="tmpl-attachment-details">$1' . $taxonomy_output . '</script>', $output );

	$output = str_replace( '<div class="attachment-compat"></div>', $taxonomy_output . "\n" . '<div class="attachment-compat"></div>', $output );

	echo $output;
}

/**
 * Adds taxonomies and terms to a specific attachment's JavaScript output.
 *
 * @param array   $response   The original attachment data.
 * @param WP_Post $attachment The attachment post.
 * @return array The modified attachment data.
 */
function atf_add_taxonomies_to_attachment_js( $response, $attachment ) {
	$response['taxonomies'] = array();

	foreach ( get_object_taxonomies( 'attachment', 'names' ) as $taxonomy_slug ) {
		$response['taxonomies'][ $taxonomy_slug ] = array();

		foreach ( (array) wp_get_object_terms( $attachment->ID, $taxonomy_slug ) as $term ) {
			$term_data = array(
				'id'		=> $term->term_id,
				'slug'		=> $term->slug,
				'name'		=> $term->name,
			);

			if ( is_taxonomy_hierarchical( $taxonomy_slug ) ) {
				$response['taxonomies'][ $taxonomy_slug ][ $term->term_id ] = $term_data;
			} else {
				$response['taxonomies'][ $taxonomy_slug ][ $term->slug ] = $term_data;
			}
		}
	}

	return $response;
}
