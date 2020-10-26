<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
	<base href="{{config('app.url')}}">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#3f51b5">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<link rel="apple-touch-icon" href="favicon.png">
	<link rel="icon" type="image/png" href="{{url('images/favicon.png')}}">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{$title}} | Editor CMS | {{ config('app.name', 'Laravel') }}</title>

	<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
	<script>
		WebFont.load({
			google: {
				"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
			},
			active: function () {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!--end::Page Vendors -->
	<link href="{{url('metro/vendors.bundle.css')}}" rel="stylesheet" type="text/css" />
	<link href="{{url('metro/style.bundle.css')}}" rel="stylesheet" type="text/css" />
	<link href="{{ url('js/datatables/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
	<link href="{{ url('js/fancy/jquery.fancybox.min.css')}}" rel="stylesheet" type="text/css" />
	<link href="{{ url('js/jqueryui/jquery-ui.min.css')}}" rel="stylesheet" type="text/css" /> @yield('estilos')
	<link href="{{url('css/panel.custom.min.css')}}" rel="stylesheet" type="text/css" />
</head>

<body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default m-brand--minimize m-aside-left--minimize m-aside-left--fixed">

	<div class="m-grid m-grid--hor m-grid--root m-page">
		<header id="m_header" class="m-grid__item m-header" m-minimize-offset="200" m-minimize-mobile-offset="200">
			<div class="m-container m-container--fluid m-container--full-height">
				<div class="m-stack m-stack--ver m-stack--desktop">
					<div class="m-stack__item m-brand  m-brand--skin-dark ">
						<div class="m-stack m-stack--ver m-stack--general">
							<div class="m-stack__item m-stack__item--middle m-brand__logo">
								<a href="{{ route('panel') }}" class="m-brand__logo-wrapper">
									<img alt="" src="{{ url('img/logo_panel.png')}}" class="img-fluid" />
								</a>
							</div>
							<div class="m-stack__item m-stack__item--middle m-brand__tools">
								<a href="javascript:;" id="m_aside_left_minimize_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-desktop-inline-block m-brand__toggler m-brand__toggler--active">
									<span></span>
								</a>
								<a href="javascript:;" id="m_aside_left_offcanvas_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-tablet-and-mobile-inline-block">
									<span></span>
								</a>
								<a id="m_aside_header_topbar_mobile_toggle" href="javascript:;" class="m-brand__icon m--visible-tablet-and-mobile-inline-block">
									<i class="flaticon-more"></i>
								</a>
							</div>
						</div>
					</div>
					<div class="m-stack__item m-stack__item--fluid m-header-head" id="m_header_nav">
						<div id="m_header_menu" class="m-header-menu m-aside-header-menu-mobile m-aside-header-menu-mobile--offcanvas  m-header-menu--skin-light m-header-menu--submenu-skin-light m-aside-header-menu-mobile--skin-dark m-aside-header-menu-mobile--submenu-skin-dark ">
							<ul class="m-menu__nav  m-menu__nav--submenu-arrow ">
								{{--
								<li class="m-menu__item  m-menu__item--submenu m-menu__item--rel" m-menu-submenu-toggle="click" m-menu-link-redirect="1"
								 aria-haspopup="true">
									<a href="{{route('screen')}}" class="m-menu__link" target="_blank">
										<span class="m-menu__link-text">Ver Landing</span>
										<i class="m-menu__link-icon la la-external-link la-2x ml-1"></i>
									</a>
								</li> --}}
							</ul>
						</div>
						<div id="m_header_topbar" class="m-topbar  m-stack m-stack--ver m-stack--general m-stack--fluid">
							<div class="m-stack__item m-topbar__nav-wrapper">
								<ul class="m-topbar__nav m-nav m-nav--inline">
									<li class="m-dropdown m-nav__item">
										<a href="{{route('screen')}}" class="m-nav__link" target="_blank">
											<span class="m-nav__link-icon"><i style="font-size:2rem;" class="la la-external-link"></i></span>
										</a>
									</li>
									<li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"
									 m-dropdown-toggle="click">
										<a href="#" class="m-nav__link m-dropdown__toggle">
											<span class="m-topbar__userpic">
												@if(auth()->user()->avatar)
													@if(strpos(auth()->user()->avatar, 'http') === false)
														<img src="photosuser/{{ auth()->user()->avatar }}" alt="Avatar {{ auth()->user()->name }}" class="img-avatar mr-2">
													@else
														<img src="{{ auth()->user()->avatar }}" alt="Avatar {{ auth()->user()->name }}" class="img-avatar mr-2">
													@endif
												 @else
												<img src="{{ url('photosuser/avatar_default.jpg') }}" alt="Avatar {{ auth()->user()->name }}" class="img-avatar mr-2">
												 @endif
											</span>
										</a>
										<div class="m-dropdown__wrapper">
											<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
											<div class="m-dropdown__inner">
												<div class="m-dropdown__body">
													<div class="m-dropdown__content">
														<ul class="m-nav m-nav--skin-light">
															<li class="m-nav__item">
																<a href="{{route('home')}}" class="m-nav__link">
																	<i class="m-nav__link-icon la la-home"></i>
																	<span class="m-nav__link-text">Inicio</span>
																</a>
															</li>
															<li class="m-nav__item">
																<a href="{{route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="m-nav__link">
																	<i class="m-nav__link-icon la la-sign-out"></i>
																	<span class="m-nav__link-text">Salir</span>
																</a>
															</li>
															<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
																@csrf
															</form>
														</ul>
													</div>
												</div>
											</div>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</header>
		<div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
	@include('../panel/menu')
			<div class="m-grid__item m-grid__item--fluid m-wrapper">
				<!-- BEGIN: Subheader -->
				{{--
				<div class="m-subheader ">
					<div class="d-flex align-items-center">
						<div class="mr-auto">
							<h3 class="m-subheader__title ">Cabezotes</h3>
						</div>
					</div>
				</div> --}}
				<!-- END: Subheader -->
				<div class="m-content">
					@yield('content')
				</div>
			</div>
		</div>
	@include('../panel/footer')
	</div>
	<input type="hidden" value="{{(isset($menu_item)) ? $menu_item:''}}" id="menu-active">

	<script src="{{url('metro/vendors.bundle.js')}}"></script>
	<script src="{{url('metro/scripts.bundle.js')}}"></script>
	<script src="{{ url('js/datatables/js/jquery.dataTables.min.js') }}"></script>
	<script src="{{ url('js/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
	<script src="{{ url('js/fancy/jquery.fancybox.min.js') }}"></script>
	<script src="{{ url('js/jqueryui/jquery-ui.min.js') }}"></script>
	<script src="{{url('js/panel.min.js')}}"></script>
	@yield('scripts')
</body>

</html>