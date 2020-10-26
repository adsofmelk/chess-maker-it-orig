window.onload = function () {
  window.ablyapp = new Ably.Realtime({
    authUrl: 'authhallwait',
  });

  ablyapp.connection.on('connected', function() {
    // console.log('Ha entrado a la sala');
    axios.get('get-channel').then(function(response) {
      // console.log(response)
      window.channel = window.ablyapp.channels.get(response.data.nameChannel);
      window.channelHall = window.ablyapp.channels.get('hall_wait');

      channel.unsubscribe();
      channelHall.unsubscribe();
      channel.subscribe('private_user', function(message) {
        if (message.data.status == 300) {
          ablyapp.close();
          delete window.ablyapp;
          $('#block-ui').show();
        }
      });

      // window.channel.presence.enter();
      channelHall.presence.enter({
        user: $('#name-user').data('name'),
        score: $('#score-user').data('score'),
        message: 'nuevo',
      });

      let templateRow = `<tr id=":idplayer:">
                            <td>:name:</td>
                            <td>:score:</td>
                            <td><a class="btn btn-primary btn-retar btn-sm text-white" data-idplayer=":idplayer:"><span>Retar</span><i class="fa fa-play ml-1"></i></a></td>
												</tr>`;
      let $sectionUsers = $('#section-users');
      let $headTableUsers = $('#table-users').find('tr.head-table');

      channelHall.presence.get(function(err, members) {
        $('#load-players').hide();
        // console.log('There are ' + members.length + ' members on this channel', members);
        // console.log('The first member has client ID: ' + members[0].clientId);
        let html = '';
        for (var i in members) {
          if (members[i].clientId != ablyapp.auth.clientId) {
            let elemNew = templateRow.replace(/:idplayer:/g, members[i].clientId).
                replace(':name:', members[i].data.user).
                replace(':score:', members[i].data.score);
            html = html + elemNew;
            // $('#section-users').append('<p id="' + members[i].clientId + '">' + members[i].data.user + ', ' + members[i].data.score + ' <a class="btn-retar" data-idplayer="' + members[i].clientId + '">Retar</a></p>');
          }
        }

        $sectionUsers.append(html);

        if ($sectionUsers.find('tr').length > 0) {
          $headTableUsers.show();
        }
      });

      channelHall.presence.subscribe('enter', function(member) {
        // alert('Member ' + member.clientId + ' entered');
        // console.log('Member ' + member + ' entered');
        // console.log('Member nuevo', member);
        if (ablyapp.auth.clientId != member.clientId) {
          let elemNew = templateRow.replace(/:idplayer:/g, member.clientId).replace(':name:', member.data.user).replace(':score:', member.data.score);
          $sectionUsers.append(elemNew);

          if ($sectionUsers.find('tr').length > 0) {
            $headTableUsers.show();
          }
        }
      });

      channelHall.presence.subscribe('leave', function(member) {
        // alert('Member ' + member.clientId + ' entered');
        // console.log('Member se fue', member);
        // $('#section-users #'+ String(member.clientId)).remove();
        // console.log(document.getElementById(member.clientId));
        document.getElementById(member.clientId).remove();

        if ($sectionUsers.find('tr').length < 1) {
          $headTableUsers.hide();
        }
      });

      channelHall.subscribe('challenger', function(message) {
        // console.log(message)
        if (message.data.status == 1 && message.data.retador != ablyapp.auth.clientId && message.data.me == ablyapp.auth.clientId) {
          askAcceptChallenge({
            name: message.data.name,
            retador: message.data.retador,
          });
        }

        if (message.data.status == 2 && message.data.retador == ablyapp.auth.clientId) {
          cleanWaitGame();
          bootbox.hideAll();
        }

        if (message.data.status == 3 && message.data.retador != ablyapp.auth.clientId && message.data.me == ablyapp.auth.clientId) {
          bootbox.hideAll();
        }

        if (message.data.status == 4 && message.data.retador == ablyapp.auth.clientId) {
          cleanWaitGame();
          bootbox.hideAll();
          checkPresencia();
        }
      });

      // $("#block-ui-load").hide();
    });
  });

  $(document).on('click', '.btn-retar', function(evt) {
    channelHall.publish('challenger', {
      retador: ablyapp.auth.clientId,
      name: $('#name-user').data('name'),
      me: $(this).data('idplayer'),
      status: 1,
    });
    // console.log(evt)
    waitAcceptChallenge({
      me: $(this).data('idplayer'),
    });
  });

  window.askAcceptChallenge = function(params) {
    let resultChallenge = false;
    // bootbox.alert('El jugador '+ params.name+' quiere jugar contigo');
    window.waitDialog = bootbox.confirm({
      message: '<h3 class="swal-title">El jugador ' + params.name + ' quiere jugar contigo, <span id="close-wait">30</span></h3>',
      modal: false,
      closeButton: false,
      buttons: {
        confirm: {
          label: 'Aceptar',
          className: 'btn-app btn text-white',
          value: 1,
        },
        cancel: {
          label: 'Rechazar',
          className: 'btn-dark',
        },
      },
      callback: function(result) {
        if (result) {
          // console.log('esta aceptando');
          resultChallenge = true;
        }
      },
    }).on('shown.bs.modal', function() {
      cronoWaitGame();
    }).on('hidden.bs.modal', function() {
      // console.log('cerrar', resultReplay)
      cleanWaitGame();
      if (resultChallenge) {
        $('#block-ui-load').show();
        axios.post('creategamechallenge', {
          user: params.retador,
        }).then(function() {
          location.href = 'playgame';
          acceptChallenge({
            retador: params.retador,
          });
        });
      } else {
        rejectChallenge({
          retador: params.retador,
        });
      }
    });
  };

  window.rejectChallenge = function(params) {
    // console.log('rechazar challenge');
    channelHall.publish('challenger', {
      retador: params.retador,
      // me: params.me,
      status: 2,
    });
  };

  window.acceptChallenge = function(params) {
    // console.log('rechazar challenge');
    channelHall.publish('challenger', {
      retador: params.retador,
      // me: params.me,
      status: 4,
    });
  };

  window.waitAcceptChallenge = function(params) {
    window.waitDialog = bootbox.dialog({
      message: '<h3 class="swal-title">Esperar a que el jugador acepte, <span id="close-wait">30</span></h3>',
      modal: true,
      closeButton: false,
      onEscape: false,
      buttons: {
        ok: {
          label: 'Cancelar',
          className: 'btn-dark',
          callback: function() {

          },
        },
      },
    }).on('shown.bs.modal', function() {
      cronoWaitGame();
    }).on('hidden.bs.modal', function() {
      // cleanWaitGame();
      cancelChallenge({
        me: params.me,
      });
    });
  };

  window.checkPresencia = function() {
    $('#block-ui-load').show();
    axios.post('checkgame').then(function() {
      location.href = 'playgame';
    });
  };

  window.cancelChallenge = function(params) {
    cleanWaitGame();
    channelHall.publish('challenger', {
      retador: ablyapp.auth.clientId,
      me: params.me,
      status: 3,
    });
  };

  window.cronoWaitGame = function() {
    window.timeWait = 30;
    window.cronoWait = setInterval(function() {
      timeWait--;
      $('#close-wait').html(timeWait);
      if (timeWait == 0) {
        // bootbox.hideAll();
        waitDialog.modal('hide');
        // console.log('se acabo');
      }
    }, 1000);
  };

  window.cleanWaitGame = function() {
    clearInterval(cronoWait);
  };
};