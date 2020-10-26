require('./vendors/headroom.min');
require('./vendors/jQuery.headroom.min');
require('./vendors/jquery.scrollToTop.min');
require('./vendors/owl.carousel.min');

$(function() {
  $('#toTop').scrollToTop(1000);

  const $sliderMain = $('#slider-main');
  if ($sliderMain.length > 0) {
    const $background = $('#background');

    $sliderMain.owlCarousel({
      items: 1,
      itemsCustom: false,
      itemsDesktop: [1199, 1],
      itemsDesktopSmall: [980, 1],
      itemsTablet: [768, 1],
      itemsTabletSmall: false,
      itemsMobile: [479, 1],
      singleItem: false,
      itemsScaleUp: false,

      slideSpeed: 200,
      paginationSpeed: 800,
      rewindSpeed: 1000,

      autoPlay: true,
      stopOnHover: true,
      afterMove: function() {
        const owlcurrent = $sliderMain.data('owlCarousel').owl;
        /* console.log(owlcurrent.currentItem, owlcurrent.userItems[owlcurrent.currentItem].dataset.image);*/
        $background.css('background-image', 'url(' + owlcurrent.userItems[owlcurrent.currentItem].dataset.image + ')');
      },
    });
  }

  window.headroomChess = $('#navbar-index').headroom({
    'tolerance': 15,
    'offset': 100,
  });
});