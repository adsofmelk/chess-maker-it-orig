<!DOCTYPE html>
<html lang="es">

<head>
	<base href="{{config('app.url')}}">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#3f51b5">
	<title>Página no encontrada</title>
	<link rel="apple-touch-icon" href="img/favicon.png">
	<link rel="icon" type="image/png" href="img/favicon.png">
	<!-- Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
	<!-- Styles -->
	<style>
		html,
		body {
			background-color: #fff;
			color: #636b6f;
			font-family: 'Raleway', sans-serif;
			font-weight: 100;
			height: 100vh;
			margin: 0;
		}

		.full-height {
			height: 100vh;
		}

		.flex-center {
			align-items: center;
			display: flex;
			justify-content: center;
		}

		.position-ref {
			position: relative;
		}

		.content {
			text-align: center;
		}

		.title {
			font-size: 36px;
			padding: 20px;
		}

		.btn {
			display: inline-block;
			font-weight: 600;
			text-align: center;
			white-space: nowrap;
			vertical-align: middle;
			user-select: none;
			border: 2px solid transparent;
			padding: .5rem 1rem;
			font-size: 1rem;
			line-height: 1.25;
			border-radius: .25rem;
			transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
			-webkit-box-shadow: none !important;
			-moz-box-shadow: none !important;
			box-shadow: none !important;
			cursor: pointer;
			text-decoration: none;
			-webkit-border-radius: 20px;
			-moz-border-radius: 20px;
			-ms-border-radius: 20px;
			-o-border-radius: 20px;
			border-radius: 20px;
			right: 0;
			margin-bottom: 4px;
		}

		.btn-primary {
			color: #5867dd;
			background-color: transparent;
			background-image: none;
			border-color: #5867dd;
		}

		.btn-primary:focus,
		.btn-primary:active,
		.btn-primary:hover {
			border-color: #5867dd;
			background: #5867dd;
			color: #fff;
		}
	</style>
</head>

<body>
	<div class="flex-center position-ref full-height">
		<div class="content">
			<div class="title"> Lo sentimos, la página no se encuentra.</div>
			<div class="">
				<a class="btn btn-primary" href="{{route('screen')}}">Regresar</a>
			</div>
		</div>
	</div>
</body>

</html>