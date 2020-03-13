<div class="pwpf-accordion pwpf-accordion_theme-default">
	<div
		data-pwpf-expander-status="collapsed"
		data-pwpf-expander-switcher="<?php echo esc_attr( $this->view_data['data']['attribute']['id'] ); ?>"
		class="pwpf-accordion__title pwpf-accordion__title_theme-default">
		<div class="pwpf-accordion__title-text pwpf-accordion__title-text_theme-default">
			<?php echo esc_html( $this->view_data['data']['attribute']['name'] ); ?>
		</div>
		<div class="pwpf-accordion__title-switcher pwpf-accordion__title-switcher_theme-default">
			<svg viewBox="0 0 448 512"><path fill="currentColor" d="M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z"></path></svg>
		</div>
	</div>

	<div
			data-pwpf-expander-status="collapsed"
			data-pwpf-expander-box="<?php echo esc_attr( $this->view_data['data']['attribute']['id'] ); ?>" class="pwpf-widget-products-filter-checkbox pwpf-widget-products-filter-checkbox_theme-default">
		<?php foreach ( $this->view_data['data']['options'] as $option ) { ?>
			<label class="pwpf-widget-products-filter-checkbox__item pwpf-widget-products-filter-checkbox__item_theme-default">
				<input
						class="
							pwpf-widget-products-filter-checkbox__item-field
							pwpf-widget-products-filter-checkbox__item-field_theme-default"
						type="checkbox"
						data-pwpf-option-id="<?php echo esc_attr( $option['id'] ); ?>"
						data-mfp-attribute-slug="<?php echo esc_attr( $this->view_data['data']['attribute']['slug'] ); ?>">
				<span class="
							pwpf-widget-products-filter-checkbox__item-title
							pwpf-widget-products-filter-checkbox__item-title_theme-default">
					<?php echo esc_html( $option['name'] ); ?>
				</span>
				<span
						class="
							pwpf-widget-products-filter-checkbox__item-icon
							pwpf-widget-products-filter-checkbox__item-icon_theme-default"></span>
			</label>
		<?php } ?>
	</div>
</div>