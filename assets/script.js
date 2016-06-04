jQuery(document)
    .ready(function ($) {

        // 添加更多
        $('#add-row')
            .on('click', function () {
                var row = $('.repeatable-fieldset:last-child').clone(true);
                row.addClass('new-row');
                row.insertAfter('.repeatable-fieldset:last-child');
                return false;
            });

        // 移除添加的元素
        $('.remove-row')
            .on('click', function () {
                $(this).parents('tr.new-row').remove();
                return false;
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