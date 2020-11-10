<!DOCTYPE html>

<html lang="{{ app()->getLocale() }}">

<head>
    <base href="{{config('app.url')}}">
    <title>Chess Make It</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name=”viewport” content=”width=1024, minimal-ui”>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#3f51b5">
    <link rel="apple-touch-icon" href="img/favicon.png">
    <link rel="icon" type="image/png" href="img/favicon.png">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ url('css/screen.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ url('css/screen.machine.css') }}" type="text/css">
    {{--    <script src="{{url('js/screen.js')}}"></script>--}}
    <script src="{{url('js/gameMachineScreen.js')}}"></script>

    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href='https://fonts.googleapis.com/css?family=Raleway:400,200,300,500,600,700|Merriweather:400,300,300italic,400italic|Roboto:300,400,600,700'
          rel='stylesheet' type='text/css'>

</head>

<body class="game">

<a href="#top" id="toTop"></a>

<header>
    <div class="header navbar-fixed-top" id="navbar-index">
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
                            <li>
                                <a class="cta" href="{{ route('register') }}">Sala Cultivarte</a>
                            </li>
                            <li>
                                <a class="cta" href="{{ route('register') }}">Sala General
                                </a>
                            </li>
                            {{-- @auth--}}
                            {{-- @else--}}
                            {{--     <li>--}}
                            {{--         <a class="cta" href="{{ route('login') }}">Iniciar Sesión</a>--}}
                            {{--     </li>--}}
                            {{--     <li>--}}
                            {{--         <a href="{{ route('register') }}">Registrarse</a>--}}
                            {{--     </li>--}}
                            {{-- @endauth--}}
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
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="wrapper">
    <div class="container">
        <div class="greycontainer">
            <div class="content">
                {!! $data['block_video']->texto !!}

                {{--                <iframe style="width:100%;height:600px;" src="{{route('playmachine.index')}}" frameborder="0"></iframe>--}}
                <div style="text-align:center" id="section-game-init">
                    <div class="pb-2">
                        <button class="button-reverse" id="play-screen" data-route="{{route('play.without.auth')}}">
                            <i class="fa fa-play"></i>
                            <span>Jugar</span>
                        </button>
                    </div>
                    <div class="alert-goal-game"
                         style="">El objetivo es capturar el balón
                    </div>
                    <div id="panel-restart" class="block-pieces-screen text-right" style="height: auto;display: none;background: 0;border:0;">
                        <span class="pull-left">Puntaje ELO-IT:&nbsp;
                            <span id="score-game" class="text-white text-monospace" style="background: black;padding: 4px 6px;border-radius: 9px;">0</span>
                        </span>
                        <button class="btn btn-primary" id="btn-restart" data-route="{{route('play.without.auth')}}">
                            <i class="fa fa-power-off mr-1"></i>
                            <span>Reiniciar</span>
                        </button>
                    </div>
                    <section class="content-game">
                        <span id="name-user" data-name="Diego"></span>
                        <div id="panel-game" style="display: none">
                            <div class="panel-status mb-2 p-3" v-show="active">
                                @verbatim
                                    <span><strong>{{ colorPlay }}</strong></span>
                                    <span>{{ status }}</span>
                                    <span class="font-weight-bold pull-right">{{ time }}</span>
                                @endverbatim
                            </div>
                        </div>
                        <div id="block-pieces" style="display: none;">
                            <img src="{{ url('img/balon.png') }}" alt="" id="piece-ball-origin" class="ispiece goal" style="width: 100%;">
                            <img src="{{ url('img/j15.png') }}" alt="" id="piece-hw-origin" data-color="1" class="ispiece piece-w" style="width: 100%;"
                                 draggable="true">
                            <img src="{{ url('img/j25.png') }}" alt="" id="piece-hb-origin" data-color="0" class="ispiece piece-b" style="width: 100%;"
                                 draggable="true">
                            <img src="{{ url('img/j11.png') }}" alt="" id="piece-tw-origin" data-color="1" class="ispiece piece-w" style="width: 100%;"
                                 draggable="true">
                            <img src="{{ url('img/j21.png') }}" alt="" id="piece-tb-origin" data-color="0" class="ispiece piece-b" style="width: 100%;"
                                 draggable="true">
                            <img src="{{ url('img/j14.png') }}" alt="" id="piece-aw-origin" data-color="1" class="ispiece piece-w" style="width: 100%;"
                                 draggable="true">
                            <img src="{{ url('img/j24.png') }}" alt="" id="piece-ab-origin" data-color="0" class="ispiece piece-b" style="width: 100%;"
                                 draggable="true">
                            <div class="block-ui" id="blockui-board-origin"></div>
                        </div>
                    </section>

                    <div class="block-pieces-screen" id="block-pieces-screen">
                        <div class="box-chess">
                            <img src="{{ url('img/balon.png') }}" alt="" class="goal" style="width: 100%;">
                        </div>
                        <div class="box-chess">
                            <img src="{{ url('img/j15.png') }}" alt="" class="" style="width: 100%;">
                        </div>
                        <div class="box-chess">
                            <img src="{{ url('img/j25.png') }}" alt="" class="" style="width: 100%;">
                        </div>
                        <div class="box-chess">
                            <img src="{{ url('img/j11.png') }}" alt="" class="" style="width: 100%;">
                        </div>
                        <div class="box-chess">
                            <img src="{{ url('img/j21.png') }}" alt="" class="" style="width: 100%;">
                        </div>
                        <div class="box-chess">
                            <img src="{{ url('img/j14.png') }}" alt="" class="" style="width: 100%;">
                        </div>
                        <div class="box-chess">
                            <img src="{{ url('img/j24.png') }}" alt="" class="" style="width: 100%;">
                        </div>
                    </div>
                    <div style="clear:both;"></div>
                    <section id="board-game" class="table-chess"></section>
                    <div class="block-pieces-screen" id="block-pieces-dead" style="display: none;"></div>
                </div>
            </div>
        </div>
        <footer>
            <div class="bottom-logo">
                <p>Diseñado y hospedado por: <a href="https://www.damos.co" target="_blank">DAMOS SOLUCIONES</a></p>
            </div>
        </footer>
    </div>
</div>
<div class="block-content show-init" id="block-ui-load" style="display: none">
    <div class="preloader-jackhammer">
        <p>Cargando...</p>
        <ul class="cssload-flex-container">
            <li>
                <span class="cssload-loading"></span>
            </li>
        </ul>
    </div>
</div>
</body>

</html>