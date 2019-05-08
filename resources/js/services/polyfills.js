if (!String.prototype.ucfirst) {
  String.prototype.ucfirst = function() {
    'use strict'
    return this.charAt(0).toUpperCase() + this.slice(1)
  }
}
