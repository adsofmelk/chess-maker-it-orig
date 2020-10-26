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
      });

      channelGame.subscribe('moves', function(message) {
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

          // console.log(box, piece);
          const indexPiece = boardChess.piecesMap.indexOf(piece);

          if ($(box).hasClass('ispiece')) {
            boardChess.piecesLoc[indexPiece] = $(box).parent().attr('id').split('-')[1];

            $(box).parent().append(document.getElementById(piece));
            $(box).remove();
          } else {
            $(box).append(document.getElementById(piece));
            boardChess.piecesLoc[indexPiece] = $(box).attr('id').split('-')[1];
          }

          if (message.data.user != nameUser) {
            let messageTurno = 'Tu turno';

            const index = boardChess.piecesMap.indexOf(box);
            boardChess.piecesLoc[index] = '99';

            let pieceDelete = 0;
            boardChess.myPieces.forEach(function(piece, index) {
              if (piece.name == box) {
                pieceDelete = index;
              }
            });

            boardChess.myPieces.splice(pieceDelete, 1);

            console.log('pieza para eliminar', pieceDelete);

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

            $('.box-lastmove').removeClass('box-lastmove');
            document.getElementById(piece).classList.add('box-lastmove');

            appGameInit.$children[0].status = messageTurno;
          }
        }
      });

      channelGame.subscribe('playagain', function(message) {
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