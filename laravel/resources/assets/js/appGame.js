require('./vendors/jquery-ui');
require('./vendors/jquery.ui.touch-punch');

import swal from 'sweetalert';
import BoardChess from './game/boardChess';
import gameInit from './components/InitGame.vue';

require('./moves');

const checkEmpate = function(params) {
  if (boardChess.movesCount == 16) {
    clearInterval(timeGameInterval);
    let play = false;
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
};

const checkPieces = function() {
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
};

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

//publish message casilla y pieza
window.writeMove = function(params) {
  boardChess.movesCount = boardChess.movesCount + 1;
  // console.log('message', message, message.clientId)
  const box = params.box;
  const piece = params.piece;

  // console.log(box, piece);
  const indexPiece = boardChess.piecesMap.indexOf(piece);
  let $piece = $(box).find('.ispiece');

  let messageTurno = 'Turno del oponente';

  if ($piece.length > 0) {
    boardChess.piecesLoc[indexPiece] = $(box).attr('id').split('-')[1];

    const idPieceDel = $piece.attr('id');
    const index = boardChess.piecesMap.indexOf(idPieceDel);
    boardChess.piecesLoc[index] = '99';

    if ($piece.hasClass('goal')) {
      // alert('Has ganado!!!');
      messageTurno = 'Has ganado!!!';
      clearInterval(timeGameInterval);
      params.play = false;
      axios.post('gameend').then((response) => {
        // console.log(response.data)
        askContinuePlay('Has ganado, quieres seguir jugando?');
      });
    } else {
      let pieceDelete = 0;
      boardChess.youPieces.forEach(function(piece, index) {
        if (piece.name == idPieceDel) {
          pieceDelete = index;
        }
      });

      boardChess.youPieces.splice(pieceDelete, 1);
    }

    movePiece({
      user: true,
      boxMove: box,
      capture: true,
      pieceDel: $piece.attr('id'),
      pieceMove: piece,
    });

  } else {
    movePiece({
      user: true,
      boxMove: box,
      capture: false,
      pieceDel: '',
      pieceMove: piece,
    });

    boardChess.piecesLoc[indexPiece] = $(box).attr('id').split('-')[1];
  }

  const resultCheckPieces = checkPieces();

  console.log('resultado de check pieces', resultCheckPieces);

  if (!resultCheckPieces) {
    // console.log('entra primer if', resultCheckPieces)
    // gana
    messageTurno = 'Has ganado!!!';
    clearInterval(timeGameInterval);
    params.play = false;
    axios.post('gameend').then((response) => {
      // console.log(response.data)
      askContinuePlay('Has ganado, quieres seguir jugando?');
    });
  } else if (resultCheckPieces == 2) {
    // console.log('entra segundo if', resultCheckPieces)
    // es empate
    messageTurno = 'Es un empate!!!';
    clearInterval(timeGameInterval);
    params.play = false;
    axios.post('empate').then((response) => {
      // console.log(response.data)
      askContinuePlay('Es un empate, quieres seguir jugando?');
    });
  }

  appGameInit.$children[0].status = messageTurno;

  channelGame.publish('moves', {
    play: params.play,
    box: params.box,
    piece: params.piece,
    user: params.user,
    empate: params.empate,
  });
};

window.boardChess = new BoardChess({
  container: '#board-game',
});

require('./game/activeDragDrop');
require('./components/user/helpers.channels');
require('./components/user/helpers.moves');

window.gameApp = function() {
  //Vue.config.devtools = true;
  window.nameUser = $('#name-user').data('name');

  window.appGameInit = new Vue({
    el: '#app',
    components: {gameInit},
  });

  window.appGame = new Vue({
    el: '#app-game',
    data: {
      distribution: 'Esperando',
      mostrar: true,
      suscri: false,
      spinnerUI: true,
      message: '',
      channelUser: '',
      channelGame: '',
      chat: false,
      nameUser: nameUser,
    },
    methods: {
      writeChat() {
        if ($('#text-chat').val() == '') {
          $('#text-chat').focus();
          return false;
        }

        channelGame.publish('chat', {
          user: nameUser,
          message: $('#text-chat').val(),
        });

        $('#text-chat').val('');
      },

      ablyChat(name) {
        boardChess.movesCount = 0;
        // console.log('se habilita chat');
        window.channelGame = ablyappGame.channels.get(name);
        channelGame.unsubscribe();

        channelGame.subscribe('chat', function(message) {
          chApp.channelChat(message);
        });

        channelGame.subscribe('moves', function(message) {
          chApp.channelMoves(message);
        });

        channelGame.subscribe('playagain', function(message) {
          chApp.channelPlayAgain(message);
        });

        channelGame.presence.enter();

        channelGame.presence.subscribe('leave', function(member) {
          // console.log('El jugador se fue', chApp.statusGame, member);

          if (chApp.statusGame == 1) {
            // no estaba jugando solo abandono
            bootbox.alert({
              message: '<h3 class="swal-title">El otro jugador abandonó, se recargará la página para encontrar otro jugador</h3>',
            }).on('hidden.bs.modal', function() {
              location.reload();
            });
          } else if (chApp.statusGame == 2) {
            // gana porque el otro jugador abandona
            $('#block-ui-load').show();
            axios.post('winbyleave').then(function(response) {
              if (response.data.status == 1) {
                $('#block-ui-load').hide();
                bootbox.alert({
                  message: '<h3 class="swal-title">Has ganado porque el otro jugador abandonó.</h3>',
                }).on('hidden.bs.modal', function() {
                  location.href = 'home';
                });
              } else {
                swal({
                  title: 'No lo intentes',
                });
              }
            });
          }
        });
      },
    },
  });

  require('./components/user/helpers.modal');

  window.playGameAgain = function() {
    boardChess.clearBoard();
    boardChess.render();

    axios.post('get-game').then(function(response) {
      // console.log(response);

      appGameInit.$children[0].spinnerUI = false;
      appGameInit.$children[0].status = 'Juegas segundo con las fichas negras';
      appGameInit.$children[0].colorPlay = 'Fichas negras - ';
      chApp.playPieces = 0;
      appGame.chat = true;

      if (response.data.me_begin) {
        $('#blockui-board').hide();
        appGameInit.$children[0].status = 'Juegas primero con las fichas blancas';
        appGameInit.$children[0].colorPlay = 'Fichas blancas - ';
        chApp.playPieces = 1;
      }

      swal({
        title: appGameInit.$children[0].status,
      });

      boardChess.asignDistribution(response.data.distribution, chApp.playPieces);

      let $pieces = $('.piece-w');
      let $piecesMachine = '.piece-b';
      if (chApp.playPieces === 0) {
        $pieces = $('.piece-b');
        $piecesMachine = '.piece-w';
      }

      window.boxActive = undefined;
      window.activeDrag($pieces);
      window.activeDrop($('.box-chess, ' + $piecesMachine));
      chApp.statusGame = 1;
      boardChess.movesCount = 0;

      appGameInit.$children[0].activeCrono(response.data.time.date);
    });
  };
};

require('./components/game/helpers.keyboard');