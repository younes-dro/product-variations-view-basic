
(function ($) {
    $('a.description-toggle').on('click', function (e) {
        e.preventDefault();
        $('div.description-variation-container').slideToggle();
        $('span.toggle').toggleClass('active');
    });
})(jQuery);

(function ($) {

/**
 * Updates the total price dynamically based on quantities and prices.
 */
function updateTotalPrice() {
    let total = 0;

    // Iterate through each quantity input
    $('input[name^="cvp-quantity"]').each(function () {
        const qty = parseInt($(this).val(), 10);

        if (qty > 0) {
            // Fetch the price from the hidden input for this variation
            const priceInput = $(this).closest('.carousel-content').find('input.display_price');
            
            if (priceInput.length === 0) {
                console.error('Price input not found for:', $(this));
                return;
            }

            const priceString = priceInput.val();
            
            if (!priceString) {
                console.error('Price string is empty or invalid for:', priceInput);
                return;
            }

            // Parse the price correctly
            const price = parsePrice(priceString);
            
            if (!isNaN(price)) {
                total += price * qty;
            } else {
                console.error('Invalid price detected:', priceString);
            }
        }
    });
    const formattedTotal = formatPrice(total);
    $('.cvp-total').text(formattedTotal);
}

/**
 * Formats a number to a currency string based on WooCommerce settings.
 * @param {number} price - The price to format.
 * @returns {string} - Formatted price string.
 */
function formatPrice(price) {
    const symbol = wc_cvp_params.currency_symbol || '$';
    const decimals = parseInt(wc_cvp_params.currency_format_num_decimals, 10) || 2;
    const decimalSeparator = wc_cvp_params.currency_format_decimal_sep || '.';
    const thousandSeparator = wc_cvp_params.currency_format_thousand_sep || ',';
    const position = wc_cvp_params.currency_position || 'left';
    const trimZeros = wc_cvp_params.currency_format_trim_zeros === 'yes';

    // Ensure the number is rounded to the correct number of decimals
    let formattedPrice = price.toFixed(decimals);

    if (trimZeros) {
        const regex = new RegExp(decimalSeparator + '0{' + decimals + '}$');
        formattedPrice = formattedPrice.replace(regex, '');
    }

    const parts = formattedPrice.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);
    formattedPrice = parts.join(decimalSeparator);

    switch (position) {
        case 'left':
            return symbol + formattedPrice;
        case 'right':
            return formattedPrice + symbol;
        case 'left_space':
            return symbol + ' ' + formattedPrice;
        case 'right_space':
            return formattedPrice + ' ' + symbol;
        default:
            return formattedPrice;
    }
}



/**
 * Parses a price string into a number, respecting WooCommerce settings.
 * @param {string} price - The price string to parse.
 * @returns {number} - Parsed number.
 */

function parsePrice(price) {
    const decimalSeparator = wc_cvp_params.currency_format_decimal_sep || '.';
    const thousandSeparator = wc_cvp_params.currency_format_thousand_sep || ',';

    if (!isNaN(price)) {
        return parseFloat(price);
    }

    let priceWithoutThousand = price;
    if (price.includes(thousandSeparator)) {
        priceWithoutThousand = price.split(thousandSeparator).join('');
    }
    
    const normalizedPrice = priceWithoutThousand.replace(decimalSeparator, '.');
    

    const parsedPrice = parseFloat(normalizedPrice);
    

    if (isNaN(parsedPrice)) {
        console.error('Failed to parse price:', price);
        return 0; // Default to 0
    }

    return parsedPrice;
}





    // Event binding for quantity changes
    $(document).on('change', 'input[name^="cvp-quantity"]', function () {
        updateTotalPrice();
    });

    // Initial update on page load
    updateTotalPrice();
})(jQuery);

(function ($) {
    $('#cvp-add-to-cart-button').on('click', function (e) {
        e.preventDefault();

        var itemsToAdd = [];
        $('input[name^="cvp-quantity"]').each(function () {
            var qty = parseInt($(this).val(), 10);

            if (qty > 0) {
                var $variationRow = $(this).closest('.carousel-content'); // Locate the parent variation row
                var attributes = {};

                // Handle attributes from <select> elements
                $variationRow.find('select[name^="attribute_"]').each(function () {
                    var attrName = $(this).attr('name'); // e.g., "attribute_pa_size"
                    var attrValue = $(this).val(); // e.g., "medium"
                    
                    if (attrValue) {
                        attributes[attrName] = attrValue;
                    }
                });

                // Handle attributes from <span> elements
                $variationRow.find('span[name^="attribute_"]').each(function () {
                    var attrName = $(this).attr('name'); // e.g., "attribute_pa_color"
                    var attrValue = $(this).text().trim(); // Get the text inside the span
                    
                    if (attrValue) {
                        attributes[attrName] = attrValue;
                    }
                });

                // Add the variation with its attributes
                itemsToAdd.push({
                    variation_id: $(this).attr('data-variation-id'),
                    quantity: qty,
                    attributes: attributes, // Attach the selected and pre-defined attributes
                });
            }
        });

        $('.cvp-error').html('');
        if (itemsToAdd.length > 0) {
            var $thisbutton = $(this);
            $.ajax({
                url: wc_cvp_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'wc_cvp_add_to_cart',
                    products: itemsToAdd,
                    'cvp_nonce': wc_cvp_params.cvp_nonce
                },
                beforeSend: function () {
                    $thisbutton.prop('disabled', true);
                    $thisbutton.addClass('loading');
                },
                complete: function () {
                    $thisbutton.prop('disabled', false);
                    $thisbutton.removeClass('loading');
                },
                success: function (response) {
                    if (response.success) {
                        alert('Added to cart!');
                        $(document.body).trigger('wc_fragment_refresh');
                    } else {
                        $('.cvp-error').html('<p class="woocommerce-error">' + response.data.message + '</p>');
                        $('input[name^="cvp-quantity"]').val('');
                    }
                },
                error: function (error) {
                    console.log('Error adding to cart:', error);
                    $('.cvp-error').html('<p class="woocommerce-error">An unexpected error occurred. Please try again.</p>');
                }
            });
        } else {
            $('.cvp-error').html('<p class="woocommerce-error">Please select at least one product.</p>');
        }
    });
})(jQuery);



(function ($) {
    $('#cvp-reset').on('click', function (e) {
        e.preventDefault();
        $('input[name^="cvp-quantity"]').val(0);
        $('.cvp-total').text('');
        updateTotalPrice();
    });
})(jQuery);

(function ($) {
    $(document).ready(function () {
        $('#variable-products-carousel').on('slide.bs.carousel', function (event) {
            var $nextSlide = $(event.relatedTarget);
            var variationImage = $nextSlide.find('.attribute-thumb').attr('src');
            var $mainGalleryImage = $('.woocommerce-product-gallery__image img');

            if (variationImage) {
                $mainGalleryImage.attr('src', variationImage);
                $mainGalleryImage.attr('srcset', variationImage);
                $mainGalleryImage.attr('alt', 'Selected Variation Image');
            } else {
                $mainGalleryImage.attr('src', $mainGalleryImage.data('default-src'));
                $mainGalleryImage.attr('srcset', $mainGalleryImage.data('default-srcset'));
            }
        });
    });
})(jQuery);

(function ( $){
    
    const showProductGallery = wc_cvp_params.pvv_show_product_gallery;

    if (!showProductGallery) {
        $('.woocommerce-product-gallery').hide();
        $('.entry-summary').css({
            'width': '100%',
            'float': 'none'
        });
    } else {
        $('.woocommerce-product-gallery').show();
        $('.entry-summary').css({
            'width': '',
            'float': ''
        });
    }

})(jQuery);


