<?php
/**
 * Instalador / Gestor de tablas propias.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LRP_Database {

	public static function table( $name ) {
		global $wpdb;
		return $wpdb->prefix . 'lrp_' . $name;
	}

	public static function install() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset = $wpdb->get_charset_collate();

		$books = self::table( 'books' );
		$sql1  = "CREATE TABLE {$books} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			business_name VARCHAR(255) NOT NULL DEFAULT '',
			trade_name VARCHAR(255) NOT NULL DEFAULT '',
			ruc VARCHAR(20) NOT NULL DEFAULT '',
			address TEXT NULL,
			phone VARCHAR(50) NOT NULL DEFAULT '',
			email VARCHAR(255) NOT NULL DEFAULT '',
			website VARCHAR(255) NOT NULL DEFAULT '',
			logo_url TEXT NULL,
			industry VARCHAR(100) NOT NULL DEFAULT '',
			template_id VARCHAR(100) NOT NULL DEFAULT '',
			settings LONGTEXT NULL,
			status VARCHAR(50) NOT NULL DEFAULT 'active',
			created_at DATETIME NULL,
			updated_at DATETIME NULL,
			PRIMARY KEY  (id),
			KEY status (status)
		) {$charset};";

		$claims = self::table( 'claims' );
		$sql2   = "CREATE TABLE {$claims} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			book_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			claim_code VARCHAR(50) NOT NULL DEFAULT '',
			claim_number BIGINT UNSIGNED NOT NULL DEFAULT 0,
			claim_type VARCHAR(50) NOT NULL DEFAULT '',
			template_id VARCHAR(100) NOT NULL DEFAULT '',
			consumer_name VARCHAR(255) NOT NULL DEFAULT '',
			document_type VARCHAR(50) NOT NULL DEFAULT '',
			document_number VARCHAR(50) NOT NULL DEFAULT '',
			email VARCHAR(255) NOT NULL DEFAULT '',
			phone VARCHAR(50) NOT NULL DEFAULT '',
			address TEXT NULL,
			department VARCHAR(100) NOT NULL DEFAULT '',
			province VARCHAR(100) NOT NULL DEFAULT '',
			district VARCHAR(100) NOT NULL DEFAULT '',
			is_minor TINYINT(1) NOT NULL DEFAULT 0,
			guardian_name VARCHAR(255) NOT NULL DEFAULT '',
			guardian_document VARCHAR(100) NOT NULL DEFAULT '',
			product_or_service VARCHAR(255) NOT NULL DEFAULT '',
			good_type VARCHAR(50) NOT NULL DEFAULT '',
			amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
			receipt_number VARCHAR(100) NOT NULL DEFAULT '',
			purchase_date DATE NULL,
			channel VARCHAR(100) NOT NULL DEFAULT '',
			claim_detail LONGTEXT NULL,
			consumer_request LONGTEXT NULL,
			additional_data LONGTEXT NULL,
			status VARCHAR(50) NOT NULL DEFAULT 'new',
			deadline_at DATETIME NULL,
			answered_at DATETIME NULL,
			ip_address VARCHAR(100) NOT NULL DEFAULT '',
			user_agent TEXT NULL,
			created_at DATETIME NULL,
			updated_at DATETIME NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY claim_code (claim_code),
			KEY status (status),
			KEY template_id (template_id),
			KEY document_number (document_number),
			KEY created_at (created_at)
		) {$charset};";

		$responses = self::table( 'claim_responses' );
		$sql3      = "CREATE TABLE {$responses} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			claim_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			response_text LONGTEXT NULL,
			response_file TEXT NULL,
			responded_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
			sent_to_email VARCHAR(255) NOT NULL DEFAULT '',
			sent_at DATETIME NULL,
			created_at DATETIME NULL,
			PRIMARY KEY  (id),
			KEY claim_id (claim_id)
		) {$charset};";

		$attachments = self::table( 'claim_attachments' );
		$sql4        = "CREATE TABLE {$attachments} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			claim_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			file_path TEXT NULL,
			file_url TEXT NULL,
			file_name VARCHAR(255) NOT NULL DEFAULT '',
			mime_type VARCHAR(100) NOT NULL DEFAULT '',
			size BIGINT UNSIGNED NOT NULL DEFAULT 0,
			created_at DATETIME NULL,
			PRIMARY KEY  (id),
			KEY claim_id (claim_id)
		) {$charset};";

		$logs = self::table( 'claim_logs' );
		$sql5 = "CREATE TABLE {$logs} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			claim_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			action VARCHAR(255) NOT NULL DEFAULT '',
			previous_status VARCHAR(100) NOT NULL DEFAULT '',
			new_status VARCHAR(100) NOT NULL DEFAULT '',
			user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			ip_address VARCHAR(100) NOT NULL DEFAULT '',
			data LONGTEXT NULL,
			created_at DATETIME NULL,
			PRIMARY KEY  (id),
			KEY claim_id (claim_id)
		) {$charset};";

		dbDelta( $sql1 );
		dbDelta( $sql2 );
		dbDelta( $sql3 );
		dbDelta( $sql4 );
		dbDelta( $sql5 );
	}

	/**
	 * Devuelve un correlativo nuevo (incremental por año, almacenado en opciones).
	 */
	public static function next_correlative() {
		$year = (int) date_i18n( 'Y' );
		$key  = 'lrp_correlative_' . $year;
		$num  = (int) get_option( $key, 0 );
		$num++;
		update_option( $key, $num, false );
		return $num;
	}
}
