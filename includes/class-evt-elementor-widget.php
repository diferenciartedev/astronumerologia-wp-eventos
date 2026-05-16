<?php
/**
 * Widget de Elementor para el calendario de eventos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EVT_Elementor_Widget extends \Elementor\Widget_Base {

	/**
	 * Nombre interno del widget.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'evt_calendario';
	}

	/**
	 * Título visible del widget.
	 *
	 * @return string
	 */
	public function get_title() {
		return 'Eventos · Calendario';
	}

	/**
	 * Icono del widget.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-calendar';
	}

	/**
	 * Categorías a las que pertenece el widget.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'astronumerologia', 'general' );
	}

	/**
	 * Palabras clave para la búsqueda en Elementor.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'eventos', 'calendario', 'agenda', 'astronumerologia' );
	}

	/**
	 * Hojas de estilo dependientes del widget.
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( Astro_Components::STYLE, 'evt-frontend' );
	}

	/**
	 * Scripts dependientes del widget.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'evt-frontend' );
	}

	/**
	 * Define los controles del widget.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'seccion_contenido',
			array(
				'label' => 'Secciones',
			)
		);

		$this->add_control(
			'mostrar_mes',
			array(
				'label'        => 'Mostrar «Eventos de este mes»',
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => 'Sí',
				'label_off'    => 'No',
				'return_value' => 'si',
				'default'      => 'si',
			)
		);

		$this->add_control(
			'mostrar_proximos',
			array(
				'label'        => 'Mostrar «Próximos eventos»',
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => 'Sí',
				'label_off'    => 'No',
				'return_value' => 'si',
				'default'      => 'si',
			)
		);

		$this->add_control(
			'mostrar_anteriores',
			array(
				'label'        => 'Mostrar «Eventos anteriores»',
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => 'Sí',
				'label_off'    => 'No',
				'return_value' => 'si',
				'default'      => 'si',
			)
		);

		$this->add_control(
			'mostrar_filtros',
			array(
				'label'        => 'Mostrar filtro por modalidad',
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => 'Sí',
				'label_off'    => 'No',
				'return_value' => 'si',
				'default'      => 'si',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Renderiza el widget en el front-end.
	 */
	protected function render() {
		$ajustes = $this->get_settings_for_display();

		echo EVT_Shortcode::render(
			array(
				'mes'        => ( 'si' === $ajustes['mostrar_mes'] ) ? 'si' : 'no',
				'proximos'   => ( 'si' === $ajustes['mostrar_proximos'] ) ? 'si' : 'no',
				'anteriores' => ( 'si' === $ajustes['mostrar_anteriores'] ) ? 'si' : 'no',
				'filtros'    => ( 'si' === $ajustes['mostrar_filtros'] ) ? 'si' : 'no',
			)
		);
	}
}
