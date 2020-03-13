<?php

namespace Plugin\Woocommerce_Products_Filters;

class Data {
	public static $assets_version;
	public static $hook_prefix;
	public static $text_domain;
	public static $plugin_path;
	public static $view_path;
	public static $plugin_uri;
	public static $assets_uri;
	public static $widget_prefix;
	public static $script_prefix;
	public static $style_prefix;
	public static $db_table_prefix;
	public static $options_prefix;

	static function init() {
		self::$assets_version = '1.0.903';

		self::$text_domain = 'plugin-woocommerce-products-filters';

		self::$hook_prefix   = 'plugin_woocommerce_products_filters_';
		self::$widget_prefix = 'plugin_woocommerce_products_filters_widget_';

		self::$plugin_path = dirname( __FILE__, 2 );
		self::$plugin_uri  = plugin_dir_url( dirname( __FILE__, 1 ) );

		self::$view_path  = self::$plugin_path . DIRECTORY_SEPARATOR . 'templates';
		self::$assets_uri = self::$plugin_uri . 'assets';

		self::$script_prefix = 'plugin-woocommerce-products-filters-script';
		self::$style_prefix  = 'plugin-woocommerce-products-filters-style';

		self::$db_table_prefix = 'plugin_wpf_';
		self::$options_prefix  = 'plugin_wpf_';
	}
}