<!DOCTYPE html>

<head>
    <base href="{{config('app.url')}}">
    <title>Chess Make It</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name=”viewport” content=”width=1024, minimal-ui”>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#3f51b5">

    <link href="css/main.css" rel="stylesheet" type="text/css"/>
    <link rel="apple-touch-icon" href="img/favicon.png">
    <link rel="icon" type="image/png" href="img/favicon.png">
    <!-- This tells the page to watch for special styling for IE9 -->
    <!--[if IE 9 ]>
    <html class="ie9"> <![endif]-->
    <!-- Important external stylesheets -->
    <link rel="stylesheet" href="{{ url('css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ url('css/owl.theme.min.css') }}">
    <link rel="stylesheet" href="{{ url('css/fontello.min.css') }}">
    <link rel="stylesheet" href="{{ url('css/jquery.fancybox8cbb.min.css?v=2.1.5') }}" type="text/css" media="screen"/>
    <!-- First we will load jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

    <!-- Now we load the JS files for the fancy things on the page -->
    <script type="text/javascript" src="{{ url('js/headroom.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('js/jQuery.headroom.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('js/owl.carousel.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('js/jquery.fitvids.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('js/jquery.fancybox.pack8cbb.min.js?v=2.1.5') }}"></script>
    <!--<script type="text/javascript" src="js/retina.min.js"></script>-->
    <script type="text/javascript" src="{{ url('js/jquery.scrollToTop.min.js') }}"></script>
    <!-- Finally we will load the 2 fonts from Google Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Raleway:400,200,300,500,600,700|Merriweather:400,300,300italic,400italic'
          rel='stylesheet' type='text/css'>
</head>

<body>

<a href="#top" id="toTop"></a>

<header>
    <div class="header navbar-fixed-top">
        <div class="header-container">
            <div class="logo">
                <a href="{{config('app.url')}}">
                    <img src="img/logo.png" style="max-width: 250px;" alt="Chess Make It"/>
                </a>
            </div>
            <div class="menu">
                <ul>
                    @if (Route::has('login'))
                        <div class="top-right links">
                            @auth
                                <li>
                                    <a class="cta" href="{{ route('home') }}">Inicio</a>
                                </li>
                            @else
                                <li>
                                    <a class="cta" href="{{ route('login') }}">Iniciar Sesión</a>
                                </li>
                                <li>
                                    <a href="{{ route('register') }}">Registrarse</a>
                                </li>
                            @endauth
                        </div>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</header>

<div id="background">
    <div class="shade">
        <div class="huge-title">
            <div id="slider-main" class="owl-carousel">
                @foreach ($cabezotes as $cabezote)
                    <div class="owl-item" data-image="{{url('cabezotes/'.$cabezote->foto)}}">
                        <h1>{{$cabezote->titulo}}</h1>
                        <h4>{{$cabezote->resumen}}</h4>
                        @if(strlen($cabezote->texto_boton)>3 && strlen($cabezote->enlace_boton)> 3)
                            <div class="button-box">
                                <a href="{{ $cabezote->enlace_boton }}" class="button-white">{{$cabezote->texto_boton}}</a>
                            </div>
                        @endif
                    </div>
                @endforeach {{--
					<div class="owl-item" data-image="{{url('img/background1.jpg')}}">
						<h1>Chess Make It</h1>
						<h4>Retos mentales en base al ajedrez</h4>
						<div class="button-box"><a href="{{ route('home') }}" class="button-white"><i class="icon-guest"></i>Inicio</a></div>
					</div>
					<div class="owl-item" data-image="{{url('img/background2.jpg')}}">
						<h1>Chess Make It</h1>
						<h4>Retos mentales en al ajedrez</h4>
					</div> --}}
            </div>
        </div>
        {{--<div id="godown" class="floating">--}}
        {{--<p>Descubre más</p>--}}
        {{--<img src="img/godown.png" alt="down"/>--}}
        {{--</div>--}}
    </div>
</div>

<div class="wrapper">
    <div class="container">
        {{--        <div class="whitecontainer">--}}
        {{--            <div class="content">--}}
        {{--                {!! $data['text_down_slider']->texto !!}--}}
        {{--            </div>--}}
        {{--        </div>--}}
        <div class="greycontainer">
            <div class="content">
                {!! $data['block_video']->texto !!}

                <iframe style="width:100%;height:800px;" src="{{route('playmachine.index')}}" frameborder="0"></iframe>

                <input type="hidden" id="diagrama" value="3.1" onclick="NuevoDiagw()" onchange="NuevoDiagz()">
                <br>
                <br>
                <canvas id="canvas" width="791" height="573" style="background: #bbb;"></canvas>
                <br>
                <div style="display:none;">
                    <img width=0px id="imagenw" class="fich" src="img/balon2.png">
                    <img width=0px id="imgj11" class="fich" src="img/j11.png">
                    <img width=0px id="imgj13" src="img/j14.png">
                    <img width=0px id="imgj15" src="img/j15.png">
                    <img width=0px id="imgj12" src="img/j11.png">
                    <img width=0px id="imgj14" src="img/j14.png">
                    <img width=0px id="imgj16" src="img/j15.png">
                    <img width=0px id="imgj21" src="img/j21.png">
                    <img width=0px id="imgj23" src="img/j24.png">
                    <img width=0px id="imgj25" src="img/j25.png">
                    <img width=0px id="imgj22" src="img/j21.png">
                    <img width=0px id="imgj24" src="img/j24.png">
                    <img width=0px id="imgj26" src="img/j25.png">
                    <img width=0px id="imgico0" src="img/ico0.png">
                    <img width=0px id="imgico0w" src="img/ico0w.png">
                    <img width=0px id="imgico2" src="img/ico2.png">
                    <img width=0px id="imgico3" src="img/ico3.png">
                    <img width=0px id="imgico4" src="img/ico4.png">
                    <img width=0px id="imgico5" src="img/ico5.png">
                    <img width=0px id="imgico4w" src="img/ico4w.png">
                    <img width=0px id="imgico10" src="img/ico10.png">
                </div>
                <br>
            </div>
            <script src="js/canvas.chess.js"></script>
        </div>
        {{--<div class="whitecontainer">--}}
        {{--<div class="content">--}}
        {{--{!! $data['carrousel_captures']->texto !!}--}}
        {{--</div>--}}
        {{--</div>--}}

        {{--<div class="greycontainer">--}}
        {{--<div class="content">--}}
        {{--{!! $data['other_block_explain']->texto !!}--}}
        {{--</div>--}}
        {{--</div>--}}

        {{--<div class="whitecontainer">--}}
        {{--<div class="content">--}}
        {{--{!! $data['other_block_explain2']->texto!!}--}}
        {{--</div>--}}
        {{--</div>--}}

        {{--<div class="greycontainer">--}}
        {{--<div class="content">--}}
        {{--{!! $data['opinion_clients']->texto!!}--}}
        {{--</div>--}}
        {{--</div>--}}

        {{--<div class="whitecontainer">--}}
        {{--<div class="content">--}}
        {{--{!! $data['galery_photos']->texto!!}--}}
        {{--</div>--}}
        {{--</div>--}}

        {{--<div class="greycontainer">--}}
        {{--<div class="content">--}}
        {{--{!! $data['table_price']->texto!!}--}}
        {{--</div>--}}
        {{--</div>--}}

        {{--<div class="whitecontainer">--}}

        {{--<div class="content">--}}
        {{--{!! $data['boletin']->texto!!}--}}
        {{--<div id="mc_embed_signup">--}}
        {{--<form action="{{route('form_index')}}" method="post" id="subscribe-form" name="subscribe-form">--}}
        {{--@csrf--}}
        {{--<div class="">--}}
        {{--<div class="row">--}}
        {{--<div class="form-group col-sm-6">--}}
        {{--<input class="form-control" type="text" value="" name="nombre" id="txtnombre" placeholder="Nombre" required>--}}
        {{--</div>--}}
        {{--<div class="form-group col-sm-6">--}}
        {{--<input class="form-control" type="email" value="" name="email" id="txtemail" placeholder="Email" required>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--<div class="form-group mb-sm-0">--}}
        {{--<input class="form-control" type="text" name="verificar" id="verificar" placeholder="{{$verificar}}" required>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--<div class="clear">--}}
        {{--<button type="submit" class="button button-reverse">Suscribirme</button>--}}
        {{--</div>--}}
        {{--</form>--}}
        {{--</div>--}}
        {{--<br> {!! $data['politica']->texto!!}--}}
        {{--</div>--}}
        {{--</div>--}}

        {{--<div class="greycontainer">--}}
        {{--<div class="content">--}}
        {{--{!! $data['redes_sociales']->texto!!}--}}
        {{--</div>--}}
        {{--</div>--}}

        <footer>
            <div class="bottom-logo">
                <p>Diseñado y hospedado por: <a href="https://www.damos.co" target="_blank">DAMOS SOLUCIONES</a></p>
            </div>
        </footer>
    </div>
</div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
  jQuery(document).ready(function() {
    /*$('#subscribe-form').on('submit', function() {
      var $form = $(this);

      swal('Enviando...');

      $.ajax({
        url: $form.attr('action'),
        data: $form.serialize(),
        method: 'POST',
      }).done(function(response) {
        if (response.status == 200) {
          $form.hide();
        }

        swal(response.message);
      });

      /!*console.log($(this).serialize());*!/

      return false;
    });*/
    /* Target your .container, .wrapper, .post, etc.*/
    var $videoContainer = jQuery('.video-container');
    if ($videoContainer.length > 0) {
      $videoContainer.fitVids();
    }
    jQuery('.fancybox').fancybox();
    jQuery('#toTop').scrollToTop(1000);
    /*jQuery('.testimonials').owlCarousel({
      /!* Most important owl features*!/
      items: 1,
      itemsCustom: false,
      itemsDesktop: [1199, 1],
      itemsDesktopSmall: [980, 1],
      itemsTablet: [768, 1],
      itemsTabletSmall: false,
      itemsMobile: [479, 1],
      singleItem: false,
      itemsScaleUp: false,
      /!*Basic Speeds*!/
      slideSpeed: 200,
      paginationSpeed: 800,
      rewindSpeed: 1000,
      /!*Autoplay*!/
      autoPlay: true,
      stopOnHover: true,
    });*/
    jQuery('#slider-main').owlCarousel({
      /* Most important owl features*/
      items: 1,
      itemsCustom: false,
      itemsDesktop: [1199, 1],
      itemsDesktopSmall: [980, 1],
      itemsTablet: [768, 1],
      itemsTabletSmall: false,
      itemsMobile: [479, 1],
      singleItem: false,
      itemsScaleUp: false,
      /*Basic Speeds*/
      slideSpeed: 200,
      paginationSpeed: 800,
      rewindSpeed: 1000,
      /*Autoplay*/
      autoPlay: true,
      stopOnHover: true,
      afterMove: function(evt) {
        /* console.log(evt)*/
        let owlcurrent = $('.owl-carousel').data('owlCarousel').owl;
        /* console.log(owlcurrent.currentItem, owlcurrent.userItems[owlcurrent.currentItem].dataset.image);*/
        $('#background').css('background-image', 'url(' + owlcurrent.userItems[owlcurrent.currentItem].dataset.image + ')');
      },
    });

    jQuery('.owl-example').owlCarousel({
      /* Most important owl features*/
      items: 2,
      itemsCustom: false,
      itemsDesktop: [1199, 2],
      itemsDesktopSmall: [980, 1],
      itemsTablet: [768, 1],
      itemsTabletSmall: false,
      itemsMobile: [479, 1],
      singleItem: false,
      itemsScaleUp: false,
      /*Basic Speeds*/
      slideSpeed: 200,
      paginationSpeed: 800,
      rewindSpeed: 1000,
      /*Autoplay*/
      autoPlay: false,
      stopOnHover: false,
      /* Navigation*/
      navigation: false,
      navigationText: ['prev', 'next'],
      rewindNav: true,
      scrollPerPage: false,
      /*Pagination*/
      pagination: true,
      paginationNumbers: false,
      /* Responsive*/
      responsive: true,
      responsiveRefreshRate: 200,
      responsiveBaseWidth: window,
      /* CSS Styles*/
      baseClass: 'owl-carousel',
      theme: 'owl-theme',
      /*Lazy load*/
      lazyLoad: false,
      lazyFollow: true,
      lazyEffect: 'fade',
      /*Auto height*/
      autoHeight: true,
      /*JSON*/
      jsonPath: false,
      jsonSuccess: false,
      /*Mouse Events*/
      dragBeforeAnimFinish: true,
      mouseDrag: true,
      touchDrag: true,
      /*Transitions*/
      transitionStyle: false,
      /* Other*/
      addClassActive: false,
      /*Callbacks*/
      beforeUpdate: false,
      afterUpdate: false,
      beforeInit: false,
      afterInit: false,
      beforeMove: false,
      afterMove: false,
      afterAction: false,
      startDragging: false,
      afterLazyLoad: false,
    });

    jQuery('.navbar-fixed-top').headroom({
      'tolerance': 15,
      'offset': 100,
    });

    /*if (jQuery(window).width() >= 1025) {
      jQuery(window).bind('scroll', function(e) {
        parallaxScroll();
      });
    }

    function parallaxScroll() {
      var scrolledY = jQuery(window).scrollTop();
      jQuery('.huge-title').css('bottom', '-' + ((scrolledY * 0.55)) + 'px');
      jQuery('.container').css('top', '-' + ((scrolledY * 0.50)) + 'px');
    }

    jQuery(window).scroll(function() {
      if (jQuery(window).width() >= 1024) {
        jQuery('#phone').each(function() {
          var imagePos = jQuery(this).offset().top;
          var topOfWindow = jQuery(window).scrollTop();
          if (imagePos < topOfWindow + 600) {
            jQuery(this).addClass('hatch');
          }
        });
      }
    });

    var didScroll = false;
    var icon = $('.huge-title, #godown');
    var $window = $(window);
    jQuery(window).scroll(function() {
      didScroll = true;
    });
    window.setInterval(function() {
      if (didScroll) {
        if (1 - $window.scrollTop() / 200 > -20) {
          icon.css({
            opacity: 1 - $window.scrollTop() / 500,
          });
        }
        didScroll = false;
      }
    }, 50);*/

  });
</script>
</body>

</html>