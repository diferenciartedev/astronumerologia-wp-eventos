<?php
/**
 * Sistema de plantillas por rubro.
 *
 * Cada plantilla tiene:
 *   - id, name, description, category
 *   - fields: campos adicionales (sobre los base)
 *   - help: texto informativo legal
 *   - status: active|inactive
 *   - type: free|pro
 *
 * Cada campo:
 *   - name (slug), label, type (text, textarea, select, date, number)
 *   - options (para select)
 *   - required (bool)
 *   - help (texto de ayuda)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LRP_Templates {

	protected static $cache = null;

	public static function all() {
		if ( null !== self::$cache ) {
			return self::$cache;
		}
		self::$cache = apply_filters( 'lrp_templates', self::default_set() );
		return self::$cache;
	}

	public static function get( $id ) {
		$all = self::all();
		return isset( $all[ $id ] ) ? $all[ $id ] : null;
	}

	public static function exists( $id ) {
		$all = self::all();
		return isset( $all[ $id ] );
	}

	public static function ids() {
		return array_keys( self::all() );
	}

	/**
	 * Devuelve los campos base que siempre acompañan a cualquier plantilla.
	 */
	public static function base_fields() {
		return array(
			'consumer_name'      => array( 'label' => __( 'Nombres y apellidos', 'libro-reclamaciones-pro' ), 'type' => 'text', 'required' => true ),
			'document_type'      => array( 'label' => __( 'Tipo de documento', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => lrp_get_document_types(), 'required' => true ),
			'document_number'    => array( 'label' => __( 'Número de documento', 'libro-reclamaciones-pro' ), 'type' => 'text', 'required' => true ),
			'phone'              => array( 'label' => __( 'Teléfono', 'libro-reclamaciones-pro' ), 'type' => 'text', 'required' => true ),
			'email'              => array( 'label' => __( 'Correo electrónico', 'libro-reclamaciones-pro' ), 'type' => 'email', 'required' => true ),
			'address'            => array( 'label' => __( 'Dirección', 'libro-reclamaciones-pro' ), 'type' => 'text', 'required' => false ),
			'department'         => array( 'label' => __( 'Departamento', 'libro-reclamaciones-pro' ), 'type' => 'text', 'required' => false ),
			'province'           => array( 'label' => __( 'Provincia', 'libro-reclamaciones-pro' ), 'type' => 'text', 'required' => false ),
			'district'           => array( 'label' => __( 'Distrito', 'libro-reclamaciones-pro' ), 'type' => 'text', 'required' => false ),
			'is_minor'           => array( 'label' => __( '¿Es menor de edad?', 'libro-reclamaciones-pro' ), 'type' => 'checkbox', 'required' => false ),
			'guardian_name'      => array( 'label' => __( 'Nombre del padre/madre/apoderado', 'libro-reclamaciones-pro' ), 'type' => 'text', 'required' => false ),
			'guardian_document'  => array( 'label' => __( 'Documento del apoderado', 'libro-reclamaciones-pro' ), 'type' => 'text', 'required' => false ),
			'good_type'          => array( 'label' => __( 'Tipo', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'producto' => __( 'Producto', 'libro-reclamaciones-pro' ), 'servicio' => __( 'Servicio', 'libro-reclamaciones-pro' ) ), 'required' => true ),
			'product_or_service' => array( 'label' => __( 'Nombre del producto o servicio', 'libro-reclamaciones-pro' ), 'type' => 'text', 'required' => true ),
			'amount'             => array( 'label' => __( 'Monto reclamado', 'libro-reclamaciones-pro' ), 'type' => 'number', 'required' => false ),
			'receipt_number'     => array( 'label' => __( 'Número de comprobante', 'libro-reclamaciones-pro' ), 'type' => 'text', 'required' => false ),
			'purchase_date'      => array( 'label' => __( 'Fecha de consumo / compra', 'libro-reclamaciones-pro' ), 'type' => 'date', 'required' => false ),
			'channel'            => array( 'label' => __( 'Canal de atención', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => lrp_get_channels(), 'required' => false ),
			'claim_type'         => array( 'label' => __( 'Tipo de registro', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'reclamo' => __( 'Reclamo', 'libro-reclamaciones-pro' ), 'queja' => __( 'Queja', 'libro-reclamaciones-pro' ) ), 'required' => true, 'help' => __( 'Reclamo: disconformidad relacionada con el producto o servicio. Queja: disconformidad no relacionada directamente (atención, demora, etc.).', 'libro-reclamaciones-pro' ) ),
			'claim_detail'       => array( 'label' => __( 'Detalle de lo ocurrido', 'libro-reclamaciones-pro' ), 'type' => 'textarea', 'required' => true ),
			'consumer_request'   => array( 'label' => __( 'Pedido concreto del consumidor', 'libro-reclamaciones-pro' ), 'type' => 'textarea', 'required' => true ),
		);
	}

	/**
	 * Conjunto inicial de plantillas por rubro.
	 */
	protected static function default_set() {
		$templates = array();

		$templates['general'] = array(
			'id'          => 'general',
			'name'        => __( 'General / servicios', 'libro-reclamaciones-pro' ),
			'description' => __( 'Plantilla genérica para servicios y negocios sin un rubro específico.', 'libro-reclamaciones-pro' ),
			'category'    => 'general',
			'status'      => 'active',
			'type'        => 'free',
			'help'        => '',
			'fields'      => array(
				array( 'name' => 'area_involucrada', 'label' => __( 'Área involucrada', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'persona_atendio', 'label' => __( 'Persona que atendió', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'medio_contratacion', 'label' => __( 'Medio de contratación', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'motivo', 'label' => __( 'Motivo principal', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'mala_atencion' => 'Mala atención', 'incumplimiento' => 'Incumplimiento del servicio', 'cobro_indebido' => 'Cobro indebido', 'demora' => 'Demora', 'info_incorrecta' => 'Información incorrecta', 'otro' => 'Otro' ) ),
			),
		);

		$templates['retail'] = array(
			'id'          => 'retail',
			'name'        => __( 'Retail / tienda física', 'libro-reclamaciones-pro' ),
			'description' => __( 'Formulario para tiendas físicas: productos, garantías, cambios y devoluciones.', 'libro-reclamaciones-pro' ),
			'category'    => 'retail',
			'status'      => 'active',
			'type'        => 'free',
			'fields'      => array(
				array( 'name' => 'sede', 'label' => __( 'Sede o tienda', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'boleta_factura', 'label' => __( 'Número de boleta o factura', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'producto', 'label' => __( 'Producto adquirido', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'marca_modelo', 'label' => __( 'Marca / modelo', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'fecha_compra', 'label' => __( 'Fecha de compra', 'libro-reclamaciones-pro' ), 'type' => 'date' ),
				array( 'name' => 'motivo', 'label' => __( 'Motivo', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'defectuoso' => 'Producto defectuoso', 'mala_atencion' => 'Mala atención', 'garantia' => 'Garantía', 'cambio_devolucion' => 'Cambio o devolución', 'publicidad' => 'Publicidad engañosa', 'demora_entrega' => 'Demora en entrega', 'cobro' => 'Cobro incorrecto' ) ),
			),
		);

		$templates['ecommerce'] = array(
			'id'          => 'ecommerce',
			'name'        => __( 'Ecommerce', 'libro-reclamaciones-pro' ),
			'description' => __( 'Comercio electrónico, pedidos online, delivery y devoluciones.', 'libro-reclamaciones-pro' ),
			'category'    => 'ecommerce',
			'status'      => 'active',
			'type'        => 'free',
			'fields'      => array(
				array( 'name' => 'numero_pedido', 'label' => __( 'Número de pedido', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'plataforma', 'label' => __( 'Plataforma de compra', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'metodo_pago', 'label' => __( 'Método de pago', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'delivery', 'label' => __( 'Empresa de delivery', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'tracking', 'label' => __( 'Código de tracking', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'estado_pedido', 'label' => __( 'Estado del pedido', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'motivo', 'label' => __( 'Motivo', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'no_llego' => 'No llegó el pedido', 'incompleto' => 'Llegó incompleto', 'equivocado' => 'Producto equivocado', 'danado' => 'Producto dañado', 'demora' => 'Demora en entrega', 'devolucion' => 'Problema con devolución', 'cobro_duplicado' => 'Cobro duplicado' ) ),
			),
		);

		$templates['restaurante'] = array(
			'id'          => 'restaurante',
			'name'        => __( 'Restaurante / cafetería', 'libro-reclamaciones-pro' ),
			'description' => __( 'Restaurantes, cafeterías y servicios de alimentos.', 'libro-reclamaciones-pro' ),
			'category'    => 'restaurante',
			'status'      => 'active',
			'type'        => 'free',
			'fields'      => array(
				array( 'name' => 'sede', 'label' => 'Sede', 'type' => 'text' ),
				array( 'name' => 'mesa_pedido', 'label' => __( 'Mesa o número de pedido', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'canal_resto', 'label' => __( 'Canal', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'salon' => 'Salón', 'delivery' => 'Delivery', 'recojo' => 'Recojo en tienda', 'app' => 'App externa' ) ),
				array( 'name' => 'fecha_hora', 'label' => __( 'Fecha y hora de consumo', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'producto_consumido', 'label' => __( 'Producto consumido', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'motivo', 'label' => __( 'Motivo', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'calidad' => 'Calidad del alimento', 'atencion' => 'Atención', 'espera' => 'Tiempo de espera', 'cobro' => 'Cobro incorrecto', 'delivery' => 'Delivery', 'higiene' => 'Higiene', 'incompleto' => 'Producto incompleto' ) ),
			),
		);

		$templates['hotel'] = array(
			'id'          => 'hotel',
			'name'        => __( 'Hotel / hospedaje', 'libro-reclamaciones-pro' ),
			'description' => __( 'Hospedaje, hotelería y reservas.', 'libro-reclamaciones-pro' ),
			'category'    => 'hotel',
			'status'      => 'active',
			'type'        => 'free',
			'fields'      => array(
				array( 'name' => 'sede', 'label' => 'Sede', 'type' => 'text' ),
				array( 'name' => 'reserva', 'label' => __( 'Número de reserva', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'checkin', 'label' => __( 'Fecha de check-in', 'libro-reclamaciones-pro' ), 'type' => 'date' ),
				array( 'name' => 'checkout', 'label' => __( 'Fecha de check-out', 'libro-reclamaciones-pro' ), 'type' => 'date' ),
				array( 'name' => 'tipo_habitacion', 'label' => __( 'Tipo de habitación', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'canal_reserva', 'label' => __( 'Canal de reserva', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'motivo', 'label' => __( 'Motivo', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'habitacion' => 'Habitación', 'limpieza' => 'Limpieza', 'atencion' => 'Atención', 'reserva' => 'Reserva', 'cobro' => 'Cobro', 'ruido' => 'Ruido', 'servicios' => 'Servicios ofrecidos', 'restaurante' => 'Restaurante', 'eventos' => 'Eventos' ) ),
			),
		);

		$templates['clinica'] = array(
			'id'          => 'clinica',
			'name'        => __( 'Clínica / centro médico', 'libro-reclamaciones-pro' ),
			'description' => __( 'Servicios de salud. Recuerde revisar las obligaciones específicas según SUSALUD u otra autoridad competente.', 'libro-reclamaciones-pro' ),
			'category'    => 'salud',
			'status'      => 'active',
			'type'        => 'free',
			'help'        => __( 'Aviso: las empresas del sector salud pueden tener obligaciones específicas según SUSALUD u otra autoridad competente. Este plugin no afirma cumplimiento legal total automáticamente; personalice los textos legales en Configuración.', 'libro-reclamaciones-pro' ),
			'fields'      => array(
				array( 'name' => 'sede', 'label' => 'Sede', 'type' => 'text' ),
				array( 'name' => 'tipo_usuario', 'label' => __( 'Tipo de usuario', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'paciente' => 'Paciente', 'familiar' => 'Familiar', 'apoderado' => 'Apoderado' ) ),
				array( 'name' => 'servicio', 'label' => __( 'Servicio recibido', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'especialidad', 'label' => __( 'Especialidad médica', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'area', 'label' => __( 'Médico o área involucrada', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'fecha_atencion', 'label' => __( 'Fecha de atención', 'libro-reclamaciones-pro' ), 'type' => 'date' ),
				array( 'name' => 'historia', 'label' => __( 'Número de historia clínica', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'motivo', 'label' => __( 'Motivo', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'admin' => 'Atención administrativa', 'espera' => 'Tiempo de espera', 'trato' => 'Trato recibido', 'cobertura' => 'Cobertura', 'resultado' => 'Resultado / informe', 'cita' => 'Cita médica', 'facturacion' => 'Facturación', 'medicamentos' => 'Medicamentos', 'procedimiento' => 'Procedimiento' ) ),
			),
		);

		$templates['laboratorio'] = array(
			'id'          => 'laboratorio',
			'name'        => __( 'Laboratorio', 'libro-reclamaciones-pro' ),
			'description' => __( 'Laboratorios clínicos y análisis.', 'libro-reclamaciones-pro' ),
			'category'    => 'salud',
			'status'      => 'active',
			'type'        => 'free',
			'fields'      => array(
				array( 'name' => 'sede', 'label' => 'Sede', 'type' => 'text' ),
				array( 'name' => 'tipo_examen', 'label' => __( 'Tipo de examen', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'orden', 'label' => __( 'Código de orden', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'fecha_muestra', 'label' => __( 'Fecha de toma de muestra', 'libro-reclamaciones-pro' ), 'type' => 'date' ),
				array( 'name' => 'fecha_resultado', 'label' => __( 'Fecha de entrega de resultado', 'libro-reclamaciones-pro' ), 'type' => 'date' ),
				array( 'name' => 'medio_entrega', 'label' => __( 'Medio de entrega', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'motivo', 'label' => __( 'Motivo', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'demora' => 'Demora de resultados', 'error_datos' => 'Error en datos', 'atencion' => 'Mala atención', 'cobro' => 'Cobro incorrecto', 'perdida' => 'Pérdida de muestra', 'no_entregado' => 'Resultado no entregado', 'plataforma' => 'Problema con plataforma' ) ),
			),
		);

		$templates['veterinaria'] = array(
			'id'          => 'veterinaria',
			'name'        => __( 'Veterinaria', 'libro-reclamaciones-pro' ),
			'description' => __( 'Servicios veterinarios y pet care.', 'libro-reclamaciones-pro' ),
			'category'    => 'veterinaria',
			'status'      => 'active',
			'type'        => 'free',
			'fields'      => array(
				array( 'name' => 'mascota', 'label' => __( 'Nombre de la mascota', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'especie', 'label' => __( 'Especie', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'raza', 'label' => __( 'Raza', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'edad_mascota', 'label' => __( 'Edad de la mascota', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'servicio_vet', 'label' => __( 'Servicio recibido', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'consulta' => 'Consulta', 'vacuna' => 'Vacuna', 'cirugia' => 'Cirugía', 'bano' => 'Baño', 'hospedaje' => 'Hospedaje', 'emergencia' => 'Emergencia', 'producto' => 'Producto comprado' ) ),
				array( 'name' => 'medico', 'label' => __( 'Médico veterinario o área', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'fecha_atencion', 'label' => __( 'Fecha de atención', 'libro-reclamaciones-pro' ), 'type' => 'date' ),
				array( 'name' => 'motivo', 'label' => __( 'Motivo', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'medica' => 'Atención médica', 'resultado' => 'Resultado del servicio', 'demora' => 'Demora', 'cobro' => 'Cobro', 'trato' => 'Trato recibido', 'producto' => 'Producto vendido', 'seguimiento' => 'Seguimiento post atención' ) ),
			),
		);

		$templates['educacion'] = array(
			'id'          => 'educacion',
			'name'        => __( 'Educación (Colegio/Instituto/Universidad)', 'libro-reclamaciones-pro' ),
			'description' => __( 'Instituciones educativas y academias.', 'libro-reclamaciones-pro' ),
			'category'    => 'educacion',
			'status'      => 'active',
			'type'        => 'free',
			'fields'      => array(
				array( 'name' => 'tipo_institucion', 'label' => __( 'Tipo de institución', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'colegio' => 'Colegio', 'instituto' => 'Instituto', 'universidad' => 'Universidad', 'academia' => 'Academia', 'capacitacion' => 'Centro de capacitación' ) ),
				array( 'name' => 'sede', 'label' => 'Sede', 'type' => 'text' ),
				array( 'name' => 'programa', 'label' => __( 'Programa o curso', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'modalidad', 'label' => __( 'Modalidad', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'presencial' => 'Presencial', 'virtual' => 'Virtual', 'semipresencial' => 'Semipresencial' ) ),
				array( 'name' => 'ciclo', 'label' => __( 'Ciclo / aula / sección', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'docente', 'label' => __( 'Docente o área involucrada', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'matricula', 'label' => __( 'Código de matrícula', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'motivo', 'label' => __( 'Motivo', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'academica' => 'Calidad académica', 'horarios' => 'Incumplimiento de horarios', 'plataforma' => 'Plataforma virtual', 'cobros' => 'Cobros', 'matricula' => 'Matrícula', 'certificacion' => 'Certificación', 'admin' => 'Atención administrativa', 'material' => 'Material académico', 'evaluaciones' => 'Evaluaciones' ) ),
			),
		);

		$templates['inmobiliaria'] = array(
			'id'          => 'inmobiliaria',
			'name'        => __( 'Inmobiliaria', 'libro-reclamaciones-pro' ),
			'description' => __( 'Proyectos inmobiliarios, ventas y postventa.', 'libro-reclamaciones-pro' ),
			'category'    => 'inmobiliaria',
			'status'      => 'active',
			'type'        => 'free',
			'fields'      => array(
				array( 'name' => 'proyecto', 'label' => __( 'Proyecto inmobiliario', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'unidad', 'label' => __( 'Código de unidad / inmueble', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'operacion', 'label' => __( 'Tipo de operación', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'compra' => 'Compra', 'alquiler' => 'Alquiler', 'separacion' => 'Separación', 'postventa' => 'Postventa', 'asesoria' => 'Asesoría' ) ),
				array( 'name' => 'asesor', 'label' => __( 'Asesor asignado', 'libro-reclamaciones-pro' ), 'type' => 'text' ),
				array( 'name' => 'fecha_atencion', 'label' => __( 'Fecha de atención', 'libro-reclamaciones-pro' ), 'type' => 'date' ),
				array( 'name' => 'motivo', 'label' => __( 'Motivo', 'libro-reclamaciones-pro' ), 'type' => 'select', 'options' => array( 'info' => 'Información incorrecta', 'entrega' => 'Incumplimiento de entrega', 'separacion' => 'Separación', 'contrato' => 'Contrato', 'cobro' => 'Cobro', 'postventa' => 'Postventa', 'asesor' => 'Atención del asesor' ) ),
			),
		);

		// Plantillas adicionales como placeholders (free, sin campos extra todavía).
		$placeholders = array(
			'financiera'  => __( 'Financiera / fintech', 'libro-reclamaciones-pro' ),
			'viajes'      => __( 'Agencia de viajes', 'libro-reclamaciones-pro' ),
			'gimnasio'    => __( 'Gimnasio / centro deportivo', 'libro-reclamaciones-pro' ),
			'belleza'     => __( 'Belleza / estética', 'libro-reclamaciones-pro' ),
			'taller'      => __( 'Taller mecánico', 'libro-reclamaciones-pro' ),
			'transporte'  => __( 'Transporte / courier', 'libro-reclamaciones-pro' ),
			'personalizada' => __( 'Personalizada', 'libro-reclamaciones-pro' ),
		);
		foreach ( $placeholders as $id => $name ) {
			$templates[ $id ] = array(
				'id'          => $id,
				'name'        => $name,
				'description' => __( 'Plantilla base. Personalizable mediante el filtro lrp_templates.', 'libro-reclamaciones-pro' ),
				'category'    => $id,
				'status'      => 'active',
				'type'        => 'free',
				'fields'      => array(),
			);
		}

		return $templates;
	}
}
