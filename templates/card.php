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

$evt_clases = 'evt-card astro-card evt-card--' . $estado;
if ( ! $evt_es_pasado ) {
	$evt_clases .= ' astro-card--hover';
}
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
			<?php echo Astro_Components::icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php echo esc_html( evt_fecha_legible( $evt_id ) ); ?></span>
		</div>

		<h3 class="evt-card__title"><?php echo esc_html( get_the_title( $evt_id ) ); ?></h3>

		<?php if ( $evt_desc ) : ?>
			<p class="evt-card__desc"><?php echo esc_html( $evt_desc ); ?></p>
		<?php endif; ?>

		<div class="evt-card__meta">
			<?php
			// Modalidad.
			echo Astro_Components::chip( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$evt_es_virtual ? 'Virtual' : 'Presencial',
				$evt_es_virtual ? 'globe' : 'pin'
			);

			// Costo.
			if ( 'gratuito' === $evt_costo ) {
				echo Astro_Components::chip( 'Gratuito', 'ticket', 'free' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo Astro_Components::chip( $evt_precio ? $evt_precio : 'De pago', 'ticket' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			// Aforo.
			if ( $evt_aforo ) {
				echo Astro_Components::chip( 'Aforo ' . (int) $evt_aforo, 'users' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			// Dirección (presencial) o sesión online (virtual).
			if ( ! $evt_es_virtual && $evt_direccion ) {
				echo Astro_Components::chip( $evt_direccion, 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} elseif ( $evt_es_virtual && $evt_enlace && ! $evt_es_pasado ) {
				echo Astro_Components::chip( 'Sesión online', 'globe' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
	</div>

	<div class="evt-card__aside">
		<?php
		if ( $evt_es_pasado ) {
			echo Astro_Components::badge( 'Finalizado', 'neutral' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			echo Astro_Components::badge( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$evt_agotado ? 'Sold out' : 'Lugares disponibles',
				$evt_agotado ? 'soldout' : 'open'
			);
			if ( $evt_wa ) {
				echo Astro_Components::button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'text'    => 'Pedir información',
						'href'    => $evt_wa,
						'variant' => 'whatsapp',
						'icon'    => 'whatsapp',
						'target'  => '_blank',
						'rel'     => 'noopener nofollow',
					)
				);
			}
		}
		?>
	</div>

</article>
