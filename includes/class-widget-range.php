<?php

namespace Plugin\Woocommerce_Products_Filters;

class Widget_Range extends \WP_Widget {
	public function __construct() {
		$id   = Data::$widget_prefix . 'range';
		$name = _x(
			'Range | WPF',
			'Widget',
			Data::$text_domain
		);

		$options = [
			'classname'   => $id,
			'description' => _x(
				'Customize parameters for filtering products by range',
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
			'widgets/range',
			(object) [
				'title_to'      => $widget_data['title-to'],
				'title_to_id'   => microtime( false ),
				'title_from'    => $widget_data['title-from'],
				'title_from_id' => microtime( false ),
				'option_id'     => $widget_data['attribute-id'],
				'name'          => $widget_data['name'],
				'box_id'        => microtime( false ),
			]
		);

		wp_enqueue_style( Enqueue::get_plugin_style_name( 'widget-range' ) );
		wp_enqueue_script( Enqueue::get_plugin_script_name( 'widget-range' ) );
		wp_enqueue_scripts();
	}

	public function form( $instance ) {
		if ( ( ! isset( $instance['attribute-id'] ) ) || empty( $instance['attribute-id'] ) ) {
			$instance['attribute-id'] = 0;
		}

		if ( ! ( isset( $instance['name'] ) ) || empty( $instance['name'] ) ) {
			$instance['name'] = '';
		}

		if ( ! ( isset( $instance['title-from'] ) ) || empty( $instance['title-from'] ) ) {
			$instance['title-from'] = '';
		}

		if ( ! ( isset( $instance['title-to'] ) ) || empty( $instance['title-to'] ) ) {
			$instance['title-to'] = '';
		}

		$view = new View();
		$view->render(
			'admin/widgets/range/form',
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