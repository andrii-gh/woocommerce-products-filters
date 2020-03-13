<?php

namespace Plugin\Woocommerce_Products_Filters;

class Util {
	public $product_id_meta_key_prefix = 'montala-products-filters';

	public function process_update_product( $product_id ) {
		$product    = wc_get_product( $product_id );
		$attributes = $product->get_attributes();

		foreach ( $attributes as $attribute ) {
			$attribute_slug = $attribute->get_name();

			$option_id_meta_key    = $this->product_id_meta_key_prefix . '_id_' . $attribute_slug;
			$option_value_meta_key = $this->product_id_meta_key_prefix . '_value_' . $attribute_slug;

			delete_post_meta( $product_id, $option_id_meta_key );
			delete_post_meta( $product_id, $option_value_meta_key );

			$options = $attribute->get_options();

			foreach ( $options as $option_id ) {
				$option_term = get_term( $option_id, $attribute_slug );

				$option_id   = $option_term->term_id;
				$option_name = $option_term->name;

				add_post_meta( $product_id, $option_id_meta_key, $option_id );
				add_post_meta( $product_id, $option_value_meta_key, $option_name );
			}
		}
	}
}