@extends('layouts.game', [
	'blockUI' => true,
	'machine' => false,
])

@section('contentGame')
    <section class="content-game">
        <span id="name-user" data-name="{{ Auth::user()->name }}"></span>
        <div id="block-pieces" style="display: none;">
            <img src="{{ url('img/balon.png') }}" alt="" id="piece-ball-origin" class="ispiece goal" style="width: 100%;">
            <img src="{{ url('img/j15.png') }}" alt="" id="piece-hw-origin" data-color="1" class="ispiece piece-w" style="width: 100%;"
                 draggable="true">
            <img src="{{ url('img/j25.png') }}" alt="" id="piece-hb-origin" data-color="0" class="ispiece piece-b" style="width: 100%;"
                 draggable="true">
            <img src="{{ url('img/j11.png') }}" alt="" id="piece-tw-origin" data-color="1" class="ispiece piece-w" style="width: 100%;"
                 draggable="true">
            <img src="{{ url('img/j21.png') }}" alt="" id="piece-tb-origin" data-color="0" class="ispiece piece-b" style="width: 100%;"
                 draggable="true">
            <img src="{{ url('img/j14.png') }}" alt="" id="piece-aw-origin" data-color="1" class="ispiece piece-w" style="width: 100%;"
                 draggable="true">
            <img src="{{ url('img/j24.png') }}" alt="" id="piece-ab-origin" data-color="0" class="ispiece piece-b" style="width: 100%;"
                 draggable="true">
            <div class="block-ui" id="blockui-board-origin"></div>
        </div>
        <div id="app" name-user="{{ Auth::user()->name }}">
            <game-init pre-game="{{$preGame}}"></game-init>
        </div>
        <section id="app-game" class="" v-show="chat">
            <span class="time-game" id="time-game"></span>
            <section id="board-game" class="table-chess"></section>
            <section class="chat">
                <div v-html="message" class="content-chat" id="chat-body">
                </div>
                <div class="form-group mb-0" v-show="chat">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Escriba mensaje" id="text-chat">
                        <div class="input-group-append">
                            <button class="btn btn-success btn-sm btn-app" @click="writeChat">
                                <i class="fa fa-reply"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a id="btn-surrender" data-href="{{route('surrender')}}" class="btn btn-warning">
                            <i class="fa fa-flag mr-1"></i>
                            <span>Rendirse</span>
                        </a>
                    </div>
                </div>
            </section>
        </section>
    </section>
    <script>
      window.onload = function() {
        gameApp();
      };
    </script>
@endsection