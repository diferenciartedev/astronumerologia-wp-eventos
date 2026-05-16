/* Eventos · Astronumerología — filtro de modalidad en el front-end */
( function () {
	'use strict';

	function visible( card ) {
		return card.style.display !== 'none';
	}

	function aplicar( app, filtro ) {
		app.querySelectorAll( '.evt-card' ).forEach( function ( card ) {
			var coincide = filtro === 'todos' || card.getAttribute( 'data-modalidad' ) === filtro;
			card.style.display = coincide ? '' : 'none';
		} );

		// Oculta las secciones que se quedan sin eventos visibles.
		app.querySelectorAll( '.evt-section' ).forEach( function ( seccion ) {
			var quedan = Array.prototype.filter.call(
				seccion.querySelectorAll( '.evt-card' ),
				visible
			).length;
			seccion.style.display = quedan ? '' : 'none';
		} );
	}

	function init() {
		document.querySelectorAll( '[data-evt-filter]' ).forEach( function ( bar ) {
			var app = bar.closest( '.evt-app' );
			if ( ! app ) {
				return;
			}
			bar.addEventListener( 'click', function ( e ) {
				var btn = e.target.closest( '.evt-pill' );
				if ( ! btn ) {
					return;
				}
				bar.querySelectorAll( '.evt-pill' ).forEach( function ( p ) {
					p.classList.remove( 'is-active' );
				} );
				btn.classList.add( 'is-active' );
				aplicar( app, btn.getAttribute( 'data-f' ) );
			} );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
