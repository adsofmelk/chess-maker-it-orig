/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function (config) {
	// Define changes to default configuration here. For example:
	config.language = 'es';
	// config.uiColor = '#AADC6E';
	config.allowedContent = true;
	// Permite iconos <i></i>
	// config.protectedSource.push(/<i[^>]*><\/i>/g);
	// evita quitar la clase a la etiqueta h4
	// config.protectedSource.push(/[^<]*(<h4>([^<]+)<\/h4>)/g);
	// evita quitar la clase a la etiqueta ul
	//config.protectedSource.push(/[^<]*(<ul>([^<]+)<\/ul>)/g);
	// evita quitar la clase a la etiqueta li
	//config.protectedSource.push(/[^<]*(<li>([^<]+)<\/li>)/g);
	// ALLOW <span></span>
	config.protectedSource.push(/<span[^>]*><\/span>/g);
	config.entities_latin = false;
	config.entities = false;

	//Esta forma aun funciona, ya que me permite los br 7 jun 2016
	config.protectedSource.push(/<br[^>]*>/g);

	// evita quitar la clase a la etiqueta ul
	//config.protectedSource.push(/(<ul[^>]*><\/ul>)/g);

	config.extraPlugins = 'codemirror';
	config.removePlugins = 'sourcedialog';
	config.embed_provider = '//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}';

	config.extraAllowedContent = 'a span(*)';
	config.extraAllowedContent = 'p ul ol li i';

	config.filebrowserBrowseUrl = '../../filemanager/dialog.php?type=2&editor=ckeditor&fldr=&lang=es';
	config.filebrowserUploadUrl = '../../filemanager/dialog.php?type=2&editor=ckeditor&fldr=&lang=es';
	config.filebrowserImageBrowseUrl = '../../filemanager/dialog.php?type=1&editor=ckeditor&fldr=&lang=es';
};