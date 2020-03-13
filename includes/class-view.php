<?php

namespace Plugin\Woocommerce_Products_Filters;

class View {
	private $view_data;

	public function render( $path, $view_data ) {
		$views_path = Data::$view_path;
		$view_path = $views_path . DIRECTORY_SEPARATOR . $path . '.php';

		$this->view_data = $view_data;

		require $view_path;

		$this->view_data = null;
	}

	public function get_rendered_content( $path, $data ) {
		ob_start();
		$this->render( $path, $data );

		return ob_get_clean();
	}
}