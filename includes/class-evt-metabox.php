<?php
/**
 * Metabox con los detalles del evento.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EVT_Metabox {

	const NONCE = 'evt_metabox_nonce';

	/**
	 * Engancha el metabox y su guardado.
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add' ) );
		add_action( 'save_post_' . EVT_CPT, array( __CLASS__, 'save' ), 10, 2 );
	}

	/**
	 * Registra el metabox en la pantalla de edición del evento.
	 */
	public static function add() {
		add_meta_box(
			'evt_detalles',
			'Detalles del evento',
			array( __CLASS__, 'render' ),
			EVT_CPT,
			'normal',
			'high'
		);
	}

	/**
	 * Pinta los campos del metabox.
	 *
	 * @param WP_Post $post Evento en edición.
	 */
	public static function render( $post ) {
		wp_nonce_field( 'evt_save_meta', self::NONCE );

		$fecha     = evt_meta( $post->ID, 'fecha' );
		$hora      = evt_meta( $post->ID, 'hora' );
		$modalidad = evt_meta( $post->ID, 'modalidad', 'presencial' );
		$enlace    = evt_meta( $post->ID, 'enlace' );
		$direccion = evt_meta( $post->ID, 'direccion' );
		$costo     = evt_meta( $post->ID, 'costo', 'gratuito' );
		$precio    = evt_meta( $post->ID, 'precio' );
		$aforo     = evt_meta( $post->ID, 'aforo' );
		$cupo      = evt_meta( $post->ID, 'cupo', 'disponible' );
		$whatsapp  = evt_meta( $post->ID, 'whatsapp' );
		$deshab    = evt_is_deshabilitado( $post->ID );
		$wa_global = get_option( 'evt_whatsapp_default', '' );
		?>
		<div class="evt-mb">
			<p class="evt-mb__desc">La descripción larga del evento se escribe en el editor de contenido de arriba. Aquí defines la ficha técnica.</p>

			<div class="evt-mb__grid">
				<label class="evt-mb__field">
					<span class="evt-mb__label">Fecha del evento</span>
					<input type="date" name="evt_fecha" value="<?php echo esc_attr( $fecha ); ?>" />
				</label>

				<label class="evt-mb__field">
					<span class="evt-mb__label">Hora</span>
					<input type="time" name="evt_hora" value="<?php echo esc_attr( $hora ); ?>" />
				</label>

				<label class="evt-mb__field">
					<span class="evt-mb__label">Modalidad</span>
					<select name="evt_modalidad">
						<option value="presencial" <?php selected( $modalidad, 'presencial' ); ?>>Presencial</option>
						<option value="virtual" <?php selected( $modalidad, 'virtual' ); ?>>Virtual</option>
					</select>
				</label>

				<label class="evt-mb__field">
					<span class="evt-mb__label">Estado del cupo</span>
					<select name="evt_cupo">
						<option value="disponible" <?php selected( $cupo, 'disponible' ); ?>>Lugares disponibles</option>
						<option value="agotado" <?php selected( $cupo, 'agotado' ); ?>>Agotado / Sold out</option>
					</select>
				</label>

				<label class="evt-mb__field">
					<span class="evt-mb__label">Aforo (número de lugares)</span>
					<input type="number" min="0" step="1" name="evt_aforo" value="<?php echo esc_attr( $aforo ); ?>" placeholder="Ej. 30" />
				</label>

				<label class="evt-mb__field">
					<span class="evt-mb__label">Costo</span>
					<select name="evt_costo">
						<option value="gratuito" <?php selected( $costo, 'gratuito' ); ?>>Gratuito</option>
						<option value="pago" <?php selected( $costo, 'pago' ); ?>>De pago</option>
					</select>
				</label>

				<label class="evt-mb__field">
					<span class="evt-mb__label">Precio (si es de pago)</span>
					<input type="text" name="evt_precio" value="<?php echo esc_attr( $precio ); ?>" placeholder="Ej. S/ 120 · $35" />
				</label>

				<label class="evt-mb__field">
					<span class="evt-mb__label">Dirección del lugar (si es presencial)</span>
					<input type="text" name="evt_direccion" value="<?php echo esc_attr( $direccion ); ?>" placeholder="Ej. Av. Siempre Viva 123, Lima" />
				</label>

				<label class="evt-mb__field">
					<span class="evt-mb__label">Enlace de la sesión (si es virtual)</span>
					<input type="url" name="evt_enlace" value="<?php echo esc_attr( $enlace ); ?>" placeholder="https://zoom.us/..." />
				</label>

				<label class="evt-mb__field">
					<span class="evt-mb__label">WhatsApp para este evento</span>
					<input type="text" name="evt_whatsapp" value="<?php echo esc_attr( $whatsapp ); ?>" placeholder="<?php echo esc_attr( $wa_global ? 'Global: ' . $wa_global : 'Ej. 51999888777' ); ?>" />
					<span class="evt-mb__hint">Opcional. Si se deja vacío se usa el número global de Ajustes.</span>
				</label>
			</div>

			<label class="evt-mb__check">
				<input type="checkbox" name="evt_deshabilitado" value="1" <?php checked( $deshab ); ?> />
				<span>Deshabilitar este evento (no se mostrará en la web aunque esté publicado).</span>
			</label>
		</div>
		<?php
	}

	/**
	 * Guarda los metadatos del evento.
	 *
	 * @param int     $post_id ID del evento.
	 * @param WP_Post $post    Objeto del evento.
	 */
	public static function save( $post_id, $post ) {
		if ( ! isset( $_POST[ self::NONCE ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE ] ) ), 'evt_save_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( evt_campos() as $campo => $tipo ) {
			$input = isset( $_POST[ 'evt_' . $campo ] ) ? wp_unslash( $_POST[ 'evt_' . $campo ] ) : '';

			switch ( $tipo ) {
				case 'bool':
					$valor = ( '1' === (string) $input ) ? '1' : '';
					break;
				case 'int':
					$valor = '' === $input ? '' : (string) absint( $input );
					break;
				case 'url':
					$valor = esc_url_raw( $input );
					break;
				case 'date':
					$valor = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $input ) ? $input : '';
					break;
				case 'time':
					$valor = preg_match( '/^\d{2}:\d{2}$/', $input ) ? $input : '';
					break;
				case 'choice':
					$valor = sanitize_key( $input );
					break;
				default:
					$valor = sanitize_text_field( $input );
			}

			if ( '' === $valor ) {
				delete_post_meta( $post_id, '_evt_' . $campo );
			} else {
				update_post_meta( $post_id, '_evt_' . $campo, $valor );
			}
		}
	}
}
