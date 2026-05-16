<?php
/**
 * Página de ajustes del plugin Eventos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EVT_Settings {

	/**
	 * Engancha la página de ajustes.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
	}

	/**
	 * Añade el submenú «Ajustes» bajo Eventos.
	 */
	public static function menu() {
		add_submenu_page(
			'edit.php?post_type=' . EVT_CPT,
			'Ajustes de Eventos',
			'Ajustes',
			'manage_options',
			'evt-ajustes',
			array( __CLASS__, 'render' )
		);
	}

	/**
	 * Registra las opciones del plugin.
	 */
	public static function register() {
		register_setting(
			'evt_settings',
			'evt_whatsapp_default',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( __CLASS__, 'sanitize_phone' ),
				'default'           => '',
			)
		);
	}

	/**
	 * Sanea un número de teléfono dejando solo dígitos.
	 *
	 * @param string $valor Valor introducido.
	 * @return string
	 */
	public static function sanitize_phone( $valor ) {
		return preg_replace( '/[^0-9]/', '', (string) $valor );
	}

	/**
	 * Pinta la página de ajustes.
	 */
	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$whatsapp = get_option( 'evt_whatsapp_default', '' );
		?>
		<div class="wrap">
			<h1>Ajustes de Eventos</h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'evt_settings' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="evt_whatsapp_default">Número de WhatsApp global</label></th>
						<td>
							<input type="text" id="evt_whatsapp_default" name="evt_whatsapp_default"
								value="<?php echo esc_attr( $whatsapp ); ?>" class="regular-text"
								placeholder="51999888777" />
							<p class="description">
								Número con código de país, solo dígitos (sin «+», espacios ni guiones).
								Se usa en el botón de WhatsApp de cada evento, salvo que el evento tenga su propio número.
							</p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>

			<hr />
			<h2>Cómo mostrar el calendario</h2>
			<p>Inserta el calendario de eventos en cualquier página con el shortcode:</p>
			<p><code>[eventos_calendario]</code></p>
			<p>Parámetros opcionales (valores <code>si</code> / <code>no</code>):</p>
			<ul style="list-style:disc;margin-left:20px;">
				<li><code>[eventos_calendario mes="si" proximos="si" anteriores="si"]</code></li>
			</ul>
			<p>En Elementor también está disponible el widget <strong>«Eventos · Calendario»</strong> dentro de la categoría <em>Astronumerología</em>.</p>
		</div>
		<?php
	}
}
