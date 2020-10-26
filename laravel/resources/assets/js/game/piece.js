export default class Piece {
  constructor(args = {}) {
    this.location = args.location || [0, 0];
    this.color = args.color || "white";
    this.codeColor = args.codeColor || 0;
    this.name = args.name || "piece-";
    this.type = args.type || 0;
    this.status = 1;
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

  set(key, val) {
    this[key] = val;
  }

  get(key) {
    return this[key];
  }
}