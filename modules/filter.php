<?php

/**
 * Class Wizhi_Filter
 */
class Wizhi_Filter {


	/*
	 * 要显示的过滤条件的自定义分类法名称
	 *
	 * @var array
	 */
	private $post_types;
	private $taxonomies = [ ];
	private $hide_search;

	function __construct( $post_types, $taxonomies, $hide_search ) {

		// 获取参数
		$this->post_types  = $post_types;
		$this->taxonomies  = $taxonomies;
		$this->hide_search = $hide_search;

		add_action( 'generate_rewrite_rules', [ $this, 'add_rewrite_rules' ] );

		// 显示多条件过滤元素
		$this->wizhi_multi_filters();
		$this->wizhi_filter_search();
		$this->wizhi_current_filter();
	}

	/**
	 * 获取分类法过滤链接
	 *
	 * @param array $taxonomies 需要显示的过滤条件的自定义分类法名称
	 */
	public function wizhi_multi_filters() {

		$taxonomies = $this->taxonomies;

		echo '<div class="wizhi-selects">';

		foreach ( $taxonomies as $taxonomy ) :

			$tax = get_taxonomy( $taxonomy ); ?>

			<div class="wizhi-select">
				<strong><?php echo $tax->label; ?></strong>
				<?php

				$query_var = $taxonomy;

				$terms = get_terms( $taxonomy );

				$is_all = get_query_var( $query_var ) ? '' : 'selected';
				$count  = count( $terms );

				$origin_url = $_SERVER[ 'REQUEST_URI' ];

				if ( $count > 0 ) :

					echo '<a class="' . $is_all . '" href="' . remove_query_arg( $query_var ) . '">所有</a>';
					foreach ( $terms as $term ) :
						if ( get_query_var( $query_var ) == $term->slug ) {
							echo '<a href="' . remove_query_arg( $query_var ) . '" class="selected">' . $term->name . "</a>";
						} else {
							echo '<a href="' . add_query_arg( [ $query_var => $term->slug, 'paged' => false ] ) . '">' . $term->name . "</a>";
						}
					endforeach;

				endif;

				?>
			</div>

			<?php

		endforeach;

		echo '</div>';

	}


	// 添加重定向规则
	public function add_rewrite_rules() {

		global $wp_rewrite;
		// 获取需要筛选的文章类型和分类法
		$post_types = $this->post_types;
		$taxonomies = $this->taxonomies;

		// 排除默认分类法方法
		$taxonomies = [
			'category',
			'post_tag',
		];

		// Polylang 支持
		if ( function_exists( 'pll_current_language' ) ) {
			array_push( $taxonomies, 'language' );
		}

		// 搜索字符串
		$addtion_var = [ 'q' ];

		if ( $post_types ) {
			// 初始化重定向类
			$rewrite_rule_class = new Wizhi_Filters_Rewrite_Rules();
			foreach ( $post_types as $post_type ) {
				// 为每个文章类型运行此功能
				$new_rules         = $rewrite_rule_class->generate_rewrite_rules( $post_type, $taxonomies, $addtion_var );
				$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
			}
		}

	}


	/**
	 * 显示当前选择的过滤项目
	 *
	 */
	public function wizhi_current_filter() {

		$taxonomies             = $this->taxonomies;

		echo '<div class="wizhi-btns">';

		foreach ( $taxonomies as $taxonomy ) :

			// 获取查询变量值
			$query_var = $taxonomy;
			$query_value        = get_query_var( $query_var );
			$wizhi_show_current = get_option( 'wizhi_show_current' );

			// 如果获取的查询变量值非空
			if ( ! empty( $query_value ) && ! $wizhi_show_current ) :

				$term = get_term_by( 'slug', $query_value, $taxonomy ); ?>

				<div class="wizhi-btn-group">
					<a href="#" class="wizhi-btn wizhi-btn-default"><?php echo $term->name; ?></a>
					<a href="<?php echo remove_query_arg( $query_var ); ?>" class="wizhi-btn wizhi-btn-close">X</a>
				</div>

				<?php

			endif;

		endforeach;

		echo '</div>';

	}


	/**
	 * 显示按条件搜索的选项
	 *
	 */
	public function wizhi_filter_search() {
		if ( ! $this->hide_search ) {
			?>

			<form class="wizhi-form" role="search" method="get" id="searchform" action="">
				<input type="text" name="q" class="wizhi-search" placeholder="" value="<?php echo get_query_var( 'q', '' ); ?>">
				<input type="hidden" name="paged" value="1">
				<button type="submit" class="wizhi-btn wizhi-btn-primary">搜索</button>
			</form>

			<?php

		}

	}


	/**
	 * 输入获取到的文章循环对象
	 *
	 * @param array $taxonomies 需要显示的过滤条件的自定义分类法名称
	 *
	 * @return \WP_Query
	 */
	public function wizhi_get_filter_object() {

		$post_types = $this->post_types;
		$taxonomies = $this->taxonomies;

		// 初始化分类法查询数组
		$tax_query_array = [ 'relation' => 'AND' ];

		foreach ( $taxonomies as $taxonomy ) :

			// 获取查询变量值
			$query_var   = $taxonomy;
			$query_value = get_query_var( $query_var );

			// 如果获取的查询变量值非空
			if ( ! empty( $query_value ) ) :

				$tax_query = [
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $query_value,
				];

				// 添加新的分类法查询到查询数组
				array_push( $tax_query_array, $tax_query );

			endif;

		endforeach;

		// 添加搜索变量 
		$q = get_query_var( 'q', '' );

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$args  = [
			'post_type'      => $post_types,
			'posts_per_page' => get_option( 'posts_per_page' ),
			'paged'          => $paged,
			's'              => $q,
			'tax_query'      => $tax_query_array,
		];

		$wp_query = new WP_Query( $args );

		return $wp_query;

	}


}