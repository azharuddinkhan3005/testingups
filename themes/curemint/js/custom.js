jQuery(document).ready(function() {
  // most purchased items slider
  jQuery(".most-purchased-listing .view-content").slick({
    dots: false,
    infinite: true,
    slidesToShow: 5,
    slidesToScroll: 1
  });
  // supplies category slider
  jQuery(".supplies .view-content").slick({
    dots: false,
    infinite: true,
    slidesToShow: 5,
    slidesToScroll: 1
  });
  // shop by suppliers slider
  jQuery(".shop-supplier .view-content").slick({
    dots: false,
    infinite: true,
    slidesToShow: 16,
    slidesToScroll: 1
  });
  //  checkout button clicked
  jQuery(".checkout .btn-default").click(function(){
     jQuery("#edit-checkout").trigger("click");
     return false;
  });

  // checkout button disable remove on dom ready
  jQuery(".checkout .btn-default").removeClass('disabled');

  // modules show/hide on dom 
  jQuery('.shop-supplier').removeClass('hide');
  jQuery('.supplies').removeClass('hide');
  jQuery('.most-purchased-listing').removeClass('hide');

  // sort by option added and relevance option remove
  jQuery(".path-search .sort-products select option[value='search_api_relevance ASC']").remove();
});

//sticky footer
var height = jQuery(window).height();
var headerHeight = jQuery('header').outerHeight();
var footerHeight = jQuery('.custom-footer').outerHeight();
var finalHeight = height - headerHeight - footerHeight - 75;
jQuery('.main-container').css('min-height', finalHeight);

jQuery(window).resize(function(){
  var height = jQuery(window).height();
  var headerHeight = jQuery('header').outerHeight();
  var footerHeight = jQuery('.custom-footer').outerHeight();
  var finalHeight = height - headerHeight - footerHeight - 75;
  jQuery('.main-container').css('min-height', finalHeight);
});
