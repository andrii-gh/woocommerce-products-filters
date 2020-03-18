<?php

namespace Plugin\Woocommerce_Products_Filters;

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Builder {
	public function init() {
		add_action(
			Hook::get_hook_name( 'hooks' ),
			[ $this, 'hooks' ]
		);
	}

	public function hooks() {
		if ( ! isset( $_GET['plugin-wpf-build'] ) ) {
			return;
		}

		add_action(
			'wp',
			[ $this, 'build' ]
		);
	}

	public function build() {
//		$this->build_db_structure();

		require_once get_stylesheet_directory() . '/includes/composer/vendor/autoload.php';

		global $wpdb;

		$wp_table_prefix = $wpdb->prefix;

		$products_attributes_options     = $wp_table_prefix . Data::$db_table_prefix . 'products_attributes_options';
		$terms_attributes_options        = $wp_table_prefix . Data::$db_table_prefix . 'terms_attributes_options';
		$attributes_options_table        = $wp_table_prefix . Data::$db_table_prefix . 'attributes_options';
		$attributes_options_values_table = $wp_table_prefix . Data::$db_table_prefix . 'attributes_options_values';
		$values_table                    = $wp_table_prefix . Data::$db_table_prefix . 'values';
		$values_words_table              = $wp_table_prefix . Data::$db_table_prefix . 'values_words';
		$words_table                     = $wp_table_prefix . Data::$db_table_prefix . 'words';
		$values_numbers_table            = $wp_table_prefix . Data::$db_table_prefix . 'values_numbers';
		$numbers_table                   = $wp_table_prefix . Data::$db_table_prefix . 'numbers';

		$numbers_attributes_taxonomies = [
			'pa_kol-vo-stranicz'
		];

		wp_suspend_cache_addition( true );

		$logger = new Logger( 'build-products-attributes' );

		$logger->pushHandler( new StreamHandler( get_stylesheet_directory() . '/logs/build-attributes-ru.log', Logger::INFO ) );
		$logger->pushHandler( new FirePHPHandler() );

		$paged = 0;
		while ( true ) {
			$paged ++;
			$logger->info( "Paged: $paged" );

			$args           = [
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'lang'           => 'ru',
				'posts_per_page' => 100,
				'paged'          => $paged,
			];
			$products_posts = get_posts( $args );

			if ( empty( $products_posts ) ) {
				$logger->info( "Break. Empty posts" );

				break;
			}

			foreach ( $products_posts as $product_post ) {
				$product            = wc_get_product( $product_post );
				$product_id         = $product_post->ID;
				$product_lang       = pll_get_post_language( $product_post->ID );
				$product_attributes = $product->get_attributes();

				foreach ( $product_attributes as $product_attribute ) {
					$attribute_data = $product_attribute->get_data();

					$attribute_id = $attribute_data['id'];
					$is_taxonomy  = $attribute_data['is_taxonomy'];

					if ( empty( $is_taxonomy ) ) {
						$logger->error( "Attribute is not Taxonomy $product_id:$attribute_id" );

						continue;
					}

					$attribute_taxonomy_slug = $attribute_data['name'];
					$options_ids             = $attribute_data['options'];
					$options                 = get_terms(
						[
							'taxonomy'   => $attribute_taxonomy_slug,
							'include'    => $options_ids,
							'hide_empty' => true,
							'lang'       => $product_lang,
						]
					);

					if ( empty( $options ) ) {
						$logger->warning( "Empty options" );

						continue;
					}

					foreach ( $options as $option ) {
						$option_value = $option->name;
						$option_id    = $option->term_id;

						$wpdb->query(
							$wpdb->prepare(
								"INSERT IGNORE INTO $attributes_options_table SET  `attribute_id` = %d, `option_id` = %d",
								$attribute_id,
								$option_id
							)
						);

						$attributes_option_id_results = $wpdb->get_row(
							$wpdb->prepare(
								"SELECT id FROM $attributes_options_table WHERE `attribute_id` = %d AND `option_id` = %d",
								$attribute_id,
								$option_id
							)
						);
						$attribute_option_id          = $attributes_option_id_results->id;

						$wpdb->query(
							$wpdb->prepare(
								"INSERT IGNORE INTO $products_attributes_options SET  `product_id` = %d, `attribute_option_id` = %d",
								$product_id,
								$attribute_option_id
							)
						);
						$wpdb->query(
							$wpdb->prepare(
								"INSERT IGNORE INTO $values_table SET  `value` = %s",
								$option_value
							)
						);
						$value_id_results = $wpdb->get_row(
							$wpdb->prepare(
								"SELECT id FROM $values_table WHERE `value` = %s",
								$option_value
							)
						);
						$value_id         = $value_id_results->id;

						$wpdb->query(
							$wpdb->prepare(
								"INSERT IGNORE INTO $attributes_options_values_table SET  `attribute_option_id` = %d, value_id = %d",
								$attribute_option_id,
								$value_id
							)
						);

						$words = explode( ' ', $option_value );

						foreach ( $words as $word ) {
							$wpdb->query(
								$wpdb->prepare(
									"INSERT IGNORE INTO $words_table SET  `value` = %s",
									$word
								)
							);

							$word_id_results = $wpdb->get_row(
								$wpdb->prepare(
									"SELECT id FROM $words_table WHERE `value` = %s",
									$word
								)
							);
							$words_id        = $word_id_results->id;

							$wpdb->query(
								$wpdb->prepare(
									"INSERT IGNORE INTO $values_words_table SET  `value_id` = %d, `word_id` = %d",
									$value_id,
									$words_id
								)
							);
						}

						if ( ! in_array( $attribute_taxonomy_slug, $numbers_attributes_taxonomies, true ) ) {
							continue;
						}

						$number = (int) trim( $option_value );
						$wpdb->query(
							$wpdb->prepare(
								"INSERT IGNORE INTO $numbers_table SET `value` = %d",
								$number
							)
						);

						$number_id_results = $wpdb->get_row(
							$wpdb->prepare(
								"SELECT id FROM $numbers_table WHERE `value` = %d",
								$number
							)
						);
						$numbers_id        = $number_id_results->id;
						$wpdb->query(
							$wpdb->prepare(
								"INSERT IGNORE INTO $values_numbers_table SET  `value_id` = %d, `number_id` = %d",
								$value_id,
								$numbers_id
							)
						);
					}
				}
			}

			$logger->info( 'Complete Posts', wp_list_pluck( $products_posts, 'post_title' ) );
		}
		exit;
	}

	public function build_db_structure() {
		if ( $this->is_built_db_structure() ) {
			return true;
		}

		global $wpdb;

		$wp_table_prefix = $wpdb->prefix;
		$charset_collate = $wpdb->get_charset_collate();

		$products_attributes_options_table_name = Data::$db_table_prefix . 'products_attributes_options';
		$products_attributes_options_table_name = $wp_table_prefix . $products_attributes_options_table_name;
		$products_attributes_options_table_ddl  = "
			CREATE TABLE $products_attributes_options_table_name
				( 
					`id` INT NOT NULL AUTO_INCREMENT,
					`product_id` INT NOT NULL ,
					`attribute_option_id` INT NOT NULL,
					PRIMARY KEY (id)
				) $charset_collate;
		";

		$terms_attributes_options_table_name = Data::$db_table_prefix . 'terms_attributes_options';
		$terms_attributes_options_table_name = $wp_table_prefix . $terms_attributes_options_table_name;
		$terms_attributes_options_table_ddl  = "
			CREATE TABLE $terms_attributes_options_table_name
				( 
					`id` INT NOT NULL AUTO_INCREMENT,
					`term_id` INT NOT NULL ,
					`attribute_option_id` INT NOT NULL,
					PRIMARY KEY (id)
				) $charset_collate;
		";

		$attributes_options_table_name = Data::$db_table_prefix . 'attributes_options';
		$attributes_options_table_name = $wp_table_prefix . $attributes_options_table_name;
		$attributes_options_table_ddl  = "
			CREATE TABLE $attributes_options_table_name
				( 
					`id` INT NOT NULL AUTO_INCREMENT,
					`attribute_id` INT NOT NULL,
					`option_id` INT NOT NULL ,
					PRIMARY KEY (id)
				) $charset_collate;
		";

		$attributes_options_values_table_name = Data::$db_table_prefix . 'attributes_options_values';
		$attributes_options_values_table_name = $wp_table_prefix . $attributes_options_values_table_name;
		$attributes_options_values_table_ddl  = "
			CREATE TABLE $attributes_options_values_table_name
				( 
					`id` INT NOT NULL AUTO_INCREMENT,
					`attribute_option_id` INT NOT NULL,
					`value_id` INT NOT NULL ,
					PRIMARY KEY (id)
				) $charset_collate;
		";

		$values_table_name = Data::$db_table_prefix . 'values';
		$values_table_name = $wp_table_prefix . $values_table_name;
		$values_table_ddl  = "
			CREATE TABLE $values_table_name
				( 
					`id` INT NOT NULL AUTO_INCREMENT,
					`value` VARCHAR(190) NOT NULL UNIQUE,
					PRIMARY KEY (id)
				) $charset_collate;
		";

		$values_numbers_table_name = Data::$db_table_prefix . 'values_numbers';
		$values_numbers_table_name = $wp_table_prefix . $values_numbers_table_name;
		$values_numbers_table_ddl  = "
			CREATE TABLE $values_numbers_table_name
				( 
					`id` INT NOT NULL AUTO_INCREMENT,
					`value_id` INT NOT NULL,
					`number_id` INT NOT NULL ,
					PRIMARY KEY (id)
				) $charset_collate;
		";

		$numbers_table_name = Data::$db_table_prefix . 'numbers';
		$numbers_table_name = $wp_table_prefix . $numbers_table_name;
		$numbers_table_ddl  = "
			CREATE TABLE $numbers_table_name
				( 
					`id` INT NOT NULL AUTO_INCREMENT,
					`value` INT NOT NULL UNIQUE,
					PRIMARY KEY (id)
				) $charset_collate;
		";

		$values_words_table_name = Data::$db_table_prefix . 'values_words';
		$values_words_table_name = $wp_table_prefix . $values_words_table_name;
		$values_words_table_ddl  = "
			CREATE TABLE $values_words_table_name
				( 
					`id` INT NOT NULL AUTO_INCREMENT,
					`value_id` INT NOT NULL,
					`word_id` INT NOT NULL ,
					PRIMARY KEY (id)
				) $charset_collate;
		";

		$words_table_name = Data::$db_table_prefix . 'words';
		$words_table_name = $wp_table_prefix . $words_table_name;
		$words_table_ddl  = "
			CREATE TABLE $words_table_name
				( 
					`id` INT NOT NULL AUTO_INCREMENT,
					`value` VARCHAR(190) NOT NULL UNIQUE,
					PRIMARY KEY (id)
				) $charset_collate;
		";

		$ddls = [
			$words_table_ddl,
			$values_words_table_ddl,
			$numbers_table_ddl,
			$values_numbers_table_ddl,
			$values_table_ddl,
			$attributes_options_values_table_ddl,
			$attributes_options_table_ddl,
			$terms_attributes_options_table_ddl,
			$products_attributes_options_table_ddl,
		];

		$ddl_results  = array_map(
			function ( $ddl ) {
				global $wpdb;

				return $wpdb->query( $ddl );
			},
			$ddls
		);
		$fail_results = array_filter(
			$ddl_results,
			function ( $result ) {
				return $result !== true;
			}
		);

		$is_success = empty( $fail_results );
		$this->build_db_indexes();

		$is_success
			? $this->set_built_status()
			: $this->unset_built_status();
	}

	public function build_db_indexes() {
		global $wpdb;

		$wp_table_prefix = $wpdb->prefix;
		$table_prefix    = $wp_table_prefix . Data::$db_table_prefix;

		$products_attributes_table   = $table_prefix . 'products_attributes_options';
		$products_attributes_indexes = [
			"CREATE INDEX ix__attribute_option__product ON $products_attributes_table (attribute_option_id, product_id)",
			"CREATE UNIQUE INDEX ix__product__attribute_option ON $products_attributes_table (product_id, attribute_option_id)"
		];

		$terms_attributes_table   = $table_prefix . 'terms_attributes_options';
		$terms_attributes_indexes = [
			"CREATE INDEX ix__attribute_option__term ON $terms_attributes_table (attribute_option_id, term_id)",
			"CREATE UNIQUE INDEX ix__term__attribute_option ON $terms_attributes_table (term_id, attribute_option_id)"
		];

		$attributes_options_table   = $table_prefix . 'attributes_options';
		$attributes_options_indexes = [
			"CREATE INDEX ix__attribute__id__option ON $attributes_options_table (attribute_id, id, option_id)",
			"CREATE INDEX ix__attribute__option__id ON $attributes_options_table (attribute_id, option_id, id)",
			"CREATE INDEX ix__id__attribute__option ON $attributes_options_table (id, attribute_id, option_id)",
			"CREATE INDEX ix__option_id ON $attributes_options_table (option_id)",
			"CREATE UNIQUE INDEX uq__attribute__option ON $attributes_options_table ( attribute_id, option_id )"
		];

		$attributes_options_values_table   = $table_prefix . 'attributes_options_values';
		$attributes_options_values_indexes = [
			"CREATE UNIQUE INDEX ix__attribute_option__value ON $attributes_options_values_table (attribute_option_id, value_id)",
			"CREATE INDEX ix__value__attribute_option ON $attributes_options_values_table (value_id, attribute_option_id)",
		];

		$values_table   = $table_prefix . 'values';
		$values_indexes = [
			"CREATE INDEX ix__id__value ON $values_table (id, value)",
			"CREATE INDEX ix__value__id ON $values_table (value, id)",
		];

		$values_words_table   = $table_prefix . 'values_words';
		$values_words_indexes = [
			"CREATE UNIQUE INDEX ix__value__word ON $values_words_table (value_id, word_id)",
			"CREATE INDEX ix__word__value ON $values_words_table (word_id, value_id)",
		];

		$values_numbers_table   = $table_prefix . 'values_numbers';
		$values_numbers_indexes = [
			"CREATE UNIQUE INDEX ix__value__number ON $values_numbers_table (value_id, number_id)",
			"CREATE INDEX ix__number__value ON $values_numbers_table (number_id, value_id)",
		];

		$words_table   = $table_prefix . 'words';
		$words_indexes = [
			"CREATE INDEX ix__id__value ON $words_table (id, value)",
			"CREATE INDEX ix__value__id ON $words_table (value, id)",
		];

		$numbers_table   = $table_prefix . 'numbers';
		$numbers_indexes = [
			"CREATE INDEX ix__id__value ON $numbers_table (id, value)",
			"CREATE INDEX ix__value__id ON $numbers_table (value, id)",
		];

		$indexes = [
			$products_attributes_indexes,
			$terms_attributes_indexes,
			$attributes_options_indexes,
			$attributes_options_values_indexes,
			$values_indexes,
			$values_words_indexes,
			$words_indexes,
			$values_numbers_indexes,
			$numbers_indexes
		];

		foreach ( $indexes as $table_indexes ) {
			array_walk(
				$table_indexes,
				function ( $table_index ) {
					global $wpdb;

					$wpdb->query( $table_index );
				}
			);
		}
	}

	public function is_built_db_structure() {
		$is_built = get_option( Data::$options_prefix . 'is_built' );

		return ! ( empty( $is_built ) );
	}

	public function set_built_status() {
		update_option( Data::$options_prefix . 'is_built', true );
	}

	public function unset_built_status() {
		delete_option( Data::$options_prefix . 'is_built' );
	}
}