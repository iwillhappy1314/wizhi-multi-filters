jQuery(document)
    .ready(function ($) {

       $('#add-row').on('click', function() {
            var row = $('#repeatable-fieldset-one tr.repeatable-fieldset:last').clone(true);
            row.removeClass('empty-row screen-reader-text');
            row.insertAfter('#repeatable-fieldset-one tbody>tr:last').addClass('new-row');
            return false;
        });

        $('.remove-row').on('click', function() {
            $(this).parents('tr.new-row').remove();
            return false;
        });

        $('#repeatable-fieldset-one tbody').sortable({
            opacity: 0.6,
            revert: true,
            cursor: 'move',
            handle: '.sort'
        });

        // 根据文章类型获取分类法
        $("#wizhi-type")
            .on('change', function () {

                $("#wizhi-tax").html('<span class="spinner is-active"></span>');

                $.ajax({
                    type: "get",
                    url: ajaxurl,
                    data: {
                        'action': 'wizhi_filter',
                        "type": $(this).val()
                    },
                    dataType: "json",
                    success: function (data) {
                        $("#wizhi-tax").empty();
                        $.each(data, function (i, item) {
                            $("#wizhi-tax")
                                .append('<div><label><input type="checkbox" name="to_filter_tax[]" value="' + item.name + '">' + item.label) + '</label> </div>';
                        });
                    }
                });

            });

    });