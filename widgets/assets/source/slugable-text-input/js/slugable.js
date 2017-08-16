var Slugable = function ($) {
    var settings = {};
    var dependencies = {};

    var initComponents = function () {

        $('body').off('click', '.editable .input-group .btn').on('click', '.editable .input-group .btn', function () {
            var editable = $(this).closest('.editable');
            var text = editable.find('.input-group input').val().trim();
            var slug = slugify(text);

            editable.find('.input-group input').val(slug);
            editable.find('.input a').text(slug);
            editable.find('.input').show();
            editable.find('.input-group').hide();
        });

        $('.editable .input a').click(function () {
            var editable = $(this).closest('.editable');
            editable.find('.input').hide();
            editable.find('.input-group').css('display', 'table');
        });
    };

    var isEqualRelatedSlugs = function (slugInputId, attributeInputId) {
        var parentSlug = slugify($('#' + attributeInputId).val().trim());
        var currentSlug = $('#' + slugInputId).val().trim();
        return (currentSlug.length === 0) || (parentSlug === currentSlug);
    };

    return {
        init: function () {
            if (typeof SlugableSettings !== "undefined") {
                $.extend(true, settings, SlugableSettings);
            }

            initComponents();
        },
        addDependency: function (slugInputId, attributeInputId) {
            dependencies[slugInputId] = attributeInputId;
            var editable = isEqualRelatedSlugs(slugInputId, attributeInputId);
            $('#' + attributeInputId).data('editable', editable);

            $('body').off('input', '#' + attributeInputId).on('input', '#' + attributeInputId, function () {
                if ($(this).data('editable')) {
                    var editable = $('#' + slugInputId).closest('.editable');
                    var slug = slugify($(this).val().trim());

                    editable.find('.input-group input').val(slug);
                    editable.find('.input a').text(slug);
                }
            });

            $('body').off('input', '#' + slugInputId).on('input', '#' + slugInputId, function () {
                var editable = isEqualRelatedSlugs(slugInputId, attributeInputId);
                $('#' + attributeInputId).data('editable', editable);
            });
        }
    };

}(jQuery);

jQuery(document).ready(function () {
    Slugable.init();
});