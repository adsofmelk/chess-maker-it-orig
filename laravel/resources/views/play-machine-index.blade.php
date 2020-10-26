<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Play machine</title>


    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,600,700" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{asset('js/app.js')}}" defer></script>
    <script src="{{asset('js/gameMachine.js')}}" defer></script>
    <style>
        [draggable] {
            cursor: pointer;
        }
    </style>
</head>
<body class="game">
<section class="content-game">
    <span id="name-user" data-name="Diego">Diego</span>
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
</section>
<section id="board-game" class="table-chess"></section>
<script>
  window.onload = function() {
    window.boardChess.render();
    window.boardChess.asignDistribution();
    $('#blockui-board').hide();

    let $pieces = $('.piece-w');
    let $piecesMachine = '.piece-b';
    if (chApp.playPieces == 0) {
      $pieces = $('.piece-b');
      $piecesMachine = '.piece-w';
    }

    window.boxActive = undefined;

    $pieces.draggable({
      scroll: false,
      addClasses: false,
      opacity: '0.6',
      cursor: 'pointer',
      containment: '.table-chess',
      start: function() {
        //$(this).css('opacity', '0.6');
        //boxActive = $(this).parent();
      },
      stop: function() {
        //$(this).css('opacity', '1');
      },
    });
    $('.box-chess, ' + $piecesMachine).droppable({
      addClasses: false,
      accept: '.ispiece',
      // hoverClass: 'box-hover',
      drop: function(event, ui) {
        //console.log('element for move', ui);
        let pieceAttacked = undefined;

        if ($(this).hasClass('ispiece')) {
          console.log('es un ataque');

          return false;
        } else if ($(this).children().length < 1) {
          console.log('es una casilla normal',$(this).children().length);
          // $(this).append(ui.draggable);
        } else if ($($(this).children().get(0)).hasClass('ispiece')) {
          console.log('box with piece');
          pieceAttacked = boardChess.pieces[boardChess.piecesMap.indexOf($($(this).children().get(0)).attr('id'))];
        }else if ($($(this).children().get(0)).hasClass('goal')) {
          console.log('le pega al balon');
          pieceAttacked = boardChess.pieces[boardChess.piecesMap.indexOf($($(this).children().get(0)).attr('id'))];
        }

        ui.draggable.
            removeClass('ui-draggable ui-draggable-handle').
            css('top', '0').
            css('left', '0');

        console.log('nodeChild', $(this), $(this).children().length);

        // $(this).removeClass('box-hover');
        $('.box-chess, .ispiece').removeClass('box-hover');

        console.group('elements');
        const $element = ui.draggable.attr('id');
        const pieceAttacker = boardChess.pieces[boardChess.piecesMap.indexOf($element)];

        const posInitial = $('#' + $element).parent().data('location').split(',');

        console.log('$element', $element);
        console.log('pieceAttacker', pieceAttacker);
        console.log('pieceAttacked', pieceAttacked);
        console.log('posInitial', posInitial);

        console.groupEnd();

        let $target = $(this);

        let posFinal = $target.data('location').split(',');

        console.log('posFinal ', posFinal);

        if (!checkEmpate({
          data: $element,
          box: $target.attr('id'),
        })) {
          return false;
        }

        const checkmove = checkMove({
          posInitial,
          posFinal,
          type: pieceAttacker.type,
          piece: $element,
          pieces: boardChess.piecesLoc,
        });

        if (!checkmove) {
          return false;
        }

        if (!checkEmpate({
          data: $element,
          box: $target.attr('id'),
        })) {
          return false;
        }

        //$('#blockui-board').show();

        let messageTurno = 'Turno del servidor';
        let play = true;

        if ($(this).hasClass('imgpiece')) {
          /*writeMove({
            play,
            box: $target.parent(),
            piece: data,
            user: nameUser,
          });*/
        } else {
          /*writeMove({
            play,
            box: $(this),
            piece: data,
            user: nameUser,
          });*/
        }

        $(this).append(ui.draggable);

        $('.box-lastmove').removeClass('box-lastmove');
        document.getElementById($element).classList.add('box-lastmove');

        //appGameInit.$children[0].status = messageTurno;
      },
      over: function() {
        $('.box-chess, .ispiece').removeClass('box-hover');
        $(this).addClass('box-hover');
      },
    });
  };
</script>

</body>
</html>