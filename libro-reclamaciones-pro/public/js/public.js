/* Libro de Reclamaciones PRO — Public JS */
(function () {
	document.addEventListener('DOMContentLoaded', function () {
		// Toggle del bloque "apoderado" cuando el usuario marca menor de edad.
		var minorCheckbox = document.querySelector('.lrp-form input[name="is_minor"]');
		var guardianBlock = document.querySelector('.lrp-form .lrp-guardian');
		if (minorCheckbox && guardianBlock) {
			var toggle = function () {
				guardianBlock.style.display = minorCheckbox.checked ? 'grid' : 'none';
			};
			minorCheckbox.addEventListener('change', toggle);
			toggle();
		}

		// Validación mínima previa al submit.
		var form = document.querySelector('.lrp-form');
		if (!form) return;
		form.addEventListener('submit', function (e) {
			var required = form.querySelectorAll('[required]');
			var ok = true;
			required.forEach(function (el) {
				if (!el.value || (el.type === 'checkbox' && !el.checked)) {
					el.classList.add('lrp-input-error');
					ok = false;
				} else {
					el.classList.remove('lrp-input-error');
				}
			});
			if (!ok) {
				e.preventDefault();
				window.scrollTo({ top: form.getBoundingClientRect().top + window.scrollY - 40, behavior: 'smooth' });
			}
		});
	});
})();
