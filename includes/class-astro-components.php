<?php
/**
 * Librería de componentes compartidos del design system de Astronumerología.
 *
 * Provee el set de iconos y los renderizadores de componentes base
 * (botón, chip, badge, eyebrow, encabezado de sección) reutilizables por
 * cualquier módulo del sitio: eventos, sesiones, etc.
 *
 * El marcado generado usa clases con prefijo `.astro-` que se estilan en
 * assets/css/design-system.css, todas acotadas a `.astro-scope` para no
 * interferir con el resto de astronumerologia.com.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Astro_Components {

	/**
	 * Handle de la hoja de estilos del design system.
	 */
	const STYLE = 'astro-ds';

	/**
	 * Engancha el registro de recursos.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register' ) );
	}

	/**
	 * Registra (sin encolar) la hoja del design system.
	 */
	public static function register() {
		wp_register_style( self::STYLE, EVT_URL . 'assets/css/design-system.css', array(), EVT_VERSION );
	}

	/**
	 * Devuelve el marcado SVG de un icono del set de la marca.
	 *
	 * @param string $nombre Nombre del icono.
	 * @return string
	 */
	public static function icon( $nombre ) {
		$iconos = array(
			'calendar' => '<path d="M7 3v4M17 3v4M4 9h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1z"/>',
			'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
			'globe'    => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c3 3 3 15 0 18c-3-3-3-15 0-18"/>',
			'pin'      => '<path d="M12 21s-7-7-7-12a7 7 0 0 1 14 0c0 5-7 12-7 12z"/><circle cx="12" cy="9" r="2.5"/>',
			'ticket'   => '<path d="M4 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2 2 2 0 0 0 0 4 2 2 0 0 1-2 2H6a2 2 0 0 1-2-2 2 2 0 0 0 0-4z"/><path d="M14 6v2M14 16v2M14 11v2"/>',
			'users'    => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 19c0-3 2.5-5 5.5-5s5.5 2 5.5 5"/><path d="M16 6.2a3 3 0 0 1 0 5.6M17 14.5c2.5.4 4 2.3 4 4.5"/>',
			'arrow'    => '<path d="M5 12h14M14 6l6 6-6 6"/>',
			'sparkle'  => '<path d="M12 3v6M12 15v6M3 12h6M15 12h6"/><path d="M12 9l1.6 1.6L12 12l-1.6-1.6z" fill="currentColor"/>',
		);

		if ( 'whatsapp' === $nombre ) {
			return '<svg class="astro-ic astro-ic--solid" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M17.5 14.4c-.3-.15-1.78-.88-2.05-.98-.28-.1-.48-.15-.68.15-.2.3-.78.98-.96 1.18-.18.2-.36.23-.66.08-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.46-.52.15-.18.2-.3.3-.5.1-.2.05-.38-.03-.53-.08-.15-.68-1.64-.93-2.24-.24-.59-.49-.51-.68-.52H7.8c-.2 0-.5.07-.77.38-.27.3-1.02 1-1.02 2.43s1.04 2.83 1.19 3.02c.15.2 2.05 3.14 4.97 4.4.7.3 1.24.48 1.66.62.7.22 1.33.19 1.83.12.56-.08 1.78-.73 2.03-1.43.25-.7.25-1.3.18-1.43-.08-.13-.28-.2-.58-.35zM12 2C6.48 2 2 6.48 2 12c0 1.74.46 3.4 1.34 4.88L2 22l5.27-1.32A9.94 9.94 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg>';
		}

		$contenido = isset( $iconos[ $nombre ] ) ? $iconos[ $nombre ] : '';
		return '<svg class="astro-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $contenido . '</svg>';
	}

	/**
	 * Renderiza un «eyebrow»: etiqueta breve en versalitas sobre un título.
	 *
	 * @param string $texto Texto del eyebrow.
	 * @return string
	 */
	public static function eyebrow( $texto ) {
		if ( '' === $texto ) {
			return '';
		}
		return '<span class="astro-eyebrow">' . esc_html( $texto ) . '</span>';
	}

	/**
	 * Renderiza el encabezado centrado de una sección.
	 *
	 * @param string $eyebrow Eyebrow opcional.
	 * @param string $titulo  Título de la sección.
	 * @param string $sub     Subtítulo / descripción opcional.
	 * @return string
	 */
	public static function section_head( $eyebrow, $titulo, $sub = '' ) {
		$html  = '<header class="astro-section__head">';
		$html .= self::eyebrow( $eyebrow );
		$html .= '<h2 class="astro-section__title">' . esc_html( $titulo ) . '</h2>';
		if ( '' !== $sub ) {
			$html .= '<p class="astro-section__sub">' . esc_html( $sub ) . '</p>';
		}
		$html .= '</header>';
		return $html;
	}

	/**
	 * Renderiza un botón (enlace o <button>) del design system.
	 *
	 * @param array $args {
	 *     @type string $text    Texto del botón.
	 *     @type string $href    URL; si está vacío se renderiza un <button>.
	 *     @type string $variant primary | whatsapp | ghost-light | ghost-dark.
	 *     @type string $icon    Nombre de icono opcional.
	 *     @type string $target  Atributo target.
	 *     @type string $rel     Atributo rel.
	 *     @type string $class   Clases CSS extra.
	 * }
	 * @return string
	 */
	public static function button( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'text'    => '',
				'href'    => '',
				'variant' => 'primary',
				'icon'    => '',
				'target'  => '',
				'rel'     => '',
				'class'   => '',
			)
		);

		$clases = 'astro-btn astro-btn--' . sanitize_html_class( $args['variant'] );
		if ( $args['class'] ) {
			$clases .= ' ' . sanitize_html_class( $args['class'] );
		}

		$interior  = $args['icon'] ? self::icon( $args['icon'] ) : '';
		$interior .= '<span>' . esc_html( $args['text'] ) . '</span>';

		if ( $args['href'] ) {
			$target = $args['target'] ? ' target="' . esc_attr( $args['target'] ) . '"' : '';
			$rel    = $args['rel'] ? ' rel="' . esc_attr( $args['rel'] ) . '"' : '';
			return '<a class="' . esc_attr( $clases ) . '" href="' . esc_url( $args['href'] ) . '"' . $target . $rel . '>' . $interior . '</a>';
		}

		return '<button type="button" class="' . esc_attr( $clases ) . '">' . $interior . '</button>';
	}

	/**
	 * Renderiza un «chip»: etiqueta compacta con icono opcional.
	 *
	 * @param string $texto   Texto del chip.
	 * @param string $icon    Nombre de icono opcional.
	 * @param string $variant Variante opcional (ej. «free»).
	 * @return string
	 */
	public static function chip( $texto, $icon = '', $variant = '' ) {
		$clases = 'astro-chip';
		if ( $variant ) {
			$clases .= ' astro-chip--' . sanitize_html_class( $variant );
		}
		$html  = '<span class="' . esc_attr( $clases ) . '">';
		$html .= $icon ? self::icon( $icon ) : '';
		$html .= '<span class="astro-chip__text">' . esc_html( $texto ) . '</span>';
		$html .= '</span>';
		return $html;
	}

	/**
	 * Renderiza un «badge» de estado.
	 *
	 * @param string $texto   Texto del badge.
	 * @param string $variant open | soldout | neutral.
	 * @return string
	 */
	public static function badge( $texto, $variant = 'neutral' ) {
		return '<span class="astro-badge astro-badge--' . sanitize_html_class( $variant ) . '">'
			. esc_html( $texto ) . '</span>';
	}
}
