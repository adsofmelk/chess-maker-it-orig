/**
 * Init time in modal
 */
window.cronoWaitGame = function() {
  window.timeWait = 30;
  window.cronoWait = setInterval(function() {
    timeWait--;
    $('#close-wait').html(timeWait);
    if (timeWait == 0) {
      // bootbox.hideAll();
      window.waitDialog.modal('hide');
      // console.log('se acabo')
    }
  }, 1000);
};

/**
 * Stop time in modal
 */
window.cleanWaitGame = function() {
  clearInterval(window.cronoWait);
};

/**
 * Open modal it ask if you want to play again.
 * @param message => text with status of game ending
 */
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
    chApp.statusGame = 2;
  }).on('hidden.bs.modal', function() {
    // console.log('cerrar', resultReplay)
    cleanWaitGame();
    if (resultReplay) {
      // appGameInit.$children[0].spinnerUI = true;
      // appGameInit.$children[0].status = 'Esperando jugador...';
      // appGameInit.$children[0].colorPlay = '';
      // appGameInit.$children[0].time = '';
      // appGame.chat = false;
      // axios.post('playagain').then(function(response) {
      //   // console.log(response)
      //   //activar cronometro para revisar si el jugador se desconecto
      //   chApp.statusGame = 1;
      // });
      window.playGame();
    } else if (!chApp.notRequest) {
      // axios.post('playagain', {
      //   noagain: false,
      // }).then(function() {
      //   location.href = 'home';
      // });
    }
  });
};