jQuery(function($){
    $(document).ready(init);

    function init() {
        $('[data-pwpf-widget-slider]').each(initSlider)
    }

    function initSlider() {
        const slider = $(this);

        const min = slider.attr('data-pwpf-widget-slider-prop-min');
        const max = slider.attr('data-pwpf-widget-slider-prop-max');

        const start = slider.attr('data-pwpf-widget-slider-prop-min');
        const end = slider.attr('data-pwpf-widget-slider-prop-max');

        const points = slider.attr('data-pwpf-range-slider-data-points');

        // const step = slider.attr('data-pwpf-range-slider-data-step');

        const step = 10;

        slider.ionRangeSlider({
            type: "double",
            skin: 'round',
            min: min,
            max: max,
            from: start,
            to: end,
            grid: true,
            step: 10
        });
    }
});