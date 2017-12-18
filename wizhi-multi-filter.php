<?php
/*
Plugin Name:        Wizhi Multi Filters
Plugin URI:         https://www.wpzhiku.com/wizhi-multi-filters/
Description:        为WordPress的文章类型添加按照自定义分类法进行多条件筛选的功能。
Version:            1.8.4
Author:             Amos Lee
Author URI:         https://www.wpzhiku.com/
License:            MIT License
License URI:        http://opensource.org/licenses/MIT
*/

define( 'WIZHI_FILTER', plugin_dir_path( __FILE__ ) );


if ( version_compare( phpversion(), '5.6.0', '<' ) ) {

    // 显示警告信息
    if ( is_admin() ) {
        add_action( 'admin_notices', function () {
            printf( '<div class="error"><p>' . __( '您当前的PHP版本（%1$s）不符合插件要求, 请升级到 PHP %2$s 或更新的版本， 否则插件没有任何作用。', 'wizhi' ) . '</p></div>', phpversion(), '5.6.0' );
        } );
    }

    return;
}

//快速添加文章类型和分类法
require_once( WIZHI_FILTER . 'modules/post_types.php' );
require_once( WIZHI_FILTER . 'modules/filter.php' );

// 根据当前查询的文章类型，字段判断文章类型和分类法
function wizhi_multi_filters() {
    $current_query = get_queried_object();

    if ( ! is_tax() ) {
        $post_type = [ $current_query->name ];
    } else {
        $taxonomy_object = get_taxonomy( $current_query->taxonomy );
        $post_type       = $taxonomy_object->object_type;
    }

    $args_tax   = [
        'object_type' => $post_type,
        'public'      => true,
        '_builtin'    => false,
    ];
    $taxonomies = get_taxonomies( $args_tax, 'names', 'and' );

    $filters = new Wizhi_Filter( $post_type, $taxonomies, false );

    return $filters;
}


/**
 * 显示默认的CSS
 */
add_action( 'wp_head', function () { ?>
    <style>
        .wizhi-btns, .wizhi-form {
            margin: 10px 0;
        }

        .wizhi-select {
            padding: 0 0 8px 0;
            margin: 0 0 8px 0;
            border-bottom: 1px solid #ddd;
        }

        .wizhi-select a {
            padding: 2px 5px;
        }

        .wizhi-select strong {
            padding-right: 5px;
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
} );
