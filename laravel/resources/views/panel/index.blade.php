@extends('layouts.panel', [
    'title' => 'Inicio'
    ] )
@section('content') {{--
<script src="{{url('js/ckeditor/4/ckeditor.js')}}"></script> --}}
<script>
  /*document.addEventListener("DOMContentLoaded",function(){
      let editor = CKEDITOR.replace('ckeditor',{
        filebrowserBrowseUrl: 'filemanager/dialog.php?type=2&editor=ckeditor&fldr=&lang=es',
        filebrowserUploadUrl: 'filemanager/dialog.php?type=2&editor=ckeditor&fldr=&lang=es',
        filebrowserImageBrowseUrl: 'filemanager/dialog.php?type=1&editor=ckeditor&fldr=&lang=es',
        height: 200,
      });
      window.diego = editor;
    });*/

</script>
{{-- <textarea id="ckeditor1" name="ckeditor" id="" cols="30" rows="10"></textarea> --}}
<div class="m-portlet col-md-5">
    <div class="m-portlet__body  m-portlet__body--no-padding">
        <div class="m-row--no-padding m-row--col-separator-xl">
            <div class="">
                <!--begin:: Widgets/Stats2-1 -->
                <div class="m-widget1">
                    <div class="m-widget1__item">
                        <div class="row m-row--no-padding align-items-center">
                            <div class="col">
                                <h3 class="m-widget1__title">Partidas Totales</h3>
                                <span class="m-widget1__desc"></span>
                            </div>
                            <div class="col col-1 m--align-right">
                                <span class="m-widget1__number m--font-brand">{{$data->number_games}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="m-widget1__item">
                        <div class="row m-row--no-padding align-items-center">
                            <div class="col">
                                <h3 class="m-widget1__title mb-2">Usuario con mayor partidas ganadas</h3>
                                @foreach ($data->wingames as $key => $user)
                                    <span class="d-block">{{$key+1}}. {{$user->name}} - {{$user->email}}</span> @endforeach
                            </div>
                            <div class="col col-1 m--align-right">
                                <span class="m-widget1__number m--font-danger">{{$data->games_win}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="m-widget1__item">
                        <div class="row m-row--no-padding align-items-center">
                            <div class="col">
                                <h3 class="m-widget1__title mb-2">Usuario con mejor Puntaje</h3>
                                @foreach ($data->maxscores as $key => $user)
                                    <span class="d-block">{{$key+1}}. {{$user->name}} - {{$user->email}}</span> @endforeach
                            </div>
                            <div class="col col-1 m--align-right">
                                <span class="m-widget1__number m--font-success">{{$data->score_max}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end:: Widgets/Stats2-1 -->
            </div>
        </div>
    </div>
</div>
@endsection