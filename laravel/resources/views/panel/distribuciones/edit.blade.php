@extends('layouts.panel', ['title' => 'Editar distribución', 'menu_item' => '#menu-4',] )
@section('content')
<div class="m-portlet portlet-section" id="portlet-section">
	<div class="m-portlet__head">
		<div class="m-portlet__head-caption">
			<div class="m-portlet__head-title">
				<h3 class="m-portlet__head-text">
					Editar Distribución
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
	@if (session('alert'))
	<div id="message-alert" class="d-none">{{session('alert')}}</div>
	@endif
	<div class="m-portlet__body">
		<p>Distribución: <strong id="span-distro">{{$distribution->distribution}}</strong></p>
		<div class="form-group m-form__group row">
			<div id="distribucion-board" class="table-chess" data-distribution="{{$distribution->distribution}}"></div>
			<div id="block-pieces" class="pl-3">
				<img src="{{ url('img/balon.png') }}" alt="" id="piece-ball" class="ispiece goal d-block" draggable="true" ondragstart="window.drag(event)">
				<img src="{{ url('img/j11.png') }}" alt="" id="piece-tw" data-color="1" class="ispiece d-block" draggable="true" ondragstart="window.drag(event)">
				<img src="{{ url('img/j14.png') }}" alt="" id="piece-aw" data-color="1" class="ispiece d-block" draggable="true" ondragstart="window.drag(event)">
				<img src="{{ url('img/j15.png') }}" alt="" id="piece-hw" data-color="1" class="ispiece d-block" draggable="true" ondragstart="window.drag(event)">
				<img src="{{ url('img/j21.png') }}" alt="" id="piece-tb" data-color="0" class="ispiece d-block" draggable="true" ondragstart="window.drag(event)">
				<img src="{{ url('img/j24.png') }}" alt="" id="piece-ab" data-color="0" class="ispiece d-block" draggable="true" ondragstart="window.drag(event)">
				<img src="{{ url('img/j25.png') }}" alt="" id="piece-hb" data-color="0" class="ispiece d-block" draggable="true" ondragstart="window.drag(event)">
			</div>
		</div>
		@csrf
	</div>
	<div class="m-portlet__foot text-right">
		<div class="m-form__actions">
			<a class="btn btn-secondary m-btn--air m-btn--square mr-1" href="{{route('distri')}}">
					<i class="la la-undo mr-1"></i>
					<span>Regresar</span>
				</a>
			<button data-id="{{$distribution->id}}" data-url="{{route('distri_update')}}" id="btn-add" type="button" class="btn btn-primary m-btn--air m-btn--square">
				<i class="la la-save mr-1"></i>
				<span>Actualizar</span>
			</button>
		</div>
	</div>
</div>
@endsection
 
@section('scripts')
<script src="{{url('js/panel.distri.min.js')}}"></script>
<script>
	$(function(){
		boardChess.container = '#distribucion-board';
		boardChess.distribution = $('#distribucion-board').data('distribution');
		boardChess.render();
		boardChess.asignDistribution();
		$('.box-chess').each(function(index, el){
			$(el).attr('data-number', index+1).attr('ondrop','window.drop(event)').attr('ondragover','window.allowDrop(event)');
		});

		$(document).on('click','#btn-add', function () {
			if($('#block-pieces').children().length>0){
				swal('No ha ubicado todas las piezas en el tablero');
				return false;
			}

			const pieces = ['#piece-ball', '#piece-tw', '#piece-aw', '#piece-hw', '#piece-tb', '#piece-ab', '#piece-hb'];
			let distribution = [];
			for(let i=0; i<7; i++){
				let $pieceball = $(pieces[i]).parent().attr('id').split('-')[1].split('');
				distribution.push(String(($pieceball[0]*8)+ ($pieceball[1]*1 + 1)));
			}
			const sdistribution = String(distribution.join(','));
			mApp.block('#portlet-section');
			axios.post($(this).data('url'), {
				id: $(this).data('id'),
				distribution: sdistribution,
			}).then(function (response) {
				if (response.data.status == 200) {
					swal({title:'El registro ha sido actualizado!', type: 'success'});
					$('#span-distro').html(sdistribution);
				} else {
					swal('Ha ocurrido un error al crear la distribucón, por favor intente mas tarde');
				}
			});
			mApp.unblock('#portlet-section');
		});

	});

</script>
@endsection