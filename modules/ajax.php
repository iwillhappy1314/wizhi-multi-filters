<?php

add_action( 'wp_ajax_wizhi_filter', 'wizhi_filter_callback' );


/**
 * 根据前台提交的文章类型，返回关联到文章类型的分类方法
 */
function wizhi_filter_callback() {

	$wizhi_type         = isset( $_GET[ 'type' ] ) ? $_GET[ 'type' ] : '';
	$taxonomy_array     = [ ];
	$taxonomy_array_all = [ ];

	// 获取所有分类法
	$args_tax = [
		'object_type' => [ $wizhi_type ],
		'public'      => true,
		'_builtin'    => false,
	];

	$taxonomies = get_taxonomies( $args_tax, 'objects', 'and' );

	foreach ( $taxonomies as $taxonomy ):
		$taxonomy_array[ 'name' ]  = $taxonomy->name;
		$taxonomy_array[ 'label' ] = $taxonomy->label;
		$taxonomy_array_all[]      = $taxonomy_array;
	endforeach;

	echo json_encode( $taxonomy_array_all );

	die();
}
