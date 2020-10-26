import BoardChess from './game/boardChess';

window.boardChess = new BoardChess();

boardChess.asignDistribution = function (str) {
	this.distribution = str || this.distribution;

	let distribution = this.distribution;
	let pieces = distribution.split(',');
	let mtypes = [4, 1, 2, 3, 1, 2, 3];
	let mcolors = ['none', 'white', 'white', 'white', 'black', 'black', 'black'];
	let mnames = ['piece-ball', 'piece-tw', 'piece-aw', 'piece-hw', 'piece-tb', 'piece-ab', 'piece-hb'];
	let piecesJs = [];
	let piecesLoc = [];
	let piecesJsMap = [];

	for (let i = 0; i < 7; i++) {
		let locPiece = this.boxes[pieces[i] - 1].location;

		piecesJsMap.push(mnames[i]);

		piecesLoc.push(String(locPiece[0]) + String(locPiece[1]));
		// piecesJs.push(new Piece({
		// 	location: locPiece,
		// 	type: mtypes[i],
		// 	color: mcolors[i],
		// 	name: mnames[i],
		// }));
	}

	let cloneBall = $('#piece-ball');
	let cloneTowerWhite = $('#piece-tw');
	let cloneAlfilWhite = $('#piece-aw');
	let cloneHorseWhite = $('#piece-hw');
	let cloneTowerBlack = $('#piece-tb');
	let cloneAlfilBlack = $('#piece-ab');
	let cloneHorseBlack = $('#piece-hb');

	$('#' + this.boxes[pieces[0] - 1].name).html('').addClass('box-main').append(cloneBall);
	$('#' + this.boxes[pieces[1] - 1].name).html('').append(cloneTowerWhite);
	$('#' + this.boxes[pieces[2] - 1].name).html('').append(cloneAlfilWhite);
	$('#' + this.boxes[pieces[3] - 1].name).html('').append(cloneHorseWhite);
	$('#' + this.boxes[pieces[4] - 1].name).html('').append(cloneTowerBlack);
	$('#' + this.boxes[pieces[5] - 1].name).html('').append(cloneAlfilBlack);
	$('#' + this.boxes[pieces[6] - 1].name).html('').append(cloneHorseBlack);

	this.pieces = piecesJs;
	this.piecesMap = piecesJsMap;
	this.piecesLoc = piecesLoc;
};


const allowDrop = function (ev) {
	ev.preventDefault();
	$('.box-chess, .ispiece').removeClass('box-hover')
	ev.target.classList.add('box-hover')

};

const drag = function (ev) {
	ev.dataTransfer.setData("text", ev.target.id);
};

const drop = function (ev) {
	ev.preventDefault();
	$('.box-chess, .ispiece').removeClass('box-hover')
	const data = ev.dataTransfer.getData("text");

	if ($('#' + data).hasClass('goal')) {
		$('.box-main').removeClass('box-main');
		$(ev.target).addClass('box-main');
	}
	if ($(ev.target).hasClass('ispiece')) {
		$(ev.target).parent().append(document.getElementById(data));
		// $(ev.target).remove();
	} else {
		ev.target.appendChild(document.getElementById(data));
	}
};

Object.defineProperty(window, "allowDrop", {
	value: allowDrop,
	writable: false,
	enumerable: true,
	configurable: true
});

Object.defineProperty(window, "drag", {
	value: drag,
	writable: false,
	enumerable: true,
	configurable: true
});

Object.defineProperty(window, "drop", {
	value: drop,
	writable: false,
	enumerable: true,
	configurable: true
});