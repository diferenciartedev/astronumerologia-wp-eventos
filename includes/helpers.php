<?php
/**
 * Funciones auxiliares compartidas del plugin Eventos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lista de claves de metadatos del evento y su saneamiento.
 *
 * @return array
 */
function evt_campos() {
	return array(
		'fecha'         => 'date',
		'hora'          => 'time',
		'modalidad'     => 'choice',   // presencial | virtual
		'enlace'        => 'url',
		'direccion'     => 'text',
		'costo'         => 'choice',   // gratuito | pago
		'precio'        => 'text',
		'aforo'         => 'int',
		'cupo'          => 'choice',   // disponible | agotado
		'whatsapp'      => 'text',
		'deshabilitado' => 'bool',
	);
}

/**
 * Devuelve un metadato del evento con su prefijo interno.
 *
 * @param int    $post_id ID del evento.
 * @param string $key     Clave sin prefijo.
 * @param mixed  $default Valor por defecto.
 * @return mixed
 */
function evt_meta( $post_id, $key, $default = '' ) {
	$value = get_post_meta( $post_id, '_evt_' . $key, true );
	return ( '' === $value || null === $value ) ? $default : $value;
}

/**
 * Indica si un evento está marcado como deshabilitado.
 *
 * @param int $post_id ID del evento.
 * @return bool
 */
function evt_is_deshabilitado( $post_id ) {
	return '1' === (string) get_post_meta( $post_id, '_evt_deshabilitado', true );
}

/**
 * Nombres de meses en español.
 *
 * @return array
 */
function evt_meses() {
	return array(
		1 => 'Enero',
		2 => 'Febrero',
		3 => 'Marzo',
		4 => 'Abril',
		5 => 'Mayo',
		6 => 'Junio',
		7 => 'Julio',
		8 => 'Agosto',
		9 => 'Septiembre',
		10 => 'Octubre',
		11 => 'Noviembre',
		12 => 'Diciembre',
	);
}

/**
 * Abreviaturas de meses en español.
 *
 * @return array
 */
function evt_meses_cortos() {
	return array(
		1 => 'Ene',
		2 => 'Feb',
		3 => 'Mar',
		4 => 'Abr',
		5 => 'May',
		6 => 'Jun',
		7 => 'Jul',
		8 => 'Ago',
		9 => 'Sep',
		10 => 'Oct',
		11 => 'Nov',
		12 => 'Dic',
	);
}

/**
 * Nombres de los días de la semana (0 = Domingo).
 *
 * @return array
 */
function evt_dias() {
	return array( 'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado' );
}

/**
 * Devuelve la fecha del evento legible en español.
 *
 * @param int  $post_id  ID del evento.
 * @param bool $con_hora Incluir la hora si existe.
 * @return string
 */
function evt_fecha_legible( $post_id, $con_hora = true ) {
	$fecha = evt_meta( $post_id, 'fecha' );
	if ( ! $fecha ) {
		return '';
	}
	$ts    = strtotime( $fecha );
	$dias  = evt_dias();
	$meses = evt_meses();
	$texto = $dias[ (int) gmdate( 'w', $ts ) ] . ' ' . (int) gmdate( 'j', $ts )
		. ' de ' . $meses[ (int) gmdate( 'n', $ts ) ] . ', ' . gmdate( 'Y', $ts );

	$hora = evt_meta( $post_id, 'hora' );
	if ( $con_hora && $hora ) {
		$texto .= ' · ' . esc_html( $hora ) . ' h';
	}
	return $texto;
}

/**
 * Devuelve una descripción corta del evento.
 *
 * @param int $post_id  ID del evento.
 * @param int $palabras Número máximo de palabras.
 * @return string
 */
function evt_descripcion( $post_id, $palabras = 30 ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}
	$texto = $post->post_excerpt ? $post->post_excerpt : $post->post_content;
	$texto = wp_strip_all_tags( strip_shortcodes( $texto ) );
	return wp_trim_words( $texto, $palabras, '…' );
}

/**
 * Devuelve el número de WhatsApp aplicable a un evento (override o global).
 *
 * @param int $post_id ID del evento (0 para usar solo el global).
 * @return string Solo dígitos.
 */
function evt_get_whatsapp_number( $post_id = 0 ) {
	$numero = $post_id ? evt_meta( $post_id, 'whatsapp' ) : '';
	if ( ! $numero ) {
		$numero = get_option( 'evt_whatsapp_default', '' );
	}
	return preg_replace( '/[^0-9]/', '', (string) $numero );
}

/**
 * Construye la URL de WhatsApp con un mensaje prellenado para el evento.
 *
 * @param int $post_id ID del evento.
 * @return string URL o cadena vacía si no hay número.
 */
function evt_whatsapp_url( $post_id ) {
	$numero = evt_get_whatsapp_number( $post_id );
	if ( ! $numero ) {
		return '';
	}
	$mensaje = sprintf(
		"Hola, vengo de la web de Astronumerología y quisiera información sobre el evento:\n\n*%s*\n%s\n\n¿Me podrían contar cómo reservar mi lugar? Gracias.",
		get_the_title( $post_id ),
		evt_fecha_legible( $post_id )
	);
	return 'https://wa.me/' . $numero . '?text=' . rawurlencode( $mensaje );
}

/**
 * Consulta todos los eventos publicados ordenados por fecha ascendente.
 *
 * @return WP_Post[]
 */
function evt_query_eventos() {
	$query = new WP_Query(
		array(
			'post_type'      => EVT_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_key'       => '_evt_fecha',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);
	return $query->posts;
}

/**
 * Agrupa los eventos en: este mes, próximos y anteriores (mes anterior).
 *
 * @return array{mes:WP_Post[],proximos:WP_Post[],anteriores:WP_Post[]}
 */
function evt_get_eventos_agrupados() {
	$hoy          = current_time( 'Y-m-d' );
	$mes_inicio   = current_time( 'Y-m-01' );
	$mes_fin      = gmdate( 'Y-m-t', strtotime( $mes_inicio ) );
	$mes_ant_ini  = gmdate( 'Y-m-01', strtotime( $mes_inicio . ' -1 month' ) );

	$grupos = array(
		'mes'        => array(),
		'proximos'   => array(),
		'anteriores' => array(),
	);

	foreach ( evt_query_eventos() as $post ) {
		if ( evt_is_deshabilitado( $post->ID ) ) {
			continue;
		}
		$fecha = evt_meta( $post->ID, 'fecha' );
		if ( ! $fecha ) {
			continue;
		}
		if ( $fecha >= $hoy && $fecha <= $mes_fin ) {
			$grupos['mes'][] = $post;
		} elseif ( $fecha > $mes_fin ) {
			$grupos['proximos'][] = $post;
		} elseif ( $fecha < $hoy && $fecha >= $mes_ant_ini ) {
			$grupos['anteriores'][] = $post;
		}
	}

	// Los eventos anteriores se muestran del más reciente al más antiguo.
	$grupos['anteriores'] = array_reverse( $grupos['anteriores'] );

	return $grupos;
}

/**
 * Devuelve el marcado SVG de un icono del set del plugin.
 *
 * @param string $nombre Nombre del icono.
 * @return string
 */
function evt_icon( $nombre ) {
	$iconos = array(
		'calendar' => '<path d="M7 3v4M17 3v4M4 9h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1z"/>',
		'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'globe'    => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c3 3 3 15 0 18c-3-3-3-15 0-18"/>',
		'pin'      => '<path d="M12 21s-7-7-7-12a7 7 0 0 1 14 0c0 5-7 12-7 12z"/><circle cx="12" cy="9" r="2.5"/>',
		'ticket'   => '<path d="M4 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2 2 2 0 0 0 0 4 2 2 0 0 1-2 2H6a2 2 0 0 1-2-2 2 2 0 0 0 0-4z"/><path d="M14 6v2M14 16v2M14 11v2"/>',
		'users'    => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 19c0-3 2.5-5 5.5-5s5.5 2 5.5 5"/><path d="M16 6.2a3 3 0 0 1 0 5.6M17 14.5c2.5.4 4 2.3 4 4.5"/>',
	);

	if ( 'whatsapp' === $nombre ) {
		return '<svg class="evt-ic evt-ic--solid" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M17.5 14.4c-.3-.15-1.78-.88-2.05-.98-.28-.1-.48-.15-.68.15-.2.3-.78.98-.96 1.18-.18.2-.36.23-.66.08-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.46-.52.15-.18.2-.3.3-.5.1-.2.05-.38-.03-.53-.08-.15-.68-1.64-.93-2.24-.24-.59-.49-.51-.68-.52H7.8c-.2 0-.5.07-.77.38-.27.3-1.02 1-1.02 2.43s1.04 2.83 1.19 3.02c.15.2 2.05 3.14 4.97 4.4.7.3 1.24.48 1.66.62.7.22 1.33.19 1.83.12.56-.08 1.78-.73 2.03-1.43.25-.7.25-1.3.18-1.43-.08-.13-.28-.2-.58-.35zM12 2C6.48 2 2 6.48 2 12c0 1.74.46 3.4 1.34 4.88L2 22l5.27-1.32A9.94 9.94 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg>';
	}

	$contenido = isset( $iconos[ $nombre ] ) ? $iconos[ $nombre ] : '';
	return '<svg class="evt-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $contenido . '</svg>';
}
