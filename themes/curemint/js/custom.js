jQuery(document).ready(function() {
  // most purchased items slider
  jQuery("#block-curemint-views-block-product-list-block-1 .view-content").slick({
    dots: false,
    infinite: true,
    slidesToShow: 5,
    slidesToScroll: 1
  });
  // supplies category slider
  jQuery("#block-views-block-supplies-block-1 .view-content").slick({
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