<?php use Plugin\Woocommerce_Products_Filters\Translator; ?>

<div class="pwpf-admin-widget">
	<p class="pwpf-admin-widget-item-title pwpf-admin-widget-item-title_theme_pwpf">
		<?php Translator::esc_html_ex( 'Attribute Name', 'Widget' ) ?>
	</p>
	<label class="pwpf-admin-widget-label pwpf-admin-widget-label_theme_pwpf">
		<select
				name="<?php echo $this->view_data->widget->get_field_name( 'attribute-id' ) ?>"
				autocomplete="off"
				data-pwpf-select="attribute"
				data-pwpf-widget-checkbox="widget-id"
				class="pwpf-admin-widget-select pwpf-admin-widget-select_type_normal">
			<?php foreach ( $this->view_data->attributes_taxonomies as $attribute_taxonomy ) { ?>
				<option
						<?php selected( $this->view_data->data['attribute-id'], $attribute_taxonomy->attribute_id ) ?>
						value="<?php echo esc_attr( $attribute_taxonomy->attribute_id ); ?>">
					<?php echo esc_html( $attribute_taxonomy->attribute_name ) ?>
				</option>
			<?php } ?>
		</select>
	</label>

	<p class="pwpf-admin-widget-item-title pwpf-admin-widget-item-title_theme_pwpf">
		<?php Translator::esc_html_ex( 'Name', 'Widget' ) ?>
	</p>
	<label class="pwpf-admin-widget-label pwpf-admin-widget-label_theme_pwpf">
		<input
				autocomplete="off"
				name="<?php echo $this->view_data->widget->get_field_name( 'name' ); ?>"
				value="<?php echo esc_attr( $this->view_data->data['name'] ); ?>"
				class="pwpf-admin-widget-input-text pwpf-admin-widget-input-text_type_normal">
	</label>
</div>