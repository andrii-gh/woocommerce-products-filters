<?php

namespace Plugin\Woocommerce_Products_Filters;

class Translator {
	public static function esc_html_x( $string, $context ) {
		return esc_html_x( $string, $context, Data::$text_domain );
	}

	public static function esc_html_ex( $string, $context ) {
		echo self::esc_html_x( $string, $context );
	}

	public static function esc_html_e( $string ) {
		return esc_html_e( $string, Data::$text_domain );
	}

	public static function esc_html_attr_ex( $string, $context ) {
		echo esc_attr_x( $string, $context, Data::$text_domain );
	}

	public static function esc_attr_x( $string, $context ) {
		return esc_attr_x( $string, $context, Data::$text_domain );
	}

	public static function esc_attr_ex( $string, $context ) {
		echo self::esc_attr_x( $string, $context );
	}

	public static function _x( $string, $context ) {
		return _x( $string, $context, Data::$text_domain );
	}

	public static function __( $string, $context ) {
		return __( $string, Data::$text_domain );
	}

	public static function _nx( $singular, $plural, $number, $context ) {
		$string = _nx( $singular, $plural, $number, $context );

		return sprintf( $string, $number );
	}

	public static function esc_html_nx( $singular, $plural, $number, $context ) {
		return esc_html( self::_nx( $singular, $plural, $number, $context ) );
	}

	public static function esc_html_nxe( $singular, $plural, $number, $context ) {
		esc_html_e( self::_nx( $singular, $plural, $number, $context ) );
	}

	public static function is_locale_equals_to( $locale ) {
		return determine_locale() === $locale;
	}
}