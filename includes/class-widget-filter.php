<?php

namespace Plugin\Woocommerce_Products_Filters;

class Widget_Filter extends \WP_Widget {
	private $widget_util;

	public function __construct() {
		$id   = 'plugin_woocommerce_products_filters_widget_filter';
		$name = _x(
			'Filter | WPF',
			'Widget',
			Data::$text_domain
		);

		$options = [
			'classname'   => $id,
			'description' => _x(
				'Customize parameters for filtering products',
				'Widget',
				Data::$text_domain
			),
		];

		$this->widget_util = new Widget_Filter_Util();
		$this->widget_util->set_widget( $this );

		parent::__construct( $id, $name, $options );
	}

	public function widget( $args, $widget_data ) {
		$this->widget_util->widget( $widget_data );
		$view_data = $this->widget_util->get_view_data();

		$view = new View();
		$view->render(
			$view_data['view-template-path'],
			[
				'args' => $args,
				'data' => $view_data['view-data'],
			]
		);
	}

	public function form( $instance ) {
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		$util_filters  = new Util_Filters();
		$filters_types = $util_filters->get_filters_types();

		$data = $this->widget_util->get_data( $instance );

		$view = new View();
		$view->render(
			'admin/widgets/products-filters/form',
			[
				'widget'               => $this,
				'widget-id'            => $this->id,
				'data'                 => $data,
				'attribute_taxonomies' => $attribute_taxonomies,
				'filters_types'        => $filters_types,
			]
		);
	}

	public function update( $new_instance, $old_instance ) {
		if ( isset( $new_instance['name'] ) ) {
			$new_instance['name'] = trim( $new_instance['name'] );
		}

		return $new_instance;
	}
}