require('bootstrap');

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
	window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
	console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

Object.defineProperty(window, "bootbox", {
	value: require('bootbox'),
	writable: false,
	enumerable: true,
	configurable: true
});

window.langDatatable = {
	"lengthMenu": "Mostrar _MENU_",
	"zeroRecords": "No se encuentra nada.",
	"info": "_TOTAL_ registros de _MAX_",
	"infoEmpty": "No hay registros",
	"infoFiltered": "(Buscando en _MAX_ registros)",
	"decimal": "",
	"emptyTable": "No hay datos",
	"infoPostFix": "",
	"thousands": ",",
	"loadingRecords": "Cargando...",
	"processing": "Procesando...",
	"search": "Buscar:",
	"zeroRecords": "No hay registros segun la busqueda",
	"paginate": {
		"first": "&lt;&lt;",
		"last": "&gt;&gt;",
		"next": "&gt;",
		"previous": "&lt;"
	},
	"aria": {
		"sortAscending": ": activate to sort column ascending",
		"sortDescending": ": activate to sort column descending"
	}
};

$(function () {
	const optionsPorlet = {
		bodyToggleSpeed: 400,
		tooltips: true,
		tools: {
			toggle: {
				collapse: 'Recoger',
				expand: 'Expandir'
			},
			fullscreen: {
				on: 'Maximizar',
				off: 'Minimizar'
			}
		}
	};
	window.portlets = [];
	const $portlets = $('.portlet-section');

	if ($portlets.length > 0) {
		$portlets.each(function (index, el) {
			const id = $(el).attr('id');
			portlets[id] = new mPortlet(id, optionsPorlet);
		})
	}

	const toggleMenu = 'm_aside_left_minimize_toggle';

	if (localStorage.getItem(toggleMenu) == null) {
		localStorage.setItem(toggleMenu, false);
		mToggle(toggleMenu).toggle();
	} else if (localStorage.getItem(toggleMenu) == 'true') {
		mToggle(toggleMenu).toggleOn();
	} else {
		mToggle(toggleMenu).toggle();
	}

	mToggle(toggleMenu).on('toggle', function (toggle) {
		// console.log('ddd', toogle);
		if (toggle.state == 'off') {
			localStorage.setItem(toggleMenu, false);
		} else {
			localStorage.setItem(toggleMenu, true);
		}
	});

	var $tooltips = $('.tooltip-damos');
	if ($tooltips.length > 0) {
		$tooltips.each(function (index, el) {
			const data = $(el).data();
			const placement = (data.placement) ? data.placement : 'top';
			const skin = (data.skin == 'dark') ? 'm-tooltip--skin-dark' : '';
			var tip = new Tooltip(el, {
				title: data.originalTitle,
				placement: placement,
				offset: '0,5px',
				trigger: 'hover',
				template: '<div class="m-tooltip ' + skin + ' m-tooltip--portlet tooltip bs-tooltip-' + placement + '" role="tooltip">\
							<div class="tooltip-arrow arrow"></div>\
							<div class="tooltip-inner"></div>\
					</div>',
				container: 'body',
			});

		});
	}

	toastr.options = {
		// "closeButton": false,
		// "debug": false,
		// "newestOnTop": false,
		// "progressBar": false,
		"positionClass": "toast-bottom-right",
		// "preventDuplicates": false,
		// "onclick": null,
		// "showDuration": "300",
		// "hideDuration": "1000",
		"timeOut": "7000",
		// "extendedTimeOut": "1000",
		// "showEasing": "swing",
		// "hideEasing": "linear",
		// "showMethod": "fadeIn",
		// "hideMethod": "fadeOut"
	};

});

$(window).on('load', function () {
	let $messagaAlert = $('#message-alert');
	if ($messagaAlert.length > 0) {
		swal({
			title: $messagaAlert.html(),
			type: 'success',
		});
	}

	let $itemActive = $('#menu-active').val();

	if ($itemActive != '') {
		$($itemActive).addClass('m-menu__item--active');
	}
});