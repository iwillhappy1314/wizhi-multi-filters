<?php


/**
 * 快速添加分类方法
 *
 * @since   wizhi 1.0
 *
 * @param string       $tax_slug     分类法名称
 * @param string|array $post_type    关联到的文章类型的名称
 * @param string       $tax_name     分类法菜单名称
 * @param boolean      $hierarchical 是否允许有父级分类
 *
 * @package backend
 *
 * @usage   wprs_tax('prodcat', 'prod', '产品分类', true);
 */
function wprs_tax( $tax_slug, $post_type, $tax_name, $hierarchical = true ) {


	//分类法的标签
	$labels = [
		'name'                       => $tax_name,
		'singular_name'              => $tax_name,
		'menu_name'                  => $tax_name,
		'all_items'                  => sprintf( __( 'All %s', 'wprs' ), $tax_name ),
		'edit_item'                  => sprintf( __( 'Edit %s', 'wprs' ), $tax_name ),
		'view_item'                  => sprintf( __( 'View %s', 'wprs' ), $tax_name ),
		'update_item'                => sprintf( __( 'Upgrade %s', 'wprs' ), $tax_name ),
		'add_new_item'               => sprintf( __( 'Add New %s', 'wprs' ), $tax_name ),
		'new_item_name'              => sprintf( __( 'New %s', 'wprs' ), $tax_name ),
		'parent_item'                => sprintf( __( 'Parent %s', 'wprs' ), $tax_name ),
		'parent_item_colon'          => sprintf( __( 'Parent %s', 'wprs' ), $tax_name ),
		'search_items'               => sprintf( __( 'Search %s', 'wprs' ), $tax_name ),
		'popular_items'              => sprintf( __( 'Popular %s', 'wprs' ), $tax_name ),
		'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'wprs' ), $tax_name ),
		'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'wprs' ), $tax_name ),
		'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', 'wprs' ), $tax_name ),
		'not_found'                  => sprintf( __( 'No %s found.', 'wprs' ), $tax_name ),
	];

	$labels = apply_filters( 'wprs_tax_labels_' . $tax_slug, $labels );

	//分类法参数
	$args = [
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'hierarchical'      => $hierarchical,
		'rewrite'           => [ 'slug' => $tax_slug ],
		'sort'              => true,
	];


	$args      = apply_filters( 'wprs_tax_args_' . $tax_slug, $args );
	$post_type = apply_filters( 'wprs_tax_types_' . $tax_slug, $post_type );

	if ( strlen( $tax_slug ) > 0 ) {
		register_taxonomy( $tax_slug, $post_type, $args );
	}

}