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
