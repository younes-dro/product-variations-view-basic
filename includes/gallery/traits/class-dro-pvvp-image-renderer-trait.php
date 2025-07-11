<?php
/**
 * Image Renderer Trait
 *
 * Provides reusable image rendering logic for gallery builders.
 *
 * @package DRO\PVVP\Includes\Gallery\Traits
 * @author Younes
 * @since 1.1.0
 * @version 1.1.0
 * @license GPL-2.0-or-later
 */

declare(strict_types=1);

namespace DRO\PVVP\Includes\Gallery\Traits;

defined( 'ABSPATH' ) || exit;

/**
 * Trait DRO_PVVP_Image_Renderer_Trait
 *
 * Shared image rendering methods for use in multiple gallery builders.
 */
trait DRO_PVVP_Image_Renderer_Trait {

	/**
	 * Render an image element (optionally wrapped with lightbox).
	 *
	 * @param array|string $image Image data array or image URL string.
	 * @param string       $size WordPress image size to render.
	 * @param bool         $enable_lightbox Whether to wrap with lightbox link.
	 * @param bool         $lazy Whether to enable lazy loading.
	 * @param int|null     $variation_id Optional variation ID for lightbox group.
	 * @return string Rendered image HTML.
	 */
	protected function render_image(
		$image,
		string $size,
		bool $enable_lightbox = false,
		bool $lazy = true,
		?int $variation_id = null
	): string {
		if ( is_array( $image ) ) {
			$image_id  = $image['id'] ?? 0;
			$image_url = $image['url'] ?? '';
			$image_alt = $image['alt'] ?? '';
		} else {
			$image_url = $image;
			$image_id  = attachment_url_to_postid( $image_url );
			$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		}

		if ( $image_id ) {
			$image_html = wp_get_attachment_image(
				$image_id,
				$size,
				false,
				array(
					'class'   => 'dro-pvvp-image',
					'alt'     => $image_alt,
					'loading' => $lazy ? 'lazy' : 'eager',
				)
			);
		} else {
			$image_html = sprintf(
				'<img src="%s" alt="%s" class="dro-pvvp-image" loading="%s">',
				esc_url( $image_url ),
				esc_attr( $image_alt ),
				$lazy ? 'lazy' : 'eager'
			);
		}

		if ( $enable_lightbox ) {
			$full_size_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : $image_url;
			$gallery_id    = $variation_id ?? 0;

			$image_html = sprintf(
				'<a href="%s" class="dro-pvvp-lightbox" data-lightbox="gallery-%d">%s</a>',
				esc_url( $full_size_url ),
				$gallery_id,
				$image_html
			);
		}

		return $image_html;
	}
}
