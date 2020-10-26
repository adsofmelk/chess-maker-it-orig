const allowDrop = function(ev) {
  ev.preventDefault();
  $('.box-chess, .ispiece').removeClass('box-hover');
  ev.target.classList.add('box-hover');
};

const drag = function(ev) {
  ev.dataTransfer.setData('text', ev.target.id);

  let $color = ev.target.dataset.color;
  if ($color == undefined) {
    const $parent = $(ev.target).parent('.ispiece');
    $color = $parent.data('color');
    ev.dataTransfer.setData('text', $parent.attr('id'));
    // console.log('esta tocando la imagen');

    // $parent.css('visibility','hidden');
  }

  if ($color != chApp.playPieces) {
    ev.preventDefault();
  }
};

const drop = function(ev) {
  ev.preventDefault();
  $('.box-chess, .ispiece').removeClass('box-hover');

  const data = ev.dataTransfer.getData('text');
  const pieceAttacker = boardChess.pieces[boardChess.piecesMap.indexOf(data)];
  let pieceAttacked = boardChess.pieces[boardChess.piecesMap.indexOf($(ev.target).attr('id'))];
  const posInitial = $('#' + data).parent().data('location').split(',');

  let posFinal = undefined;

  console.log(data, $(ev.target));
  let $target = $(ev.target);

  //$(ev.target) is attacked element DOM
  if ($target.hasClass('imgpiece')) {
    $target = $(ev.target).parent('.ispiece');
    pieceAttacked = boardChess.pieces[boardChess.piecesMap.indexOf($target.attr('id'))];

    if (pieceAttacker.color === pieceAttacked.color) {
      return false;
    }

    posFinal = $target.parent().data('location').split(',');
  } else {
    posFinal = $(ev.target).data('location').split(',');
  }

  if (!checkEmpate({
    data: data,
    box: $target.attr('id'),
  })) {
    return false;
  }

  const checkmove = checkMove({
    posInitial,
    posFinal,
    type: pieceAttacker.type,
    piece: data,
    pieces: boardChess.piecesLoc,
  });

  if (!checkmove) {
    return false;
  }

  if (!checkEmpate({
    data: data,
    box: $target.attr('id'),
  })) {
    return false;
  }

  $('#blockui-board').show();

  let messageTurno = 'Turno del servidor';
  let play = true;

  if ($(ev.target).hasClass('imgpiece')) {
    writeMove({
      play,
      box: $target.parent(),
      piece: data,
      user: nameUser,
    });
  } else {
    writeMove({
      play,
      box: $(ev.target),
      piece: data,
      user: nameUser,
    });
  }

  $('.box-lastmove').removeClass('box-lastmove');
  document.getElementById(data).classList.add('box-lastmove');

  appGameInit.$children[0].status = messageTurno;
};

const checkEmpate = function(params) {
  if (boardChess.movesCount === 16) {
    clearInterval(timeGameInterval);
    axios.post('game-end-machine-screen',{
      user: 2,
    }).then((response) => {
      askContinuePlay('Es un empate, quieres seguir jugando?');
    });

    return false;
  }

  return true;
};

const checkPieces = function() {
  if (boardChess.youPieces.length === 0) {
    return false;
  }

  if (boardChess.myPieces.length === 0) {
    return 3;
  }

  if (boardChess.youPieces.length === 1 && boardChess.myPieces.length === 1) {
    if (boardChess.youPieces[0].type === 2 && boardChess.myPieces[0].type === 2) {
      const $colorBall = ($('.box-main').hasClass('box-white')) ? 1 : 0;
      const $colorMyAlfil = ($('#' + boardChess.myPieces[0].name).parent().hasClass('box-white')) ? 1 : 0;
      const $colorYouAlfil = ($('#' + boardChess.youPieces[0].name).parent().hasClass('box-white')) ? 1 : 0;

      // es empate
      if ($colorBall !== $colorMyAlfil && $colorBall !== $colorYouAlfil) {
        return 2;
      }
    }
  }

  return true;
};

Object.defineProperty(window, 'allowDrop', {
  value: allowDrop,
  writable: false,
  enumerable: true,
  configurable: true,
});

Object.defineProperty(window, 'drag', {
  value: drag,
  writable: false,
  enumerable: true,
  configurable: true,
});

Object.defineProperty(window, 'drop', {
  value: drop,
  writable: false,
  enumerable: true,
  configurable: true,
});

Object.defineProperty(window, 'checkEmpate', {
  value: checkEmpate,
  writable: false,
  enumerable: true,
  configurable: true,
});

Object.defineProperty(window, 'checkPieces', {
  value: checkPieces,
  writable: false,
  enumerable: true,
  configurable: true,
});