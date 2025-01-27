/*! 
 * Settings Script for Product Variations View add-on
 * 
 * Author: Younes DRO (younesdro@gmail.com)
 * Date: 27/01/2025 16:51:19
 * Released under the GPLv2 or later.
 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/*!***************************************************************!*\
  !*** ./src/variation-images/dro-pvvp-add-variation-images.ts ***!
  \***************************************************************/

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
}
ProductVariationImage.instance = null;
(function ($) {
    $(function () {
        const variationImageManager = ProductVariationImage.getInstance();
        if (variationImageManager.checkMediaAvailability()) {
            // variationImageManager.init();
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