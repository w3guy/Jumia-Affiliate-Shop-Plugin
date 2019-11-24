<?php
namespace Jumia;

/**
 * Class Woo_Product
 * @package Jumia
 */
class Woo_Product {

	public static function insert( $data, $category = '' ) {

		$product_id = self::create_product( $data );

		if ( is_int( $product_id ) ) {
			self::add_meta_data( $product_id, $data, $category );
		}
	}

	/**
	 * Create a new Woocommerce product.
	 *
	 * @param array $args fetch product array arguments
	 *
	 * @return int
	 */
	public static function create_product( $args ) {

		// if product already exist (by checking if title is in DB), return.

		if ( ! self::is_title_exist( esc_sql( $args['title'] ) ) ) {

			$id = wp_insert_post(
				array(
					'post_title'   => esc_sql( $args['title'] ),
					'post_content' => $args['description'],
					'post_excerpt' => $args['detail'],
					'post_status'  => 'publish',
					'post_type'    => 'product',
				)
			);

			return $id;
		}
	}


	/**
	 * Save Woocommerce post / product meta
	 *
	 * @param int $product_id
	 * @param array $args
	 */
	public static function add_meta_data( $product_id, $args, $category ) {
		update_post_meta( $product_id, '_product_url', esc_url_raw( $args['product_url'] ) );
		update_post_meta( $product_id, '_sale_price', absint( $args['sale_price'] ) );
		update_post_meta( $product_id, '_price', absint( $args['sale_price'] ) );
		update_post_meta( $product_id, '_regular_price', absint( $args['regular_price'] ) );
		update_post_meta( $product_id, '_product_url', esc_url_raw( $args['product_url'] ) );

		// set product to external/affiliate
		wp_set_object_terms( $product_id, 'external', 'product_type' );

		// set product category
		wp_set_object_terms( $product_id, $category, 'product_cat' );

		// set the product tags
		wp_set_object_terms($product_id, 'jumia', 'product_tag');

		// update post visibility
		update_post_meta( $product_id, '_visibility', 'visible' );

		$image_url = $args['image_url'];
		
		self::set_feature_image( $image_url, $product_id );

		if ( 'yoast_seo' == ju_seo_plugin() ) {
			self::wordpress_seo_yoast( $product_id, $args['title'], $args['description'] );
		} elseif ( 'seo_ultimate' == ju_seo_plugin() ) {
			self::seo_ultimate( $product_id, $args['title'], $args['description'], $image_url );
		}

	}


	public static function set_feature_image( $image_url, $product_id ) {

		// only need these if performing outside of admin environment
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		// magic sideload image returns an HTML image, not an ID
		$media = media_sideload_image( $image_url, $product_id );

		// therefore we must find it so we can set it as featured ID
		if ( ! empty( $media ) && ! is_wp_error( $media ) ) {
			$args = array(
				'post_type'      => 'attachment',
				'posts_per_page' => - 1,
				'post_status'    => 'any',
				'post_parent'    => $product_id
			);

			// reference new image to set as featured
			$attachments = get_posts( $args );

			if ( isset( $attachments ) && is_array( $attachments ) ) {
				foreach ( $attachments as $attachment ) {
					// grab source of full size images (so no 300x150 nonsense in path)
					$image = wp_get_attachment_image_src( $attachment->ID, 'full' );
					// determine if in the $media image we created, the string of the URL exists
					if ( strpos( $media, $image[0] ) !== false ) {
						// if so, we found our image. set it as thumbnail
						set_post_thumbnail( $product_id, $attachment->ID );
						// only want one image
						break;
					}
				}
			}
		}
	}

	/**
	 * Check if product title already exist.
	 *
	 * @param string $title
	 *
	 * @return bool
	 */
	public static function is_title_exist( $title ) {
		global $wpdb;

		$count = $wpdb->get_var( "SELECT count(post_title) FROM $wpdb->posts WHERE post_title like '$title' AND post_type = 'product'" );

		return ( $count >= 1 ) ? true : false;
	}


	public static function seo_ultimate( $post_id, $title, $description, $image_url = '' ) {
		$title       = esc_sql( $title );
		$description = substr( strip_tags( $description ), 0, 137 ) . '...';
		update_post_meta( $post_id, '_su_title', $title );
		update_post_meta( $post_id, '_su_rich_snippet_review_item', $title );
		update_post_meta( $post_id, '_su_description', $description );
		update_post_meta( $post_id, '_su_rich_snippet_review_image', $image_url );
		update_post_meta( $post_id, '_su_rich_snippet_type', 'review' );
		update_post_meta( $post_id, '_su_rich_snippet_review_rating', 5 );
	}

	public static function wordpress_seo_yoast( $post_id, $title, $description ) {
		$title       = esc_sql( $title );
		$description = substr( strip_tags( $description ), 0, 153 ) . '...';
		update_post_meta( $post_id, '_yoast_wpseo_title', $title );
		update_post_meta( $post_id, '_yoast_wpseo_metadesc', $description );
	}
}