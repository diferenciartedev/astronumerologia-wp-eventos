<?php
/**
 * Personalización del listado de eventos en el panel de administración:
 * columnas, filtros, ordenación, duplicar, deshabilitar y acciones en lote.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EVT_Admin {

	/**
	 * Engancha todas las personalizaciones del administrador.
	 */
	public static function init() {
		add_filter( 'manage_' . EVT_CPT . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . EVT_CPT . '_posts_custom_column', array( __CLASS__, 'column_content' ), 10, 2 );
		add_filter( 'manage_edit-' . EVT_CPT . '_sortable_columns', array( __CLASS__, 'sortable' ) );

		add_action( 'restrict_manage_posts', array( __CLASS__, 'filtros' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'aplicar_filtros' ) );

		add_filter( 'post_row_actions', array( __CLASS__, 'row_actions' ), 10, 2 );
		add_action( 'admin_action_evt_duplicate', array( __CLASS__, 'handle_duplicate' ) );
		add_action( 'admin_action_evt_toggle', array( __CLASS__, 'handle_toggle' ) );

		add_filter( 'bulk_actions-edit-' . EVT_CPT, array( __CLASS__, 'bulk_actions' ) );
		add_filter( 'handle_bulk_actions-edit-' . EVT_CPT, array( __CLASS__, 'handle_bulk' ), 10, 3 );

		add_action( 'admin_notices', array( __CLASS__, 'avisos' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
	}

	/**
	 * Encola los estilos del metabox en la pantalla del evento.
	 *
	 * @param string $hook Pantalla actual.
	 */
	public static function assets( $hook ) {
		$screen = get_current_screen();
		if ( $screen && EVT_CPT === $screen->post_type ) {
			wp_enqueue_style( 'evt-admin', EVT_URL . 'assets/css/eventos-admin.css', array(), EVT_VERSION );
		}
	}

	/* ----------------------------------------------------------------------
	 * Columnas
	 * ------------------------------------------------------------------- */

	/**
	 * Define las columnas del listado de eventos.
	 *
	 * @param array $cols Columnas originales.
	 * @return array
	 */
	public static function columns( $cols ) {
		$nuevas = array();
		foreach ( $cols as $key => $label ) {
			$nuevas[ $key ] = $label;
			if ( 'title' === $key ) {
				$nuevas['evt_fecha']     = 'Fecha';
				$nuevas['evt_modalidad'] = 'Modalidad';
				$nuevas['evt_costo']     = 'Costo';
				$nuevas['evt_cupo']      = 'Cupo';
				$nuevas['evt_estado']    = 'Estado';
			}
		}
		return $nuevas;
	}

	/**
	 * Pinta el contenido de cada columna personalizada.
	 *
	 * @param string $col     Identificador de columna.
	 * @param int    $post_id ID del evento.
	 */
	public static function column_content( $col, $post_id ) {
		switch ( $col ) {
			case 'evt_fecha':
				$fecha = evt_meta( $post_id, 'fecha' );
				if ( $fecha ) {
					$ts    = strtotime( $fecha );
					$meses = evt_meses_cortos();
					echo '<strong>' . (int) gmdate( 'j', $ts ) . ' ' . esc_html( $meses[ (int) gmdate( 'n', $ts ) ] ) . ' ' . esc_html( gmdate( 'Y', $ts ) ) . '</strong>';
					$hora = evt_meta( $post_id, 'hora' );
					if ( $hora ) {
						echo '<br><span class="evt-col-muted">' . esc_html( $hora ) . ' h</span>';
					}
				} else {
					echo '<span class="evt-col-muted">—</span>';
				}
				break;

			case 'evt_modalidad':
				$mod = evt_meta( $post_id, 'modalidad', 'presencial' );
				echo 'virtual' === $mod ? 'Virtual' : 'Presencial';
				break;

			case 'evt_costo':
				$costo = evt_meta( $post_id, 'costo', 'gratuito' );
				if ( 'gratuito' === $costo ) {
					echo '<span class="evt-tag evt-tag--green">Gratuito</span>';
				} else {
					$precio = evt_meta( $post_id, 'precio' );
					echo esc_html( $precio ? $precio : 'De pago' );
				}
				break;

			case 'evt_cupo':
				$cupo = evt_meta( $post_id, 'cupo', 'disponible' );
				if ( 'agotado' === $cupo ) {
					echo '<span class="evt-tag evt-tag--red">Sold out</span>';
				} else {
					echo '<span class="evt-tag evt-tag--green">Disponible</span>';
				}
				break;

			case 'evt_estado':
				if ( evt_is_deshabilitado( $post_id ) ) {
					echo '<span class="evt-tag evt-tag--gray">Deshabilitado</span>';
				} else {
					echo '<span class="evt-col-muted">Activo</span>';
				}
				break;
		}
	}

	/**
	 * Marca la columna de fecha como ordenable.
	 *
	 * @param array $cols Columnas ordenables.
	 * @return array
	 */
	public static function sortable( $cols ) {
		$cols['evt_fecha'] = 'evt_fecha';
		return $cols;
	}

	/* ----------------------------------------------------------------------
	 * Filtros
	 * ------------------------------------------------------------------- */

	/**
	 * Añade los selectores de filtro encima del listado.
	 *
	 * @param string $post_type Tipo de contenido actual.
	 */
	public static function filtros( $post_type ) {
		if ( EVT_CPT !== $post_type ) {
			return;
		}

		$modalidad = isset( $_GET['evt_f_modalidad'] ) ? sanitize_key( wp_unslash( $_GET['evt_f_modalidad'] ) ) : '';
		$cupo      = isset( $_GET['evt_f_cupo'] ) ? sanitize_key( wp_unslash( $_GET['evt_f_cupo'] ) ) : '';
		$estado    = isset( $_GET['evt_f_estado'] ) ? sanitize_key( wp_unslash( $_GET['evt_f_estado'] ) ) : '';
		?>
		<select name="evt_f_modalidad">
			<option value="">Toda modalidad</option>
			<option value="presencial" <?php selected( $modalidad, 'presencial' ); ?>>Presencial</option>
			<option value="virtual" <?php selected( $modalidad, 'virtual' ); ?>>Virtual</option>
		</select>
		<select name="evt_f_cupo">
			<option value="">Todo cupo</option>
			<option value="disponible" <?php selected( $cupo, 'disponible' ); ?>>Disponible</option>
			<option value="agotado" <?php selected( $cupo, 'agotado' ); ?>>Sold out</option>
		</select>
		<select name="evt_f_estado">
			<option value="">Activos y deshabilitados</option>
			<option value="activo" <?php selected( $estado, 'activo' ); ?>>Solo activos</option>
			<option value="deshabilitado" <?php selected( $estado, 'deshabilitado' ); ?>>Solo deshabilitados</option>
		</select>
		<?php
	}

	/**
	 * Aplica los filtros y la ordenación a la consulta del listado.
	 *
	 * @param WP_Query $query Consulta principal.
	 */
	public static function aplicar_filtros( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( EVT_CPT !== $query->get( 'post_type' ) ) {
			return;
		}

		$meta_query = array();

		if ( ! empty( $_GET['evt_f_modalidad'] ) ) {
			$meta_query[] = array(
				'key'   => '_evt_modalidad',
				'value' => sanitize_key( wp_unslash( $_GET['evt_f_modalidad'] ) ),
			);
		}
		if ( ! empty( $_GET['evt_f_cupo'] ) ) {
			$meta_query[] = array(
				'key'   => '_evt_cupo',
				'value' => sanitize_key( wp_unslash( $_GET['evt_f_cupo'] ) ),
			);
		}
		if ( ! empty( $_GET['evt_f_estado'] ) ) {
			$estado = sanitize_key( wp_unslash( $_GET['evt_f_estado'] ) );
			if ( 'deshabilitado' === $estado ) {
				$meta_query[] = array(
					'key'   => '_evt_deshabilitado',
					'value' => '1',
				);
			} elseif ( 'activo' === $estado ) {
				$meta_query[] = array(
					'key'     => '_evt_deshabilitado',
					'compare' => 'NOT EXISTS',
				);
			}
		}

		if ( $meta_query ) {
			$query->set( 'meta_query', $meta_query );
		}

		// Ordenación por fecha al hacer clic en la columna «Fecha».
		if ( 'evt_fecha' === $query->get( 'orderby' ) ) {
			$query->set( 'meta_key', '_evt_fecha' );
			$query->set( 'orderby', 'meta_value' );
		}
	}

	/* ----------------------------------------------------------------------
	 * Acciones de fila: duplicar y habilitar/deshabilitar
	 * ------------------------------------------------------------------- */

	/**
	 * Añade los enlaces «Duplicar» y «Deshabilitar/Habilitar» a cada fila.
	 *
	 * @param array   $actions Acciones existentes.
	 * @param WP_Post $post    Evento de la fila.
	 * @return array
	 */
	public static function row_actions( $actions, $post ) {
		if ( EVT_CPT !== $post->post_type || ! current_user_can( 'edit_posts' ) ) {
			return $actions;
		}

		$dup_url = wp_nonce_url(
			admin_url( 'admin.php?action=evt_duplicate&post=' . $post->ID ),
			'evt_dup_' . $post->ID
		);
		$actions['evt_duplicate'] = '<a href="' . esc_url( $dup_url ) . '">Duplicar</a>';

		$deshab     = evt_is_deshabilitado( $post->ID );
		$toggle_url = wp_nonce_url(
			admin_url( 'admin.php?action=evt_toggle&post=' . $post->ID ),
			'evt_tog_' . $post->ID
		);
		$actions['evt_toggle'] = '<a href="' . esc_url( $toggle_url ) . '">' . ( $deshab ? 'Habilitar' : 'Deshabilitar' ) . '</a>';

		return $actions;
	}

	/**
	 * Procesa la acción «Duplicar» de una fila.
	 */
	public static function handle_duplicate() {
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		check_admin_referer( 'evt_dup_' . $post_id );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( 'No tienes permiso para duplicar eventos.' );
		}

		$nuevo = self::duplicar( $post_id );
		wp_safe_redirect( admin_url( 'edit.php?post_type=' . EVT_CPT . '&evt_msg=' . ( $nuevo ? 'dup' : 'error' ) ) );
		exit;
	}

	/**
	 * Procesa la acción «Habilitar/Deshabilitar» de una fila.
	 */
	public static function handle_toggle() {
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		check_admin_referer( 'evt_tog_' . $post_id );

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( 'No tienes permiso para modificar este evento.' );
		}

		if ( evt_is_deshabilitado( $post_id ) ) {
			delete_post_meta( $post_id, '_evt_deshabilitado' );
			$msg = 'enabled';
		} else {
			update_post_meta( $post_id, '_evt_deshabilitado', '1' );
			$msg = 'disabled';
		}
		wp_safe_redirect( admin_url( 'edit.php?post_type=' . EVT_CPT . '&evt_msg=' . $msg ) );
		exit;
	}

	/* ----------------------------------------------------------------------
	 * Acciones en lote
	 * ------------------------------------------------------------------- */

	/**
	 * Registra las acciones en lote propias del plugin.
	 *
	 * @param array $actions Acciones existentes.
	 * @return array
	 */
	public static function bulk_actions( $actions ) {
		$actions['evt_duplicate'] = 'Duplicar';
		$actions['evt_disable']   = 'Deshabilitar';
		$actions['evt_enable']    = 'Habilitar';
		return $actions;
	}

	/**
	 * Ejecuta las acciones en lote sobre los eventos seleccionados.
	 *
	 * @param string $redirect URL de redirección.
	 * @param string $action   Acción seleccionada.
	 * @param array  $ids      IDs de eventos.
	 * @return string
	 */
	public static function handle_bulk( $redirect, $action, $ids ) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return $redirect;
		}
		$ids = array_map( 'absint', (array) $ids );

		if ( 'evt_duplicate' === $action ) {
			foreach ( $ids as $id ) {
				self::duplicar( $id );
			}
			$redirect = add_query_arg( 'evt_msg', 'dup_bulk', $redirect );
		} elseif ( 'evt_disable' === $action ) {
			foreach ( $ids as $id ) {
				update_post_meta( $id, '_evt_deshabilitado', '1' );
			}
			$redirect = add_query_arg( 'evt_msg', 'disabled', $redirect );
		} elseif ( 'evt_enable' === $action ) {
			foreach ( $ids as $id ) {
				delete_post_meta( $id, '_evt_deshabilitado' );
			}
			$redirect = add_query_arg( 'evt_msg', 'enabled', $redirect );
		}
		return $redirect;
	}

	/* ----------------------------------------------------------------------
	 * Utilidades
	 * ------------------------------------------------------------------- */

	/**
	 * Duplica un evento como borrador, copiando contenido y metadatos.
	 *
	 * @param int $post_id ID del evento original.
	 * @return int ID del nuevo evento o 0 si falla.
	 */
	public static function duplicar( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post || EVT_CPT !== $post->post_type ) {
			return 0;
		}

		$nuevo_id = wp_insert_post(
			array(
				'post_title'   => $post->post_title . ' (copia)',
				'post_content' => $post->post_content,
				'post_excerpt' => $post->post_excerpt,
				'post_status'  => 'draft',
				'post_type'    => EVT_CPT,
			)
		);

		if ( ! $nuevo_id || is_wp_error( $nuevo_id ) ) {
			return 0;
		}

		$omitir = array( '_edit_lock', '_edit_last' );
		foreach ( get_post_meta( $post_id ) as $clave => $valores ) {
			if ( in_array( $clave, $omitir, true ) ) {
				continue;
			}
			foreach ( $valores as $valor ) {
				add_post_meta( $nuevo_id, $clave, maybe_unserialize( $valor ) );
			}
		}

		return $nuevo_id;
	}

	/**
	 * Muestra los avisos tras ejecutar acciones del plugin.
	 */
	public static function avisos() {
		$screen = get_current_screen();
		if ( ! $screen || 'edit-' . EVT_CPT !== $screen->id || empty( $_GET['evt_msg'] ) ) {
			return;
		}

		$mensajes = array(
			'dup'      => 'Evento duplicado como borrador.',
			'dup_bulk' => 'Eventos duplicados como borradores.',
			'disabled' => 'Evento(s) deshabilitado(s).',
			'enabled'  => 'Evento(s) habilitado(s).',
			'error'    => 'No se pudo completar la acción.',
		);

		$clave = sanitize_key( wp_unslash( $_GET['evt_msg'] ) );
		if ( isset( $mensajes[ $clave ] ) ) {
			$tipo = 'error' === $clave ? 'error' : 'success';
			echo '<div class="notice notice-' . esc_attr( $tipo ) . ' is-dismissible"><p>' . esc_html( $mensajes[ $clave ] ) . '</p></div>';
		}
	}
}
