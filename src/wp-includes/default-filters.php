<?php
/**
 * @package AttachmentTaxonomiesFeature
 */

add_filter( 'wp_prepare_attachment_for_js', 'atf_add_taxonomies_to_attachment_js', 10, 2 );

add_action( 'add_attachment',  'atf_save_ajax_attachment_taxonomies' );
add_action( 'edit_attachment', 'atf_save_ajax_attachment_taxonomies' );

add_action( 'wp_default_scripts', 'atf_default_scripts', 11 );

add_action( 'init', 'atf_create_initial_taxonomies' );

add_action( 'wp_enqueue_media', 'atf_media_enqueue_script' );
add_action( 'wp_enqueue_media', 'atf_media_print_styles' );
add_action( 'wp_enqueue_media', 'atf_media_adjust_templates' );
