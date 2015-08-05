(function ($) {
    function initStyler() {
        $('input:checkbox').styler();
        $('input:radio').styler();
        $('input:file').not(".fileinput-button input:file").styler();
        $('select').styler();

        $('.select-on-check-all').on('click', function () {
            var checkAllCheckbox = $(this).find('input:checkbox.select-on-check-all');
            $('.grid-view input:checkbox').not($(checkAllCheckbox)).each(function () {
                this.checked = $(checkAllCheckbox).prop("checked");
                $(this).trigger('refresh');
            });
        });
    }

    $(function () {
        initStyler();
    });

    $(document).on('pjax:complete', function () {
        initStyler();
    });

})(jQuery);