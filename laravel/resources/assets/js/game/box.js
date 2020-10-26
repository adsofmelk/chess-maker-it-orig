import Piece from "./piece";

export default class Box {
  constructor(args = {}) {
    this.location = args.location || [0, 0];
    this.color = args.color || "white";
    this.codeColor = args.codeColor || 0;
    this.name = args.name || "box-";
    this.piece = args.piece || new Piece();
    this.status = 1;
    this.html = "";

    this.setHtml();
  }

  getLocation() {
    return this.location;
  }

  setLocatio(location) {
    this.location = location;
  }

  getStatus() {
    return this.status;
  }

  setStatus(status) {
    this.status = status;
  }

  getPiece() {
    return this.piece;
  }

  setPiece(piece) {
    this.piece = piece;
  }

  setHtml() {
    let className = this.codeColor > 0 ? "box-black" : "box-white";

    // this.html = '<div data-location="' + this.location + '" id="' + this.name + '" class="box-chess ' + className + '" ondrop="window.drop(event)" ondragover="window.allowDrop(event)"></div>';
    this.html = '<div data-location="' + this.location + '" id="' + this.name + '" class="box-chess ' + className + '"></div>';
  }

  getHtml() {
    return this.html;
  }
}