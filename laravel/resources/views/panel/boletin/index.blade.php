@extends('layouts.panel', ['title' => 'Boletín', 'menu_item' => '#menu-5',] )
@section('estilos') {{--
<link rel="stylesheet" href="js/datatables/css/buttons.dataTables.min.css"> --}}
<link rel="stylesheet" href="js/datatables/css/buttons.bootstrap4.min.css">
@endsection
 
@section('content')
<div class="m-portlet portlet-section" id="portlet-section">
	<div class="m-portlet__head">
		<div class="m-portlet__head-caption">
			<div class="m-portlet__head-title">
				<h3 class="m-portlet__head-text">Boletín</h3>
			</div>
		</div>
		<div class="m-portlet__head-tools">
			<ul class="m-portlet__nav">
				<li class="m-portlet__nav-item">
					<a href="#" m-portlet-tool="toggle" class="m-portlet__nav-link m-portlet__nav-link--icon">
						<i class="la la-angle-up"></i>
					</a>
				</li>
				<li class="m-portlet__nav-item">
					<a href="#" m-portlet-tool="fullscreen" class="m-portlet__nav-link m-portlet__nav-link--icon">
						<i class="la la-expand"></i>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="m-portlet__body">
		<div class="table-responsive">
			<table id="table-boletin" class="table table-striped table-bordered table-condensed m-blockui">
				<thead>
					<tr>
						<th>ID</th>
						<th>Nombre</th>
						<th>Email</th>
						<th>Fecha Registro</th>
					</tr>
				</thead>
				<tbody id="aRegistros">
				</tbody>
			</table>
			<div id="diego"></div>
		</div>
	</div>
</div>
@endsection
 
@section('scripts')
<script src="{{ url('js/datatables/js/dataTables.buttons.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="{{ url('js/datatables/js/buttons.html5.min.js') }}"></script>
<script src="{{url('js/datatables/js/buttons.bootstrap4.min.js')}}"></script>
<script>
	window.tablas=[];
	tablas["tabla-boletin"] = $("#table-boletin").DataTable({
		columns: [
			{data: 'id', visible: false},
			{data: 'name'},
			{data: 'email'},
			{data: 'created_at'},
		],
		/*dom: 'lBf',*/
		/* dom: "<'row'<'col-md-2 toolbar-left-top'l><'col-md-10 custom-toolbar'<fB>>r>t<'row clearfix'<'col-md-3'i><'col-md-9'p>>",*/
		language: langDatatable,
		paging: true,
		searching : true,
		order: [[ 2,' desc']],
		pageLength: 50,
		buttons: [
			{
				extend: 'excel',
				text: 'Excel',
				columns:[1,2,3],
			},
			{
				extend: 'csv',
				text: 'CSV',
				columns:[1,2,3],
			},
			{
				extend: 'pdf',
				text: 'PDF',
				columns:[1,2,3],
			},
		],
	});

	tablas["tabla-boletin"].buttons().container().prependTo( $('.col-md-6:eq(1) .dataTables_filter', tablas["tabla-boletin"].table().container() ) );

	/*tablas['tabla-boletin'].ajax.url('/panel/cabezotes/all').load()*/
	tablas['tabla-boletin'].ajax.url('panel/boletin/all').load();
	tablas['tabla-boletin'].columns.adjust().draw();

	$(document).on('click', '.btn-show', function(b){
		mApp.block('#portlet-section');
		window.rowTb = $(b.currentTarget).parents("tr");
		const fila = tablas['tabla-boletin'].row($(b.currentTarget).parents("tr"));
		const data = fila.data();

		axios.get('panel/usuarios/show', {
			params: {
				id: data.id
			}
		}).then(function (response) {
			bootbox.dialog({
				title: 'Detalles',
				message: response.data,
				buttons:{
					salir:{
						'label' : '<i class="la la-ban mr-1"></i><span>Cerrar</span>',
                    	'className' : 'btn btn-secondary'
					}
				},
			})
		});

		mApp.unblock('#portlet-section');
		/*location.href = $("#table-distribu").data('url-edit')+data.id;*/
	});

	/*axios.get('/panel/distribution/all').then(function(response){
		console.log(response);
		mApp.block('#table-boletin');
		if (response.data.status == 200) {
			const rows = response.data.data;
			let count = 1;
			for (let i in rows) {
				var fototes = '<a href="/cabezotes/' + rows[i].foto + '" data-fancybox="galeria" data-caption="' + rows[i].titulo + '"> <img class="img-redonda w100" src="/cabezotes/' + rows[i].foto + '" /></a>';
				if (rows[i].foto == null) {
					fototes = '<img src="https://placehold.it/80x80" class="img-redonda" />'
				}

				let newRow = tablas['tabla-boletin'].row.add({
					'id': rows[i].id,
					'numero': count,
					'titulo': rows[i].titulo,
					'resumen': rows[i].resumen,
					'foto': fototes,
					'nfoto': rows[i].foto,
				}).node() ;

				$(newRow).attr('id', 'aRegistros-'+ rows[i].id);

				count++;

				// console.log(rows[i], newRow);
			}

			if(rows.length > 0){
				$('#panel-order').show();
			}

			tablas['tabla-boletin'].draw();
		} else {
			swal('Ha ocurrido un error al consultar los registros');
		}

		mApp.unblock('#table-boletin');
	});*/

</script>
@endsection