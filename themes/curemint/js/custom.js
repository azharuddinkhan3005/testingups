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
  //sticky footer
  footerAlign();
});

function footerAlign() {
  jQuery('.custom-footer').css('display', 'block');
  jQuery('.custom-footer').css('height', 'auto');
  var footerHeight = jQuery('.custom-footer').outerHeight();
  jQuery('body').css('padding-bottom', footerHeight);
  jQuery('.custom-footer').css('height', footerHeight);
}