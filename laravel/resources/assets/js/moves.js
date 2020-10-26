// type 1 => torre, 2 => alfil, 3 => caballo
const checkMove = function(args) {
  const posInitial = args.posInitial;
  const posFinal = args.posFinal;
  const type = args.type;
  const pieces = args.pieces;

  const row1 = posInitial[0] * 1;
  const row2 = posFinal[0] * 1;
  const col1 = posInitial[1] * 1;
  const col2 = posFinal[1] * 1;

  const rowResult = Math.abs(row1 - row2);
  const colResult = Math.abs(col1 - col2);

  let resultCheck = false;

  switch (type) {
    case 1: {
      // tower, una de las dos diferencias es 0 pero no ambas
      if ((rowResult === 0 || colResult === 0) && rowResult !== colResult) {
        let ejePoints = [col1, col2];
        let ejeFijo = row1;
        let moveRow = true;

        if (rowResult > colResult) {
          ejePoints = [row1, row2];
          ejeFijo = col1;
          moveRow = false;
        }

        if (checkPath.getBoxesTower(ejePoints, moveRow, ejeFijo, pieces)) {
          resultCheck = true;
        }
      }

      break;
    }

    case 2: {
      // alfil, las dos diferencias son iguales
      if (rowResult === colResult) {
        if (checkPath.getBoxesAlfil(posInitial, posFinal, pieces)) {
          resultCheck = true;
        }
      }

      break;
    }

    case 3: {
      // horse, la suma de las dos diferencias es 3
      if ((rowResult + colResult) === 3 && rowResult > 0 && colResult > 0) {
        const index = pieces.indexOf(String(row1) + String(col1));
        boardChess.piecesLoc[index] = String(row2) + String(col2);
        resultCheck = true;
      }

      break;
    }
  }

  if (resultCheck) {
    axios.post('validatemove', {
      'piece': args.piece,
      'location': {
        'row': posFinal[0],
        'col': posFinal[1],
      },
    }).then((response) => {
      // console.log(response.data)
    });
    // console.log(pieces)

    return true;
  }

  return false;
};

const checkPath = {
  calcPoints(point1 = 0, point2 = 0) {
    let points = [];

    if (point1 > point2) {
      for (let i = point1 - 1; i > point2; i--) {
        points.push(i);
      }
    } else {
      for (let i = point1 + 1; i < point2; i++) {
        points.push(i);
      }
    }

    return points;
  },

  getBoxesAlfil(loc1, loc2, aPieces = []) {
    const rows = this.calcPoints(loc1[0] * 1, loc2[0] * 1);
    const cols = this.calcPoints(loc1[1] * 1, loc2[1] * 1);

    for (let i in rows) {
      const indexPiece = aPieces.indexOf(String(rows[i]) + String(cols[i]));

      if (indexPiece > -1) {
        return false;
      }
      // console.log(`Location Box ${i*1+1*1} => ${rows[i]}, ${cols[i]}`)
    }
    const index = aPieces.indexOf(String(loc1[0]) + String(loc1[1]));
    boardChess.piecesLoc[index] = String(loc2[0]) + String(loc2[1]);

    return true;
  },

  getBoxesTower(loc, isrow, n1 = 0, aPieces = []) {
    // console.log(`parametros`, loc, isrow, n1, aPieces)
    const points = this.calcPoints(parseInt(loc[0]), parseInt(loc[1]));
    // console.log('puntos', points)

    if (isrow) {
      for (let i in points) {
        const indexPiece = aPieces.indexOf(String(n1) + String(points[i]));

        if (indexPiece > -1) {
          // boardChess.piecesLoc.splice(indexPiece, 1);
          return false;
        }
        // console.log(`Location Box ${i * 1 + 1 * 1} => ${n1}, ${points[i]}`, indexPiece, aPieces)
      }

      // console.log('nuevo index', indexTower, boardChess.piecesLoc)
      const index = aPieces.indexOf(String(n1) + String(loc[0]));
      boardChess.piecesLoc[index] = String(n1) + String(loc[1]);

      return true;
    }

    for (let i in points) {
      const indexPiece = aPieces.indexOf(String(points[i]) + String(n1));

      if (indexPiece > -1) {
        // boardChess.piecesLoc.splice(indexPiece, 1);
        return false;
      }
      // console.log(`Location Box ${i * 1 + 1 * 1} => ${points[i]}, ${n1}`)
      // let indexTower = aPieces.indexOf(String(loc[0]) + String(n1));
      // boardChess.piecesLoc[indexTower] = String(loc[1]) + String(n1);
      // console.log('nuevo index', indexTower, boardChess.piecesLoc)
    }

    const index = aPieces.indexOf(String(loc[0]) + String(n1));
    boardChess.piecesLoc[index] = String(loc[1]) + String(n1);

    return true;
  },
};

Object.defineProperty(window, 'checkMove', {
  __proto__: null,
  value: checkMove,
  writable: false,
  enumerable: true,
  configurable: true,
});

Object.defineProperty(window, 'checkPath', {
  __proto__: null,
  value: checkPath,
  writable: false,
  enumerable: true,
  configurable: true,
});