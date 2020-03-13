<?php

namespace Plugin\Woocommerce_Products_Filters;

class Enqueue {
	public static function get_plugin_script_name( $name ) {
		return Data::$script_prefix . $name;
	}

	public static function get_plugin_style_name( $name ) {
		return Data::$hook_prefix . $name;
	}
}
