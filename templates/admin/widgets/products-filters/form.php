<?php

use Plugin\Woocommerce_Products_Filters\Data;

?>

<div
		class="pwpf-admin-widget">
	<p class="pwpf-admin-widget-item-title pwpf-admin-widget-item-title_theme_pwpf">
		<?php echo esc_html_x( 'Attribute Name', 'Widget', Data::$text_domain ); ?>
	</p>
	<label class="pwpf-admin-widget-label pwpf-admin-widget-label_theme_pwpf">
		<select name="<?php echo $this->view_data['widget']->get_field_name('attribute'); ?>"
			data-pwpf-select="attribute"
			data-pwpf-select-type="search"
			class="pwpf-admin-widget-select pwpf-admin-widget-select_type_normal">
			<?php foreach( $this->view_data['attribute_taxonomies'] as $attribute_taxonomy ) { ?>
				<option
						<?php selected( $this->view_data['data']['attribute'], $attribute_taxonomy->attribute_id, true ); ?>
						value="<?php echo esc_attr( $attribute_taxonomy->attribute_id ); ?>">
					<?php echo esc_html( $attribute_taxonomy->attribute_name ); ?>
				</option>
			<?php } ?>
		</select>
	</label>

	<p class="pwpf-admin-widget-item-title pwpf-admin-widget-item-title_theme_pwpf">
		<?php echo esc_html_x( 'Filter Type', 'Widget', Data::$text_domain ); ?>
	</p>
	<label class="pwpf-admin-widget-label pwpf-admin-widget-label_theme_pwpf">
		<select name="<?php echo $this->view_data['widget']->get_field_name('type'); ?>"
				data-pwpf-select="filter"
				data-pwpf-widget="<?php echo esc_attr( $this->view_data['widget-id'] ); ?>"
				data-pwpf-select-group="type"
				data-pwpf-select-type="search"
				class="pwpf-admin-widget-select pwpf-admin-widget-select_type_normal">
			<?php foreach( $this->view_data['filters_types'] as $filter_id => $filter_name ) { ?>
				<option
					<?php selected( $this->view_data['data']['type'], $filter_id, true ); ?>
						value="<?php echo esc_attr($filter_id); ?>">
					<?php echo esc_html( $filter_name ); ?>
				</option>
			<?php } ?>
		</select>
	</label>

	<p class="pwpf-admin-widget-item-title pwpf-admin-widget-item-title_theme_pwpf">
		<?php echo esc_html_x( 'Name', 'Widget', Data::$text_domain ); ?>
	</p>
	<label class="pwpf-admin-widget-label pwpf-admin-widget-label_theme_pwpf">
		<input
				name="<?php echo $this->view_data['widget']->get_field_name('name'); ?>"
				<?php if ( !empty( $this->view_data['data']['name'] ) ) { ?>
					value="<?php echo esc_attr( $this->view_data['data']['name'] ); ?>"
				<?php } ?>
				class="pwpf-admin-widget-input-text pwpf-admin-widget-input-text_type_normal">
	</label>

	<div
		<?php if ( $this->view_data['data']['type'] !== 'range' ) { ?>
			style="display: none;"
		<?php } ?>
			data-pwpf-widget="<?php echo esc_attr( $this->view_data['widget-id'] ); ?>"
			data-pwpf-group="range">

		<p class="pwpf-admin-widget-item-title pwpf-admin-widget-item-title_theme_pwpf">
			<?php echo esc_html_x( 'Label', 'Widget', Data::$text_domain ); ?>
		</p>
		<label class="pwpf-admin-widget-label pwpf-admin-widget-label_theme_pwpf">
			<input
					name="<?php echo $this->view_data['widget']->get_field_name('range-label'); ?>"
				<?php if ( ! empty( $this->view_data['data']['range-label'] ) ) { ?>
					value="<?php echo esc_attr( $this->view_data['data']['range-label'] ); ?>"
				<?php } ?>
					class="pwpf-admin-widget-input-text pwpf-admin-widget-input-text_type_normal">
		</label>

		<p class="pwpf-admin-widget-item-title pwpf-admin-widget-item-title_theme_pwpf">
			<?php echo esc_html_x( 'Step', 'Widget', Data::$text_domain ); ?>
		</p>
		<label class="pwpf-admin-widget-label pwpf-admin-widget-label_theme_pwpf">
			<input
					name="<?php echo $this->view_data['widget']->get_field_name('step'); ?>"
				<?php if ( !empty( $this->view_data['data']['step'] ) ) { ?>
					value="<?php echo esc_attr( $this->view_data['data']['step'] ); ?>"
				<?php } ?>
					class="pwpf-admin-widget-input-text pwpf-admin-widget-input-text_type_normal">
		</label>

		<p class="pwpf-admin-widget-item-title pwpf-admin-widget-item-title_theme_pwpf">
			<?php echo esc_html_x( 'Points', 'Widget', Data::$text_domain ); ?>
		</p>
		<label class="pwpf-admin-widget-label pwpf-admin-widget-label_theme_pwpf">
			<input
					name="<?php echo $this->view_data['widget']->get_field_name('range-points'); ?>"
				<?php if ( !empty( $this->view_data['data']['range-points'] ) ) { ?>
					value="<?php echo esc_attr( $this->view_data['data']['range-points'] ); ?>"
				<?php } ?>
					class="pwpf-admin-widget-input-text pwpf-admin-widget-input-text_type_normal">
		</label>
	</div>
</div>