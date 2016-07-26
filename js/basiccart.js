/**
 * @file
 * Contains js for the accordion example.
 */
(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.basiccart = {
    attach: function (context, settings) {
          $(".addtocart-quantity-wrapper-container").each(function(){
                var this_id = $(this).attr('id');
                id_split = this_id.split("_");
                var dynamic_id = "quantitydynamictext_"+id_split[1];
                var dynamic_input = '<label for="edit-quantity" class="js-form-required form-required">Quantity</label> <input type="text" class="quantity_dynamic_text form-text required" id="'+dynamic_id+'">';
                $(this).html(dynamic_input);
           });
      
      $(document).on('click',".basiccart-get-quantity",function(e){
        e.preventDefault();   e.stopPropagation();
        var this_ids = $(this).attr('id');
        id_splited = this_ids.split("_");
        var quantity = $('#quantitydynamictext_'+id_splited[1]).val();
         $.ajax({url: this.href+quantity, success: function(result){
              $(".basiccart-grid").each(function(){
                $(this).html(result.block);
              });
              
              $("#"+result.id).hide();
              $("#"+result.id).html(result.text);
              $("#"+result.id).fadeIn('slow').delay(1000).hide(0);

          }});
      });

    }
  };
})(jQuery, Drupal, drupalSettings);

