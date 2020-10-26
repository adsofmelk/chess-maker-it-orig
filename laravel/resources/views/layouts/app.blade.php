<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <base href="{{ config('app.url') }}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#3f51b5">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="apple-touch-icon" href="favicon.png">
    <link rel="icon" type="image/png" href="favicon.png"> {{--
	<!-- CSRF Token -->--}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{--
    <!-- Scripts -->--}}
    <script src="{{ asset('js/app.js') }}" defer></script>

    {{--
    <!-- Fonts -->--}}
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,600,700" rel="stylesheet" type="text/css"> {{--
	<!-- Styles -->--}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('estilos')
</head>

<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container">
            <a class="navbar-brand" href="@guest{{ url('/') }}@else{{route('home')}}@endguest">
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                {{--
                <!-- Left Side Of Navbar -->--}}
                <ul class="navbar-nav mr-auto">

                </ul>

                {{--
                <!-- Right Side Of Navbar -->--}}
                <ul class="navbar-nav ml-auto">
                    {{--
                    <!-- Authentication Links -->--}}
                    @guest
                        <li><a class="nav-link" href="{{ route('login') }}">{{ __('Iniciar Sesión') }}</a></li>
                        <li><a class="nav-link" href="{{ route('register') }}">{{ __('Registrarse') }}</a></li>
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true"
                               aria-expanded="false" v-pre>
                                @if(auth()->user()->avatar)
                                    @if(strpos(auth()->user()->avatar, 'http') === false)
                                        <img src="photosuser/{{ auth()->user()->avatar }}" alt="Avatar {{ auth()->user()->name }}" class="img-avatar mr-2">
                                    @else
                                        <img src="{{ auth()->user()->avatar }}" alt="Avatar {{ auth()->user()->name }}" class="img-avatar mr-2">
                                    @endif
                                @else
                                    <img src="{{ url('photosuser/avatar_default.jpg') }}"
                                         alt="Avatar {{ auth()->user()->name }}" class="img-avatar mr-2">
                                @endif
                                {{ auth()->user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                @if(auth()->user()->admin==1)
                                    <a href="{{route('panel')}}" class="dropdown-item">
                                        <i class="fa fa-address-card mr-2"></i>
                                        <span>Panel</span>
                                    </a> @endif
                                <a href="{{route('index_perfil')}}" class="dropdown-item">
                                    <i class="fa fa-user mr-2"></i>
                                    <span>Mi cuenta</span>
                                </a>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
													 document.getElementById('logout-form').submit();">
                                    <i class="fa fa-sign-out mr-2"></i>
                                    <span>{{ __('Salir') }}</span>
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>
</div>
<div class="container">
    @yield('content2')
</div>

@yield('contentGame')

<div class="block-content" id="block-ui">
    {{-- <i class="fa fa-spin fa-spinner"></i> --}}
    <h1>Solo se puede tener una pestaña abierta a la vez.</h1>
</div>
<div class="block-content show-init" id="block-ui-load" style="{{ (isset($blockUI)) ? 'display: block' : '' }}">
    {{-- <i class="fa fa-spin fa-spinner"></i> --}}
    <div class="preloader-jackhammer">
        <ul class="cssload-flex-container">
            <li>
                <span class="cssload-loading"></span>
            </li>
        </ul>
    </div>
</div>
@yield('scripts')
</body>

</html>