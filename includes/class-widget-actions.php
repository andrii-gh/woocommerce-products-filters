<?php

namespace Plugin\Woocommerce_Products_Filters;

class Widget_Actions extends \WP_Widget {
	private $widget_util;

	public function __construct() {
		$id   = 'montala_product_filters_actions_widget';
		$name = _x(
			'Actions | WPF',
			'Widget',
			Data::$text_domain
		);

		$options = [
			'classname'   => $id,
			'description' => _x(
				'Customize parameters for filtering products actions',
				'Widget',
				Data::$text_domain
			),
		];

		$this->widget_util = new Widget_Actions_Util();
		$this->widget_util->set_widget( $this );

		parent::__construct( $id, $name, $options );
	}

	public function widget( $args, $widget_data ) {
		$this->widget_util->widget( $widget_data );
		$view_data = $this->widget_util->get_view_data();

		$view = new View();
		$view->render(
			'widgets/actions',
			[
				'args' => $args,
				'data' => $view_data['view-data'],
			]
		);

		wp_enqueue_style( Enqueue::get_plugin_style_name( 'widget-actions' ) );
		wp_enqueue_script( Enqueue::get_plugin_script_name( 'widget-actions' ) );
		wp_enqueue_script( Enqueue::get_plugin_script_name( 'products-filters' ) );

		wp_enqueue_scripts();
	}

	public function form( $instance ) {
		$data = $this->widget_util->get_data( $instance );

		$view = new View();
		$view->render(
			'admin/widgets/actions-widget-form',
			[
				'widget' => $this,
				'data'   => $data,
			]
		);
	}

	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
}