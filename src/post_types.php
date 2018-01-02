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

if ( ! function_exists( "wizhi_create_types" ) ) {

	function wizhi_create_types( $slug, $name, $support, $is_publish, $icon = 'dashicons-networking' ) {

		//文章类型的标签
		$labels = [
			'name'               => $name,
			'singular_name'      => $name,
			'add_new'            => __( 'Add New ', 'wizhi' ) . $name,
			'add_new_item'       => __( 'Add New ', 'wizhi' ) . $name,
			'edit_item'          => __( 'Edit ', 'wizhi' ) . $name,
			'new_item'           => __( 'New ', 'wizhi' ) . $name,
			'all_items'          => __( 'All ', 'wizhi' ) . $name,
			'view_item'          => __( 'View ', 'wizhi' ) . $name,
			'search_items'       => __( 'Search ', 'wizhi' ) . $name,
			'not_found'          => sprintf( __( 'Could not find %s', 'wizhi' ), $name ),
			'not_found_in_trash' => sprintf( __( 'Could not find %s in trash', 'wizhi' ), $name ),
			'menu_name'          => $name,
		];

		$labels = apply_filters( 'wizhi_type_labels' . $slug, $labels );

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
			// 'capability_type'     => [ $slug, Inflector::pluralize( $slug ) ],
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'supports'            => $support,
			'has_archive'         => $is_publish,
			'rewrite'             => [ 'slug' => $slug ],
			'query_var'           => $is_publish,

		];


		$args = apply_filters( 'wizhi_type_args' . $slug, $args );

		if ( strlen( $slug ) > 0 ) {
			register_post_type( $slug, $args );
		}

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
if ( ! function_exists( "wizhi_create_taxs" ) ) {

	function wizhi_create_taxs( $tax_slug, $post_type, $tax_name, $hierarchical ) {

		//分类法的标签
		$labels = [
			'name'                       => $tax_name,
			'singular_name'              => $tax_name,
			'menu_name'                  => $tax_name,
			'all_items'                  => __( 'All ', 'wizhi' ) . $tax_name,
			'edit_item'                  => __( 'Edit ', 'wizhi' ) . $tax_name,
			'view_item'                  => __( 'View ', 'wizhi' ) . $tax_name,
			'update_item'                => __( 'Upgrade ', 'wizhi' ) . $tax_name,
			'add_new_item'               => __( 'Add New ', 'wizhi' ) . $tax_name,
			'new_item_name'              => sprintf( __( 'New %s', 'wizhi' ), $tax_name ),
			'parent_item'                => __( 'Parent ', 'wizhi' ) . $tax_name,
			'parent_item_colon'          => __( 'Parent ', 'wizhi' ) . $tax_name,
			'search_items'               => __( 'Search ', 'wizhi' ) . $tax_name,
			'popular_items'              => __( 'Popular ', 'wizhi' ) . $tax_name,
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'wizhi' ), $tax_name ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'wizhi' ), $tax_name ),
			'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', 'wizhi' ), $tax_name ),
			'not_found'                  => sprintf( __( 'No %s found.', 'wizhi' ), $tax_name ),
		];

		$labels = apply_filters( 'wprs_tax_labels' . $tax_slug, $labels );

		//分类法参数
		$args = [
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'hierarchical'      => $hierarchical,
			'rewrite'           => [ 'slug' => $tax_slug ],
			'sort'              => true,
		];


		$args      = apply_filters( 'wprs_tax_args' . $tax_slug, $args );
		$post_type = apply_filters( 'wprs_tax_types' . $tax_slug, $post_type );

		if ( strlen( $tax_slug ) > 0 ) {
			register_taxonomy( $tax_slug, $post_type, $args );
		}

		flush_rewrite_rules();
	}

}