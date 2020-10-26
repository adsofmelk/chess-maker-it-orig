@extends('../../layouts.panel', ['title' => 'Listado de distribuciones', 'menu_item' => '#menu-4',] )
@section('content')
    <div class="m-portlet portlet-section" id="portlet-section">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">Distribuciones</h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                        <a href="{{route('distri_add')}}"
                           class="m-portlet__nav-link m-portlet__nav-link--icon tooltip-damos" data-toggle="m-tooltip"
                           data-placement="top" data-original-title="Agregar">
                            <span><i class="la la-plus"></i></span>
                        </a>
                    </li>
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
                <table id="table-distribu" class="table table-striped table-bordered table-condensed m-blockui"
                       data-url-edit="{{url('panel/distribuciones/edit/')}}/"
                       data-url-delete="{{url('panel/distribuciones/delete/')}}/">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>Distribución</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
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
    <script>
      window.tablas = [];
      tablas['tabla-distribu'] = $('#table-distribu').DataTable({
        'columns': [
          {'data': 'id'},
          {'data': 'distribution'},
        ],
        'columnDefs': [
          {
            'targets': 2,
            'data': null,
            'defaultContent': '<a tabindex class="btn-editar btn btn-outline-warning m-btn m-btn--outline-2x m-btn--square btn-sm"><i class="la la-edit la-2x"></i></a>',
          },
          {
            'targets': 3,
            'data': null,
            'defaultContent': '<a tabindex class="btn-eliminar btn btn-outline-danger m-btn m-btn--outline-2x m-btn--square btn-sm"><i class="la la-remove la-2x"></i></a>',
          },
        ],
        'language': langDatatable,
        'paging': true,
        'searching': true,
        pageLength: 50,
      });

      /*window.tablas["tabla-distribu"].column(0).visible(false);*/

      /*tablas['tabla-distribu'].ajax.url('/panel/cabezotes/all').load()*/
      tablas['tabla-distribu'].ajax.url('panel/distribuciones/all').load();

      /*axios.get('/panel/distribution/all').then(function(response){
          console.log(response);
          mApp.block('#table-distribu');
          if (response.data.status == 200) {
              const rows = response.data.data;
              let count = 1;
              for (let i in rows) {
                  var fototes = '<a href="/cabezotes/' + rows[i].foto + '" data-fancybox="galeria" data-caption="' + rows[i].titulo + '"> <img class="img-redonda w100" src="/cabezotes/' + rows[i].foto + '" /></a>';
                  if (rows[i].foto == null) {
                      fototes = '<img src="https://placehold.it/80x80" class="img-redonda" />'
                  }

                  let newRow = tablas['tabla-distribu'].row.add({
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

              tablas['tabla-distribu'].draw();
          } else {
              swal('Ha ocurrido un error al consultar los registros');
          }

          mApp.unblock('#table-distribu');
      });*/

      $(document).on('click', '.btn-editar', function(b) {
        window.rowTb = $(b.currentTarget).parents('tr');
        const fila = tablas['tabla-distribu'].row($(b.currentTarget).parents('tr'));
        const data = fila.data();
        location.href = $('#table-distribu').data('url-edit') + data.id;
      });

      $(document).on('click', '.btn-eliminar', function(b) {
        const target = $(this);
        window.rowTb = $(b.currentTarget).parents('tr');
        var fila = tablas['tabla-distribu'].row($(b.currentTarget).parents('tr'));
        var data = fila.data();

        bootbox.confirm({
          'title': 'Eliminar distribución',
          'message': '<span class="text-danger">¿Esta seguro de eliminar la distribución?, recuerde que la acción no se puede deshacer.</span>',
          'buttons': {
            'confirm': {
              'label': '<i class="la la-remove mr-1"></i><span>Eliminar distribución</span>',
              'className': 'btn btn-danger',
            },
            'cancel': {
              'label': '<i class="la la-ban mr-1"></i><span>Cancelar</span>',
            },
          },
          'callback': function(res) {
            if (res) {
              axios.post($('#table-distribu').data('url-delete'), {
                id: data.id,
              }).then(function(response) {
                if (response.data.status == 200) {
                  fila.remove().draw(false);
                  toastr.info('El registro ha sido eliminado correctamente');
                } else {
                  swal('Ha ocurrido un error al eliminar el registro');
                }
              });
            }
          },
        });
      });

    </script>
@endsection