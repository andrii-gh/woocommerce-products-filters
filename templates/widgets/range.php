<div class="pwpf-accordion pwpf-accordion_theme-default">
	<div
		data-pwpf-expander-status="collapsed"
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
		data-pwpf-expander-status="collapsed"
		data-pwpf-expander-box="<?php esc_attr_e( $this->view_data->box_id ) ?>"
		class="pwpf-widget-checkbox-checkbox pwpf-widget-checkbox-checkbox_theme-default">
		<div class="pwpf-el-range pwpf-el-range_theme-default">
			<label
					class="pwpf-el-range__label pwpf-el-range__label_theme-default"
					for="<?php esc_attr_e( $this->view_data->title_from_id ) ?>">
				<?php esc_html_e( $this->view_data->title_from ) ?>
			</label>
			<input
					id="<?php esc_attr_e( $this->view_data->title_from_id ) ?>"
					data-pwpf-range-from="<?php esc_attr_e( $this->view_data->option_id ) ?>"
					class="pwpf-el-range__input pwpf-el-range__input_theme-default"
					autocomplete="off"
					type="text">
			<label
					class="pwpf-el-range__label pwpf-el-range__label_theme-default"
					for="<?php esc_attr_e( $this->view_data->title_to_id ) ?>">
				<?php esc_html_e( $this->view_data->title_to ) ?>
			</label>
			<input
					id="<?php esc_attr_e( $this->view_data->title_to_id ) ?>"
					data-pwpf-range-to="<?php esc_attr_e( $this->view_data->option_id ) ?>"
					class="pwpf-el-range__input pwpf-el-range__input_theme-default"
					autocomplete="off"
					type="text">
		</div>
	</div>
</div>