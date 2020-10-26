@extends('layouts.panel', [
    'title' => 'Listado de usuarios',
     'menu_item' => '#menu-3',
    ] )
@section('estilos') {{--
<link rel="stylesheet" href="js/datatables/css/buttons.dataTables.min.css"> --}}
<link rel="stylesheet" href="js/datatables/css/buttons.bootstrap4.min.css">
@endsection

@section('content')
    <div class="m-portlet portlet-section" id="portlet-section">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">Usuarios</h3>
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
                <table id="table-usuarios" class="table table-striped table-bordered table-condensed m-blockui">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Puntaje</th>
                        <th>PT</th>
                        <th>PG</th>
                        <th>PE</th>
                        <th>PP</th>
                        <th>Origen</th>
                        <th>Email</th>
                        <th>Fecha Ingreso</th>
                        <th>Ver</th>
                    </tr>
                    </thead>
                    <tbody id="aRegistros">
                    </tbody>
                </table>
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
      window.tablas = [];
      tablas['tabla-usuarios'] = $('#table-usuarios').DataTable({
        'columns': [
          {'data': 'id'},
          {'data': 'name'},
          {'data': 'rating'},
          {'data': 'game_count'},
          {'data': 'game_win'},
          {'data': 'game_empates'},
          {'data': 'game_lose'},
          {'data': 'type_auth'},
          {'data': 'email', visible: false, targets: 8},
          {'data': 'created_at', visible: false, targets: 9},
        ],
        'columnDefs': [
          {
            'targets': 10,
            'data': null,
            'defaultContent': '<a tabindex class="btn-show btn btn-outline-info m-btn m-btn--outline-2x m-btn--square btn-sm"><i class="la la-search la-2x"></i></a>',
          },
        ],
        'language': langDatatable,
        'paging': true,
        'searching': true,
        pageLength: 50,
        buttons: [
          {
            extend: 'excel',
            text: 'Excel',
            columns: '',
          },
          {
            extend: 'csv',
            text: 'CSV',
            columns: '',
          },
          {
            extend: 'pdf',
            text: 'PDF',
            columns: '',
          },
        ],
      });

      tablas['tabla-usuarios'].buttons().container().prependTo($('.col-md-6:eq(1) .dataTables_filter', tablas['tabla-usuarios'].table().container()));

      /*tablas['tabla-usuarios'].ajax.url('/panel/cabezotes/all').load()*/
      tablas['tabla-usuarios'].ajax.url('panel/usuarios/all').load();
      tablas['tabla-usuarios'].columns.adjust().draw();

      $(document).on('click', '.btn-show', function(b) {
        mApp.block('#portlet-section');
        window.rowTb = $(b.currentTarget).parents('tr');
        const fila = tablas['tabla-usuarios'].row($(b.currentTarget).parents('tr'));
        const data = fila.data();

        axios.get('panel/usuarios/show', {
          params: {
            id: data.id,
          },
        }).then(function(response) {
          bootbox.dialog({
            title: 'Detalles',
            message: response.data,
            buttons: {
              salir: {
                'label': '<i class="la la-ban mr-1"></i><span>Cerrar</span>',
                'className': 'btn btn-secondary',
              },
            },
          });
        });

        mApp.unblock('#portlet-section');
        /*location.href = $("#table-distribu").data('url-edit')+data.id;*/
      });

      /*axios.get('/panel/distribution/all').then(function(response){
          console.log(response);
          mApp.block('#table-usuarios');
          if (response.data.status == 200) {
              const rows = response.data.data;
              let count = 1;
              for (let i in rows) {
                  var fototes = '<a href="/cabezotes/' + rows[i].foto + '" data-fancybox="galeria" data-caption="' + rows[i].titulo + '"> <img class="img-redonda w100" src="/cabezotes/' + rows[i].foto + '" /></a>';
                  if (rows[i].foto == null) {
                      fototes = '<img src="https://placehold.it/80x80" class="img-redonda" />'
                  }

                  let newRow = tablas['tabla-usuarios'].row.add({
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

              tablas['tabla-usuarios'].draw();
          } else {
              swal('Ha ocurrido un error al consultar los registros');
          }

          mApp.unblock('#table-usuarios');
      });*/

    </script>
@endsection