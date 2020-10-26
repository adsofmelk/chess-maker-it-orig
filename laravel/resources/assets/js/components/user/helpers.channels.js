chApp.channelMoves = function(message) {
  if (ablyappGame.auth.clientId != message.clientId) {
    if (message.data.surrender) {
      //let messageTurno = 'Has Ganado!!!';
      askContinuePlay('Has ganado porque el otro jugador se ha rendido, quieres seguir jugando?');
      $('#blockui-board').show();
      clearInterval(timeGameInterval);

      return false;
    }

    boardChess.movesCount = boardChess.movesCount + 1;
    // console.log('message', message, message.clientId)
    const box = message.data.box;
    const piece = message.data.piece;

    console.log(box, piece);
    const indexPiece = boardChess.piecesMap.indexOf(piece);
    let $piece = $(box).find('.ispiece');

    if ($piece.length > 0) {
      boardChess.piecesLoc[indexPiece] = $(box).attr('id').split('-')[1];

      const idPieceDel = $piece.attr('id');
      const index = boardChess.piecesMap.indexOf(idPieceDel);
      boardChess.piecesLoc[index] = '99';

      let pieceDelete = 0;
      boardChess.myPieces.forEach(function(piece, index) {
        if (piece.name == idPieceDel) {
          pieceDelete = index;
        }
      });

      boardChess.myPieces.splice(pieceDelete, 1);

      //console.log('pieza para eliminar', pieceDelete);

      movePiece({
        user: false,
        boxMove: box,
        capture: true,
        pieceDel: idPieceDel,
        pieceMove: piece,
      });

    } else {
      movePiece({
        user: false,
        boxMove: box,
        capture: false,
        pieceDel: '',
        pieceMove: piece,
      });

      boardChess.piecesLoc[indexPiece] = $(box).attr('id').split('-')[1];
    }

    if (message.data.user != nameUser) {
      let messageTurno = 'Tu turno';

      $('#blockui-board').hide();
      if (!message.data.play) {
        messageTurno = 'Has perdido!!!';
        let messageAsk = 'Has perdido, quieres seguir jugando?';
        if (message.data.empate) {
          messageAsk = 'Es un empate, quieres seguir jugando?';
          messageTurno = 'Es un empate!!!';
        }
        askContinuePlay(messageAsk);

        $('#blockui-board').show();
        clearInterval(timeGameInterval);
      }

      appGameInit.$children[0].status = messageTurno;
    }
  }
};

chApp.channelPlayAgain = function(message) {
  if (message.data.status == 400) {
    if (message.data.clientId != ablyappGame.auth.clientId) {
      chApp.notRequest = true;
      bootbox.hideAll();
      bootbox.alert({
        message: '<h3 class="swal-title">El otro jugador no quiere seguir jugando</h3>',
      }).on('hidden.bs.modal', function() {
        location.reload();
      });
    }
  } else if (message.data.status == 200) {
    playGameAgain();
  }
};

chApp.channelChat = function(message) {
  let classUser = 'chat-other';
  let alignText = ' text-left mr-3';

  if (appGame.nameUser == message.data.user) {
    classUser = 'chat-me';
    alignText = 'text-right ml-3';
  }

  appGame.message = appGame.message + '<p class="' + alignText + '"><span class="' + classUser + '">' + message.data.message + '</span>' + '</p>';
  $('#chat-body').animate({
    scrollTop: $('#chat-body').prop('scrollHeight'),
  }, 400);
};