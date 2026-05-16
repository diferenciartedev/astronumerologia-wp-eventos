<?php
/**
 * Integración con Elementor: categoría y widget «Eventos · Calendario».
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EVT_Elementor {

	/**
	 * Engancha la integración solo si Elementor está activo.
	 */
	public static function init() {
		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'categoria' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'registrar_widget' ) );
	}

	/**
	 * Registra la categoría «Astronumerología» en el panel de Elementor.
	 *
	 * @param object $manager Gestor de categorías de Elementor.
	 */
	public static function categoria( $manager ) {
		$manager->add_category(
			'astronumerologia',
			array(
				'title' => 'Astronumerología',
				'icon'  => 'eicon-calendar',
			)
		);
	}

	/**
	 * Registra el widget del calendario de eventos.
	 *
	 * @param object $widgets_manager Gestor de widgets de Elementor.
	 */
	public static function registrar_widget( $widgets_manager ) {
		require_once EVT_DIR . 'includes/class-evt-elementor-widget.php';
		$widgets_manager->register( new EVT_Elementor_Widget() );
	}
}
