<?php

namespace Plugin\Woocommerce_Products_Filters;

class Hook {
	public static function get_hook_name( $name ) {
		return Data::$hook_prefix . $name;
	}

	public static function apply_filters( $name, ...$args ) {
		$filter_name = self::get_hook_name( $name );

		return apply_filters( $filter_name, ...$args );
	}

	public static function do_action( $name, ...$args ) {
		$action_name = self::get_hook_name( $name );

		do_action( $action_name, ...$args );
	}
}