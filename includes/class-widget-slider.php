<?php

namespace Plugin\Woocommerce_Products_Filters;

class Widget_Slider extends \WP_Widget {
	public function __construct() {
		$id   = Data::$widget_prefix . 'slider';
		$name = _x(
			'Slider | WPF',
			'Widget',
			Data::$text_domain
		);

		$options = [
			'classname'   => $id,
			'description' => _x(
				'Customize parameters for filtering products by slider',
				'Widget',
				Data::$text_domain
			),
		];

		parent::__construct( $id, $name, $options );
	}

	public function widget( $args, $widget_data ) {
		global $wpdb;

		$wp_table_prefix                                   = $wpdb->prefix;
		$attributes_values_numeric_table_name              = Data::$db_table_prefix . 'attributes_values_numeric';
		$prefixed_products_attributes_values_table_numeric = $wp_table_prefix . $attributes_values_numeric_table_name;

		$min_max_query_results = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT MIN(value) as min_value, MAX(value) as max_value FROM $prefixed_products_attributes_values_table_numeric WHERE `attribute_id` = %d",
				$widget_data['attribute-id']
			)
		);

		$min = $min_max_query_results->min_value;
		$max = $min_max_query_results->max_value;

		$view = new View();
		$view->render(
			'widgets/slider',
			(object) [
				'name'         => $widget_data['name'],
				'from'         => $min,
				'to'           => $max,
				'min'          => $min,
				'max'          => $max,
				'attribute_id' => $widget_data['attribute-id'],
				'box_id'       => microtime( true ),
				'slider_id'    => microtime( true ),
			]
		);

		wp_enqueue_style( Enqueue::get_plugin_style_name( 'component-box' ) );
		wp_enqueue_style( Enqueue::get_plugin_style_name( 'widget-slider' ) );
		wp_enqueue_script( Enqueue::get_plugin_script_name( 'lib-ion-range-slider' ) );
		wp_enqueue_script( Enqueue::get_plugin_script_name( 'widget-slider' ) );
		wp_enqueue_scripts();
	}

	public function form( $instance ) {
		if ( ( ! isset( $instance['attribute-id'] ) ) || empty( $instance['attribute-id'] ) ) {
			$instance['attribute-id'] = 0;
		}

		if ( ! ( isset( $instance['name'] ) ) || empty( $instance['name'] ) ) {
			$instance['name'] = '';
		}

		$view = new View();
		$view->render(
			'admin/widgets/slider/form',
			(object) [
				'widget'                => $this,
				'attributes_taxonomies' => wc_get_attribute_taxonomies(),
				'data'                  => $instance,
			]
		);
	}

	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
}