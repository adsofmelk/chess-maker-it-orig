@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <span id="name-user" data-name="{{ Auth::user()->name }}"></span>
                    <span id="score-user" data-score="{{ Auth::user()->rating }}"></span>
                    <div class="card-header">
                        <span>Bienvenido a ChessMakeIt</span>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <p>Puntaje <strong>ELO-IT</strong> actual es <strong class="">{{Auth::User()->rating}}</strong></p>
                        <hr>
                        <p class="font-weight-bold">Resumen de partidas ({{Auth::User()->game_count}})</p>

                        <section class="resume-game text-center">
                            <article class="box-info-game">
                                <span class="boxig-title badge-success">Victorias</span>
                                <div class="boxig-value text-success">
                                    <span>{{Auth::User()->game_win}}</span>
                                </div>
                            </article>
                            <article class="box-info-game">
                                <span class="boxig-title badge-danger">Derrotas</span>
                                <div class="boxig-value text-danger">
                                    <span>{{Auth::User()->game_lose}}</span>
                                </div>
                            </article>
                            <article class="box-info-game">
                                <span class="boxig-title text-white badge-warning">Empates</span>
                                <div class="boxig-value text-warning">
                                    <span>{{Auth::User()->game_empates}}</span>
                                </div>
                            </article>
                        </section>

                        <hr>
                        <div>
                            <p class="font-weight-bold">Nueva Partida contra</p>
                            {{-- <a href="{{ route('play_machine')}}" class="btn btn-primary mr-2">--}}
                            {{--    <i class="fa fa-plus mr-1"></i>--}}
                            {{--    <span>Servidor</span>--}}
                            {{-- </a>--}}
                            <a href="{{ route('playgame')}}" class="btn btn-primary mr-2">
                                <i class="fa fa-plus mr-1"></i>
                                <span>Jugador</span>
                            </a>
                        </div>
                        <hr>
                        <h5 class="mb-3">Jugadores en línea <i id="load-players" class="fa fa-spin fa-spinner ml-1" style="color:#326ecf;"></i></h5>
                        <table id="table-users" class="table table-striped">
                            <thead>
                            <tr class="head-table" style="display:none;">
                                <th>Jugador</th>
                                <th>ELO-IT</th>
                                <th>Jugar</th>
                            </tr>
                            </thead>
                            <tbody id="section-users"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{url('js/home.min.js')}}"></script>
@endsection