<?php

// 设置默认的分类方法
$wizhi_default_tax = [
    'name'  => ['品牌', '产地'],
    'label' => ['brand', 'area'],
];


/**
 * 获取默认的文章类型名称
 *
 * @return mixed|string|void
 */
function wizhi_get_post_type_name()
{
    return (get_option('wizhi_type_name')) ? get_option('wizhi_type_name') : '产品';
}


/**
 * 获取默认的文章类型标签
 *
 * @return string   分类法标签
 */
function wizhi_get_post_type_label()
{
    return (get_option('wizhi_type_label')) ? get_option('wizhi_type_label') : 'prod';
}


/**
 * 获取默认的分类法
 *
 * @return array 自定义分类法 label=>name 数组
 */
function wizhi_get_taxonomies()
{

    // 设置默认的分类方法
    $wizhi_default_tax = [
        'label'  => ['品牌', '产地'],
        'name' => ['brand', 'area'],
    ];

    $wizhi_saved_tax = (get_option('wizhi_tax')) ? get_option('wizhi_tax') : $wizhi_default_tax;

    return array_combine($wizhi_saved_tax[ 'label' ], $wizhi_saved_tax[ 'name' ]);
}


/**
 * 添加默认的文章类型和分类法
 */
add_action('init', 'wizhi_filter_add_types');
function wizhi_filter_add_types()
{

    $use_build_in_types = get_option('wizhi_use_type_tax');

    if ($use_build_in_types) {

        if (function_exists('wprs_types')) {
            wprs_types(wizhi_get_post_type_name(), wizhi_get_post_type_label(), ['title', 'editor', 'author', 'thumbnail', 'comments'], true);
        }

        if (function_exists('wprs_tax')) {
            foreach (wizhi_get_taxonomies() as $label => $name) :
                wprs_tax($label, wizhi_get_post_type_name(), $name, true);
            endforeach;
        }
    }
}
