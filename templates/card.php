<?php
/**
 * Tarjeta de un evento dentro del calendario tipo lista.
 *
 * @var WP_Post $post   Evento a renderizar.
 * @var string  $estado «upcoming» o «past».
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evt_id    = $post->ID;
$evt_fecha = evt_meta( $evt_id, 'fecha' );
$evt_ts    = $evt_fecha ? strtotime( $evt_fecha ) : 0;
$evt_meses = evt_meses_cortos();

$evt_modalidad = evt_meta( $evt_id, 'modalidad', 'presencial' );
$evt_costo     = evt_meta( $evt_id, 'costo', 'gratuito' );
$evt_precio    = evt_meta( $evt_id, 'precio' );
$evt_cupo      = evt_meta( $evt_id, 'cupo', 'disponible' );
$evt_aforo     = evt_meta( $evt_id, 'aforo' );
$evt_direccion = evt_meta( $evt_id, 'direccion' );
$evt_enlace    = evt_meta( $evt_id, 'enlace' );
$evt_desc      = evt_descripcion( $evt_id );
$evt_wa        = evt_whatsapp_url( $evt_id );

$evt_es_pasado  = ( 'past' === $estado );
$evt_es_virtual = ( 'virtual' === $evt_modalidad );
$evt_agotado    = ( 'agotado' === $evt_cupo );

$evt_clases = 'evt-card evt-card--' . $estado;
if ( $evt_agotado && ! $evt_es_pasado ) {
	$evt_clases .= ' evt-card--agotado';
}
?>
<article class="<?php echo esc_attr( $evt_clases ); ?>" data-modalidad="<?php echo esc_attr( $evt_modalidad ); ?>">

	<div class="evt-card__date" aria-hidden="true">
		<span class="evt-card__day"><?php echo $evt_ts ? esc_html( gmdate( 'j', $evt_ts ) ) : '·'; ?></span>
		<span class="evt-card__month"><?php echo $evt_ts ? esc_html( $evt_meses[ (int) gmdate( 'n', $evt_ts ) ] ) : ''; ?></span>
		<span class="evt-card__year"><?php echo $evt_ts ? esc_html( gmdate( 'Y', $evt_ts ) ) : ''; ?></span>
	</div>

	<div class="evt-card__body">
		<div class="evt-card__eyebrow">
			<?php echo evt_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php echo esc_html( evt_fecha_legible( $evt_id ) ); ?></span>
		</div>

		<h3 class="evt-card__title"><?php echo esc_html( get_the_title( $evt_id ) ); ?></h3>

		<?php if ( $evt_desc ) : ?>
			<p class="evt-card__desc"><?php echo esc_html( $evt_desc ); ?></p>
		<?php endif; ?>

		<div class="evt-card__meta">
			<span class="evt-chip">
				<?php echo $evt_es_virtual ? evt_icon( 'globe' ) : evt_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo $evt_es_virtual ? 'Virtual' : 'Presencial'; ?>
			</span>

			<span class="evt-chip <?php echo ( 'gratuito' === $evt_costo ) ? 'evt-chip--free' : ''; ?>">
				<?php echo evt_icon( 'ticket' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo ( 'gratuito' === $evt_costo ) ? 'Gratuito' : esc_html( $evt_precio ? $evt_precio : 'De pago' ); ?>
			</span>

			<?php if ( $evt_aforo ) : ?>
				<span class="evt-chip">
					<?php echo evt_icon( 'users' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					Aforo <?php echo (int) $evt_aforo; ?>
				</span>
			<?php endif; ?>

			<?php if ( ! $evt_es_virtual && $evt_direccion ) : ?>
				<span class="evt-chip evt-chip--addr">
					<?php echo evt_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo esc_html( $evt_direccion ); ?>
				</span>
			<?php endif; ?>

			<?php if ( $evt_es_virtual && $evt_enlace && ! $evt_es_pasado ) : ?>
				<span class="evt-chip">
					<?php echo evt_icon( 'globe' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					Sesión online
				</span>
			<?php endif; ?>
		</div>
	</div>

	<div class="evt-card__aside">
		<?php if ( $evt_es_pasado ) : ?>
			<span class="evt-badge evt-badge--past">Finalizado</span>
		<?php else : ?>
			<span class="evt-badge <?php echo $evt_agotado ? 'evt-badge--soldout' : 'evt-badge--open'; ?>">
				<?php echo $evt_agotado ? 'Sold out' : 'Lugares disponibles'; ?>
			</span>
			<?php if ( $evt_wa ) : ?>
				<a class="evt-wa-btn" href="<?php echo esc_url( $evt_wa ); ?>" target="_blank" rel="noopener nofollow">
					<?php echo evt_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span>Pedir información</span>
				</a>
			<?php endif; ?>
		<?php endif; ?>
	</div>

</article>
