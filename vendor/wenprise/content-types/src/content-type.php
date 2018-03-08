<?php

/**
 * 快速添加文章类型
 *
 * @since   wizhi 1.0
 *
 * @param string  $slug         文章类型名称
 * @param string  $name         文章类型菜单名称
 * @param array   $support      文章类型支持的功能
 * @param boolean $is_publish   文章类型是否在前后台可见
 * @param boolean $hierarchical 文章是否分级显示
 * @param string  $icon         后台使用的 dashicon 图标
 *
 * @package backend
 *
 * @usage   wprs_types( 'prod', '产品', [ 'title', 'editor', 'thumbnail' ], true );
 */
function wprs_types( $slug, $name, $support, $is_publish, $hierarchical = false, $icon = 'dashicons-networking' ) {

	//文章类型的标签
	$labels = [
		'name'               => $name,
		'singular_name'      => $name,
		'add_new'            => sprintf( __( 'Add New %s', 'wprs' ), $name ),
		'add_new_item'       => sprintf( __( 'Add New %s', 'wprs' ), $name ),
		'edit_item'          => sprintf( __( 'Edit %s', 'wprs' ), $name ),
		'new_item'           => sprintf( __( 'New %s', 'wprs' ), $name ),
		'all_items'          => sprintf( __( 'All %s', 'wprs' ), $name ),
		'view_item'          => sprintf( __( 'View %s', 'wprs' ), $name ),
		'search_items'       => sprintf( __( 'Search %s', 'wprs' ), $name ),
		'not_found'          => sprintf( __( 'Could not find %s', 'wprs' ), $name ),
		'not_found_in_trash' => sprintf( __( 'Could not find %s in trash', 'wprs' ), $name ),
		'menu_name'          => $name,
	];

	$labels = apply_filters( 'wprs_type_labels_' . $slug, $labels );

	//注册文章类型需要的参数
	$args = [
		'labels'              => $labels,
		'description'         => '',
		'public'              => $is_publish,
		'exclude_from_search' => ! $is_publish,
		'publicly_queryable'  => $is_publish,
		'show_ui'             => true,
		'show_in_nav_menus'   => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => $icon,
		'map_meta_cap'        => true,
		'hierarchical'        => $hierarchical,
		'supports'            => $support,
		'has_archive'         => $is_publish,
		'rewrite'             => [ 'slug' => $slug ],
		'query_var'           => $is_publish,

	];


	$args = apply_filters( 'wprs_type_args_' . $slug, $args );

	if ( strlen( $slug ) > 0 ) {
		register_post_type( $slug, $args );
	}
}