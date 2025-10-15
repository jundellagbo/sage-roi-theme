document.addEventListener('DOMContentLoaded', function() {
	// whenever qty changes, update the button's data-quantity
  jQuery(document).on('input change', 'form.cart input.qty', function(){
    var $form = jQuery(this).closest('form.cart');
    var qty   = parseFloat(jQuery(this).val()) || 1;
    $form.find('.add_to_cart_button')
         .attr('data-quantity', qty)  // ensure attribute is there
         .data('quantity', qty);      // and jQuery's data cache too
  });

  // also set it right before submit/click, just in case
  jQuery(document).on('click submit', 'form.cart', function(){
    var $form = jQuery(this);
    var qty   = parseFloat($form.find('input.qty').val()) || 1;
    $form.find('.add_to_cart_button')
         .attr('data-quantity', qty)
         .data('quantity', qty);
  });
});