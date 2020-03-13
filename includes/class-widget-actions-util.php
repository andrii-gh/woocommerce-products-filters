<?php

namespace Plugin\Woocommerce_Products_Filters;

class Widget_Actions_Util {
	private $widget;
	private $widget_data;

	public function set_widget( $widget ) {
		$this->widget = $widget;
	}

	public function get_data( $widget_instance_data ) {
		$data = [];

		$data['reset-filters-text'] = empty( $widget_instance_data['reset-filters-text'] )
			? ''
			: $widget_instance_data['reset-filters-text'];

		$data['filter-text'] = empty( $widget_instance_data['filter-text'] )
			? ''
			: $widget_instance_data['filter-text'];

		return $data;
	}

	public function get_view_data() {
		$filter_text = empty( $this->widget_data['filter-text'] )
			? _x( 'Filter', 'Widget', Data::$text_domain )
			: $this->widget_data['filter-text'];

		$reset_filters_text = empty( $this->widget_data['reset-filters-text'] )
			? _x( 'Reset filters', 'Widget', Data::$text_domain )
			: $this->widget_data['reset-filters-text'];

		$view_template_path = 'widgets/filters-actions/filters-actions';

		$view_data = [
			'filter-text'        => $filter_text,
			'reset-filters-text' => $reset_filters_text,
		];

		return [
			'view-data'          => $view_data,
			'view-template-path' => $view_template_path,
		];
	}

	public function widget( $widget_data ) {
		$this->widget_data = $widget_data;

//		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_widget_scripts_and_styles' ], 99 );
//		wp_enqueue_scripts();
	}

	public function enqueue_widget_scripts_and_styles() {

	}
}