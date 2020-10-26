/**
 * Move piece in DOM and delete element(piece) if is the case
 *
 * args.boxMove => box to move piece
 * args.capture => condition than indicate delete piece by attack
 * args.pieceDel => piece to remove (id)
 * args.pieceMove => piece to move (id)
 * args.user => condition than indicate if move is the user
 *
 * @param args
 */
window.movePiece = function(args) {
  $('.box-lastmove').removeClass('box-lastmove');
  // const $panelDead = $('#block-pieces-dead');
  const $blockUi = $('#blockui-board');

  if (args.user) {
    $blockUi.show();
    movePieceDom(args.pieceMove, args.boxMove);

    if (args.capture) {
      // $panelDead.append('<div class="box-chess"><img src="' + $('#' + args.pieceDel).attr('src') + '" style="width:100%;"></div>');
      document.getElementById(args.pieceDel).remove();
    }
  } else {
    // console.log('animar movimiento');
    let $widthPiece = '60px';
    if (window.innerWidth < 670) {
      $widthPiece = '41px';
    }

    let $piece = $('#' + args.pieceMove);
    let $locInitial = $piece.position();
    let $locFinal = $(args.boxMove).position();

    $piece.css({
      'position': 'absolute',
      'width': $widthPiece,
      'top': $locInitial.top + 'px',
      'left': $locInitial.left + 'px',
    });
    // console.log('animate start');
    $piece.animate({
      'top': $locFinal.top + 'px',
      'left': $locFinal.left + 'px',
    }, 600, function() {
      movePieceDom(args.pieceMove, args.boxMove);

      if (args.capture) {
        // $panelDead.append('<div class="box-chess"><img src="' + $('#' + args.pieceDel).attr('src') + '" style="width:100%;"></div>');
        document.getElementById(args.pieceDel).remove();
      }

      $blockUi.hide();
    });
  }
};

/**
 * Animation move of piece
 *
 * @param piece
 * @param box
 */
window.movePieceDom = function(piece, box) {
  $(box).append(document.getElementById(piece));

  setTimeout(() => {
    document.getElementById(piece).classList.add('box-lastmove');
  }, 200);
};