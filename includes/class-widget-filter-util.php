<?php

namespace Plugin\Woocommerce_Products_Filters;

class Widget_Filter_Util {
	private $widget;
	private $widget_data;

	public function set_widget( $widget ) {
		$this->widget = $widget;
	}

	public function get_data( $widget_instance_data ) {
		$data = [];

		$data['attribute'] = empty( $widget_instance_data['attribute'] )
			? ''
			: $widget_instance_data['attribute'];

		$data['type'] = empty( $widget_instance_data['type'] )
			? ''
			: $widget_instance_data['type'];

		$data['name'] = empty( $widget_instance_data['name'] )
			? ''
			: $widget_instance_data['name'];

		$data['step'] = ( ! empty( $widget_instance_data['step'] ) && 'range' === $data['type'] )
			? $widget_instance_data['step']
			: '';

		$data['range-label'] = ( ! empty( $widget_instance_data['range-label'] ) && 'range' === $data['type'] )
			? $widget_instance_data['range-label']
			: '';

		$data['range-points'] = ( ! empty( $widget_instance_data['range-points'] ) && 'range' === $data['type'] )
			? $widget_instance_data['range-points']
			: '';

		return $data;
	}

	public function get_view_data() {
		$widget_type = $this->widget_data['type'];

		$views              = [
			'range'    => 'widgets/products-filters/range',
			'checkbox' => 'widgets/products-filters/checkbox',
		];
		$view_template_path = $views[ $widget_type ];

		$views_data_methods_mapping = [
			'range'    => 'get_range_view_data',
			'checkbox' => 'get_checkbox_view_data',
		];

		$view_data_method = $views_data_methods_mapping[ $widget_type ];

		$view_data = $this->$view_data_method();

		return [
			'view-data'          => $view_data,
			'view-template-path' => $view_template_path,
		];
	}

	private function get_checkbox_view_data() {
		$attribute_id   = $this->widget_data['attribute'];
		$attribute      = wc_get_attribute( $attribute_id );
		$attribute_slug = $attribute->slug;

		$options_terms = get_terms(
			[
				'taxonomy' => $attribute_slug,
			]
		);

		$options = [];
		foreach ( $options_terms as $options_term ) {
			$option_id   = $options_term->term_id;
			$option_name = $options_term->name;

			$options[] = [
				'id'   => $option_id,
				'name' => $option_name,
			];
		}

		if ( empty( $options ) ) {
			return false;
		}

		$name = empty( $this->widget_data['name'] )
			? $attribute->name
			: $this->widget_data['name'];

		return [
			'attribute' => [
				'id'   => $attribute_id,
				'slug' => $attribute_slug,
				'name' => $name,
			],
			'options'   => $options,
		];
	}

	private function get_range_view_data() {
		global $wpdb;

		$attribute_id   = $this->widget_data['attribute'];
		$attribute      = wc_get_attribute( $attribute_id );
		$attribute_slug = $attribute->slug;

		$filters_util          = new Util_Filters();
		$attribute_id_meta_key = $filters_util->get_filter_meta_key_value_name_by_attribute_slug( $attribute_slug );

		$post_meta_table_name = $wpdb->prefix . 'postmeta';

		$min_max_query = $wpdb->prepare(
			"SELECT MIN(meta_value*1) as min, MAX(meta_value*1) as max FROM $post_meta_table_name
						WHERE meta_key = %s",
			$attribute_id_meta_key
		);

		$min_max_query_result = $wpdb->get_results( $min_max_query );

		if ( empty( $min_max_query_result ) ) {
			return false;
		}

		$min_max_data = array_pop( $min_max_query_result );
		$min          = $min_max_data->min;
		$max          = $min_max_data->max;

		$step_query = "
			SELECT
				(length(meta_value) - length(SUBSTRING_INDEX((meta_value), '.', 1)) -1 ) as decimal_points
			FROM {$wpdb->prefix}postmeta
			    WHERE
				    meta_key = 'montala-products-filters_value_$attribute_slug' AND
			    	meta_value LIKE '%.%'";
		$step_query = $wpdb->get_results( $step_query );

		$step = $this->widget_data['step'];

		if ( ! empty( $step_query ) ) {
			$decimal_points = end( $step_query )->decimal_points;
			$step           = '0.' . str_repeat( '0', $decimal_points - 1 ) . '1';
		};

		$name = empty( $this->widget_data['name'] )
			? $attribute->name
			: $this->widget_data['name'];

		$label = isset( $this->widget_data['range-label'] ) && ! empty( $this->widget_data['range-label'] )
			? $this->widget_data['range-label']
			: '';

		$points = isset( $this->widget_data['range-points'] ) && ! empty( $this->widget_data['range-points'] )
			? $this->widget_data['range-points']
			: '';

		return [
			'attribute' => [
				'id'   => $attribute_id,
				'slug' => $attribute_slug,
				'name' => $name,
			],
			'filter'    => [
				'min'    => $min,
				'max'    => $max,
				'step'   => $step,
				'start'  => $min,
				'end'    => $max,
				'label'  => $label,
				'points' => $points,
			],
		];
	}

	public function widget( $widget_data ) {
		$this->widget_data = $widget_data;

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_widget_scripts_and_styles' ], 99 );
		wp_enqueue_scripts();
	}

	public function enqueue_widget_scripts_and_styles() {
		wp_enqueue_script( 'montala-products-filters-admin-script-lib-ion-range-slider' );
		wp_enqueue_script( 'montala-products-filters-admin-script-widget-products-filters' );


		wp_enqueue_style( 'montala-products-filters-style-widget-products-filters' );

		wp_enqueue_style( 'montala-products-filters-style-lib-widget-ion-range-slider' );
		wp_enqueue_style( 'montala-products-filters-style-widget-products-filters-range' );
	}
}