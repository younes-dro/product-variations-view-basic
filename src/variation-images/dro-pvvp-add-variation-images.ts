// declare var wp: any;
// import* as  $ from 'jquery';

class ProductVariationImage {
  private static instance: ProductVariationImage | null = null;

  private constructor() { }

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

  public addImage(event: JQuery.ClickEvent): void {


    const addImageButton = event.currentTarget;

    event.preventDefault();
    event.stopPropagation();

    var frame: MediaFrame | null = null;
    var product_variation_id = jQuery(addImageButton).data('dro-pvvp-variation-id');
    var loop = jQuery(addImageButton).data('dro-pvvp-variation-loop');

    if (typeof wp !== 'undefined' && wp.media && wp.media?.editor) {

      if (frame) {
        frame?.open();
        return;
      }

      frame = wp.media({
        title: 'Select media',
        button: {
          text: 'Add media'
        },

        library: {
          type: ['video', 'image']

        }

      });

      frame?.on('select', () => {
        var images = frame?.state().get('selection').toJSON();
        var html = images.map(function (image: Image) {
          if (image.type === 'image') {
            var id = image.id, _image$sizes: ImageSizes = image.sizes;
            _image$sizes = _image$sizes === void 0 ? { full: { url: '' }, thumbnail: { url: '' } } : _image$sizes;

            var thumbnail = _image$sizes.thumbnail, full = _image$sizes.full;
            var url = thumbnail ? thumbnail.url : full.url;
            var template = wp.template('dro-pvvp-variation-image-collections');
            return template({
              id: id,
              url: url,
              product_variation_id: product_variation_id,
              loop: loop
            });
          }
        }).join('');
        jQuery(addImageButton).parent().prev().find('.dro-pvvp-variation-images-grid-images').append(html);
        this.collectionsChanged(addImageButton);

      });

      frame?.open();
    }
  }

  public removeImage(event: JQuery.ClickEvent): void{

    const removeImageIcon = event.currentTarget;
    event.preventDefault();
    event.stopPropagation();

    this.collectionsChanged(removeImageIcon);
    jQuery(removeImageIcon).parent().remove();
  
  }

  public collectionsChanged(addImageButton: HTMLElement) {

    jQuery(addImageButton).closest('.woocommerce_variation').addClass('variation-needs-update');
    jQuery('button.cancel-variation-changes, button.save-variation-changes').removeAttr('disabled');
    jQuery('#variable_product_options').trigger('woocommerce_variations_input_changed');

    // const event = document.createEvent('Event');
    // event.initEvent('woo_variation_gallery_admin_variation_changed', true, true); 
    // document.dispatchEvent(event);
  }
}
(function ($) {
  $(function () {
    const variationImageManager = ProductVariationImage.getInstance();
    if (variationImageManager.checkMediaAvailability()) {
      variationImageManager.init();
      $('#woocommerce-product-data').on('woocommerce_variations_loaded', function () {
        variationImageManager.init();
      });

    } else {
      console.error('wp.media is NOT available! Ensure wp_enqueue_media() is called.');
    }

  });
})(jQuery);
