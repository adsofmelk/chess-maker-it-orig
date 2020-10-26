require('./bootstrap');

Object.defineProperty(window, "bootbox", {
  value: require('bootbox'),
  writable: false,
  enumerable: true,
  configurable: true
});

Object.defineProperty(window, "Vue", {
  value: require('vue'),
  writable: false,
  enumerable: true,
  configurable: true
});

Object.defineProperty(window, "Ably", {
  value: require('ably'),
  writable: false,
  enumerable: true,
  configurable: true
});

require('vue-resource');

window.chApp = {};

Object.defineProperty(window.chApp, "version", {
  value: '1.0.0',
  writable: false,
  enumerable: true,
  configurable: true
});

Object.defineProperty(window.chApp, "Developer Company", {
  value: 'Damos Soluciones',
  writable: false,
  enumerable: true,
  configurable: true
});

Object.defineProperty(window.chApp, "Development Team", {
  value: {
    'programmers': [{
      'name': 'Diego Lozano'
    }],
    'designers': []
  },
  writable: false,
  enumerable: true,
  configurable: true
});

require('./vendors/jquery-ui');
require('./vendors/jquery.ui.touch-punch');
