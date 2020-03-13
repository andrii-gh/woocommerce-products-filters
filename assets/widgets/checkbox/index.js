jQuery(function ($) {
    $(document).ready(init);

    function init() {
        $('[data-pwpf-expander-switcher]').on('click', toggleCheckboxGroup);
        $(document).on('change', '[data-pwpf-checkbox-attribute-id]', onChangeCheckbox);
    }

    function onChangeCheckbox() {
        const attributeName = 'data-pwpf-checkbox-attribute-id';
        const attributeId = $(this).attr(attributeName);

        const pickedCount = $(`[${attributeName}="${attributeId}"]:checked`).length;

        switch (true) {
            case (pickedCount === 0): {
                $(`[data-pwpf-hint-attribute-count="${attributeId}"]`).remove();

                break;
            }
            case (pickedCount > 0): {
                $(`[data-pwpf-hint-attribute-keeper="${attributeId}"]`).append(`
                    <div class="ybk-el-hint ybk-el-hint--type-checkbox ybk-el-hint--theme-default" data-pwpf-hint-attribute-count="${attributeId}">${pickedCount}</div>
                `);

                break;
            }
        }

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
});