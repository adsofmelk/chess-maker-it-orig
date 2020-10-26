@extends('../../layouts.panel', ['title' => 'Agregar Cabezote', 'menu_item' => '#menu-1',] ) 
@section('estilos')
<link rel="stylesheet" href="{{url('js/slim/css/slim.min.css')}}">
@endsection
 
@section('content')

<div class="m-portlet portlet-section" id="portlet-section">
	<div class="m-portlet__head">
		<div class="m-portlet__head-caption">
			<div class="m-portlet__head-title">
				<h3 class="m-portlet__head-text">
					Agregar Cabezote
				</h3>
			</div>
		</div>
		<div class="m-portlet__head-tools">
			<ul class="m-portlet__nav">
				<li class="m-portlet__nav-item">
					<a href="#" m-portlet-tool="fullscreen" class="m-portlet__nav-link m-portlet__nav-link--icon">
						<i class="la la-expand"></i>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<form action="{{route('cabezotes_create')}}" method="POST" class="m-form">
		<div class="m-portlet__body">
			@csrf
			<div class="form-group m-form__group row">
				<div class="col-lg-6">
					<label for="titulo">Título</label>
					<input id="titulo" type="text" class="form-control m-input" name="titulo">
				</div>
				<div class="col-lg-6">
					<label for="resumen">Resumen</label>
					<textarea class="form-control m-input" id="resumen" rows="2" name="resumen"></textarea>
				</div>
			</div>
			<div class="form-group m-form__group row">
				<div class="col-lg-6">
					<label for="texto-boton">Texto Botón</label>
					<input id="texto-boton" type="text" class="form-control m-input" name="texto_boton">
				</div>
				<div class="col-lg-6">
					<label for="enlace-boton">Enlace Botón</label>
					<input id="enlace-boton" type="text" class="form-control m-input" name="enlace_boton">
				</div>
			</div>
			<hr>
			<div style="max-width:300px;" class="form-group">
				<label for="resumen">Foto <strong>1920px * 1080px</strong></label>
				<div class="slim" data-label="Arrastre su foto o de click" data-label-loading="Cargando..." data-button-edit-title="Editar"
				 data-button-remove-title="Quitar" data-button-rotate-title="Girar" data-button-cancel-title="Cancelar" data-button-cancel-label="Cancelar"
				 data-button-confirm-title="Confirmar" data-button-confirm-label="Confirmar" data-ratio="1920:1080" data-jpeg-compression="100">
					<input type="file" accept="image/*" id="txtfoto" name="foto" />
				</div>
			</div>
		</div>
		<div class="m-portlet__foot m-portlet__foot--fit text-right">
			<div class="m-form__actions">
				<a class="btn btn-secondary m-btn--air m-btn--square mr-1" href="{{route('cabezotes')}}">
					<i class="la la-undo mr-1"></i>
					<span>Regresar</span>
				</a>
				<button type="submit" class="btn btn-primary m-btn--air m-btn--square">
					<i class="la la-plus mr-1"></i>
					<span>Agregar</span>
				</button>
			</div>
		</div>
	</form>
</div>
@endsection
 
@section('scripts')
<script src="{{url('js/slim/js/slim.kickstart.min.js')}}"></script>
@endsection