<?php

namespace Plugin\Woocommerce_Products_Filters;

class Autoloader {
	public function init() {
		try {
			spl_autoload_register( __NAMESPACE__ . '\Autoloader::autoload' );
		} catch ( \Exception $e ) {
			wp_die( '' );
		}
	}

	public function autoload( $class_name ) {
		if ( false === strpos( $class_name, __NAMESPACE__ ) ) {
			return;
		}
		$class_file = str_replace( __NAMESPACE__ . '\\', '', $class_name );
		$class_file = strtolower( $class_file );
		$class_file = str_replace( '_', '-', $class_file );
		$class_path = explode( '\\', $class_file );
		$class_file = array_pop( $class_path );
		$class_path = implode( '/', $class_path );
		require_once __DIR__ . '/class-' . $class_file . '.php';
	}
}