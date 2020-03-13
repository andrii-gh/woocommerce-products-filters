jQuery(function ($) {
    $(document).ready(init);

    function init() {
        initFiltersWidget();

        $(document).on('ajaxSuccess', processBackendUpdatedWidget);
        $(document).on('mpf-plugin-admin-saved-widget-products-filters', saveProductsFiltersWidget);
        $(document).on('change', '[data-pwpf-widget][data-pwpf-select-group]', changeFiltersGroup);
    }

    function changeFiltersGroup() {
        const groupPicker = $(this);

        const groupName = groupPicker.val();
        const widgetId = groupPicker.attr('data-pwpf-widget');

        const groupCssDisplay = '';
        const groupStatus = 'enabled';

        const notGroupCssDisplay = 'none';
        const notGroupStatus = 'disabled';

        const notGroup = $(`[data-pwpf-group!="${groupName}"][data-pwpf-widget="${widgetId}"]`);
        const group = $(`[data-pwpf-group="${groupName}"][data-pwpf-widget="${widgetId}"]`);

        group
            .css('display', groupCssDisplay)
            .attr('data-pwpf-group-status', groupStatus);

        notGroup
            .css('display', notGroupCssDisplay)
            .attr('data-pwpf-group-status', notGroupStatus)
    }

    function saveProductsFiltersWidget() {
        const widget = $(arguments[1]);
        const selectElement = widget.find('[data-pwpf-select][data-pwpf-select-type="search"]');

        selectElement.selectize();
    }

    function initFiltersWidget() {
        $('[data-pwpf-select][data-pwpf-select-type="search"]').selectize();
    }

    function processBackendUpdatedWidget(event, XMLHttpRequest, ajaxOptions) {
        let request = {}, pairs = ajaxOptions.data.split('&'), i, split, widget;

        for (i in pairs) {
            split = pairs[i].split('=');
            request[decodeURIComponent(split[0])] = decodeURIComponent(split[1]);
        }

        if (!request.action) {
            return;
        }

        if (request.action !== 'save-widget') {
            return;
        }

        const productsFiltersWidgetBase = 'montala_product_filters_widget';

        if (ajaxOptions.data.search('id_base=' + productsFiltersWidgetBase) === -1) {
            return;
        }

        widget = $('input.widget-id[value="' + request['widget-id'] + '"]').parents('.widget');

        !XMLHttpRequest.responseText
            ? wpWidgets.save(widget, 0, 1, 0)
            : $(document).trigger('mpf-plugin-admin-saved-widget-products-filters', widget);
    }
});