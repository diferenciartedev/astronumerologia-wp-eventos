<?php
/**
 * Shortcode y carga de recursos del front-end del calendario de eventos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EVT_Shortcode {

	/**
	 * Engancha el shortcode y el registro de recursos.
	 */
	public static function init() {
		add_shortcode( 'eventos_calendario', array( __CLASS__, 'render' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_assets' ) );
	}

	/**
	 * Registra (sin encolar) los recursos del front-end.
	 */
	public static function register_assets() {
		wp_register_style( 'evt-frontend', EVT_URL . 'assets/css/eventos.css', array( Astro_Components::STYLE ), EVT_VERSION );
		wp_register_script( 'evt-frontend', EVT_URL . 'assets/js/eventos.js', array(), EVT_VERSION, true );
	}

	/**
	 * Renderiza el calendario de eventos.
	 *
	 * @param array $atts Atributos del shortcode.
	 * @return string
	 */
	public static function render( $atts = array() ) {
		$atts = shortcode_atts(
			array(
				'mes'        => 'si',
				'proximos'   => 'si',
				'anteriores' => 'si',
				'filtros'    => 'si',
			),
			$atts,
			'eventos_calendario'
		);

		wp_enqueue_style( 'evt-frontend' );
		wp_enqueue_script( 'evt-frontend' );

		$grupos = evt_get_eventos_agrupados();
		if ( 'si' !== $atts['mes'] ) {
			$grupos['mes'] = array();
		}
		if ( 'si' !== $atts['proximos'] ) {
			$grupos['proximos'] = array();
		}
		if ( 'si' !== $atts['anteriores'] ) {
			$grupos['anteriores'] = array();
		}

		$mostrar_filtros = ( 'si' === $atts['filtros'] );

		ob_start();
		include EVT_DIR . 'templates/calendario.php';
		return ob_get_clean();
	}
}
