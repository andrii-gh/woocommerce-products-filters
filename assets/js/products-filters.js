jQuery(function ($) {
    const pluginSettings = window.PluginWooCommerceProductsFiltersData;

    $(document).ready(init);

    function init() {
        initRangeSliders();
        initCheckboxes();
        initActions();
    }

    function initRangeSliders() {
        $('[data-pwpf-range-slider]').each(initRangeSlider);

        $(document).on('montala-products-filters-filter', () => {
            filter();
        });
    }

    function initRangeSlider() {
        const rangeSliderInitElement = $(this);

        const min = rangeSliderInitElement.attr('data-pwpf-widget-slider-prop-min');
        const max = rangeSliderInitElement.attr('data-pwpf-widget-slider-prop-max');

        const start = rangeSliderInitElement.attr('data-pwpf-widget-slider-prop-min');
        const end = rangeSliderInitElement.attr('data-pwpf-widget-slider-prop-max');

        const points = rangeSliderInitElement.attr('data-pwpf-range-slider-data-points');

        const step = rangeSliderInitElement.attr('data-pwpf-range-slider-data-step');

        const options = {
            'min': min,
            'max': max,

            'from': start,
            'to': end,

            'step': 1,

            'grid': true,

            'type': 'double',

            'prefix': '',
            'postfix': '',

            'hideMinMax': false,
            'hideFromTo': false,
        };

        if (points) {
            options['grid_num'] = points;
        }

        if (!points) {
            options['grid_snap'] = true;
        }

        rangeSliderInitElement.ionRangeSlider(options);
    }

    function initCheckboxes() {
        // $('[data-pwpf-expander-switcher]').on('click', toggleCheckboxGroup)
    }

    function initActions() {
        $(document).on('click', '[data-pwpf-filter]', filter);
        $(document).on('click', '[data-pwpf-reset-filters]', resetFilters);
        $(document).on('click', '.woocommerce-pagination ul > li .page-numbers:not(.current)', paginateFilter);
    }

    function toggleCheckboxGroup() {
        const switcher = $(this);
        const name = switcher.attr('data-pwpf-expander-switcher');

        const status = switcher.attr('data-pwpf-expander-status');
        const nextStatuses = {
            'expanded': 'collapsed',
            'collapsed': 'expanded'
        };
        const nextStatus = nextStatuses[status];

        const switchers = $(`[data-pwpf-expander-switcher="${name}"]`);
        const boxes = $(`[data-pwpf-expander-box="${name}"]`);

        switchers.attr('data-pwpf-expander-status', nextStatus);
        boxes.attr('data-pwpf-expander-status', nextStatus);
    }

    function paginateFilter(event) {
        event.preventDefault();

        const $pageNavigator = $(this);
        const page = $pageNavigator.text().trim();

        filter({
            page: page
        });
    }

    function filter(options) {
        const filterOptions = $.extend({}, {
            page: 1,
        }, options);

        if (isFiltering()) {
            return;
        }

        setFilterLoadingStatus();

        const url = pluginSettings['ajax-url'];
        const action = pluginSettings['action-filter'];

        const filterData = collectFiltersData();

        const data = {
            'action': action,
            'data': {
                'page': filterOptions['page'],
                'attributes': filterData,
            }
        };

        $.post(url, data).done(processFrontendFiltering);
    }

    function resetFilters() {
        $('[data-pwpf-option-id]').attr("checked", false);

        $('[data-pwpf-range-slider]').each(function () {
            const rangeElement = $(this);

            const min = rangeElement.attr('data-pwpf-range-slider-data-min');
            const max = rangeElement.attr('data-pwpf-range-slider-data-max');

            const start = rangeElement.attr('data-pwpf-range-slider-data-start');
            const end = rangeElement.attr('data-pwpf-range-slider-data-end');

            const step = rangeElement.attr('data-pwpf-range-slider-data-step');

            const range = rangeElement.data("ionRangeSlider");

            range.update({
                'min': min,
                'max': max,
                'from': start,
                'to': end,
                'step': step
            });
        });
    }

    function isFiltering() {
        const isFiltering = $('body').data('pwpf-is-filtering');

        return isFiltering;
    }

    function setFilterLoadingStatus() {
        const options = $('[data-pwpf-option-id]');
        options.attr('disabled', true);

        const rangeLabel = $('[data-pwpf-filters-range-slider-label]');
        rangeLabel.attr('data-pwpf-filters-loading', true);

        $('[data-pwpf-range-slider]').each(function () {
            const range = $(this).data("ionRangeSlider");

            range.update({
                'disable': true
            });
        });

        const loadingTemplate = `
            <div
                data-pwpf-filters-preloader="true"
                class="pwpf-element-loading pwpf-element-loading_theme-default">
                    <div class="pwpf-element-loading__content pwpf-element-loading__content_theme-default">
                        <div class="pwpf-element-loading-content__preloader pwpf-element-loading-content__preloader_theme-default"></div>
                        <div class="pwpf-element-loading-content__text pwpf-element-loading-content__text_theme-default">
                            ${pluginSettings['loading-text']}
                        </div>
                    </div>
            </div>
        `;

        const productsContainer = getProductsContainer();
        productsContainer.addClass('--pwpf-is-loading');

        $('body')
            .data('pwpf-is-filtering', true)
            .attr('data-pwpf-filters-loading', 'true')
            .append(loadingTemplate);
    }

    function removeLoadingStatus() {
        const options = $('[data-pwpf-option-id]');
        options.removeAttr('disabled');

        const rangeLabel = $('[data-pwpf-filters-range-slider-label]');
        rangeLabel.removeAttr('data-pwpf-filters-loading');

        $('[data-pwpf-range-slider]').each(function () {
            const range = $(this).data("ionRangeSlider");

            range.update({
                'disable': false
            });
        });

        $('[data-pwpf-filters-preloader]').remove();

        const productsContainer = getProductsContainer();
        productsContainer.removeClass('--pwpf-is-loading');

        $('body')
            .data('pwpf-is-filtering', false)
            .removeAttr('data-pwpf-filters-loading', 'true');
    }

    function collectFiltersData() {
        const filtersGroups = {};

        collectCheckboxOptionsFilterData(filtersGroups);
        collectSliderOptionsFilterData(filtersGroups);
        collectDropdownOptionsFilterData(filtersGroups);

        const filtersGroupsKeys = Object.keys(filtersGroups);
        const filtersData = [];

        filtersGroupsKeys.forEach((filterGroupKey) => {
            for (let filterType in filtersGroups[filterGroupKey]) {
                filtersData.push(filtersGroups[filterGroupKey][filterType]);
            }
        });

        return filtersData;
    }

    function collectSliderOptionsFilterData(filtersGroups) {
        $('[data-pwpf-widget-slider]').each(function () {
            const range = $(this);

            collectSliderOptionFilterData(filtersGroups, range);
        });
    }

    function collectSliderOptionFilterData(filters, range) {
        const rangeData = range.data();

        const min = rangeData.from;
        const max = rangeData.to;

        const attributeId = range.attr('data-pwpf-widget-slider');

        if (!(attributeId in filters)) {
            filters[attributeId] = {}
        }

        if (!('slider' in filters[attributeId])) {
            filters[attributeId]['slider'] = {}
        }

        if (!('id' in filters[attributeId]['slider'])) {
            filters[attributeId]['slider']['id'] = attributeId;
        }

        if (!('type' in filters[attributeId]['slider'])) {
            filters[attributeId]['slider']['type'] = 'slider';
        }

        if (!('data' in filters[attributeId]['slider'])) {
            filters[attributeId]['slider']['data'] = {};
        }

        if (!('options' in filters[attributeId]['slider']['data'])) {
            filters[attributeId]['slider']['data'] = {};
        }

        filters[attributeId]['slider']['data'] = {
            'min': min,
            'max': max
        }
    }

    function collectCheckboxOptionsFilterData(filtersGroups) {
        $('[data-pwpf-option-id]:checked').each(function () {
            collectCheckboxOptionFilterData(filtersGroups, $(this));
        });
    }

    function collectDropdownOptionsFilterData(filtersGroups) {
        $('[data-pwpf-search-dropdown]').each(function () {
            const $select = $(this);
            const attributeId = $select.attr('data-pwpf-search-dropdown');

            const $options = $(this).find('option:selected');
            $options.each(function () {
                const $option = $(this);
                const data = {
                    'attributeId': attributeId
                };

                collectDropdownOptionFilerData(filtersGroups, $option, data);
            });
        });
    }

    function collectDropdownOptionFilerData(filtersGroups, option, optionData) {
        const optionValue = option.text();
        const optionId = option.val();
        const attributeId = optionData.attributeId;

        if (!(attributeId in filtersGroups)) {
            filtersGroups[attributeId] = {}
        }

        if (!('dropdown' in filtersGroups[attributeId])) {
            filtersGroups[attributeId]['dropdown'] = {}
        }

        if (!('id' in filtersGroups[attributeId]['dropdown'])) {
            filtersGroups[attributeId]['dropdown']['id'] = attributeId;
        }

        if (!('type' in filtersGroups[attributeId]['dropdown'])) {
            filtersGroups[attributeId]['dropdown']['type'] = 'dropdown';
        }

        if (!('data' in filtersGroups[attributeId]['dropdown'])) {
            filtersGroups[attributeId]['dropdown']['data'] = {};
        }

        if (!('options' in filtersGroups[attributeId]['dropdown']['data'])) {
            filtersGroups[attributeId]['dropdown']['data']['options'] = [];
        }

        filtersGroups[attributeId]['dropdown']['data']['options'].push({
            'value': optionValue,
            'id': optionId,
        });
    }

    function collectCheckboxOptionFilterData(filters, option) {
        const attributeSlug = option.attr('data-mfp-attribute-slug');
        const attributeId = option.attr('data-pwpf-attribute-id');
        const optionId = option.attr('data-pwpf-option-id');

        if (!(attributeId in filters)) {
            filters[attributeId] = {}
        }

        if (!('checkbox' in filters[attributeId])) {
            filters[attributeId]['checkbox'] = {}
        }

        if (!('id' in filters[attributeId]['checkbox'])) {
            filters[attributeId]['checkbox']['id'] = attributeId;
        }

        if (!('type' in filters[attributeId]['checkbox'])) {
            filters[attributeId]['checkbox']['type'] = 'checkbox';
        }

        if (!('data' in filters[attributeId]['checkbox'])) {
            filters[attributeId]['checkbox']['data'] = {};
        }

        if (!('options' in filters[attributeId]['checkbox']['data'])) {
            filters[attributeId]['checkbox']['data']['options'] = [];
        }

        filters[attributeId]['checkbox']['data']['options'].push({
            'id': optionId
        });
    }

    function clearProducts() {
        // const productsContainer = getProductsContainer();
        // productsContainer.html('');
    }

    function processFrontendFiltering(data) {
        clearProducts();

        const htmlPieces = data.data.html;

        for (let selector of Object.keys(htmlPieces)) {
            $(selector).html(htmlPieces[selector]);
        }

        const htmlToReplacePieces =
            'html-replace' in data.data
                ? data.data['html-replace']
                : {};

        for (let selector of Object.keys(htmlToReplacePieces)) {
            $(selector).replaceWith(htmlToReplacePieces[selector]);
        }

        removeLoadingStatus();
    }

    function getProductsContainer() {
        const containerSelector = pluginSettings['items-container-selector'];

        return $(containerSelector);
    }
});