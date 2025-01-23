/*! 
 * Settings Script for Product Variations View add-on
 * 
 * Author: Younes DRO (younesdro@gmail.com)
 * Date: 23/01/2025 14:43:18
 * Released under the GPLv2 or later.
 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/*!***************************************************************!*\
  !*** ./src/variation-images/dro-pvvp-add-variation-images.ts ***!
  \***************************************************************/

class ProductVariationImage {
    static checkMediaAvailability() {
        if (typeof wp !== 'undefined' && typeof wp.media !== 'undefined') {
            console.log('wp.media is loaded and available');
        }
        else {
            console.error('wp.media is NOT available! Ensure wp_enqueue_media() is called.');
        }
    }
    static test() {
        ProductVariationImage.checkMediaAvailability();
        console.log('Add Multiple Variation Images');
    }
}
// Usage example
ProductVariationImage.test();

/******/ })()
;
//# sourceMappingURL=dro-pvvp-add-variation-images.js.map