/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

console.log(wc_cvp_params);

(function ($) {

    $('a.description-toggle').on('click', function (e) {
        e.preventDefault();
        $('div.description-variation-container').slideToggle();
        $('span.toggle').toggleClass('active');
    });
})(jQuery);


(function($) {
    // Function to update total price
    function updateTotalPrice() {
        var total = 0;
        $('input[name^="cvp-quantity"]').each(function() {
            var quantity = parseInt($(this).val(), 10);
            var price = parseFloat($(this).closest('.carousel-item').find('.display_price').val());
            if (!isNaN(quantity) && !isNaN(price)) {
                total += quantity * price;
            }
        });
        $('.cvp-total').text(formatPrice(total));
    }

    // Utilize the passed currency symbol and formatting
    function formatPrice(price) {
        return wc_cvp_params.currency_symbol + price.toFixed(wc_cvp_params.currency_format_num_decimals);
    }

    // Event binding for quantity changes
    $(document).on('change', 'input[name^="cvp-quantity"]', function() {
        updateTotalPrice();
    });

    // Initial update on page load
    updateTotalPrice();
})(jQuery);



(function($) {
    $('#cvp-add-to-cart-button').on('click', function(e) {
        e.preventDefault();
        var itemsToAdd = [];
        $('input[name^="cvp-quantity"]').each(function() {
            var qty = parseInt($(this).val(), 10); 
            if (qty > 0) {
                itemsToAdd.push({
                    variation_id: $(this).attr('data-variation-id'),
                    quantity: qty
                });
            }
        });
        if (itemsToAdd.length > 0) {
            $.ajax({
                url: wc_cvp_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'wc_cvp_add_to_cart',
                    products: itemsToAdd,
                    'cvp_nonce': wc_cvp_params.cvp_nonce
                },
                success: function(response) {
                    console.log();
                    if (response.success) {
                        // Update UI: e.g., cart count, messages
                        
                        alert('Added to cart!');
                    }
                },
                error: function(error) {
                    console.log('Error adding to cart:', error);
                }
            });
        } else {
            alert('Please select at least one product.');
        }
    });
})(jQuery);


(function($) {
    $('#cvp-reset').on('click', function(e) {
        e.preventDefault();
        $('input[name^="cvp-quantity"]').val(0);
        updateTotalPrice();
    });
})(jQuery);
