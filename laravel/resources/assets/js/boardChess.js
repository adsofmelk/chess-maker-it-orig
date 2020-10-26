window.boardChess = {
  'constructor': function (args) {
    args = args || {};
    let bColor = true,
      board = [];

    for (let i = 0; i < 8; i++) {
      if (i % 2 == 0) {
        bColor = true;
      } else {
        bColor = false;
      }

      for (let j = 0; j < 8; j++) {
        var color = 'black',
          codeColor = 1;

        if (bColor) {
          color = 'white',
            codeColor = 0;
          bColor = false;
        } else {
          bColor = true;
        }

        board.push(this.box.create({
          location: [i, j],
          color: color,
          codeColor: codeColor,
          name: 'box-' + i.toString() + j.toString()
        }));
      }
    }

    return {
      boxes: board,
      date: new Date().toString(),
      nameGame: 'Test ' + new Date().getTime().toString(),
      distribution: '',
      container: args.container || 'body',
      asignDistribution: this.asignDistribution,
      pieces: []
    };
  },

  'box': {
    'create': function (args) {
      args = args || {};
      args.location = args.location || [0, 0];
      args.color = args.color || 'white';
      args.codeColor = args.codeColor || 0;
      args.name = args.name || 'box-';
      args.piece = args.piece || '';

      return {
        location: args.location,
        color: args.color,
        codeColor: args.codeColor,
        name: args.name,
        piece: args.piece
      };
    }
  },

  'piece': {
    'create': function (args) {
      args = args || {};
      args.location = args.location || [0, 0];
      args.color = args.color || 'white';
      args.codeColor = args.codeColor || 0;
      args.name = args.name || 'piece-';
      args.type = args.type || 0;

      return {
        location: args.location,
        color: args.color,
        codeColor: args.codeColor,
        name: args.name,
        type: args.type
      };
    }
  },

  'render': function () {

    window.boardGame = this.constructor({
      'container': '#board-game'
    });

    let boxes = window.boardGame.boxes,
      html = '';

    for (let i in boxes) {
      let className = 'box-white',
        box = boxes[i];

      if (box.codeColor > 0) {
        className = 'box-black';
      }

      html = html + '<div data-location="' + box.location + '" id="' + box.name + '" class="box-chess ' + className + '" ondrop="window.drop(event)" ondragover="window.allowDrop(event)"></div>';
      // console.log(`Location {i}`,window.boardGame.boxes[i])
    }

    $(window.boardGame.container).append(html);
  },

  'asignDistribution': function () {
    let distribution = window.boardGame.distribution,
      pieces = distribution.split(',');
    // console.log(pieces);
    // var el = document.getElementById(window.boardGame.boxes[pieces[0]].name);
    // el.ondrop=null;
    // el.ondragover=null;
    $('#' + window.boardGame.boxes[pieces[0] - 1].name).html('').addClass('box-main').append($('#piece-ball'));
    $('#piece-ball')[0].draggable = false;
    $('#' + window.boardGame.boxes[pieces[1] - 1].name).html('').append($('#piece-tw'));
    $('#' + window.boardGame.boxes[pieces[2] - 1].name).html('').append($('#piece-aw'));
    $('#' + window.boardGame.boxes[pieces[3] - 1].name).html('').append($('#piece-hw'));
    $('#' + window.boardGame.boxes[pieces[4] - 1].name).html('').append($('#piece-tb'));
    $('#' + window.boardGame.boxes[pieces[5] - 1].name).html('').append($('#piece-ab'));
    $('#' + window.boardGame.boxes[pieces[6] - 1].name).html('').append($('#piece-hb'));

    let mtypes = [4, 1, 2, 3, 1, 2, 3],
      mcolors = ['none', 'white', 'white', 'white', 'black', 'black', 'black'],
      mnames = ['piece-ball', 'piece-tw', 'piece-aw', 'piece-hw', 'piece-tb', 'piece-ab', 'piece-hb'],
      piecesJs = [],
      piecesJsMap = [];

    for (let i = 0; i < 7; i++) {
      piecesJsMap.push(mnames[i]);
      piecesJs.push(this.piece.create({
        location: window.boardGame.boxes[pieces[i] - 1].location,
        type: mtypes[i],
        color: mcolors[i],
        name: mnames[i]
      }));
    }

    window.boardGame.pieces = piecesJs;
    window.boardGame.piecesMap = piecesJsMap;
  }
};

window.chApp.boardChess = window.boardChess;