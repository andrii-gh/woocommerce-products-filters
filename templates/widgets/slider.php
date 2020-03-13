<div class="pwpf-accordion pwpf-accordion_theme-default">
	<div
			data-pwpf-expander-status="expanded"
			data-pwpf-expander-switcher="<?php esc_attr_e( $this->view_data->box_id ) ?>"
			class="pwpf-accordion__title pwpf-accordion__title_theme-default">
		<div class="pwpf-accordion__title-text pwpf-accordion__title-text_theme-default">
			<?php esc_html_e( $this->view_data->name ) ?>
		</div>
		<div class="pwpf-accordion__title-switcher pwpf-accordion__title-switcher_theme-default">
			<svg viewBox="0 0 448 512">
				<path fill="currentColor"
						d="M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z"></path>
			</svg>
		</div>
	</div>

	<div
			data-pwpf-expander-status="expander"
			data-pwpf-expander-box="<?php esc_attr_e( $this->view_data->box_id ) ?>"
			class="pwpf-el-box pwpf-el-box_theme-default">
		<div class="pwpf-el-slider pwpf-el-slider_theme-default">
			<label class="pwpf-el-slider__container pwpf-el-slider__container_theme-default">
				<input
						id="<?php esc_attr_e( $this->view_data->slider_id ) ?>"
						data-pwpf-widget-slider="<?php esc_attr_e( $this->view_data->attribute_id ) ?>"
						data-pwpf-widget-slider-prop-from="<?php esc_attr_e( $this->view_data->from ) ?>"
						data-pwpf-widget-slider-prop-to="<?php esc_attr_e( $this->view_data->to ) ?>"
						data-pwpf-widget-slider-prop-min="<?php esc_attr_e( $this->view_data->min ) ?>"
						data-pwpf-widget-slider-prop-max="<?php esc_attr_e( $this->view_data->max ) ?>"
						autocomplete="off"
						type="text"
						name="test-slider"
						class="pwpf-el-slider-input pwpf-el-slider-input_theme-default">
			</label>
		</div>
	</div>
</div>