import swal from 'sweetalert';

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
      appGameInit.$children[0].spinnerUI = true;
      appGameInit.$children[0].status = 'Esperando jugador...';
      appGameInit.$children[0].colorPlay = '';
      appGameInit.$children[0].time = '';
      appGame.chat = false;
      axios.post('playagain').then(function(response) {
        // console.log(response)
        //activar cronometro para revisar si el jugador se desconecto
        chApp.statusGame = 1;
      });
    } else {
      if (!chApp.notRequest) {
        axios.post('playagain', {
          noagain: false,
        }).then(function() {
          location.href = 'home';
        });
      }
      // location.href = 'home';
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
      channelGame.publish('moves', {
        surrender: 1,
        user: nameUser,
      });
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