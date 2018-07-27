jQuery(document).ready(function() {
  // most purchased items slider
  jQuery(".most-purchased .view-content").slick({
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