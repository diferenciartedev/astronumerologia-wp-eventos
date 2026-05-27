/* Libro de Reclamaciones PRO — admin JS */
(function ($) {
	$(function () {
		// Vista previa: alternar desktop/mobile.
		$('.lrp-tab').on('click', function (e) {
			e.preventDefault();
			$('.lrp-tab').removeClass('active');
			$(this).addClass('active');
			var w = $(this).data('w');
			$('.lrp-preview-frame').removeClass('lrp-w-desktop lrp-w-mobile').addClass('lrp-w-' + w);
		});

		// Confirmaciones de borrado están en HTML.
	});
})(jQuery);
