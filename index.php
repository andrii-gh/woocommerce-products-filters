<?php
/*
Plugin Name: WooCommerce | Products Filters
Description: Plugin for filtering products by attributes | prices
Author: Andrii
Version: 1.0.0
Author URI: https://t.me/andrii_tm
Text Domain: plugin-woocommerce-products-filters
Domain Path: /languages/
*/

function plugin_woocommerce_products_filters_run() {
	require_once 'includes/class-autoloader.php';
	$autoloader = new \Plugin\Woocommerce_Products_Filters\Autoloader();
	$autoloader->init();

	require_once 'includes/class-app.php';
	$app = new \Plugin\Woocommerce_Products_Filters\App();
	$app->run();
}

plugin_woocommerce_products_filters_run();