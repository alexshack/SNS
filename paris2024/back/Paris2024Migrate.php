<?php


class Paris2024Migrate {

	static function table_create() {
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		$name = $wpdb->prefix . Paris2024Event::$tableName;
		$sql = "CREATE TABLE `{$name}` (
		event_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		sport_id BIGINT(20) UNSIGNED NOT NULL,
		event_name VARCHAR (255) NOT NULL,
		event_type VARCHAR (255) NOT NULL,
		custom TEXT NOT NULL,
		event_time DATETIME NOT NULL,
		PRIMARY KEY  event_id (event_id),
		KEY sport_id (sport_id),
		KEY event_time (event_time)
		)
		{$collate};";

		dbDelta( $sql );

		$name = $wpdb->prefix . Paris2024Sport::$tableName;
		$sql = "CREATE TABLE `{$name}` (
		        sport_id BIGINT(20) UNSIGNED NOT NULL,
		        sport_name VARCHAR (255) NOT NULL,
		        medals TINYINT UNSIGNED NOT NULL,
		        UNIQUE KEY  sport_id (sport_id),
		        KEY medals (medals)
		      ) $collate;";

		dbDelta( $sql );

		$name = $wpdb->prefix . Paris2024Medals::$tableName;
		$sql = "CREATE TABLE `{$name}` (
		        country_id BIGINT(20) UNSIGNED NOT NULL,
		        country_name VARCHAR (255) NOT NULL,
		        gold TINYINT UNSIGNED NOT NULL,
		        silver TINYINT UNSIGNED NOT NULL,
		        bronze TINYINT UNSIGNED NOT NULL,
		        amount TINYINT UNSIGNED NOT NULL,
		        UNIQUE KEY  country_id (country_id),
		        KEY amount (amount)
		      ) $collate;";

		dbDelta( $sql );

		$name = $wpdb->prefix . Paris2024Table::$tableName;
		$sql = "CREATE TABLE `{$name}` (
		        tid BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		        sport_id BIGINT(20) UNSIGNED NOT NULL,
		        type VARCHAR (50) NOT NULL,
		        table_name VARCHAR (255) NOT NULL,
		        table_header TEXT NOT NULL,
		        table_content TEXT NOT NULL,
		        updated_at DATETIME NOT NULL,
		        PRIMARY KEY  tid (tid),
		        KEY sport_id (sport_id),
		        KEY table_name (table_name)
		      ) $collate;";

		dbDelta( $sql );

		$name = $wpdb->prefix . Paris2024Quiz::$tableName;
		$sql = "CREATE TABLE `{$name}` (
		        email VARCHAR (255) NOT NULL,
		        a_date DATETIME NOT NULL,
				a_1 TINYINT UNSIGNED NOT NULL,
				a_2 TINYINT UNSIGNED NOT NULL,
				a_3 TINYINT UNSIGNED NOT NULL,
				a_4 TINYINT UNSIGNED NOT NULL,
				a_5 TINYINT UNSIGNED NOT NULL,
				a_6 TINYINT UNSIGNED NOT NULL,
				a_7 TINYINT UNSIGNED NOT NULL,
				a_8 TINYINT UNSIGNED NOT NULL,
				points TINYINT UNSIGNED,
		        PRIMARY KEY  email (email),
		        KEY a_date (a_date)
		      ) $collate;";

		dbDelta( $sql );

	}

}
