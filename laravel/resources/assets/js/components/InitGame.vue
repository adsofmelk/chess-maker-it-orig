<template>
    <div>
        <div v-show="spinnerUI" class="preloader-square-swapping">
            <div class="cssload-square-part cssload-square-green"></div>
            <div class="cssload-square-part cssload-square-pink"></div>
            <div class="cssload-square-blend"></div>
        </div>
        <div class="panel-status mb-2 p-3">
            <span><strong>{{ colorPlay }}</strong></span>
            <span>{{ status }}</span>
            <span class="float-right">{{ time }}</span>
        </div>
        <div v-html="message"></div>
    </div>
</template>

<script>
  export default {
    props: ['preGame'],

    mounted() {
      if (this.preGame) {
        this.connectPreGame();
      } else {
        this.conectar();
      }
    },

    data() {
      return {
        status: 'Esperando',
        spinnerUI: true,
        message: '',
        channelUser: '',
        channelGame: '',
        colorPlay: '',
        time: '00:00',
      };
    },

    methods: {
      conectar() {
        const thisApp = this;

        window.ablyapp = new Ably.Realtime({
          authUrl: 'auth-ably',
        });

        window.ablyapp.connection.on('connected', function() {
          thisApp.status = 'Buscando jugador...';

          thisApp.$http.get('get-channel').then(function(response) {
            window.channel = window.ablyapp.channels.get(
                response.body.nameChannel,
            );

            thisApp.channelUser = response.body.nameChannel;
            window.channel.unsubscribe();

            window.channel.subscribe('private_user', function(message) {
              if (message.data.status === 300) {
                $('#block-ui').show();
              }

              if (message.data.status === 201) {
                window.ablyappGame = new Ably.Realtime({
                  authUrl: 'auth-ably',
                  authParams: {
                    begin: true,
                  },
                });

                window.ablyappGame.connection.on('connected', function() {
                  window.ablyapp.close();
                  delete window.ablyapp;

                  appGame.ablyChat(message.data.nameChannelGame);
                  thisApp.spinnerUI = false;
                  thisApp.status = 'Juegas segundo con las fichas negras';
                  thisApp.colorPlay = 'Fichas negras - ';
                  window.chApp.playPieces = 0;
                  appGame.chat = true;

                  if (message.data.me_begin) {
                    $('#blockui-board').hide();
                    thisApp.status = 'Juegas primero con las fichas blancas';
                    thisApp.colorPlay = 'Fichas blancas - ';
                    window.chApp.playPieces = 1;
                  }

                  swal({title: thisApp.status});

                  // window.renderTable(message.data.distribution);
                  // window.boardGame.distribution = message.data.distribution;
                  window.boardChess.asignDistribution(
                      message.data.distribution,
                      chApp.playPieces,
                  );

                  $('.box-chess').each(function(index, el) {
                    // $(el).attr('data-number', index + 1).attr('ondrop', 'window.drop(event)').attr('ondragover', 'window.allowDrop(event)');
                    $(el).attr('data-number', index + 1);
                  });

                  boardChess.movesCount = 0;
                  thisApp.activeCrono(message.data.time.date);

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
                });
              }
            });

            window.channel.presence.enter();
            thisApp.findPlayer();
            $('#block-ui-load').hide();
            thisApp.renderBoard();
          });
        });
      },

      findPlayer() {
        const thisApp = this;

        this.$http.get('find-player').then(function(response) {
          if (response.body.status === 2) {
            window.ablyappGame = new Ably.Realtime({
              authUrl: 'auth-ably',
              authParams: {
                begin: true,
              },
            });

            window.ablyappGame.connection.on('connected', function() {
              window.ablyapp.close();
              delete window.ablyapp;
              appGame.ablyChat(response.body.channel);
              thisApp.spinnerUI = false;

              appGame.chat = true;
              thisApp.status = 'Juegas segundo con las fichas negras';
              thisApp.colorPlay = 'Fichas negras -';
              window.chApp.playPieces = 0;

              if (response.body.me_begin) {
                $('#blockui-board').hide();
                thisApp.status = 'Juegas primero con las fichas blancas';
                thisApp.colorPlay = 'Fichas blancas - ';
                window.chApp.playPieces = 1;
              }

              swal({title: thisApp.status});

              thisApp.channelGame = response.body.channel;
              window.boardChess.asignDistribution(
                  response.body.distribution,
                  chApp.playPieces,
              );

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

              thisApp.activeCrono(response.body.time.date);
            });
          }
        });
      },

      connectPreGame() {
        const thisApp = this;
        thisApp.renderBoard();
        axios.post('get-game').then(function(response) {
          window.ablyappGame = new Ably.Realtime({
            authUrl: 'auth-ably',
            authParams: {
              begin: true,
            },
          });

          window.ablyappGame.connection.on('connected', function(aa, ff) {
            $('#block-ui-load').hide();
            appGame.ablyChat(response.data.channel);
            thisApp.spinnerUI = false;

            appGame.chat = true;
            thisApp.status = 'Juegas segundo con las fichas negras';
            thisApp.colorPlay = 'Fichas negras -';
            window.chApp.playPieces = 0;

            if (response.data.me_begin) {
              $('#blockui-board').hide();
              thisApp.status = 'Juegas primero con las fichas blancas';
              thisApp.colorPlay = 'Fichas blancas - ';
              window.chApp.playPieces = 1;
            }

            swal({title: thisApp.status});

            thisApp.channelGame = response.data.channel;
            // window.renderTable(response.data.distribution);
            // window.boardGame.distribution = response.data.distribution;
            window.boardChess.asignDistribution(
                response.data.distribution,
                chApp.playPieces,
            );

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

            thisApp.activeCrono(response.data.time.date);
          });
        });
      },

      activeCrono(dateInit = new Date()) {
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

      renderBoard() {
        window.boardChess.render();
      },

      suscribirse() {
        const thisApp = this;
        window.channelDamos = window.ablyapp.channels.get('damos');

        window.channelDamos.subscribe('greeting', function(message) {
          if (message.data.status === 300) {
            location.reload();
          }

          thisApp.message =
              thisApp.message +
              'Nuevo mensaje del servidor: ' +
              message.data +
              '<br>';
        });

        window.channelDamos.presence.subscribe('greeting');
        window.channelDamos.presence.enter();
      },
    },
  };
</script>

