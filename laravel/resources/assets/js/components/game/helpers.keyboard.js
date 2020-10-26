// send message if it keypress key enter
$(document).on('keydown', '#text-chat', function(evt) {
  if (evt.keyCode == 13 || evt.which == 13) {
    appGame.writeChat();
  }
});