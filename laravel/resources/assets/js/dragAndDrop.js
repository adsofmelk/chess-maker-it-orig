const allowDrop = function (ev) {
  ev.preventDefault();
  // console.log(ev.target.classList)
  $('.box-chess, .ispiece').removeClass('box-hover');
  ev.target.classList.add('box-hover');

};

const drag = function (ev) {
  ev.dataTransfer.setData("text", ev.target.id);
  // console.log(ev.target.dataset.color);
  // window.testtranfer = ev.target.id;
  // console.log(testtranfer);

  if (ev.target.dataset.color != chApp.playPieces) {
    ev.preventDefault();
  }
};

const drop = function (ev) {
  ev.preventDefault();
  $('.box-chess, .ispiece').removeClass('box-hover');
  const data = ev.dataTransfer.getData("text");
  // const data = window.testtranfer;
  // console.log(data)
  const pieceAttacker = boardChess.pieces[boardChess.piecesMap.indexOf(data)];
  const pieceAttacked = boardChess.pieces[boardChess.piecesMap.indexOf($(ev.target).attr('id'))]
  const posInitial = $('#' + data).parent().data('location').split(',');

  let posFinal = undefined;

  //$(ev.target) is atacked element DOM
  if ($(ev.target).hasClass('ispiece')) {
    if (pieceAttacker.color == pieceAttacked.color) {
      return false;
    }

    posFinal = $(ev.target).parent().data('location').split(',');
  } else {
    posFinal = $(ev.target).data('location').split(',');
  }

  if (!checkEmpate({
      data: data,
      box: $(ev.target).attr('id'),
    })) {
    return false;
  }

  const checkmove = checkMove({
    posInitial,
    posFinal,
    type: pieceAttacker.type,
    piece: data,
    pieces: boardChess.piecesLoc
  });

  if (!checkmove) {
    return false;
  }

  boardChess.movesCount = boardChess.movesCount + 1;

  if (!checkEmpate({
      data: data,
      box: $(ev.target).attr('id'),
    })) {
    return false;
  }

  $('#blockui-board').show();

  let messageTurno = 'Turno del oponente';
  let play = true;

  if ($(ev.target).hasClass('ispiece')) {
    $(ev.target).parent().append(document.getElementById(data));
    $(ev.target).remove();

    const index = boardChess.piecesMap.indexOf($(ev.target).attr('id'));
    boardChess.piecesLoc[index] = '99';
    let pieceDelete = 0;
    boardChess.youPieces.forEach(function (piece, index) {
      if (piece.name == $(ev.target).attr('id')) {
        pieceDelete = index;
      }
    });

    // console.log('pieza para eliminar', pieceDelete);

    boardChess.youPieces.splice(pieceDelete, 1);

    const resultCheckPieces = checkPieces();
    // console.log('resultado de check pieces', resultCheckPieces);

    if (!resultCheckPieces) {
      // console.log('entra primer if', resultCheckPieces)
      // gana
      messageTurno = 'Has ganado!!!';
      clearInterval(timeGameInterval)
      play = false;
      axios.post('gameend').then((response) => {
        // console.log(response.data)
        askContinuePlay('Has ganado, quieres seguir jugando?');
        writeMove({
          play,
          box: $(ev.target).attr('id'),
          piece: data,
          user: nameUser,
        });
      });

      return false;
    } else if (resultCheckPieces == 2) {
      // console.log('entra segundo if', resultCheckPieces)
      // es empate
      messageTurno = 'Es un empate!!!';
      clearInterval(timeGameInterval)
      play = false;
      axios.post('empate').then((response) => {
        // console.log(response.data)
        askContinuePlay('Es un empate, quieres seguir jugando?');
        writeMove({
          play,
          box: $(ev.target).attr('id'),
          piece: data,
          user: nameUser,
          empate: true,
        });
      });

      return false;
    }

    if ($(ev.target).hasClass('goal')) {
      // alert('Has ganado!!!');
      messageTurno = 'Has ganado!!!';
      clearInterval(timeGameInterval)
      play = false;
      axios.post('gameend').then((response) => {
        // console.log(response.data)
        askContinuePlay('Has ganado, quieres seguir jugando?');
        writeMove({
          play,
          box: $(ev.target).attr('id'),
          piece: data,
          user: nameUser,
        });
      });

      return false;
    }

    writeMove({
      play,
      box: $(ev.target).attr('id'),
      piece: data,
      user: nameUser,
    });
  } else {
    ev.target.appendChild(document.getElementById(data));
    writeMove({
      play,
      box: $(ev.target).attr('id'),
      piece: data,
      user: nameUser,
    });
  }

  $('.box-lastmove').removeClass('box-lastmove');
  document.getElementById(data).classList.add('box-lastmove');

  appGameInit.$children[0].status = messageTurno;
};

const checkEmpate = function (params) {
  if (boardChess.movesCount == 16) {
    // console.log('es un empate');
    messageTurno = 'Es un empate!!!';
    clearInterval(timeGameInterval)
    play = false;
    axios.post('empate').then((response) => {
      // console.log(response.data)
      askContinuePlay('Es un empate, quieres seguir jugando?');
      writeMove({
        play,
        box: params.box,
        piece: params.data,
        user: nameUser,
        empate: true,
      });
    });

    return false;
  }

  return true;
}

const checkPieces = function () {
  // console.log(boardChess)
  if (boardChess.youPieces.length == 0) {
    return false;
  }

  if (boardChess.youPieces.length == 1 && boardChess.myPieces.length == 1) {
    if (boardChess.youPieces[0].type == 2 && boardChess.myPieces[0].type == 2) {
      let $colorBall = ($('.box-main').hasClass('box-white')) ? 1 : 0;
      let $colorMyAlfil = ($('#' + boardChess.myPieces[0].name).parent().hasClass('box-white')) ? 1 : 0;
      let $colorYouAlfil = ($('#' + boardChess.youPieces[0].name).parent().hasClass('box-white')) ? 1 : 0;

      // es empate
      if ($colorBall != $colorMyAlfil && $colorBall != $colorYouAlfil) {
        return 2;
      }
    }
  }

  return true;
}

//publica mensaje casilla y pieza
window.writeMove = function (params) {
  channelGame.publish('moves', {
    play: params.play,
    box: params.box,
    piece: params.piece,
    user: params.user,
    empate: params.empate,
  })
}

Object.defineProperty(window, "allowDrop", {
  value: allowDrop,
  writable: false,
  enumerable: true,
  configurable: true
});

Object.defineProperty(window, "drag", {
  value: drag,
  writable: false,
  enumerable: true,
  configurable: true
});

Object.defineProperty(window, "drop", {
  value: drop,
  writable: false,
  enumerable: true,
  configurable: true
});

Object.defineProperty(window, "checkEmpate", {
  value: checkEmpate,
  writable: false,
  enumerable: true,
  configurable: true
});

Object.defineProperty(window, "checkPieces", {
  value: checkPieces,
  writable: false,
  enumerable: true,
  configurable: true
});