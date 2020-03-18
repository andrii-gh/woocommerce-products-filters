<?php

namespace Plugin\Woocommerce_Products_Filters;

class Filter {
	public function init() {
		add_filter( 'posts_join', [ $this, 'filter_posts_join' ], 10, 2 );
		add_filter( 'posts_where', [ $this, 'filter_posts_where' ], 10, 2 );
		add_filter( 'posts_request', [ $this, 'filter_posts_request' ], 99999, 2 );
		add_action( 'pre_get_posts', [ $this, 'action_pre_get_posts' ], 10, 2 );

		add_filter(
			'woocommerce_product_data_store_cpt_get_products_query',
			[ $this, 'filter_woocommerce_product_data_store_cpt_get_products_query' ],
			10,
			2
		);
	}

	public function action_pre_get_posts( $query ) {
		if ( ! isset( $query->query_vars['plugin_wpf_filter'] ) ) {
			return;
		}

		global $query_to_test;

		$query_to_test = $query;
	}

	public function filter_posts_request( $request, $wp_query ) {
		global $test_wc_request;

		$test_wc_request = $request;

		return $request;
	}

	public function filter_woocommerce_product_data_store_cpt_get_products_query( $query, $query_vars ) {
		if ( ! empty( $query_vars['plugin_wpf_filter'] ) ) {
			$query->query_vars['plugin_wpf_filter'] = $query_vars['plugin_wpf_filter'];
		}

		$query->query_vars['lang'] = '';

		return $query;

		if ( ! empty( $query_vars['lang'] ) ) {
			$query->query_vars['lang'] = '';
		}

		return $query;
	}

	public function filter_posts_join( $join, $wp_query ) {
		if ( ! isset( $wp_query->query_vars['plugin_wpf_filter'] ) ) {
			return $join;
		}

		$filter_data = $wp_query->query_vars['plugin_wpf_filter'];

		global $wpdb;
		$wp_table_prefix = $wpdb->prefix;

		$products_attributes_options_table = $wp_table_prefix . Data::$db_table_prefix . 'products_attributes_options';
		$attributes_options_table          = $wp_table_prefix . Data::$db_table_prefix . 'attributes_options';

		$table_index = 0;
		$join_pieces = array_map(
			function ( $filter_data_item ) use ( &$table_index, $products_attributes_options_table, $attributes_options_table ) {
				global $wpdb;

				$type = $filter_data_item['type'];
				$table_index ++;

				switch ( $type ) {
					case 'dropdown':
					case 'checkbox':
					{
						$products_attributes_options_alias = "pao_$table_index";
						$products_attributes_options_alias = "pao_$table_index";
						$attributes_option_alias           = "ao_$table_index";
						$posts_alias                       = $wpdb->posts;

						return "
							JOIN $products_attributes_options_table $products_attributes_options_alias ON $posts_alias.ID = $products_attributes_options_alias.product_id
							JOIN $attributes_options_table $attributes_option_alias ON $products_attributes_options_alias.attribute_option_id = $attributes_option_alias.id
							";
					}
					case 'slider':
					{
						return $this->filter_posts_join_slider( $table_index );
					}
				}

				return '';
			},
			$filter_data
		);

		$attributes_values_join = implode( ' ', $join_pieces );

		$join .= " $attributes_values_join";

		return $join;
	}

	public function filter_request( $request ) {
		if ( ! isset( $wp_query->query_vars['plugin_wpf_filter'] ) ) {
			return $request;
		}

		return $request;
	}

	public function filter_posts_join_checkbox( $table_index ) {
		global $wpdb;

		$wp_table_prefix           = $wpdb->prefix;
		$products_attributes_table = $wp_table_prefix . Data::$db_table_prefix . 'products_attributes';

		$products_attributes_table_alias = "ppat_$table_index";

		return "JOIN $products_attributes_table as $products_attributes_table_alias ON $products_attributes_table_alias.product_id = {$wpdb->posts}.ID";
	}

	public function filter_posts_join_dropdown( $table_index ) {
		global $wpdb;

		$wp_table_prefix           = $wpdb->prefix;
		$products_attributes_table = $wp_table_prefix . Data::$db_table_prefix . 'attributes_values_text';

		$products_attributes_table_alias = "pavt_$table_index";

		return "JOIN $products_attributes_table as $products_attributes_table_alias ON $products_attributes_table_alias.product_id = {$wpdb->posts}.ID";
	}

	public function filter_posts_join_slider( $table_index ) {
		global $wpdb;

		$wp_table_prefix                                   = $wpdb->prefix;
		$attributes_values_numeric_table_name              = Data::$db_table_prefix . 'attributes_values_numeric';
		$prefixed_products_attributes_values_table_numeric = $wp_table_prefix . $attributes_values_numeric_table_name;

		$products_attributes_table_numeric_alias = "ppatv_$table_index";

		return "JOIN $prefixed_products_attributes_values_table_numeric as $products_attributes_table_numeric_alias ON $products_attributes_table_numeric_alias.product_id = {$wpdb->posts}.ID";
	}

	public function filter_posts_where( $where, $wp_query ) {
		if ( ! isset( $wp_query->query_vars['plugin_wpf_filter'] ) ) {
			return $where;
		}

		$filter_data = $wp_query->query_vars['plugin_wpf_filter'];
		$table_index = 0;

		$attributes_where_pieces = array_map(
			function ( $filter_data_item ) use ( &$table_index ) {
				global $wpdb;

				$table_index ++;
				$type = $filter_data_item['type'];

				switch ( $type ) {
					case 'dropdown':
					case 'checkbox':
					{
						$attributes_options_alias = "ao_$table_index";

						$options          = $filter_data_item['options'];
						$options_ids      = array_column( $options, 'id' );
						$options_ids      = array_map(
							function ( $option_id ) {
								global $wpdb;

								return $wpdb->prepare( '%d', $option_id );
							},
							$options_ids
						);
						$options_ids_list = implode( $options_ids, ',' );

						return $wpdb->prepare(
							"($attributes_options_alias.attribute_id = %d AND $attributes_options_alias.option_id IN ($options_ids_list) )",
							$filter_data_item['attribute-id']
						);
					}
					case 'slider':
					{
						return $this->filter_posts_where_slider( $filter_data_item, $table_index );
					}
				}
			},
			$filter_data
		);
		$attributes_where        = implode( ' AND ', $attributes_where_pieces );

		$where .= " AND ($attributes_where)";

		return $where;
	}

	public function filter_posts_where_slider( $filter_data_item, $table_index ) {
		$products_attributes_values_numeric_table_prefix = 'ppatv_';
		$products_attributes_values_numeric_table        = $products_attributes_values_numeric_table_prefix . $table_index;
		$attribute_id                                    = $filter_data_item['attribute-id'];

		$options = $filter_data_item['options'];
		$min     = $options['min'];
		$max     = $options['max'];

		return "($products_attributes_values_numeric_table.attribute_id = $attribute_id AND $products_attributes_values_numeric_table.value BETWEEN $min AND $max)";
	}

	public function filter_posts_where_checkbox( $filter_data_item, $table_index ) {
		$products_attributes_table_prefix = 'ppat_';
		$products_attributes_table        = $products_attributes_table_prefix . $table_index;
		$attribute_id                     = $filter_data_item['attribute-id'];

		$options          = $filter_data_item['options'];
		$options_ids      = array_column( $options, 'id' );
		$options_ids      = array_map(
			function ( $option_id ) {
				global $wpdb;

				return $wpdb->prepare( '%d', $option_id );
			},
			$options_ids
		);
		$options_ids_list = implode( $options_ids, ',' );

		return "($products_attributes_table.attribute_id = $attribute_id AND $products_attributes_table.value_id IN( $options_ids_list ))";
	}

	public function filter_posts_where_dropdown( $filter_data_item, $table_index ) {
		global $wpdb;

		$attributes_values_text_table_prefix = 'pavt_';
		$attributes_values_text_table        = $attributes_values_text_table_prefix . $table_index;
		$attribute_id                        = $filter_data_item['attribute-id'];

		$options          = $filter_data_item['options'];
		$options_values   = array_column( $options, 'value' );
		$options_values   = array_map(
			function ( $value ) {
				global $wpdb;

				return $wpdb->prepare( '%s', $value );
			},
			$options_values
		);
		$options_ids_list = implode( $options_values, ',' );

		return "($attributes_values_text_table.attribute_id = $attribute_id AND $attributes_values_text_table.value IN( $options_ids_list ))";
	}

	public function parse_filters_args( $args ) {
		if ( ! is_array( $args ) ) {
			return false;
		}

		$attributes_args = isset( $args['attributes'] ) && is_array( $args['attributes'] )
			? $args['attributes']
			: [];

		$filter_data = [];
		foreach ( $attributes_args as $attribute_args ) {
			if ( ! isset( $attribute_args['type'] ) ) {
				continue;
			}

			if ( $attribute_args['type'] === 'checkbox' ) {
				$filter_data[] = [
					'attribute-id' => $attribute_args['id'],
					'type'         => 'checkbox',
					'options'      => $attribute_args['data']['options'],
				];

				continue;
			}

			if ( $attribute_args['type'] === 'slider' ) {
				$filter_data[] = [
					'attribute-id' => $attribute_args['id'],
					'type'         => 'slider',
					'options'      => [
						'min' => $attribute_args['data']['min'],
						'max' => $attribute_args['data']['max']
					],
				];

				continue;
			}

			if ( $attribute_args['type'] === 'dropdown' ) {
				$filter_data[] = [
					'attribute-id' => $attribute_args['id'],
					'type'         => 'dropdown',
					'options'      => $attribute_args['data']['options'],
				];

				continue;
			}
		}

		$page = isset( $args['page'] )
			? (int) $args['page']
			: 0;

		$products_per_page = apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page() );
		$products_per_page = Hook::apply_filters( 'filter_products_per_page', $products_per_page );

		$product_types = array_keys( wc_get_product_types() );

//		dump( $product_types );
//		exit;

		$ordering_args = [
			'orderby' => 'date',
			'order'   => 'desc',
		];
		if ( isset( $args['orderby'] ) || ! ( empty( $args['orderby'] ) ) ) {
			switch ( $args['orderby'] ) {
				case 'price-desc':
					$ordering_args['orderby'] = 'price';
					$ordering_args['order']   = 'desc';

					break;
				default:
					$ordering_args['orderby'] = $args['orderby'];
					$ordering_args['order']   = 'asc';
			}
		}

		return [
			'wc-args'       => Hook::apply_filters(
				'filter_query_vars',
				[
					'post_type'         => 'product',
					'type'              => $product_types,
					'status'            => 'publish',
					'limit'             => $products_per_page,
					'page'              => $page,
					'plugin_wpf_filter' => $filter_data,
					'lang'              => $args['lang'],
					'paginate'          => true,
				]
			),
			'ordering-args' => $ordering_args,
		];
	}

	public function get_products( $args ) {
		$filters_args  = $this->parse_filters_args( $args );
		$wc_args       = $filters_args['wc-args'];
		$ordering_args = $filters_args['ordering-args'];

		$query = new \WC_Query();

		$query->get_catalog_ordering_args( $ordering_args['orderby'], $ordering_args['order'] );
		$products = wc_get_products( $wc_args );
		$query->remove_ordering_args();

		return [
			'wc-products' => $products,
			'args'        => $wc_args,
		];
	}
}