<?php
/**
 * Registro del tipo de contenido «Evento».
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EVT_CPT_Registrar {

	/**
	 * Engancha el registro del CPT al hook init.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
	}

	/**
	 * Registra el tipo de contenido «evento».
	 */
	public static function register() {
		$labels = array(
			'name'               => 'Eventos',
			'singular_name'      => 'Evento',
			'add_new'            => 'Añadir evento',
			'add_new_item'       => 'Añadir nuevo evento',
			'edit_item'          => 'Editar evento',
			'new_item'           => 'Nuevo evento',
			'view_item'          => 'Ver evento',
			'view_items'         => 'Ver eventos',
			'search_items'       => 'Buscar eventos',
			'not_found'          => 'No se encontraron eventos',
			'not_found_in_trash' => 'No hay eventos en la papelera',
			'all_items'          => 'Todos los eventos',
			'menu_name'          => 'Eventos',
			'name_admin_bar'     => 'Evento',
		);

		register_post_type(
			EVT_CPT,
			array(
				'labels'              => $labels,
				'public'              => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'has_archive'         => 'eventos',
				'exclude_from_search' => false,
				'menu_icon'           => 'dashicons-calendar-alt',
				'menu_position'       => 26,
				'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
				'rewrite'             => array( 'slug' => 'eventos' ),
				'capability_type'     => 'post',
			)
		);
	}
}
