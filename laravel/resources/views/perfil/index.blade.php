@extends('layouts.app')
@section('estilos')
    <link rel="stylesheet" href="{{url('js/slim/css/slim.min.css')}}">
@endsection
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <span>Mi Cuenta</span>
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <p>Puntaje <strong>ELO-IT</strong> actual es <strong class="">{{auth()->user()->rating}}</strong>
                        </p>
                        <hr>
                        <p class="font-weight-bold">Resumen de partidas ({{auth()->user()->game_count}})</p>

                        <section class="resume-game text-center">
                            <article class="box-info-game">
                                <span class="boxig-title badge-success">Victorias</span>
                                <div class="boxig-value text-success">
                                    <span>{{auth()->user()->game_win}}</span>
                                </div>
                            </article>
                            <article class="box-info-game">
                                <span class="boxig-title badge-danger">Derrotas</span>
                                <div class="boxig-value text-danger">
                                    <span>{{auth()->user()->game_lose}}</span>
                                </div>
                            </article>
                            <article class="box-info-game">
                                <span class="boxig-title text-white badge-warning">Empates</span>
                                <div class="boxig-value text-warning">
                                    <span>{{auth()->user()->game_empates}}</span>
                                </div>
                            </article>
                        </section>
                        <hr>

                        <p class="font-weight-bold">Actualizar datos</p>
                        <form action="{{ route('update_perfil') }}" class="formulario" id="form-update-perfil"
                              data-reload="true">
                            <div class="alert alert-danger error" style="display: none;"></div>
                            <div class="alert alert-success success" style="display: none;"></div>
                            <div class="mb-3 row">
                                <div class="col-lg-3">
                                    @if(auth()->user()->avatar)
                                        @if(strpos(auth()->user()->avatar, 'http') === false)
                                            <img src="photosuser/{{ auth()->user()->avatar }}" alt="Avatar {{ auth()->user()->name }}" class="img mr-2">
                                        @else
                                            <img src="{{ auth()->user()->avatar }}" alt="Avatar {{ auth()->user()->name }}" class="img mr-2">
                                        @endif
                                    @else
                                        <img src="{{ url('photosuser/avatar_default.jpg') }}"
                                             alt="Avatar {{ auth()->user()->name }}" class="img mr-2">
                                    @endif
                                </div>
                                <div class="col-lg-6" style="max-width:160px;">
                                    <div class="slim" data-label="Seleccionar Foto"
                                         data-label-loading="Cargando..." data-button-edit-title="Editar"
                                         data-button-remove-title="Quitar" data-button-rotate-title="Girar"
                                         data-button-cancel-title="Cancelar" data-button-cancel-label="Cancelar"
                                         data-button-confirm-title="Confirmar" data-button-confirm-label="Confirmar"
                                         data-ratio="120:120" data-jpeg-compression="100">
                                        <input type="file" accept="image/*" id="txtfoto" name="foto"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="txtnombre">Nombre</label>
                                <input class="form-control" type="text" name="nombre" id="txtnombre"
                                       value="{{auth()->user()->name}}" required>
                            </div>
                            <div class="form-group">
                                <label for="txtcorreo">Email</label>
                                <input class="form-control" type="text" name="correo" id="txtcorreo"
                                       value="{{auth()->user()->email}}" required>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save mr-2"></i>
                                    <span>Actualizar</span>
                                </button>
                            </div>
                        </form>

                        <hr>
                        <p class="font-weight-bold">Cambiar contraseña</p>
                        <form action="{{ route('change_password') }}" class="formulario" id="form-change-pass">
                            <div class="alert alert-danger error" style="display: none;"></div>
                            <div class="alert alert-success success" style="display: none;"></div>
                            <div class="form-group">
                                <label for="txtpassant">Contraseña Actual</label>
                                <input class="form-control" type="password" name="passant" id="txtpassant" required>
                            </div>
                            <div class="form-group">
                                <label for="txtpassnew">Contraseña nueva</label>
                                <input class="form-control" type="password" name="passnew" id="txtpassnew" required>
                            </div>
                            <div class="form-group">
                                <label for="txtpassrepeat">Repetir contraseña</label>
                                <input class="form-control" type="password" name="passrepeat" id="txtpassrepeat"
                                       required>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-lock mr-2"></i>
                                    <span>Cambiar Contraseña</span>
                                </button>
                            </div>
                        </form>
                        <hr>
                        <p class="font-weight-bold">Eliminar Cuenta</p>
                        <p>Para eliminar la cuenta, por favor escriba su contraseña actual, recuerde que esta acción no se puede deshacer</p>
                        <form action="{{ route('delete_account') }}" class="formulario" id="form-delaccount"
                              data-reload="true">
                            <div class="alert alert-danger error" style="display: none;"></div>
                            <div class="alert alert-success success" style="display: none;"></div>
                            <div class="form-group">
                                <label for="passdelete">Contraseña Actual</label>
                                <input class="form-control form-danger" type="password" name="passdelete"
                                       id="passdelete" required>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa fa-remove mr-2"></i>
                                    <span>Eliminar Cuenta</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{url('js/slim/js/slim.kickstart.min.js')}}"></script>
    <script src="{{ url('js/blockui.min.js') }}" defer></script>
    <script>
      window.onload = function() {
        $.blockUI.defaults.css.backgroundColor = 'none';
        $.blockUI.defaults.css.border = 'none';
        let $formularios = $('.formulario');
        if ($formularios.length > 0) {
          $formularios.each(function(index, el) {
            $(el).on('submit', function(event) {
              $form = $(this);
              $form.block({message: '<i class="fa fa-spinner fa-spin"></i>'});
              console.log('formulario', $(el), $form.serialize());
              axios.post($form.attr('action'), $form.serialize()).then(function(response) {
                let data = response.data;
                if (data.action === 0) {
                  $form.find('.error').html(data.message).show().delay(8000).fadeOut();
                } else if (data.action === 1) {
                  $form.find('.error').hide();
                  $form.find('.success').html(data.message).show().delay(10000).fadeOut();
                  $form.get(0).reset();

                  if ($form.data('reload') == true) {
                    location.reload();
                  }
                }

                $form.unblock();
              });

              event.preventDefault();
            });
          });
        }
      };
    </script>
@endsection