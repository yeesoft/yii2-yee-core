var Editable = function ($) {
    var settings = {};

    var initComponents = function () {

        $('body').off('click', '.editable .input a').on('click', '.editable .input a', function () {
            var editable = $(this).closest('.editable');
            editable.find('.input').hide();
            editable.find('.input-group').css('display', 'table');
        });

        $('body').off('click', '.editable .input-group .btn').on('click', '.editable .input-group .btn', function () {
            var editable = $(this).closest('.editable');
            var text = editable.find('.input-group input').val();

            editable.find('.input a').text(text);
            editable.find('.input').show();
            editable.find('.input-group').hide();
        });

    };

    return {
        init: function () {
            if (typeof EditableSettings !== "undefined") {
                $.extend(true, settings, EditableSettings);
            }

            initComponents();
        }
    };

}(jQuery);

jQuery(document).ready(function () {
    Editable.init();
});