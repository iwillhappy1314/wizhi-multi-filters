<?php
/**
 * 快速添加文章类型
 *
 * @since wizhi 1.0
 *
 * @param string  $slug       文章类型名称
 * @param string  $name       文章类型菜单名称
 * @param array   $support    文章类型支持的功能
 * @param boolean $is_publish 文章类型是否在前后台可见
 */

if ( !function_exists ("wizhi_create_types") ) {

    function wizhi_create_types( $slug, $name, $support, $is_publish ) {

    	//文章类型的标签
    	$labels_type = array(
    		'name'               => $name,
    		'singular_name'      => $name,
    		'add_new'            => '添加' . $name,
    		'add_new_item'       => '添加新' . $name,
    		'edit_item'          => '编辑' . $name,
    		'new_item'           => '新' . $name,
    		'all_items'          => '所有' . $name,
    		'view_item'          => '查看' . $name,
    		'search_items'       => '搜索' . $name,
    		'not_found'          => '没有找到' . $name,
    		'not_found_in_trash' => '没有在回收站中找到' . $name,
    		'menu_name'          => $name,
    	);

    	//注册文章类型需要的参数
    	$args_type = array(
    		'labels'             => $labels_type,
    		'public'             => $is_publish,
    		'publicly_queryable' => $is_publish,
    		'show_ui'            => true,
    		'show_in_menu'       => true,
    		'query_var'          => true,
    		'rewrite'            => array( 'slug' => $slug ),
    		'capability_type'    => 'post',
    		'has_archive'        => $is_publish,
    		'hierarchical'       => false,
    		'menu_position'      => 5,
    		'supports'           => $support,
    	);

    	register_post_type( $slug, $args_type );

    	flush_rewrite_rules();
    }

}


/**
 * 快速添加分类方法
 *
 * @since wizhi 1.0
 *
 * @param string  $tax_slug     分类法名称
 * @param string  $hook_type    关联到的文章类型的名称
 * @param string  $tax_name     分类法菜单名称
 * @param boolean $hierarchical 是否允许有父级分类
 */
if ( !function_exists ("wizhi_create_taxs") ) {

    function wizhi_create_taxs( $tax_slug, $hook_type, $tax_name, $hierarchical ) {

    	//分类法的标签
    	$labels_tax = array(
    		'name'              => $tax_name,
    		'singular_name'     => $tax_name,
    		'search_items'      => '搜索' . $tax_name,
    		'all_items'         => '所有' . $tax_name,
    		'parent_item'       => '父级' . $tax_name,
    		'parent_item_colon' => '父级' . $tax_name,
    		'edit_item'         => '编辑' . $tax_name,
    		'update_item'       => '更新' . $tax_name,
    		'add_new_item'      => '添加新' . $tax_name,
    		'new_item_name'     => '新' . $tax_name . '名称',
    		'menu_name'         => $tax_name,
    	);

    	//分类法参数
    	$args_tax = array(
    		'hierarchical'      => $hierarchical,
    		'labels'            => $labels_tax,
    		'show_ui'           => true,
    		'show_admin_column' => true,
    		'query_var'         => true,
    		'rewrite'           => array( 'slug' => $tax_slug ),
    	);

    	register_taxonomy( $tax_slug, array( $hook_type ), $args_tax );

    	flush_rewrite_rules();
    }
    
}

?>