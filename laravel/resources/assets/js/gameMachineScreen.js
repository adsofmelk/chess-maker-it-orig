require('./components/game/bootstrap.screen');
require('./components/chapp');

require('./vendors/jquery-ui');
require('./vendors/jquery.ui.touch-punch');

import swal from 'sweetalert';
import BoardChess from './game/boardChess';

require('./machine/dragAndDrop');
require('./machine/moves');
require('./components/game/helpers.moves');
require('./components/game/helpers.modal');

window.boardChess = new BoardChess({
  container: '#board-game',
});

window.writeMove = function(params) {
  //console.log('params function writeMove', params);
  boardChess.movesCount = boardChess.movesCount + 1;
  const box = params.box;
  const piece = params.piece;
  const indexPiece = boardChess.piecesMap.indexOf(piece);
  let $piece = $(box).find('.ispiece');

  let animateMove = {
    user: params.play,
    capture: false,
    pieceDel: '',
    pieceMove: piece,
    boxOrigin: '#' + $('#' + piece).parent().attr('id'),
    boxMove: box,
  };

  // remove piece if is the case
  if ($piece.length > 0) {
    let $pieceDelete = $piece.attr('id');
    animateMove.pieceDel = $pieceDelete;
    animateMove.capture = true;
    boardChess.piecesLoc[indexPiece] = $(box).attr('id').split('-')[1];

    let pieceDelete = 0;
    let messageWin = 'Has perdido, quieres seguir jugando?';
    let winuser = false;
    const index = boardChess.piecesMap.indexOf($pieceDelete);
    boardChess.piecesLoc[index] = '99';

    // si es pieza jugador o es pieza servidor
    if (params.play) {
      boardChess.youPieces.forEach(function(piece0, index) {
        if (piece0.name == $pieceDelete) {
          pieceDelete = index;
        }
      });

      boardChess.youPieces.splice(pieceDelete, 1);
      messageWin = 'Has ganado, quieres seguir jugando?';
      winuser = true;
    } else {
      boardChess.myPieces.forEach(function(piece0, index) {
        if (piece0.name == $pieceDelete) {
          pieceDelete = index;
        }
      });

      boardChess.myPieces.splice(pieceDelete, 1);
    }

    // si la pieza que ha capturado es el balon
    if ($piece.hasClass('goal')) {
      clearInterval(timeGameInterval);
      //play = false;
      axios.post('game-end-machine-screen', {
        user: winuser,
      }).then((response) => {
        // console.log(response.data)
        updateScore(response.data.new_eloit);
        setTimeout(function() {
          askContinuePlay(messageWin);
        }, 600);
      });
      movePiece(animateMove);

      return false;
    }
  } else {
    boardChess.piecesLoc[indexPiece] = $(box).attr('id').split('-')[1];
  }

  movePiece(animateMove);

  // verificar las piezas que quedan
  const resultCheckPieces = checkPieces();
  if (!resultCheckPieces) {
    // gana jugador
    clearInterval(timeGameInterval);
    axios.post('game-end-machine-screen', {
      user: true,
    }).then((response) => {
      // console.log(response.data)
      updateScore(response.data.new_eloit);
      askContinuePlay('Has ganado, quieres seguir jugando?');
    });

    return false;
  } else if (resultCheckPieces === 2) {
    // es empate
    clearInterval(timeGameInterval);
    axios.post('game-end-machine-screen', {
      user: 2,
    }).then((response) => {
      // console.log(response.data)
      updateScore(response.data.new_eloit);
      askContinuePlay('Es un empate, quieres seguir jugando?');
    });

    return false;
  } else if (resultCheckPieces === 3) {
    clearInterval(timeGameInterval);
    axios.post('game-end-machine-screen', {
      user: false,
    }).then((response) => {
      // console.log(response.data)
      updateScore(response.data.new_eloit);
      askContinuePlay('Has perdido, quieres seguir jugando?');
    });

    return false;
  }

  let messageTurno = 'Tu turno';

  if (params.play) {
    // movimiento del jugador
    messageTurno = 'Turno del servidor';
    chApp.$panelGameScreen.status = messageTurno;
    setTimeout(function() {
      moveMachine();
    }, 1000);

    return false;
  }

  chApp.$panelGameScreen.status = messageTurno;

  if (!checkEmpate({})) {
    return false;
  }
};

/**
 * Solicita movimiento a la máquina
 * @param params
 */
window.moveMachine = function(params) {
  //console.log('params function moveMachine', params);
  axios.post('move-machine-screen').then(function(response) {
    const data = response.data;
    const box = '#box-' + data.box;
    const piece = data.piece;

    // console.log('Piece to move', piece, box);
    if (data.action === 3) {
      // clearInterval(timeGameInterval);
      // axios.post('game-end-machine-screen', {
      //   user: true,
      // }).then((response) => {
      //   // console.log(response.data)
      //   askContinuePlay('Has ganado, quieres seguir jugando?');
      // });
    } else if (data.action === 2) {
      // clearInterval(timeGameInterval);
      // axios.post('game-end-machine-screen', {
      //   user: false,
      // }).then((response) => {
      //   // console.log(response.data)
      //   //swal('Has perdido, quieres seguir jugando?');
      // });
    }

    writeMove({
      box: box,
      piece: piece,
      play: false,
    });
  });
};

window.$buttonPlay = undefined;

require('./game/activeDragDrop');

window.playGame = function() {
  const $loader = $('#block-ui-load');
  $loader.show();

  axios.post($buttonPlay.data('route')).then((response) => {
    chApp.playPieces = 1;
    window.boardChess.clearBoard();
    window.boardChess.render();
    window.boardChess.distribution = response.data.distribution;
    window.boardChess.asignDistribution(response.data.distribution, 1);
    boardChess.movesCount = 0;
    chApp.statusGame = 1;
    $('#blockui-board').hide();

    let $pieces = $('.piece-w');
    let $piecesMachine = '.piece-b';
    if (chApp.playPieces === 0) {
      $pieces = $('.piece-b');
      $piecesMachine = '.piece-w';
    }

    window.boxActive = undefined;
    window.activeDrag($pieces);
    window.activeDrop($('.box-chess, ' + $piecesMachine));

    $('#block-pieces-screen').hide();
    $loader.hide();
    swal('Juegas primero con las fichas blancas');
    $buttonPlay.hide();
    $('#panel-restart').show();
    $('#block-pieces-dead').html('').show();

    chApp.$panelGameScreen.active = true;
    chApp.$panelGameScreen.activeCrono();
  });
};

window.restartGame = function() {
  bootbox.confirm('Esta seguro?', function(response) {
    if (response) {
      window.playGame();
    }
  });
};

window.updateScore = function(score) {
  //console.log('execute function updateScore',score);
  localStorage.setItem('eloitchess', String(score));
  $('#score-game').html(localStorage.getItem('eloitchess'));
};

window.onload = function() {
  window.boardChess.render();
  // window.boardChess.asignDistribution();
  window.$buttonPlay = $('#play-screen');
  $buttonPlay.on('click', window.playGame);
  $('#btn-restart').on('click', window.restartGame);

  chApp.$panelGameScreen = new Vue({
    el: '#panel-game',
    data: {
      status: 'Juegas primero con las fichas blancas',
      active: false,
      message: '',
      colorPlay: 'Fichas Blancas - ',
      time: '00:00',
    },
    methods: {
      activeCrono: function(dateInit = new Date()) {
        // $("#time-game").html("00:00");
        const thisApp = this;
        window.timeGame = new Date(dateInit);
        window.timeGameInterval = setInterval(function() {
          const timeActual = new Date() - window.timeGame;

          const minutes = ('0' + new Date(timeActual).getMinutes()).substr(-2);
          const seconds = ('0' + new Date(timeActual).getSeconds()).substr(-2);
          thisApp.time = minutes + ':' + seconds;
        }, 1000);
      },
    },
  });

  $('#panel-game').show();
  if (localStorage.getItem('eloitchess') == null) {
    localStorage.setItem('eloitchess', '10000');
  }

  $('#score-game').html(localStorage.getItem('eloitchess'));

  //localStorage.removeItem('popup_register');

  /*$('body').on('mouseleave', function() {
    if (localStorage.getItem('popup_register') == null) {
      localStorage.setItem('popup_register', 1);
      bootbox.alert('Te invitamos a registrarte para jugar contra otros jugadores');
    }
  });*/
};

require('./front.index');