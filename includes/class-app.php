<?php

namespace Plugin\Woocommerce_Products_Filters;

class App {
	function run() {
		Data::init();

		$ajax = new Ajax();
		$ajax->init();

		$builder = new Builder();
		$builder->init();

		$actions = new Hooks();
		$actions->init();
	}
}