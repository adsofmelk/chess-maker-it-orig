window.activeDrag = function($pieces) {
  $pieces.draggable({
    scroll: false,
    addClasses: false,
    opacity: '0.6',
    cursor: 'pointer',
    containment: '.table-chess',
    start: function() {},
    stop: function() {},
  });
};

window.activeDrop = function($elements) {
  let $boxAndPieces = $('.box-chess, .ispiece');

  $elements.droppable({
    addClasses: false,
    accept: '.ispiece',
    // hoverClass: 'box-hover',
    drop: function(event, ui) {
      //console.log('element for move', ui);
      //let pieceAttacked = undefined;

      if ($(this).hasClass('ispiece')) {
        //console.log('es un ataque');

        return false;
      }
      // else if ($(this).children().length < 1) {
      //   // console.log('es una casilla normal', $(this).children().length);
      //   // $(this).append(ui.draggable);
      // } else if ($($(this).children().get(0)).hasClass('ispiece')) {
      //   // console.log('box with piece');
      //   //pieceAttacked = boardChess.pieces[boardChess.piecesMap.indexOf($($(this).children().get(0)).attr('id'))];
      // } else if ($($(this).children().get(0)).hasClass('goal')) {
      //   // console.log('le pega al balon');
      //   //pieceAttacked = boardChess.pieces[boardChess.piecesMap.indexOf($($(this).children().get(0)).attr('id'))];
      // }

      ui.draggable.
          removeClass('ui-draggable ui-draggable-handle').
          css('top', '0').
          css('left', '0');

      // console.log('nodeChild', $(this), $(this).children().length);
      // $(this).removeClass('box-hover');
      $('.box-chess, .ispiece').removeClass('box-hover');

      if (chApp.statusGame == 2) { return false;}

      // console.log(ui.draggable.parent().attr('id'), $(this).attr('id'));
      if (ui.draggable.parent().attr('id') == $(this).attr('id')) {
        //console.log('Is equals same box');
        return false;
      }

      const $element = ui.draggable.attr('id');
      const pieceAttacker = boardChess.pieces[boardChess.piecesMap.indexOf($element)];
      const posInitial = $('#' + $element).parent().data('location').split(',');
      let $target = $(this);
      let posFinal = $target.data('location').split(',');

      // console.group('elements');
      // console.log('$element', $element);
      // console.log('pieceAttacker', pieceAttacker);
      // console.log('pieceAttacked', pieceAttacked);
      // console.log('posInitial', posInitial);
      // console.log('posFinal ', posFinal);
      // console.groupEnd();

      const checkmove = checkMove({
        posInitial,
        posFinal,
        type: pieceAttacker.type,
        piece: $element,
        pieces: boardChess.piecesLoc,
      });

      if (!checkmove) {
        //console.log('Move invalid');
        return false;
      }

      writeMove({
        play: true,
        box: '#' + $target.attr('id'),
        piece: $element,
        user: (window.nameUser) ? window.nameUser : 'dd',
      });
    },
    over: function() {
      $boxAndPieces.removeClass('box-hover');
      $(this).addClass('box-hover');
    },
  });
};