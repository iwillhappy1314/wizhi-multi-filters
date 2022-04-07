<?php

/**
 * 旧的过滤方法，已弃用
 *
 * @param string $post_type
 * @param array  $taxonomies
 *
 * @return \Wizhi\Filter\Legacy
 */
function wizhi_multi_filters($post_type = '', $taxonomies = [])
{
    $current_query = get_queried_object();

    /**
     * 获取文章类型
     */
    if ( ! $post_type) {
        if ( ! is_tax()) {
            $post_type = [$current_query->name];
        } else {
            $taxonomy_object = get_taxonomy($current_query->taxonomy);
            $post_type       = $taxonomy_object->object_type;
        }
    }

    /**
     * 获取分类方法
     */
    if ( ! $taxonomies) {
        $args_tax = [
            'object_type' => $post_type,
            'public'      => true,
            '_builtin'    => false,
        ];

        $taxonomies = get_taxonomies($args_tax, 'names', 'and');
    }

    return new Wizhi\Filter\Legacy($post_type, $taxonomies, false);
}


/**
 * 新的过滤方法，可以按需显示需要的元素
 *
 * @param string $post_type
 * @param array  $taxonomies
 *
 * @return \Wizhi\Filter\Filter
 */
function wizhi_filter($post_type = '', $taxonomies = [])
{

    $current_query = get_queried_object();

    if ( ! $post_type) {
        if ( ! is_tax()) {
            $post_type = [$current_query->name];
        } else {
            $taxonomy_object = get_taxonomy($current_query->taxonomy);
            $post_type       = $taxonomy_object->object_type;
        }
    }

    if ( ! $taxonomies) {
        $args_tax   = [
            'object_type' => $post_type,
            'public'      => true,
            '_builtin'    => false,
        ];
        $taxonomies = get_taxonomies($args_tax, 'names', 'and');
    }

    return new Wizhi\Filter\Filter($post_type, $taxonomies);
}


/**
 * 显示默认的CSS
 */
add_action('wp_head', function ()
{
    $hide_css = get_option('hide_css');

    if ($hide_css) {
        return;
    }

    ?>
    <style>
        .wizhi-buttons, .wizhi-form {
            margin: 10px 0;
        }

        .wizhi-select {
            padding: 0;
            margin: 0;
            display: flex;
        }

        .wizhi-select + .wizhi-select {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
        }

        .wizhi-select a {
            padding: 2px 5px;
        }

        .wizhi-select strong {
            padding-right: 5px;
            min-width: 80px;
        }

        .wizhi-select a.selected {
            color: #337AB7;
        }

        .wizhi-btn-group {
            margin-top: 10px;
            display: inline-block;
        }

        .wizhi-btn {
            color: #333;
            vertical-align: top;
            background-color: #efefef;
            border: 1px solid #ccc;
            padding: 3px 8px;
            font-size: 14px;
            line-height: 1.42857143;
        }

        .wizhi-btn-close {
            margin-left: -5px;
        }

        .wizhi-search {
            padding: 3px !important;
            line-height: 1.3 !important;
            font-size: 14px;
            vertical-align: top !important;
        }
    </style>
    <?php
});