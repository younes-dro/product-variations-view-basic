declare var wp: any;

class ProductVariationImage {
  static checkMediaAvailability(): void {
    if (typeof wp !== 'undefined' && typeof wp.media !== 'undefined') {
      console.log('wp.media is loaded and available');
    } else {
      console.error('wp.media is NOT available! Ensure wp_enqueue_media() is called.');
    }
  }

  static test(): void {
    ProductVariationImage.checkMediaAvailability();
    console.log('Add Multiple Variation Images');
  }
}

// Usage example
ProductVariationImage.test();
