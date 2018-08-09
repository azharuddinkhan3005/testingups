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