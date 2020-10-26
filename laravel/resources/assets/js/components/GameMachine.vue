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
    props: ['preGame', 'distribution', 'begin', 'timeGame'],

    mounted() {
      if (this.preGame) {
        this.connectPreGame();
      } else {
        this.startGame();
      }
      // this.startGame();
      // console.log(this.preGame);
    },

    data() {
      return {
        status: 'Esperando',
        spinnerUI: true,
        message: '',
        channelUser: '',
        channelGame: '',
        colorPlay: '',
        time: '',
      };
    },

    methods: {
      startGame() {
        const thisApp = this;
        thisApp.renderBoard();

        thisApp.spinnerUI = false;
        thisApp.status = 'Juegas segundo con las fichas negras';
        thisApp.colorPlay = 'Fichas negras - ';
        window.chApp.playPieces = 0;

        chApp.statusGame = 2;

        if (this.begin) {
          $('#blockui-board').hide();
          thisApp.status = 'Juegas primero con las fichas blancas';
          thisApp.colorPlay = 'Fichas blancas - ';

          window.chApp.playPieces = 1;
        } else {
          // console.log('get first move because i play black');
        }

        swal({title: thisApp.status}).then(function() {
          if (!thisApp.begin) {
            setTimeout(function() {
              axios.post('getfirstmove').then(function(response) {
                console.log(response.data);
                boardChess.movesCount = 0;

                const data = response.data;
                const box = '#box-' + data.box;
                const piece = data.piece;

                writeMove({
                  box: box,
                  piece: piece,
                  play: false,
                });
              });
            }, 1000);
          }
        });

        window.boardChess.asignDistribution(
            thisApp.distribution,
            chApp.playPieces,
        );

        $('.box-chess').each(function(index, el){
          $(el).attr('data-number', index+1).attr('ondrop','window.drop(event)').attr('ondragover','window.allowDrop(event)');
        });

        boardChess.movesCount = 0;
        $('#block-ui-load').hide();
        thisApp.activeCrono(thisApp.timeGame);
      },

      renderBoard() {
        window.boardChess.render();
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
          // $("#time-game").html(minutes + ":" + seconds);
        }, 1000);
      },
    },
  };
</script>