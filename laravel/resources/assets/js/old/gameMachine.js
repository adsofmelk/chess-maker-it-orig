import swal from 'sweetalert';
import BoardChess from './game/boardChess';

require('./machine/dragAndDrop');
require('./machine/moves');

window.boardChess = new BoardChess({
  container: '#board-game',
});

// Vue.component('game-machine', require('./components/GameMachine.vue'));

import gameMachine from './components/GameMachine.vue';

window.gameApp = function() {
  //Vue.config.devtools = true;

  window.nameUser = $('#name-user').data('name');

  window.appGameInit = new Vue({
    el: '#app',
    components: {gameMachine},
  });

  window.writeMove = function(params) {
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

    // se mata pieza
    if ($piece.length > 0) {
      let $pieceDelete = $piece.attr('id');
      boardChess.piecesLoc[indexPiece] = $(box).attr('id').split('-')[1];
      // $(box).append(document.getElementById(piece));
      animateMove.pieceDel = $pieceDelete;
      animateMove.capture = true;

      const index = boardChess.piecesMap.indexOf($pieceDelete);
      let pieceDelete = 0;
      let messageWin = 'Has perdido, quieres seguir jugando?';
      let winuser = false;
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
        axios.post('gameendmachine', {
          user: winuser,
        }).then((response) => {
          // console.log(response.data)
          setTimeout(function() {
            askContinuePlay(messageWin);
          }, 600);
        });
        movePiece(animateMove);

        return false;
      }
      // document.getElementById($pieceDelete).remove();
    } else {
      // $(box).append(document.getElementById(piece));
      boardChess.piecesLoc[indexPiece] = $(box).attr('id').split('-')[1];
    }

    movePiece(animateMove);

    $('.box-lastmove').removeClass('box-lastmove');
    document.getElementById(piece).classList.add('box-lastmove');

    // verificar las piezas que quedan
    const resultCheckPieces = checkPieces();
    if (!resultCheckPieces) {
      // gana jugador
      clearInterval(timeGameInterval);
      axios.post('gameendmachine', {
        user: true,
      }).then((response) => {
        // console.log(response.data)
        askContinuePlay('Has ganado, quieres seguir jugando?');
      });

      return false;
    } else if (resultCheckPieces === 2) {
      // es empate
      clearInterval(timeGameInterval);
      axios.post('empatemachine').then((response) => {
        // console.log(response.data)
        askContinuePlay('Es un empate, quieres seguir jugando?');
      });

      return false;
    } else if (resultCheckPieces === 3) {
      clearInterval(timeGameInterval);
      axios.post('gameendmachine', {
        user: false,
      }).then((response) => {
        // console.log(response.data)
        askContinuePlay('Has perdido, quieres seguir jugando?');
      });

      return false;
    }

    let messageTurno = 'Tu turno';

    if (params.play) {
      // movimiento del jugador
      messageTurno = 'Turno del servidor';
      appGameInit.$children[0].status = messageTurno;
      setTimeout(function() {
        moveMachine();
      }, 1000);

      return false;
    }

    appGameInit.$children[0].status = messageTurno;
  };

  window.movePiece = function(args) {
    if (args.user) {
      $(args.boxMove).append(document.getElementById(args.pieceMove));
      if (args.capture) {
        document.getElementById(args.pieceDel).remove();
      }
    } else {
      // console.log('animar movimiento');
      let $piece = $('#' + args.pieceMove);
      let $locInitial = $piece.position();
      let $locFinal = $(args.boxMove).position();
      $piece.css({'position': 'absolute', 'width': '60px', 'top': $locInitial.top + 'px', 'left': $locInitial.left + 'px'});

      $piece.animate({
        'top': $locFinal.top + 'px',
        'left': $locFinal.left + 'px',
      }, 600, function() {
        $(args.boxMove).append(document.getElementById(args.pieceMove));
        if (args.capture) {
          document.getElementById(args.pieceDel).remove();
        }
        $('#blockui-board').hide();
      });

    }
  };

  /**
   * Solicita movimiento a la máquina
   * @param params
   */
  window.moveMachine = function(params) {
    $('#blockui-board').show();
    axios.post('movemachine').then(function(response) {
      const data = response.data;
      const box = '#box-' + data.box;
      const piece = data.piece;

      console.log('Piece to move', piece, box);

      if (data.action === 3) {
        /*clearInterval(timeGameInterval);
        axios.post('gameendmachine', {
          user: true,
        }).then((response) => {
          // console.log(response.data)
          askContinuePlay('Has ganado, quieres seguir jugando?');
        });*/
      } else if (data.action === 2) {
        // clearInterval(timeGameInterval);
        // axios.post('gameendmachine', {
        //   user: false,
        // }).then((response) => {
        //   // console.log(response.data)
        //   askContinuePlay('Has perdido, quieres seguir jugando?');
        // });
      }

      writeMove({
        box: box,
        piece: piece,
        play: false,
      });

    });
  };

  window.cronoWaitGame = function() {
    window.timeWait = 30;
    window.cronoWait = setInterval(function() {
      timeWait--;
      $('#close-wait').html(timeWait);
      if (timeWait === 0) {
        // bootbox.hideAll();
        window.waitDialog.modal('hide');
        // console.log('se acabo')
      }
    }, 1000);
  };

  window.cleanWaitGame = function() {
    clearInterval(window.cronoWait);
  };

  window.askContinuePlay = function(message) {
    window.resultReplay = false;
    chApp.statusGame = 1;
    chApp.notRequest = false;
    window.waitDialog = bootbox.confirm({
      message: '<h3 class="swal-title">' + message + ' <span id="close-wait">30</span></h3>',
      modal: false,
      closeButton: false,
      buttons: {
        confirm: {
          label: 'Sí',
          className: 'btn-app btn text-white',
          value: 1,
        },
        cancel: {
          label: 'No',
          className: 'btn-dark',
        },
      },
      callback: function(result) {
        if (result) {
          window.resultReplay = true;
        }
      },
    }).on('shown.bs.modal', function() {
      cronoWaitGame();
    }).on('hidden.bs.modal', function() {
      // console.log('cerrar', resultReplay)
      cleanWaitGame();
      if (resultReplay) {
        location.reload();
      } else {
        location.href = 'home';
      }
    });
  };

  $(document).on('click', '#btn-surrender', function() {
    const urlSurrender = $(this).data('href');
    swal({
      title: '¿Está seguro?',
      icon: 'warning',
      closeOnEsc: false,
      closeOnClickOutside: false,
      buttons: {
        danger: {
          text: 'Rendirse',
          value: 1,
        },
        cancel: {
          text: 'Seguir jugando',
          visible: true,
          value: 0,
        },
      },
    }).then((value) => {
      if (value) {
        $('#blockui-board').show();
        clearInterval(timeGameInterval);
        axios.post(urlSurrender).then(function(response) {
          // console.log(response)
          askContinuePlay('Te has rendido, quieres seguir jugando?');
        });
        // console.log('Me rindo')
      }
    });
  });
};
