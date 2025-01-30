/*! 
 * Settings Script for Product Variations View add-on
 * 
 * Author: Younes DRO (younesdro@gmail.com)
 * Date: 29/01/2025 18:16:12
 * Released under the GPLv2 or later.
 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/*!***************************************************************!*\
  !*** ./src/variation-images/dro-pvvp-add-variation-images.ts ***!
  \***************************************************************/

// declare var wp: any;
// import* as  $ from 'jquery';
class ProductVariationImage {
    constructor() { }
    static getInstance() {
        if (this.instance == null) {
            this.instance = new ProductVariationImage();
        }
        return this.instance;
    }
    checkMediaAvailability() {
        if (typeof wp !== 'undefined' && typeof wp.media !== 'undefined') {
            return true;
        }
        else {
            return false;
        }
    }
    // initialize events
    init() {
        jQuery(document).off('click', '.dro-pvvp-variation-images-add-image');
        jQuery(document).off('click', '.dro-pvvp-variation-images-remove-image');
        jQuery(document).on('click', '.dro-pvvp-variation-images-add-image', (event) => this.addImage(event));
        jQuery(document).on('click', '.dro-pvvp-variation-images-remove-image', (event) => this.removeImage(event));
        jQuery('.woocommerce_variation').each((index, element) => {
            var optionsWrapper = jQuery(element).find('.options:first');
            var galleryWrapper = jQuery(element).find('.dro-pvvp-variation-images-container');
            galleryWrapper.insertBefore(optionsWrapper);
        });
        jQuery(document.body).on('click', '.dro-pvvp-handlediv', function () {
            jQuery(this).closest('.dro-pvvp-variation-images-postbox').toggleClass('closed');
            var ariaExpandedValue = !jQuery(this).closest('.dro-pvvp-variation-images-postbox').hasClass('closed');
            jQuery(this).attr('aria-expanded', '' + ariaExpandedValue + '');
        });
    }
    addImage(event) {
        var _a;
        const addImageButton = event.currentTarget;
        event.preventDefault();
        event.stopPropagation();
        var frame = null;
        var product_variation_id = jQuery(addImageButton).data('dro-pvvp-variation-id');
        var loop = jQuery(addImageButton).data('dro-pvvp-variation-loop');
        if (typeof wp !== 'undefined' && wp.media && ((_a = wp.media) === null || _a === void 0 ? void 0 : _a.editor)) {
            if (frame) {
                frame.open();
                return;
            }
            frame = wp.media({
                title: 'Select media',
                button: {
                    text: 'Add media'
                },
                multiple: true,
                library: {
                    type: ['video', 'image']
                }
            });
            frame.on('select', () => {
                const images = frame.state().get('selection').toJSON();
                let html = '';
                images.forEach((image) => {
                    if (image.type === 'image') {
                        const id = image.id;
                        let _image$sizes = image.sizes;
                        _image$sizes = _image$sizes === void 0 ? { full: { url: '' }, thumbnail: { url: '' } } : _image$sizes;
                        const thumbnail = _image$sizes.thumbnail;
                        const full = _image$sizes.full;
                        const url = thumbnail ? thumbnail.url : full.url;
                        const template = wp.template('dro-pvvp-variation-image-collections');
                        html += template({
                            id: id,
                            url: url,
                            product_variation_id: product_variation_id,
                            loop: loop
                        });
                    }
                });
                jQuery(addImageButton).parent().prev().find('.dro-pvvp-variation-images-grid-images').append(html);
                this.collectionsChanged(addImageButton);
            });
            frame.open();
        }
    }
    removeImage(event) {
        const removeImageIcon = event.currentTarget;
        event.preventDefault();
        event.stopPropagation();
        this.collectionsChanged(removeImageIcon);
        jQuery(removeImageIcon).parent().remove();
    }
    collectionsChanged(addImageButton) {
        jQuery(addImageButton).closest('.woocommerce_variation').addClass('variation-needs-update');
        jQuery('button.cancel-variation-changes, button.save-variation-changes').removeAttr('disabled');
        jQuery('#variable_product_options').trigger('woocommerce_variations_input_changed');
        // const event = document.createEvent('Event');
        // event.initEvent('woo_variation_gallery_admin_variation_changed', true, true); 
        // document.dispatchEvent(event);
    }
}
ProductVariationImage.instance = null;
(function ($) {
    $(function () {
        const variationImageManager = ProductVariationImage.getInstance();
        if (variationImageManager.checkMediaAvailability()) {
            variationImageManager.init();
            $('#woocommerce-product-data').on('woocommerce_variations_loaded', function () {
                variationImageManager.init();
            });
        }
        else {
            console.error('wp.media is NOT available! Ensure wp_enqueue_media() is called.');
        }
    });
})(jQuery);

/******/ })()
;
//# sourceMappingURL=dro-pvvp-add-variation-images.js.map