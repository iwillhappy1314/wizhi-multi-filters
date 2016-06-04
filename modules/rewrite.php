<?php

/**
 * 生成指定文章类型的所有重定向规则
 *
 * @link       http://tigerton.se
 * @since      1.0.0
 *
 * 必须被挂载到generate_rewrite_rules的函数调用，以便全局的 $wp_rewrite->preg_index 函数返回正确值。
 *
 */
class Wizhi_Filters_Rewrite_Rules {

	/**
	 * 此重定向规则允许一个文章类型被所有可能自定义分类法参数过滤，同时添加自定义查询参数
	 *
	 * @param string|object $post_type  需要创建重定向规则的文章类型
	 * @param array         $query_vars 可选参数，附加的不是自定义文章类型的查询参数，创建类似'/query_var/(.+)/ 的查询参数'
	 *
	 * @since    1.0.0
	 * @return array
	 */
	public function generate_rewrite_rules( $post_type, $excluded_taxonomies = [ ], $query_vars = [ ] ) {

		global $wp_rewrite;

		// 如果不是文章类型对象，获取对象
		if ( ! is_object( $post_type ) ) {
			$post_type = get_post_type_object( $post_type );
		}

		// 获取分类方法
		$new_rewrite_rules = [ ];
		$taxonomies        = get_object_taxonomies( $post_type->name, 'objects' );

		// 添加自定义分类法过滤到查询参数
		foreach ( $taxonomies as $taxonomy ) {
			if ( ! empty( $excluded_taxonomies ) ) {
				if ( $taxonomy->rewrite[ 'slug' ] != '' ) {
					if ( ! in_array( $taxonomy->rewrite[ 'slug' ], $excluded_taxonomies ) ) {
						$query_vars[] = $taxonomy->query_var;
					}
				} else {
					if ( ! in_array( $taxonomy->query_var, $excluded_taxonomies ) ) {
						$query_vars[] = $taxonomy->query_var;
					}
				}
			} else {

				$query_vars[] = $taxonomy->query_var;

			}
		}

		$query_vars[] = 'q';

		// 遍历所有的查询参数组合
		for ( $i = 1; $i <= count( $query_vars ); $i ++ ) {

			$new_rewrite_rule = $post_type->rewrite[ 'slug' ] . '/';
			$new_query_string = 'index.php?post_type=' . $post_type->name;

			// 把重定向规则和查询前置
			for ( $n = 1; $n <= $i; $n ++ ) {
				$new_rewrite_rule .= '(' . implode( '|', $query_vars ) . ')/(.+?)/';
				$new_query_string .= '&' . $wp_rewrite->preg_index( $n * 2 - 1 ) . '=' . $wp_rewrite->preg_index( $n * 2 );
			}

			// 添加分页支持
			$new_paged_rewrite_rule = $new_rewrite_rule . 'page/([0-9]{1,})/';
			$new_paged_query_string = $new_query_string . '&paged=' . $wp_rewrite->preg_index( $i * 2 + 1 );

			// 让网址最后的斜杠是可选的
			$new_paged_rewrite_rule = $new_paged_rewrite_rule . '?$';
			$new_rewrite_rule       = $new_rewrite_rule . '?$';

			// 添加新重定向规则
			$new_rewrite_rules = [
				                     $new_paged_rewrite_rule => $new_paged_query_string,
				                     $new_rewrite_rule       => $new_query_string,
			                     ] + $new_rewrite_rules;
		}

		return $new_rewrite_rules;

	}

}