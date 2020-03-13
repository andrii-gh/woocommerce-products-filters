<?php

namespace Plugin\Woocommerce_Products_Filters;

class Widget_Dropdown extends \WP_Widget {
	public function __construct() {
		$id   = Data::$widget_prefix . 'dropdown';
		$name = _x(
			'Dropdown | WPF',
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

		parent::__construct( $id, $name, $options );
	}

	public function widget( $args, $widget_data ) {
		$attribute_id = $widget_data['attribute-id'];

		$attribute      = wc_get_attribute( $attribute_id );
		$attribute_slug = $attribute->slug;

		$options_terms = get_terms(
			[
				'taxonomy'   => $attribute_slug,
				'hide_empty' => false,
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

		$view = new View();
		$view->render(
			'widgets/dropdown',
			(object) [
				'attribute'   => [
					'id'   => $attribute_id,
					'slug' => $attribute_slug,
					'name' => $widget_data['name'],
				],
				'placeholder' => Translator::_x( 'Начинайте вводить', 'Frontend' ),
				'options'     => $options,
			]
		);

		wp_enqueue_script( Enqueue::get_plugin_script_name( 'lib-tokenize2' ) );
		wp_enqueue_style( Enqueue::get_plugin_style_name( 'widget-dropdown' ) );
		wp_enqueue_script( Enqueue::get_plugin_script_name( 'widget-dropdown' ) );
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
			'admin/widgets/dropdown/form',
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