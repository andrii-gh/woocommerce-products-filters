<?php if ( empty ( $this->view_data['data']['filter']['label'] ) ) { ?>
	<div class="pwpf-widget-products-filter-title pwpf-widget-products-filter-title_theme-default">
		<?php echo esc_html( $this->view_data['data']['attribute']['name'] ); ?>
	</div>
<?php } ?>

<?php if ( ! empty ( $this->view_data['data']['filter']['label'] ) ) { ?>
	<div class="pwpf-widget-products-filter-title pwpf-widget-products-filter-title_theme-default">
		<span><?php echo esc_html( $this->view_data['data']['attribute']['name'] ); ?></span>
		<?php if ( ! empty ( $this->view_data['data']['filter']['label'] ) ) { ?>
			<span>(<?php echo esc_html( $this->view_data['data']['filter']['label'] ); ?>)</span>
		<?php } ?>
	</div>
<?php } ?>

<label
		class="pwpf-widget-products-filter-range pwpf-widget-products-filter-range_theme-default"
		data-pwpf-filters-range-slider-label="<?php echo esc_attr( $this->view_data['data']['filter']['id'] ); ?>">
	<input
			data-pwpf-range-slider="<?php echo esc_attr( $this->view_data['data']['filter']['id'] ); ?>"
			data-pwpf-attribute-id="<?php echo esc_attr( $this->view_data['data']['attribute']['id'] ); ?>"
			data-mfp-attribute-slug="<?php echo esc_attr( $this->view_data['data']['attribute']['slug'] ); ?>"
			data-pwpf-range-slider-data-min="<?php echo esc_attr( $this->view_data['data']['filter']['min'] ); ?>"
			data-pwpf-range-slider-data-max="<?php echo esc_attr( $this->view_data['data']['filter']['max'] ); ?>"
			data-pwpf-range-slider-data-start="<?php echo esc_attr( $this->view_data['data']['filter']['start'] ); ?>"
			data-pwpf-range-slider-data-end="<?php echo esc_attr( $this->view_data['data']['filter']['end'] ); ?>"

			<?php if ( ! empty( $this->view_data['data']['filter']['points'] ) ) { ?>
				data-pwpf-range-slider-data-points="<?php echo esc_attr( $this->view_data['data']['filter']['points'] ); ?>"
			<?php } ?>

			data-pwpf-range-slider-data-label="<?php echo esc_attr( $this->view_data['data']['filter']['end'] ); ?>"
			data-pwpf-range-slider-data-step="<?php echo esc_attr( $this->view_data['data']['filter']['step'] ); ?>">
</label>