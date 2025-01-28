<?php
	// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<script type="text/html" id="tmpl-dro-pvvp-variation-image-collections">
    <li class="image">
        <input type="hidden" name="dro_pvvp_variation_image_collections[{{data.product_variation_id}}][]" value="{{data.id}}">
        <img data-id="{{data.id}}" src="{{data.url}}">
        <a href="#" class="delete dro-pvvp-variation-images-remove-image"><span class="dashicons dashicons-dismiss"></span></a>
    </li>
</script>