<?php

use Plugin\Woocommerce_Products_Filters\Data;

?>

<div class="pwpf-admin-widget">
	<p class="pwpf-admin-widget-item-title pwpf-admin-widget-item-title_theme_pwpf">
		<?php echo esc_html_x( 'Reset Filters Button Text', 'Widget', Data::$text_domain ); ?>
	</p>
	<label class="pwpf-admin-widget-label pwpf-admin-widget-label_theme_pwpf">
		<input
			name="<?php echo $this->view_data['widget']->get_field_name('reset-filters-text'); ?>"
			<?php if ( !empty( $this->view_data['data']['reset-filters-text'] ) ) { ?>
				value="<?php echo esc_attr( $this->view_data['data']['reset-filters-text'] ); ?>"
			<?php } ?>
			class="pwpf-admin-widget-input-text pwpf-admin-widget-input-text_type_normal">
	</label>
	<p class="pwpf-admin-widget-item-title pwpf-admin-widget-item-title_theme_pwpf">
		<?php echo esc_html_x( 'Filter Button Text', 'Widget', Data::$text_domain ); ?>
	</p>
	<label class="pwpf-admin-widget-label pwpf-admin-widget-label_theme_pwpf">
		<input
				name="<?php echo $this->view_data['widget']->get_field_name('filter-text'); ?>"
			<?php if ( !empty( $this->view_data['data']['filter-text'] ) ) { ?>
				value="<?php echo esc_attr( $this->view_data['data']['filter-text'] ); ?>"
			<?php } ?>
				class="pwpf-admin-widget-input-text pwpf-admin-widget-input-text_type_normal">
	</label>
</div>