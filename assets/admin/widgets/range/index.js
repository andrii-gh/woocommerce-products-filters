jQuery(function ($) {
    $(document).ready(init);

    function init() {
        initWidget();

        $(document).on('ajaxSuccess', processBackendUpdatedWidget);
        $(document).on('plugin-wpf-admin-saved-widget-range', saveWidget);
    }

    function saveWidget() {
        const widget = $(arguments[1]);
        const selectElement = widget.find('[data-pwpf-select][data-pwpf-widget-range]');

        selectElement.selectize();
    }

    function initWidget() {
        $('[data-pwpf-select][data-pwpf-widget-range]').selectize();
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

        const widgetBase = pluginWoocommerceProductsFiltersWidgetRange.widgetName;

        if (ajaxOptions.data.search('id_base=' + widgetBase) === -1) {
            return;
        }

        widget = $('input.widget-id[value="' + request['widget-id'] + '"]').parents('.widget');

        !XMLHttpRequest.responseText
            ? wpWidgets.save(widget, 0, 1, 0)
            : $(document).trigger('plugin-wpf-admin-saved-widget-range', widget);
    }
});