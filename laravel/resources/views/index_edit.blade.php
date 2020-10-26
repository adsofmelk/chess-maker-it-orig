<!DOCTYPE html>

<head>
	<base href="{{config('app.url')}}">
	<title>Chess Make It</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name=”viewport” content=”width=1024, minimal-ui”>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="theme-color" content="#3f51b5">

	<link href="css/main.css" rel="stylesheet" type="text/css" />
	<link rel="apple-touch-icon" href="img/favicon.png">
	<link rel="icon" type="image/png" href="img/favicon.png">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<!-- This tells the page to watch for special styling for IE9 -->
	<!--[if IE 9 ]>    <html class= "ie9"> <![endif]-->
	<!-- Important external stylesheets -->
	<link rel="stylesheet" href="{{ url('css/owl.carousel.min.css') }}">
	<link rel="stylesheet" href="{{ url('css/owl.theme.min.css') }}">
	<link rel="stylesheet" href="{{ url('css/fontello.min.css') }}">
	<link rel="stylesheet" href="{{ url('css/jquery.fancybox8cbb.min.css?v=2.1.5') }}" type="text/css" media="screen" />
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
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt"
	 crossorigin="anonymous">
	<link href='https://fonts.googleapis.com/css?family=Raleway:400,200,300,500,600,700|Merriweather:400,300,300italic,400italic'
	 rel='stylesheet' type='text/css'>
	<link href="{{ url('css/index.edit.css') }}" rel='stylesheet' type='text/css'>
	<style>
		.d-none{display:none;}
	</style>
</head>

<body>
	<a href="{{url('/')}}" class="link-viewpreview" target="_blank">Ver página</a>
	<a href="#top" id="toTop"></a>
	<header>
		<div class="header navbar-fixed-top">
			<div class="header-container">
				<div class="logo">
					<a href="{{config('app.url')}}">
						<img src="img/logo.png" style="max-width: 250px;" alt="Chess Make It" />
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
				<h1>Chess Make It</h1>
				<h4>Retos mentales en base al ajedrez</h4>
				<div class="testimonials owl-carousel">
					@if (Route::has('login')) @auth
					<div>
						<div class="button-box"><a href="{{ route('home') }}" class="button-white"><i class="icon-guest"></i>Inicio</a></div>
					</div>
					@else
					<div>
						<div class="button-box"><a href="{{ route('register') }}" class="button-white"><i class="icon-guest"></i>Registrarse</a></div>
					</div> @endauth @endif
				</div>
			</div>
			<div id="godown" class="floating d-none">
				<p>Descubre más</p>
				<img src="img/godown.png" alt="down" />
			</div>
		</div>
	</div>

	<div class="wrapper">
		<div class="container">
			<div class="d-none">
				<div class="content">
					<div class="ckeditor" id="editor-{{$data['text_down_slider']->id}}" data-id="{{$data['text_down_slider']->id}}" contenteditable="true">
						{!! $data['text_down_slider']->texto !!}
					</div>
					<div class="panel-actions">
						<button class="btn btn-save-ck" data-editor="editor-{{$data['text_down_slider']->id}}" data-texto="{{$data['text_down_slider']->id}}">
							<i class="fa fa-save"></i>
						</button>
						<button class="btn btn-default-ck" data-editor="editor-{{$data['text_down_slider']->id}}" data-texto="{{$data['text_down_slider']->id}}">
							<i class="fa fa-marker"></i>
						</button>
						<button class="btn btn-reset-ck" data-editor="editor-{{$data['text_down_slider']->id}}" data-texto="{{$data['text_down_slider']->id}}">
							<i class="fa fa-undo"></i>
						</button>
					</div>
				</div>
			</div>
			<div class="greycontainer">
				<div class="content">
					<div class="ckeditor" id="editor-{{$data['block_video']->id}}" data-id="{{$data['block_video']->id}}" contenteditable="true">
						{!! $data['block_video']->texto !!}
					</div>
					<div class="panel-actions">
						<button class="btn btn-save-ck" data-editor="editor-{{$data['block_video']->id}}" data-texto="{{$data['block_video']->id}}">
							<i class="fa fa-save"></i>
						</button>
						<button class="btn btn-default-ck" data-editor="editor-{{$data['block_video']->id}}" data-texto="{{$data['block_video']->id}}">
							<i class="fa fa-marker"></i>
						</button>
						<button class="btn btn-reset-ck" data-editor="editor-{{$data['block_video']->id}}" data-texto="{{$data['block_video']->id}}">
							<i class="fa fa-undo"></i>
						</button>
					</div>
				</div>
			</div>
			<div class="whitecontainer d-none">
				<div class="content">
					<div class="ckeditor" id="editor-{{$data['carrousel_captures']->id}}" data-id="{{$data['carrousel_captures']->id}}" contenteditable="true">
						{!! $data['carrousel_captures']->texto!!}
					</div>
					<div class="panel-actions">
						<button class="btn btn-save-ck" data-editor="editor-{{$data['carrousel_captures']->id}}" data-texto="{{$data['carrousel_captures']->id}}">
							<i class="fa fa-save"></i>
						</button>
						<button class="btn btn-default-ck" data-editor="editor-{{$data['carrousel_captures']->id}}" data-texto="{{$data['carrousel_captures']->id}}">
							<i class="fa fa-marker"></i>
						</button>
						<button class="btn btn-reset-ck" data-editor="editor-{{$data['carrousel_captures']->id}}" data-texto="{{$data['carrousel_captures']->id}}">
							<i class="fa fa-undo"></i>
						</button>
					</div>
				</div>
			</div>

			<div class="greycontainer d-none">
				<div class="content" style="display:flex;">
					<div class="ckeditor" id="editor-{{$data['other_block_explain']->id}}" data-id="{{$data['other_block_explain']->id}}" contenteditable="true">
						{!! $data['other_block_explain']->texto!!}
					</div>
					<div class="panel-actions">
						<button class="btn btn-save-ck" data-editor="editor-{{$data['other_block_explain']->id}}" data-texto="{{$data['other_block_explain']->id}}">
							<i class="fa fa-save"></i>
						</button>
						<button class="btn btn-default-ck" data-editor="editor-{{$data['other_block_explain']->id}}" data-texto="{{$data['other_block_explain']->id}}">
							<i class="fa fa-marker"></i>
						</button>
						<button class="btn btn-reset-ck" data-editor="editor-{{$data['other_block_explain']->id}}" data-texto="{{$data['other_block_explain']->id}}">
							<i class="fa fa-undo"></i>
						</button>
					</div>
				</div>
			</div>

			<div class="whitecontainer d-none">
				<div class="content">
					<div style="display:flex;">

						<div class="ckeditor" id="editor-{{$data['other_block_explain2']->id}}" data-id="{{$data['other_block_explain2']->id}}" contenteditable="true">
							{!! $data['other_block_explain2']->texto!!}
						</div>
						<div class="panel-actions">
							<button class="btn btn-save-ck" data-editor="editor-{{$data['other_block_explain2']->id}}" data-texto="{{$data['other_block_explain2']->id}}">
								<i class="fa fa-save"></i>
							</button>
							<button class="btn btn-default-ck" data-editor="editor-{{$data['other_block_explain2']->id}}" data-texto="{{$data['other_block_explain2']->id}}">
								<i class="fa fa-marker"></i>
							</button>
							<button class="btn btn-reset-ck" data-editor="editor-{{$data['other_block_explain2']->id}}" data-texto="{{$data['other_block_explain2']->id}}">
								<i class="fa fa-undo"></i>
							</button>
						</div>
					</div>
				</div>
			</div>

			<div class="greycontainer d-none">
				<div class="content">
					<div class="ckeditor" id="editor-{{$data['opinion_clients']->id}}" data-id="{{$data['opinion_clients']->id}}" contenteditable="true">
						{!! $data['opinion_clients']->texto!!}
					</div>
					<div class="panel-actions">
						<button class="btn btn-save-ck" data-editor="editor-{{$data['opinion_clients']->id}}" data-texto="{{$data['opinion_clients']->id}}">
							<i class="fa fa-save"></i>
						</button>
						<button class="btn btn-default-ck" data-editor="editor-{{$data['opinion_clients']->id}}" data-texto="{{$data['opinion_clients']->id}}">
							<i class="fa fa-marker"></i>
						</button>
						<button class="btn btn-reset-ck" data-editor="editor-{{$data['opinion_clients']->id}}" data-texto="{{$data['opinion_clients']->id}}">
							<i class="fa fa-undo"></i>
						</button>
					</div>
				</div>
			</div>

			<div class="whitecontainer d-none">
				<div class="content" style="display:flex;">
					<div class="ckeditor" id="editor-{{$data['galery_photos']->id}}" data-id="{{$data['galery_photos']->id}}" contenteditable="true">
						{!! $data['galery_photos']->texto!!}
					</div>
					<div class="panel-actions">
						<button class="btn btn-save-ck" data-editor="editor-{{$data['galery_photos']->id}}" data-texto="{{$data['galery_photos']->id}}">
							<i class="fa fa-save"></i>
						</button>
						<button class="btn btn-default-ck" data-editor="editor-{{$data['galery_photos']->id}}" data-texto="{{$data['galery_photos']->id}}">
							<i class="fa fa-marker"></i>
						</button>
						<button class="btn btn-reset-ck" data-editor="editor-{{$data['galery_photos']->id}}" data-texto="{{$data['galery_photos']->id}}">
							<i class="fa fa-undo"></i>
						</button>
					</div>
				</div>
			</div>

			<div class="greycontainer d-none">
				<div class="content">
					<div class="ckeditor" id="editor-{{$data['table_price']->id}}" data-id="{{$data['table_price']->id}}" contenteditable="true">
						{!! $data['table_price']->texto!!}
					</div>
					<div class="panel-actions">
						<button class="btn btn-save-ck" data-editor="editor-{{$data['table_price']->id}}" data-texto="{{$data['table_price']->id}}">
							<i class="fa fa-save"></i>
						</button>
						<button class="btn btn-default-ck" data-editor="editor-{{$data['table_price']->id}}" data-texto="{{$data['table_price']->id}}">
							<i class="fa fa-marker"></i>
						</button>
						<button class="btn btn-reset-ck" data-editor="editor-{{$data['table_price']->id}}" data-texto="{{$data['table_price']->id}}">
							<i class="fa fa-undo"></i>
						</button>
					</div>
				</div>
			</div>

			<div class="whitecontainer d-none">

				<div class="content">
					<div style="position: relative;margin-bottom:70px;">
						<div class="ckeditor" id="editor-{{$data['boletin']->id}}" data-id="{{$data['boletin']->id}}" contenteditable="true">
							{!! $data['boletin']->texto!!}
						</div>
						<div class="panel-actions">
							<button class="btn btn-save-ck" data-editor="editor-{{$data['boletin']->id}}" data-texto="{{$data['boletin']->id}}">
								<i class="fa fa-save"></i>
							</button>
							<button class="btn btn-default-ck" data-editor="editor-{{$data['boletin']->id}}" data-texto="{{$data['boletin']->id}}">
								<i class="fa fa-marker"></i>
							</button>
							<button class="btn btn-reset-ck" data-editor="editor-{{$data['boletin']->id}}" data-texto="{{$data['boletin']->id}}">
								<i class="fa fa-undo"></i>
							</button>
						</div>
					</div>

					<div id="mc_embed_signup">
						<form action="" method="post" id="subscribe-form" name="subscribe-form">
							@csrf
							<div class="">
								<div class="row">
									<div class="form-group col-sm-6">
										<input class="form-control" type="text" value="" name="nombre" id="txtnombre" placeholder="Nombre" required>
									</div>
									<div class="form-group col-sm-6">
										<input class="form-control" type="email" value="" name="email" id="txtemail" placeholder="Email" required>
									</div>
								</div>
								<div class="form-group mb-sm-0">
									<input class="form-control" type="text" name="verificar" id="verificar" placeholder="" required>
								</div>
							</div>
							<div class="clear">
								<button type="submit" class="button button-reverse">Suscribirme</button>
							</div>
						</form>
					</div>
					<div style="position:relative;">
						<div class="ckeditor" id="editor-{{$data['politica']->id}}" data-id="{{$data['politica']->id}}" contenteditable="true">
							{!! $data['politica']->texto!!}
						</div>
						<div class="panel-actions">
							<button class="btn btn-save-ck" data-editor="editor-{{$data['politica']->id}}" data-texto="{{$data['politica']->id}}">
								<i class="fa fa-save"></i>
							</button>
							<button class="btn btn-default-ck" data-editor="editor-{{$data['politica']->id}}" data-texto="{{$data['politica']->id}}">
								<i class="fa fa-marker"></i>
							</button>
							<button class="btn btn-reset-ck" data-editor="editor-{{$data['politica']->id}}" data-texto="{{$data['politica']->id}}">
								<i class="fa fa-undo"></i>
							</button>
						</div>
					</div>
				</div>
			</div>

			<div class="greycontainer d-none">
				<div class="content">
					<div class="ckeditor" id="editor-{{$data['redes_sociales']->id}}" data-id="{{$data['redes_sociales']->id}}" contenteditable="true">
						{!! $data['redes_sociales']->texto!!}
					</div>
					<div class="panel-actions">
						<button class="btn btn-save-ck" data-editor="editor-{{$data['redes_sociales']->id}}" data-texto="{{$data['redes_sociales']->id}}">
							<i class="fa fa-save"></i>
						</button>
						<button class="btn btn-default-ck" data-editor="editor-{{$data['redes_sociales']->id}}" data-texto="{{$data['redes_sociales']->id}}">
							<i class="fa fa-marker"></i>
						</button>
						<button class="btn btn-reset-ck" data-editor="editor-{{$data['redes_sociales']->id}}" data-texto="{{$data['redes_sociales']->id}}">
							<i class="fa fa-undo"></i>
						</button>
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
	<div class="block-content" id="block-content">
		<div class="preloader-jackhammer">
			<ul class="cssload-flex-container">
				<li>
					<span class="cssload-loading"></span>
				</li>
			</ul>
		</div>
	</div>
	<input type="hidden" name="" data-default="{{route('default_editorial')}}" data-reset="{{route('reset_editorial')}}" value="{{route('save_editorial')}}"
	 id="url-update-editorial">

	<script src="{{url('js/ckeditor/4/ckeditor.js')}}"></script>
	<script src="https://unpkg.com/popper.js@1.14.3/dist/umd/popper.min.js"></script>
	<script src="https://unpkg.com/tooltip.js@1.2.0/dist/umd/tooltip.min.js"></script>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

	<script>
		jQuery(document).ready(function(){
			$('.btn-save-ck').each(function(index, el){
				new Tooltip($(el), {
					placement: 'right',
					title: "Guardar"
				});
			});
			$('.btn-reset-ck').each(function(index, el){
				new Tooltip($(el), {
					placement: 'right',
					title: "Restablecer por defecto"
				});
			});
			$('.btn-default-ck').each(function(index, el){
				new Tooltip($(el), {
					placement: 'right',
					title: "Establecer por defecto"
				});
			});
			$(document).on('click','.btn-save-ck',function(){
				var token = document.head.querySelector('meta[name="csrf-token"]').content;
				var data = {
					_token: token,
					html: ckeditors[$(this).data('editor')].getData(),
					id: $(this).data('texto'),
				};
				/* console.log(data)*/
				$.ajax({
					url: $('#url-update-editorial').val(),
					data: data,
					method: 'POST',
					beforeSend: function(){
						$('#block-content').show();
					},
				})
				.always(function(){
					$('#block-content').hide();
				}).done(function(response){
					swal('Se ha guardado correctamente');
				});
				/* console.log($(this))*/

			});
			$(document).on('click','.btn-default-ck',function(){
				var esto = $(this);
				swal({
					title: "¿Está seguro de establecer el contenido actual por defecto?",
					text: "",
					icon: "warning",
					buttons: {
						cancel:{
							value: 0,
							text:'Cancelar',
							visible:true,
						},
						confirm: {
							text: "Seguro",
							value: true,
							visible: true,
							className: "",
							closeModal: true,
						}
					},
				}).then((willDefault) => {
					if (willDefault) {
						var token = document.head.querySelector('meta[name="csrf-token"]').content;
						var data = {
							_token: token,
							html: ckeditors[esto.data('editor')].getData(),
							id: esto.data('texto'),
						};
						$.ajax({
							url: $('#url-update-editorial').data('default'),
							data: data,
							method: 'POST',
							beforeSend: function(){
								$('#block-content').show();
							},
						})
						.always(function(){
							$('#block-content').hide();
						}).done(function(response){
							swal('Se ha establecido por defecto correctamente');
						});
					}
				});
			});
			$(document).on('click','.btn-reset-ck',function(){
				var esto = $(this);
				swal({
					title: "¿Está seguro de restablecer el contenido por defecto?",
					text: "",
					icon: "warning",
					buttons: {
						cancel:{
							value: 0,
							text:'Cancelar',
							visible:true,
						},
						confirm: {
							text: "Seguro",
							value: true,
							visible: true,
							className: "",
							closeModal: true,
						}
					},
				}).then((willDefault) => {
					if (willDefault) {
						var token = document.head.querySelector('meta[name="csrf-token"]').content;
						var data = {
							_token: token,
							/*html: ckeditors[esto.data('editor')].getData(),*/
							id: esto.data('texto'),
						};
						$.ajax({
							url: $('#url-update-editorial').data('reset'),
							data: data,
							method: 'POST',
							beforeSend: function(){
								$('#block-content').show();
							},
						})
						.always(function(){
							$('#block-content').hide();
						}).done(function(response){
							swal('Se ha establecido por defecto correctamente');
							ckeditors[esto.data('editor')].setData(response.html);
						});
					}
				});
			});

			/* jQuery(".fancybox").fancybox();*/
			jQuery("#toTop").scrollToTop(1000);

			jQuery(".navbar-fixed-top").headroom({
				"tolerance": 15,
				"offset": 100
			});
			$('.owl-carousel').css('display','block');

			if(jQuery(window).width() >= 1025){
				jQuery(window).bind('scroll',function(e){
					parallaxScroll();
				});
			}

			function parallaxScroll(){
				var scrolledY = jQuery(window).scrollTop();
				jQuery('.huge-title').css('bottom','-'+((scrolledY*0.55))+'px');
				jQuery('.container').css('top','-'+((scrolledY*0.50))+'px');
			}

			jQuery(window).scroll(function() {
				if(jQuery(window).width() >= 1024){
					jQuery('#phone').each(function(){
					var imagePos = jQuery(this).offset().top;
					var topOfWindow = jQuery(window).scrollTop();
						if (imagePos < topOfWindow+600) {
							jQuery(this).addClass("hatch");
						}
					});
				}
			});

				var didScroll = false;
				var icon = $(".huge-title, #godown");
				var $window = $(window);
				jQuery(window).scroll(function(){
					didScroll = true;
				});
				window.setInterval(function () {
					if (didScroll){
						if (1-$window.scrollTop()/200 > -20) {
							icon.css({opacity: 1-$window.scrollTop()/500});
						}
						didScroll = false;
					}
				}, 50);

				CKEDITOR.disableAutoInline = true;
				CKEDITOR.dtd.$editable.i = 1;
				CKEDITOR.dtd.$block.i=1;

				var toolbar_inline = [{
						name: 'document',
						items: ['Sourcedialog']
					}, {
						name: 'clipboard',
						groups: ['clipboard', 'undo'],
						items: ['Undo', 'Redo']
					},{
						name: 'basicstyles',
						groups: ['basicstyles', 'cleanup'],
						items: ['Bold', 'Italic', 'Underline', 'Strike']
					}, {
						name: 'paragraph',
						groups: ['list', 'indent', 'blocks', 'align', 'bidi'],
						items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
					}, {
						name: 'links',
						items: ['Link', 'Unlink']
					},
					'/',
					 {
						name: 'styles',
						items: ['Styles', 'Format', 'Font', 'FontSize']
					}, {
						name: 'colors',
						items: ['TextColor', 'BGColor']
					}, {
						name: 'tools',
						items: ['Maximize', 'ShowBlocks']
					}, {
						name: 'others',
						items: ['-']
					}, {
						name: 'about',
						items: ['About']
					},

					 {
						name: 'insert',
						items: ['Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe']
					},
					'/',
				];

				let $ckeditors = $('.ckeditor');
				window.ckeditors = [];

				if($ckeditors.length>0){
					$ckeditors.each(function(index, el){
						const idelem = $(el).attr('id');
						window.ckeditors[idelem] = CKEDITOR.inline( idelem,{
							removePlugins: 'sourcearea, source, wordcount',
							extraPlugins:'sourcedialog',
							removeButtons: 'Subscript,Superscript,Source,Templates,Searchcode,AutoFormat',
							toolbar :  toolbar_inline,
						});
					});
				}

		});
	</script>
</body>

</html>