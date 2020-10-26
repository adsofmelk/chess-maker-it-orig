@extends('layouts.game', ['blockUI' => true, 'machine'=> true,] )
@section('contentGame')

    <section class="content-game">
        <span id="name-user" data-name="{{ Auth::user()->name }}"></span>
        <div id="block-pieces" style="display: none;">
            <img src="{{ url('img/balon.png') }}" alt="" id="piece-ball-origin" class="goal" style="width: 100%;">
            <img src="{{ url('img/j15.png') }}" alt="" id="piece-hw-origin" data-color="1" class="ispiece piece-w" style="width: 100%;" draggable="true">
            <img src="{{ url('img/j25.png') }}" alt="" id="piece-hb-origin" data-color="0" class="ispiece piece-b" style="width: 100%;" draggable="true">
            <img src="{{ url('img/j11.png') }}" alt="" id="piece-tw-origin" data-color="1" class="ispiece piece-w" style="width: 100%;" draggable="true">
            <img src="{{ url('img/j21.png') }}" alt="" id="piece-tb-origin" data-color="0" class="ispiece piece-b" style="width: 100%;" draggable="true">
            <img src="{{ url('img/j14.png') }}" alt="" id="piece-aw-origin" data-color="1" class="ispiece piece-w" style="width: 100%;" draggable="true">
            <img src="{{ url('img/j24.png') }}" alt="" id="piece-ab-origin" data-color="0" class="ispiece piece-b" style="width: 100%;" draggable="true">
            <div class="block-ui" id="blockui-board-origin"></div>
        </div>
        <div id="app" style="z-index: 99999;">
            <game-machine pre-game="{{$preGame}}" distribution="{{$distribution}}" begin="{{$begin}}" time-game="{{$time_game}}"></game-machine>
        </div>
        <section id="app-game" class="" v-show="chat">
            <span class="time-game" id="time-game"></span>
            <section id="board-game" class="table-chess"></section>
            <section class="chat">
                <div class="form-group mb-0" v-show="chat">
                    <div class="mt-2">
                        <a id="btn-surrender" data-href="{{route('surrendermachine')}}" class="btn btn-warning">
                            <i class="fa fa-flag mr-1"></i>
                            <span>Rendirse</span>
                        </a>
                    </div>
                </div>
            </section>
        </section>
    </section>
    <style>
        [draggable] {
            cursor: pointer;
        }
    </style>
    <script>
      window.onload = function() {
        gameApp();

        let $pieces = $('.piece-w');
        let $piecesMachine = '.piece-b';
        if (chApp.playPieces == 0) {
          $pieces = $('.piece-b');
          $piecesMachine = '.piece-w';
        }

        $pieces.draggable({
          scroll: false,
          addClasses: false,
          opacity: '0.6',
          cursor: 'crosshair',
          containment: '.table-chess',
          start: function() {
            //$(this).css('opacity', '0.6');
          },
          stop: function() {
            //$(this).css('opacity', '1');
          },
        });
        $('.box-chess, ' + $piecesMachine).droppable({
          addClasses: false,
          accept: '.ispiece',
          hoverClass: 'box-hover',
          drop: function(event, ui) {
            //console.log('element for move', ui);

            if ($(this).hasClass('ispiece')) {
              console.log('es un ataque');

            } else if ($(this).children().length < 1) {

              console.log('es un ataque2');
              $(this).append(ui.draggable);
            } else if ($($(this).children().get(0)).hasClass('goal')) {
              console.log('le pega al balon');
            }

            ui.draggable.
                removeClass('ui-draggable ui-draggable-handle').
                css('top', '0').
                css('left', '0');

            console.log('nodeChild', $(this), $(this).children().length);

            $(this).removeClass('box-hover');
          },
          over: function(event, ui) {
            // $('.box-chess, .ispiece').removeClass('box-hover');
            // $(this).addClass('box-hover');
          },
        });
      };
    </script>
@endsection