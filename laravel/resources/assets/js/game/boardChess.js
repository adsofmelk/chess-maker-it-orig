import Box from './box';
import Piece from './piece';

export default class BoardChess {
  constructor(args = {}) {
    this.boxes = [];
    this.date = new Date().toString();
    this.nameGame = 'Game ' + new Date().getTime().toString();
    this.distribution = '28,37,23,19,30,5,27';
    this.container = args.container || 'body';
    this.pieces = [];
    this.piecesMap = [];
    this.piecesLoc = [];
    this.myPieces = [];
    this.youPieces = [];
    this.myColor = 0;

    this.buildBoxes();
  }

  render() {
    let boxes = this.boxes;
    let html = '';

    for (let i in boxes) {
      let box = boxes[i];
      html = html + box.html;
    }

    $(this.container).append($('#blockui-board-origin').clone().attr('id', 'blockui-board'));
    $(this.container).append(html);
  }

  asignDistribution(str, color) {
    this.distribution = str || this.distribution;
    this.myColor = color;

    // console.log('------------- Distribuion Me ----------', this.distribution, str);
    // console.log('------------- Color Me ----------', this.myColor, color);

    let distribution = this.distribution;
    let pieces = distribution.split(',');
    let mtypes = [4, 1, 2, 3, 1, 2, 3];
    let mcolors = ['none', 'white', 'white', 'white', 'black', 'black', 'black'];
    let mnames = ['piece-ball', 'piece-tw', 'piece-aw', 'piece-hw', 'piece-tb', 'piece-ab', 'piece-hb'];
    let piecesJs = [];
    let piecesLoc = [];
    let piecesJsMap = [];
    let piecesWhite = [];
    let piecesBlack = [];

    for (let i = 0; i < 7; i++) {
      let locPiece = this.boxes[pieces[i] - 1].location;
      piecesJsMap.push(mnames[i]);
      piecesLoc.push(String(locPiece[0]) + String(locPiece[1]));

      const piece = new Piece({
        location: locPiece,
        type: mtypes[i],
        color: mcolors[i],
        name: mnames[i],
      });

      if (piece.color == 'white') {
        piecesWhite.push(piece);
      } else if (piece.color == 'black') {
        piecesBlack.push(piece);
      }

      piecesJs.push(piece);
    }

    if (this.myColor == 0) {
      this.myPieces = piecesBlack;
      this.youPieces = piecesWhite;
    } else {
      this.myPieces = piecesWhite;
      this.youPieces = piecesBlack;
    }

    let cloneBall = $('#piece-ball-origin').clone();
    let cloneTowerWhite = $('#piece-tw-origin').clone();
    let cloneAlfilWhite = $('#piece-aw-origin').clone();
    let cloneHorseWhite = $('#piece-hw-origin').clone();
    let cloneTowerBlack = $('#piece-tb-origin').clone();
    let cloneAlfilBlack = $('#piece-ab-origin').clone();
    let cloneHorseBlack = $('#piece-hb-origin').clone();

    cloneBall.attr('id', 'piece-ball');
    cloneBall.attr('draggable', false);
    cloneTowerWhite.attr('id', 'piece-tw');
    cloneAlfilWhite.attr('id', 'piece-aw');
    cloneHorseWhite.attr('id', 'piece-hw');
    cloneTowerBlack.attr('id', 'piece-tb');
    cloneAlfilBlack.attr('id', 'piece-ab');
    cloneHorseBlack.attr('id', 'piece-hb');

    $('#' + this.boxes[pieces[0] - 1].name).html('').addClass('box-main').append(cloneBall);
    $('#piece-ball')[0].draggable = false;
    $('#' + this.boxes[pieces[1] - 1].name).html('').append(cloneTowerWhite);
    $('#' + this.boxes[pieces[2] - 1].name).html('').append(cloneAlfilWhite);
    $('#' + this.boxes[pieces[3] - 1].name).html('').append(cloneHorseWhite);
    $('#' + this.boxes[pieces[4] - 1].name).html('').append(cloneTowerBlack);
    $('#' + this.boxes[pieces[5] - 1].name).html('').append(cloneAlfilBlack);
    $('#' + this.boxes[pieces[6] - 1].name).html('').append(cloneHorseBlack);
    // $('#' + this.boxes[pieces[1] - 1].name).html('').append('<div ondragstart="window.drag(event)" draggable="true" class="ispiece" data-color="'+cloneTowerWhite.data('color')+'" id="'+cloneTowerWhite.attr('id')+'"><img src="'+cloneTowerWhite.attr('src')+'" class="imgpiece" style="width:100%"></div>');
    // $('#' + this.boxes[pieces[2] - 1].name).html('').append('<div ondragstart="window.drag(event)" draggable="true" class="ispiece" data-color="'+cloneAlfilWhite.data('color')+'" id="'+cloneAlfilWhite.attr('id')+'"><img src="'+cloneAlfilWhite.attr('src')+'" class="imgpiece" style="width:100%"></div>');
    // $('#' + this.boxes[pieces[3] - 1].name).html('').append('<div ondragstart="window.drag(event)" draggable="true" class="ispiece" data-color="'+cloneHorseWhite.data('color')+'" id="'+cloneHorseWhite.attr('id')+'"><img src="'+cloneHorseWhite.attr('src')+'" class="imgpiece" style="width:100%"></div>');
    // $('#' + this.boxes[pieces[4] - 1].name).html('').append('<div ondragstart="window.drag(event)" draggable="true" class="ispiece" data-color="'+cloneTowerBlack.data('color')+'" id="'+cloneTowerBlack.attr('id')+'"><img src="'+cloneTowerBlack.attr('src')+'" class="imgpiece" style="width:100%"></div>');
    // $('#' + this.boxes[pieces[5] - 1].name).html('').append('<div ondragstart="window.drag(event)" draggable="true" class="ispiece" data-color="'+cloneAlfilBlack.data('color')+'" id="'+cloneAlfilBlack.attr('id')+'"><img src="'+cloneAlfilBlack.attr('src')+'" class="imgpiece" style="width:100%"></div>');
    // $('#' + this.boxes[pieces[6] - 1].name).html('').append('<div ondragstart="window.drag(event)" draggable="true" class="ispiece" data-color="'+cloneHorseBlack.data('color')+'" id="'+cloneHorseBlack.attr('id')+'"><img src="'+cloneHorseBlack.attr('src')+'" class="imgpiece" style="width:100%"></div>');

    this.pieces = piecesJs;
    this.piecesMap = piecesJsMap;
    this.piecesLoc = piecesLoc;
  }

  buildBoxes() {
    let bColor = true;
    let boxes = [];

    for (let i = 0; i < 8; i++) {
      if (i % 2 === 0) {
        bColor = true;
      } else {
        bColor = false;
      }

      for (let j = 0; j < 8; j++) {
        let color = 'black';
        let codeColor = 1;

        if (bColor) {
          color = 'white',
              codeColor = 0;
          bColor = false;
        } else {
          bColor = true;
        }

        let box = new Box({
          location: [i, j],
          color: color,
          codeColor: codeColor,
          name: 'box-' + i.toString() + j.toString(),
        });
        // box.setHtml()
        boxes.push(box);
      }
    }

    this.boxes = boxes;
  }

  clearBoard() {
    $(this.container).html('');
  }
}