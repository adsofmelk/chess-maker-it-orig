@extends('../../layouts.panel', ['title' => 'Listado de cabezotes', 'menu_item' => '#menu-1',] )
@section('content')
    <div class="m-portlet portlet-section" id="portlet-section">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Cabezotes
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                        <a href="{{route('cabezotes_add')}}"
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
            <div class="text-center" id="panel-order" style="display:none;">
                <p>Arrastre y mueva los registros en el orden deseado y despues haga click en el botón </p>
                <a data-href="{{route('cabezotes_order')}}" id="btn-order" tabindex
                   class="btn-order btn btn-outline-primary m-btn m-btn--custom m-btn--square btn-sm">
                    <i class="la la-sort mr-1"></i>
                    <span>Ordenar</span>
                </a>
                <br>
            </div>
            <div class="table-responsive">
                <table id="table-cabezotes" class="table table-striped table-bordered table-condensed m-blockui"
                       data-url-edit="{{url('panel/cabezotes/edit/')}}/"
                       data-url-delete="{{url('panel/cabezotes/delete/')}}/">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>#</th>
                        <th>Título</th>
                        <th>Resumen</th>
                        <th>Foto</th>
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
      tablas['tabla-cabezotes'] = $('#table-cabezotes').DataTable({
        'columns': [
          {'data': 'id'},
          {'data': 'numero'},
          {'data': 'titulo'},
          {'data': 'resumen'},
          {'data': 'foto'},
        ],
        'columnDefs': [
          {
            'targets': 5,
            'data': null,
            'defaultContent': '<a tabindex class="btn-editar btn btn-outline-warning m-btn m-btn--outline-2x m-btn--square"><i class="la la-edit la-2x"></i></a>',
          },
          {
            'targets': 6,
            'data': null,
            'defaultContent': '<a tabindex class="btn-eliminar btn btn-outline-danger m-btn m-btn--outline-2x m-btn--square"><i class="la la-remove la-2x"></i></a>',
          },
        ],
        'language': langDatatable,
        'paging': false,
        'searching': false,
        pageLength: 50,
      });

      window.tablas['tabla-cabezotes'].column(0).visible(false);

      /*tablas['tabla-cabezotes'].ajax.url('/panel/cabezotes/all').load()*/

      axios.get('panel/cabezotes/all').then(function(response) {
        /*console.log(response);*/
        mApp.block('#table-cabezotes');
        if (response.data.status == 200) {
          const rows = response.data.data;
          let count = 1;
          for (let i in rows) {
            var fototes = '<a href="cabezotes/' + rows[i].foto + '" data-fancybox="galeria" data-caption="' +
                rows[i].titulo + '"> <img class="img-redonda w100" src="cabezotes/' + rows[i].foto + '" /></a>';
            if (rows[i].foto == null) {
              fototes = '<img src="https://placehold.it/80x80" class="img-redonda" />';
            }

            let newRow = tablas['tabla-cabezotes'].row.add({
              'id': rows[i].id,
              'numero': count,
              'titulo': rows[i].titulo,
              'resumen': rows[i].resumen,
              'foto': fototes,
              'nfoto': rows[i].foto,
            }).node();

            $(newRow).attr('id', 'aRegistros-' + rows[i].id);

            count++;

            /* console.log(rows[i], newRow);*/
          }

          if (rows.length > 0) {
            $('#panel-order').show();
          }

          tablas['tabla-cabezotes'].draw();
        } else {
          swal('Ha ocurrido un error al consultar los registros');
        }

        mApp.unblock('#table-cabezotes');
      });

      $(document).on('click', '.btn-editar', function(b) {
        window.rowTb = $(b.currentTarget).parents('tr');
        const fila = tablas['tabla-cabezotes'].row($(b.currentTarget).parents('tr'));
        const data = fila.data();
        location.href = $('#table-cabezotes').data('url-edit') + data.id;
      });

      $(document).on('click', '.btn-eliminar', function(b) {
        const target = $(this);
        window.rowTb = $(b.currentTarget).parents('tr');
        var fila = tablas['tabla-cabezotes'].row($(b.currentTarget).parents('tr'));
        var data = fila.data();

        bootbox.confirm({
          'title': 'Eliminar cabezote',
          'message': '<span class="text-danger">¿Esta seguro de eliminar el cabezote?, recuerde que la acción no se puede deshacer.</span>',
          'buttons': {
            'confirm': {
              'label': '<i class="la la-remove mr-1"></i><span>Eliminar cabezote</span>',
              'className': 'btn btn-danger',
            },
            'cancel': {
              'label': '<i class="la la-ban mr-1"></i><span>Cancelar</span>',
            },
          },
          'callback': function(res) {
            if (res) {
              axios.post($('#table-cabezotes').data('url-delete'), {
                id: data.id,
                foto: data.nfoto,
              }).then(function(response) {
                if (response.data.status == 200) {
                  fila.remove().draw(false);
                  toastr.info('El registro ha sido eliminado correctamente');
                  if (tablas['tabla-cabezotes'].rows().count() == 0) {
                    $('#panel-order').hide();
                  }
                } else {
                  swal('Ha ocurrido un error al eliminar el registro');
                }
              });
            }
          },
        });
      });

      $('#aRegistros').sortable({
        placeholder: 'ui-state-highlight', opacity: 0.6, cursor: 'move', update: function() {},
      });

      $('#btn-order').on('click', function() {
        let esto = $(this);
        var order = $('#aRegistros').sortable('serialize');
        mApp.block('#portlet-section');
        axios.post(esto.data('href'), order).then(function(response) {
          if (response.data.status == 200) {
            toastr.info('Los registros han sido ordenados correctamente');
          } else {
            swal('Ha ocurrido un error al ordenar los registros');
          }
          mApp.unblock('#portlet-section');
        });
      });

    </script>
@endsection