<?php

namespace Plugin\Woocommerce_Products_Filters;

class Suggestion {
	public function suggest( $args ) {
		global $wpdb;

		$lang         = $args['data']['lang'];

		$lang_term    = get_term_by( 'slug', $lang, 'language', ARRAY_A );
		$lang_term_id = $lang_term['term_id'];

		$search_query = $args['data']['value-to-search'];
		$attribute_id = $args['data']['attribute-id'];

		$words = explode( ' ', $search_query );

		$wp_table_prefix = $wpdb->prefix;

		$term_relationship_table         = $wp_table_prefix . 'term_relationships';
		$products_attributes_options     = $wp_table_prefix . Data::$db_table_prefix . 'products_attributes_options';
		$attributes_options_table        = $wp_table_prefix . Data::$db_table_prefix . 'attributes_options';
		$attributes_options_values_table = $wp_table_prefix . Data::$db_table_prefix . 'attributes_options_values';
		$values_table                    = $wp_table_prefix . Data::$db_table_prefix . 'values';
		$values_words_table              = $wp_table_prefix . Data::$db_table_prefix . 'values_words';
		$words_table                     = $wp_table_prefix . Data::$db_table_prefix . 'words';
		$posts_table                     = $wp_table_prefix . 'posts';

		$table_index              = 0;
		$values_words_join_pieces = array_map(
			function ( $word ) use ( &$table_index, $values_words_table ) {
				$table_index ++;
				$table_alias = "vw_$table_index";

				return "JOIN $values_words_table $table_alias ON v.id = $table_alias.value_id";
			},
			$words
		);
		$values_words_join        = implode( ' ', $values_words_join_pieces );

		$table_index                    = 0;
		$values_words_words_join_pieces = array_map(
			function ( $word ) use ( &$table_index, $words_table, $values_words_table ) {
				$table_index ++;
				$table_words_values_alias = "vw_$table_index";
				$table_words_alias        = "w_$table_index";

				return "JOIN $words_table $table_words_alias ON $table_words_values_alias.word_id = $table_words_alias.id";
			},
			$words
		);
		$values_words_words_join        = implode( ' ', $values_words_words_join_pieces );

		$table_index  = 0;
		$where_pieces = array_map(
			function ( $word ) use ( &$table_index ) {
				global $wpdb;

				$table_index ++;
				$table_alias = "w_$table_index";

				$like_cause = $wpdb->esc_like( $word ) . '%';

				return
					$wpdb->prepare(
						"$table_alias.value LIKE %s",
						$like_cause
					);
			},
			$words
		);
		$words_where  = implode( ' AND ', $where_pieces );

		$values_table_alias = 'v';
		$query              = $wpdb->prepare(
			"	
					SELECT v.value as value, ao.option_id as option
						FROM $values_table $values_table_alias 
							$values_words_join
							$values_words_words_join
						
			            	JOIN $attributes_options_values_table aov ON v.id = aov.value_id
		                	JOIN $attributes_options_table ao ON aov.attribute_option_id = ao.id
		                	
		                	JOIN $term_relationship_table trlang ON ao.option_id = trlang.object_id
		
		                  JOIN $products_attributes_options pao ON pao.attribute_option_id = ao.id
		                  JOIN $posts_table p ON pao.product_id = p.ID
						WHERE 
							ao.attribute_id = %d AND
							trlang.term_taxonomy_id = $lang_term_id AND
							( $words_where )
						GROUP BY v.value, ao.option_id
				        ORDER BY v.value
				        LIMIT %d;
					",
			$attribute_id,
			HOOK::apply_filters( 'max_suggestion_items', 10 )
		);

		$results = $wpdb->get_results( $query );
		$values  = array_map(
			function ( $row ) {
				return [
					'option' => $row->option,
					'text'   => $row->value
				];
			},
			$results
		);

		$last_query = $wpdb->last_query;

		wp_send_json_success( compact( 'values', 'last_query' ) );
		exit;
	}
}