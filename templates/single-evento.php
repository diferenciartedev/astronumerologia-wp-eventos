<?php
/**
 * Plantilla del detalle de un evento (CPT single).
 * Reutiliza los componentes del design system compartido.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		$evt_id        = get_the_ID();
		$evt_fecha     = evt_meta( $evt_id, 'fecha' );
		$evt_hora      = evt_meta( $evt_id, 'hora' );
		$evt_ts        = $evt_fecha ? strtotime( $evt_fecha ) : 0;
		$evt_meses     = evt_meses_cortos();
		$evt_modalidad = evt_meta( $evt_id, 'modalidad', 'presencial' );
		$evt_costo     = evt_meta( $evt_id, 'costo', 'gratuito' );
		$evt_precio    = evt_meta( $evt_id, 'precio' );
		$evt_cupo      = evt_meta( $evt_id, 'cupo', 'disponible' );
		$evt_aforo     = evt_meta( $evt_id, 'aforo' );
		$evt_direccion = evt_meta( $evt_id, 'direccion' );
		$evt_enlace    = evt_meta( $evt_id, 'enlace' );
		$evt_wa        = evt_whatsapp_url( $evt_id );
		$evt_es_virtual = ( 'virtual' === $evt_modalidad );
		$evt_agotado    = ( 'agotado' === $evt_cupo );
		$evt_es_pasado  = ( $evt_fecha && $evt_fecha < current_time( 'Y-m-d' ) );

		wp_enqueue_style( Astro_Components::STYLE );
		wp_enqueue_style( 'evt-frontend' );
		?>
		<main class="astro-scope evt-single">

			<section class="evt-single__hero">
				<div class="evt-single__hero-bg" aria-hidden="true"></div>
				<div class="evt-single__hero-inner">
					<a class="evt-single__back" href="<?php echo esc_url( get_post_type_archive_link( EVT_CPT ) ); ?>">
						<?php echo Astro_Components::icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span>Ver todos los eventos</span>
					</a>
					<span class="astro-eyebrow evt-single__eyebrow">
						Evento · <?php echo $evt_es_virtual ? 'Virtual' : 'Presencial'; ?>
					</span>
					<h1 class="evt-single__title"><?php the_title(); ?></h1>
					<?php if ( $evt_fecha ) : ?>
						<p class="evt-single__date-line">
							<?php echo Astro_Components::icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo esc_html( evt_fecha_legible( $evt_id ) ); ?></span>
						</p>
					<?php endif; ?>
				</div>
				<?php if ( $evt_ts ) : ?>
					<div class="evt-single__numeral" aria-hidden="true">
						<span class="evt-single__numeral-day"><?php echo esc_html( gmdate( 'j', $evt_ts ) ); ?></span>
						<span class="evt-single__numeral-month"><?php echo esc_html( $evt_meses[ (int) gmdate( 'n', $evt_ts ) ] ); ?></span>
					</div>
				<?php endif; ?>
			</section>

			<section class="evt-single__body">
				<div class="evt-single__grid">

					<div class="evt-single__content">
						<div class="evt-single__desc">
							<?php
							if ( get_the_content() ) {
								the_content();
							} else {
								echo '<p><em>Este evento aún no tiene descripción ampliada.</em></p>';
							}
							?>
						</div>
					</div>

					<aside class="evt-single__ficha astro-card">
						<span class="astro-eyebrow">Ficha del evento</span>

						<ul class="evt-single__ficha-list">
							<?php if ( $evt_fecha ) : ?>
								<li>
									<span class="evt-single__ficha-key">Fecha</span>
									<span class="evt-single__ficha-val"><?php echo esc_html( evt_fecha_legible( $evt_id, false ) ); ?></span>
								</li>
							<?php endif; ?>
							<?php if ( $evt_hora ) : ?>
								<li>
									<span class="evt-single__ficha-key">Hora</span>
									<span class="evt-single__ficha-val"><?php echo esc_html( $evt_hora ); ?> h</span>
								</li>
							<?php endif; ?>
							<li>
								<span class="evt-single__ficha-key">Modalidad</span>
								<span class="evt-single__ficha-val"><?php echo $evt_es_virtual ? 'Virtual' : 'Presencial'; ?></span>
							</li>
							<li>
								<span class="evt-single__ficha-key">Costo</span>
								<span class="evt-single__ficha-val">
									<?php
									if ( 'gratuito' === $evt_costo ) {
										echo 'Gratuito';
									} else {
										echo esc_html( $evt_precio ? $evt_precio : 'De pago' );
									}
									?>
								</span>
							</li>
							<?php if ( $evt_aforo ) : ?>
								<li>
									<span class="evt-single__ficha-key">Aforo</span>
									<span class="evt-single__ficha-val"><?php echo (int) $evt_aforo; ?> lugares</span>
								</li>
							<?php endif; ?>
							<?php if ( ! $evt_es_virtual && $evt_direccion ) : ?>
								<li>
									<span class="evt-single__ficha-key">Dirección</span>
									<span class="evt-single__ficha-val"><?php echo esc_html( $evt_direccion ); ?></span>
								</li>
							<?php endif; ?>
							<?php if ( $evt_es_virtual && $evt_enlace && ! $evt_es_pasado ) : ?>
								<li>
									<span class="evt-single__ficha-key">Enlace</span>
									<span class="evt-single__ficha-val">
										<a href="<?php echo esc_url( $evt_enlace ); ?>" target="_blank" rel="noopener nofollow">Abrir sesión online</a>
									</span>
								</li>
							<?php endif; ?>
						</ul>

						<div class="evt-single__ficha-cta">
							<?php
							if ( $evt_es_pasado ) {
								echo Astro_Components::badge( 'Evento finalizado', 'neutral' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
					</aside>

				</div>
			</section>

		</main>
		<?php
	endwhile;
endif;

get_footer();
