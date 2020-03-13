<?php

namespace Plugin\Woocommerce_Products_Filters;

class Ajax {
	public function init() {
		add_action( Hook::get_hook_name( 'hooks' ), [ $this, 'add_ajax_endpoints' ] );
	}

	public function add_ajax_endpoints() {
		add_action(
			'wp_ajax_plugin_woocommerce_products_filters_filter_products',
			[ $this, 'process_ajax_endpoint_woocommerce_filter_products' ]
		);
		add_action(
			'wp_ajax_nopriv_plugin_woocommerce_products_filters_filter_products',
			[ $this, 'process_ajax_endpoint_woocommerce_filter_products' ]
		);

		add_action(
			'wp_ajax_plugin_woocommerce_products_filters_attribute_values_suggestions',
			[ $this, 'process_ajax_endpoint_woocommerce_attribute_values_suggestions' ]
		);

		add_action(
			'wp_ajax_nopriv_plugin_woocommerce_products_filters_attribute_values_suggestions',
			[ $this, 'process_ajax_endpoint_woocommerce_attribute_values_suggestions' ]
		);
	}

	public function process_ajax_endpoint_woocommerce_filter_products() {
		$filters_data = $_POST['data'];

		$filter = new Filter();
		$filter->init();

		$filtered_result = $filter->get_products( $filters_data );

		$fragments                  = new Fragments();
		$filtered_product_fragments = $fragments->get_fragments( $filtered_result );

		wp_send_json_success( $filtered_product_fragments );
		exit;
	}

	public function process_ajax_endpoint_woocommerce_attribute_values_suggestions() {
		$suggestion = new Suggestion();
		$suggestion->suggest( $_POST );
	}
}