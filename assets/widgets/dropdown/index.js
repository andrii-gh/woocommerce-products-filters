jQuery(function ($) {
    const pluginSettings = window.pluginWoocommerceProductsFiltersWidgetDropdown;

    $(document).ready(init);

    function init() {
        $('[data-pwpf-search-dropdown-select]').each(initSearchDropdownSelect);
    }

    function initSearchDropdownSelect() {
        const dropdown = $(this);
        const placeholder = dropdown.attr('data-pwpf-search-dropdown-placeholder');

        dropdown.tokenize2({
            dataSource: processDataSource,
            searchFromStart: false,
            inputPlaceholder: placeholder,
            debounce: 200
        });
    }

    function processDataSource(search, object) {
        const url = pluginSettings['ajax-url'];
        const action = pluginSettings['action-suggestions'];

        const dropdownAttribute = 'data-pwpf-search-dropdown-select';
        const $select = object.element;
        const attributeId = $select.attr(dropdownAttribute);

        const data = {
            'action': action,
            'data': {
                'value-to-search': search,
                'attribute-id': attributeId,
                'lang': pluginSettings['lang']
            }
        };

        $.post(url, data).done(completeSuggestions.bind(object, object));
    }

    function completeSuggestions(tokenizeObject, data) {
        const values = data.data.values;
        const items = values.map((value) => {
            return {
                text: value['text'],
                value: value['option']
            }
        });


        // const $items = [
        //     {
        //         text: 'Author' + Math.random(),
        //         value: 'Author' + Math.random()
        //     },
        //     {
        //         text: 'Author1' + Math.random(),
        //         value: 'Author1' + Math.random()
        //     },
        // ];

        console.log(items);

        tokenizeObject.trigger('tokenize:dropdown:fill', [items]);
    }
});