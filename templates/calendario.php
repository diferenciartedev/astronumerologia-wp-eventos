<?php
/**
 * Plantilla del calendario de eventos tipo lista.
 *
 * @var array $grupos          Eventos agrupados (mes, proximos, anteriores).
 * @var bool  $mostrar_filtros Si se muestra la barra de filtros.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evt_secciones = array(
	'mes'        => array(
		'eyebrow' => 'Calendario',
		'titulo'  => 'Eventos de este mes',
		'sub'     => 'Lo que está por venir este mes. Reserva tu lugar a tiempo.',
	),
	'proximos'   => array(
		'eyebrow' => 'Agenda',
		'titulo'  => 'Próximos eventos',
		'sub'     => 'Anticípate y aparta tu lugar en los próximos encuentros.',
	),
	'anteriores' => array(
		'eyebrow' => 'Archivo',
		'titulo'  => 'Eventos anteriores',
		'sub'     => 'Encuentros recientes que ya se realizaron.',
	),
);

$evt_hay_algo = ! empty( $grupos['mes'] ) || ! empty( $grupos['proximos'] ) || ! empty( $grupos['anteriores'] );
?>
<div class="evt-app">

	<?php if ( $mostrar_filtros && ( ! empty( $grupos['mes'] ) || ! empty( $grupos['proximos'] ) ) ) : ?>
		<div class="evt-filterbar" data-evt-filter>
			<button type="button" class="evt-pill is-active" data-f="todos">Todos</button>
			<button type="button" class="evt-pill" data-f="presencial">Presencial</button>
			<button type="button" class="evt-pill" data-f="virtual">Virtual</button>
		</div>
	<?php endif; ?>

	<?php
	foreach ( $evt_secciones as $clave => $info ) :
		$items = $grupos[ $clave ];
		if ( empty( $items ) ) {
			continue;
		}
		$es_pasado = ( 'anteriores' === $clave );
		?>
		<section class="evt-section evt-section--<?php echo esc_attr( $clave ); ?>">
			<header class="evt-section__head">
				<span class="evt-eyebrow"><?php echo esc_html( $info['eyebrow'] ); ?></span>
				<h2 class="evt-section__title"><?php echo esc_html( $info['titulo'] ); ?></h2>
				<p class="evt-section__sub"><?php echo esc_html( $info['sub'] ); ?></p>
			</header>
			<div class="evt-list">
				<?php
				foreach ( $items as $post ) {
					$estado = $es_pasado ? 'past' : 'upcoming';
					include EVT_DIR . 'templates/card.php';
				}
				?>
			</div>
		</section>
	<?php endforeach; ?>

	<?php if ( ! $evt_hay_algo ) : ?>
		<p class="evt-empty">Pronto anunciaremos nuevos eventos. Vuelve a visitarnos.</p>
	<?php endif; ?>

</div>
