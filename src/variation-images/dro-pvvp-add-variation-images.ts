declare var wp: any;

class ProductVariationImage {
  private static instance: ProductVariationImage | null = null;

  private constructor() {}

  public static getInstance(): ProductVariationImage {
    if (this.instance == null) {
      this.instance = new ProductVariationImage();
    }
    return this.instance;
  }

  public checkMediaAvailability(): boolean {
    if (typeof wp !== 'undefined' && typeof wp.media !== 'undefined') {
      return true;
    } else {
      return false;
    }
  }

  // initialize events
  public init(): void {

    jQuery('.woocommerce_variation').each((index, element) => {
      var optionsWrapper = jQuery(element).find('.options:first');
      var galleryWrapper = jQuery(element).find('.dro-pvvp-variation-images-container');
      galleryWrapper.insertBefore(optionsWrapper);
      
    });
    jQuery(document.body).on('click', '.dro-pvvp-handlediv', function () {
      jQuery(this).closest('.dro-pvvp-variation-images-postbox').toggleClass('closed');
      var ariaExpandedValue = !jQuery(this).closest('.dro-pvvp-variation-images-postbox').hasClass('closed');
      jQuery(this).attr('aria-expanded', ''+ariaExpandedValue+'');
    });
  }
}
(function ($) {
  $(function () {
    const variationImageManager = ProductVariationImage.getInstance();
    if( variationImageManager.checkMediaAvailability()){
      // variationImageManager.init();
    $('#woocommerce-product-data').on('woocommerce_variations_loaded', function () {
      variationImageManager.init();
    });

  }else{
    console.error('wp.media is NOT available! Ensure wp_enqueue_media() is called.');
  }
    
  });
})(jQuery);
