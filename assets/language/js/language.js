//language pills for switching multilingual fields
(function ($) {
    $(function () {
        hideLanguageFields();

        $(document).on('click', '.language-pills a', function (e) {
            var lang = $(this).attr('data-lang');
            $('div[data-toggle="multilang"]').hide();
            $('div[data-lang="' + lang + '"]').show();
        });

        $(document).on("mediaDetailsLoaded", function (e) {
            hideLanguageFields();
        });

    });

    function hideLanguageFields() {
        $('div[data-toggle="multilang"]').not('.in').hide();
    }

})(jQuery);
