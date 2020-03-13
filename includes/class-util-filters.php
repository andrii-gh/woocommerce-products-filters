<?php

namespace Plugin\Woocommerce_Products_Filters;

class Util_Filters {
	public function get_filters_types() {
		$filters_types = [
			'range'    => _x( 'Range', 'Filters', Data::$text_domain ),
			'checkbox' => _x( 'Checkbox', 'Filters', Data::$text_domain ),
		];

		return apply_filters( 'montala-products-filters-util-filters-types', $filters_types );
	}

	public function get_meta_key_prefix() {
		return 'montala-products-filters';
	}

	public function get_filter_meta_key_value_name_by_attribute_slug( $attribute_slug ) {
		return $this->get_meta_key_prefix() . '_value_' . $attribute_slug;
	}

	public function get_filter_meta_key_id_name_by_attribute_slug( $attribute_slug ) {
		return $this->get_meta_key_prefix() . '_id_' . $attribute_slug;
	}
}
