<?php


namespace Plugin\Woocommerce_Products_Filters;

class Fragments {
	public function get_fragments( $filtered_results ) {
		Hook::do_action( 'filtered_results_fragments', $filtered_results );

		return Hook::apply_filters(
			'filtered_results_fragments_filter',
			[
				'html'   => [],
				'locale' => pll_current_language(),
			],
			$filtered_results
		);
	}
}