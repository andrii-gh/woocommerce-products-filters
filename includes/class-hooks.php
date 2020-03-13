<?php

namespace Plugin\Woocommerce_Products_Filters;

class Hooks {
	public function init() {
		add_action( 'widgets_init', [ $this, 'action_widgets_init' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'action_admin_enqueue_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'action_admin_enqueue_styles' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'action_wp_enqueue_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'action_wp_enqueue_styles' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'action_wp_register_scripts' ], PHP_INT_MIN );
		add_action( 'wp_enqueue_scripts', [ $this, 'action_wp_register_styles' ], PHP_INT_MIN );

		add_action( 'woocommerce_update_product', [ $this, 'action_woocommerce_product_updated' ], 10, 1 );

		add_filter( 'query_vars', [ $this, 'filter_query_vars' ] );

		Hook::do_action( 'hooks' );
	}

	public function action_widgets_init() {
		register_widget( 'Plugin\Woocommerce_Products_Filters\Widget_Checkbox' );
		register_widget( 'Plugin\Woocommerce_Products_Filters\Widget_Range' );
		register_widget( 'Plugin\Woocommerce_Products_Filters\Widget_Slider' );
		register_widget( 'Plugin\Woocommerce_Products_Filters\Widget_Dropdown' );
		register_widget( 'Plugin\Woocommerce_Products_Filters\Widget_Actions' );
	}

	public function action_admin_enqueue_scripts() {
		global $pagenow;

		if ( 'widgets.php' === $pagenow ) {
			wp_enqueue_script(
				Enqueue::get_plugin_script_name( 'lib-selectize' ),
				Data::$assets_uri . '/libs/selectize/dist/js/standalone/selectize.min.js',
				[ 'jquery' ],
				Data::$assets_version,
				true
			);
		}

		if ( 'widgets.php' === $pagenow ) {
			wp_enqueue_script(
				Enqueue::get_plugin_script_name( 'admin-widget-checkbox' ),
				Data::$assets_uri . '/admin/widgets/checkbox/index.js',
				[
					'jquery',
					Enqueue::get_plugin_script_name( 'lib-selectize' )
				],
				Data::$assets_version,
				true
			);

			wp_localize_script(
				Enqueue::get_plugin_script_name( 'admin-widget-checkbox' ),
				'pluginWoocommerceProductsFiltersWidgetCheckbox',
				[
					'widgetName' => Data::$widget_prefix . 'checkbox',
				]
			);
		}

		if ( 'widgets.php' === $pagenow ) {
			wp_enqueue_script(
				Enqueue::get_plugin_script_name( 'admin-widget-range' ),
				Data::$assets_uri . '/admin/widgets/range/index.js',
				[
					'jquery',
					Enqueue::get_plugin_script_name( 'lib-selectize' )
				],
				Data::$assets_version,
				true
			);

			wp_localize_script(
				Enqueue::get_plugin_script_name( 'admin-widget-range' ),
				'pluginWoocommerceProductsFiltersWidgetRange',
				[
					'widgetName' => Data::$widget_prefix . 'range',
				]
			);
		}

		if ( 'widgets.php' === $pagenow ) {
			wp_enqueue_script(
				Enqueue::get_plugin_script_name( 'admin-widget-slider' ),
				Data::$assets_uri . '/admin/widgets/slider/index.js',
				[
					'jquery',
					Enqueue::get_plugin_script_name( 'lib-selectize' )
				],
				Data::$assets_version,
				true
			);

			wp_localize_script(
				Enqueue::get_plugin_script_name( 'admin-widget-slider' ),
				'pluginWoocommerceProductsFiltersWidgetSlider',
				[
					'widgetName' => Data::$widget_prefix . 'slider',
				]
			);
		}

		if ( 'widgets.php' === $pagenow ) {
			wp_enqueue_script(
				Enqueue::get_plugin_script_name( 'admin-widget-dropdown' ),
				Data::$assets_uri . '/admin/widgets/dropdown/index.js',
				[
					'jquery',
					Enqueue::get_plugin_script_name( 'lib-selectize' )
				],
				Data::$assets_version,
				true
			);

			wp_localize_script(
				Enqueue::get_plugin_script_name( 'admin-widget-dropdown' ),
				'pluginWoocommerceProductsFiltersWidgetDropdown',
				[
					'widgetName' => Data::$widget_prefix . 'dropdown',
				]
			);
		}
	}

	public function action_admin_enqueue_styles() {
		global $pagenow;

		if ( 'widgets.php' === $pagenow ) {
			wp_enqueue_style(
				'montala-products-filters-admin-style-lib-selectize',
				Data::$assets_uri . '/libs/selectize/dist/css/selectize.css',
				[],
				Data::$assets_version
			);
		}

		if ( 'widgets.php' === $pagenow ) {
			wp_enqueue_style(
				'montala-products-filters-admin-style-products-filters-widget',
				Data::$assets_uri . '/admin/css/products-filters-widget.css',
				[ 'montala-products-filters-admin-style-lib-selectize' ],
				Data::$assets_version
			);
		}
	}

	public function action_wp_enqueue_scripts() {
	}

	public function action_wp_enqueue_styles() {

	}

	public function action_wp_register_scripts() {
		wp_register_script(
			Enqueue::get_plugin_script_name( 'lib-ion-range-slider' ),
			Data::$assets_uri . '/libs/ion-range-slider/js/ion-range-slider.min.js',
			[ 'jquery' ],
			Data::$assets_version,
			true
		);

		wp_register_script(
			Enqueue::get_plugin_script_name( 'lib-tokenize2' ),
			Data::$assets_uri . '/libs/tokenize2/tokenize2.js',
			[ 'jquery' ],
			Data::$assets_version,
			true
		);

		wp_register_script(
			Enqueue::get_plugin_script_name( 'products-filters' ),
			Data::$assets_uri . '/js/products-filters.js',
			[
				'jquery',
			],
			Data::$assets_version,
			true
		);

		wp_localize_script(
			Enqueue::get_plugin_script_name( 'products-filters' ),
			'PluginWooCommerceProductsFiltersData',
			[
				'ajax-url'                 => admin_url( 'admin-ajax.php' ),
				'action-filter'            => 'plugin_woocommerce_products_filters_filter_products',
				'loading-text'             => Translator::esc_html_x( 'Loading', 'Frontend' ),
				'items-container-selector' => Hook::apply_filters( 'items_container_css_selector', '' ),
			]
		);

		wp_register_script(
			Enqueue::get_plugin_script_name( 'widget-checkbox' ),
			Data::$assets_uri . '/widgets/checkbox/index.js',
			[ 'jquery' ],
			Data::$assets_version,
			true
		);

		wp_register_script(
			Enqueue::get_plugin_script_name( 'widget-slider' ),
			Data::$assets_uri . '/widgets/slider/index.js',
			[
				'jquery',
				Enqueue::get_plugin_script_name( 'lib-ion-range-slider' ),
			],
			Data::$assets_version,
			true
		);

		wp_register_script(
			Enqueue::get_plugin_script_name( 'widget-range' ),
			Data::$assets_uri . '/widgets/range/index.js',
			[ 'jquery' ],
			Data::$assets_version,
			true
		);

		wp_register_script(
			Enqueue::get_plugin_script_name( 'widget-dropdown' ),
			Data::$assets_uri . '/widgets/dropdown/index.js',
			[
				'jquery',
				Enqueue::get_plugin_script_name( 'lib-tokenize2' ),
			],
			Data::$assets_version,
			true
		);

		wp_localize_script(
			Enqueue::get_plugin_script_name( 'widget-dropdown' ),
			'pluginWoocommerceProductsFiltersWidgetDropdown',
			[
				'widgetName'         => Data::$widget_prefix . 'checkbox',
				'ajax-url'           => admin_url( 'admin-ajax.php' ),
				'action-suggestions' => 'plugin_woocommerce_products_filters_attribute_values_suggestions',
			]
		);
	}

	public function action_wp_register_styles() {
		wp_register_style(
			Enqueue::get_plugin_script_name( 'lib-ion-range-slider' ),
			Data::$assets_uri . '/libs/ion-range-slider/css/ion-range-slider.css',
			[],
			Data::$assets_version
		);

		wp_register_style(
			Enqueue::get_plugin_style_name( 'widget-checkbox' ),
			Data::$assets_uri . '/widgets/checkbox/index.css',
			[],
			Data::$assets_version
		);

		wp_register_style(
			Enqueue::get_plugin_style_name( 'widget-range' ),
			Data::$assets_uri . '/widgets/range/index.css',
			[],
			Data::$assets_version
		);

		wp_register_style(
			Enqueue::get_plugin_style_name( 'widget-slider' ),
			Data::$assets_uri . '/widgets/slider/index.css',
			[],
			Data::$assets_version
		);

		wp_register_style(
			Enqueue::get_plugin_style_name( 'widget-dropdown' ),
			Data::$assets_uri . '/widgets/dropdown/index.css',
			[],
			Data::$assets_version
		);

		wp_register_style(
			Enqueue::get_plugin_style_name( 'widget-actions' ),
			Data::$assets_uri . '/widgets/actions/index.css',
			[],
			Data::$assets_version
		);

		wp_register_style(
			Enqueue::get_plugin_style_name( 'component-box' ),
			Data::$assets_uri . '/components/box/index.css',
			[],
			Data::$assets_version
		);

		wp_register_style(
			Enqueue::get_plugin_style_name( 'products-filters' ),
			Data::$assets_uri . '/css/products-filters.css',
			[],
			Data::$assets_version
		);
	}

	public function action_woocommerce_product_updated( $product_id ) {
		$util = new Util();
		$util->process_update_product( $product_id );
	}

	public function filter_query_vars( $query_vars ) {
		$query_vars[] = 'plugin_wpf_filter';

		return $query_vars;
	}
}