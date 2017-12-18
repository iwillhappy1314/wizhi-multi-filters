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
	private $taxonomies = [];
	private $hide_search;

	function __construct( $post_types, $taxonomies, $hide_search ) {

		// 获取参数
		$this->post_types  = $post_types;
		$this->taxonomies  = $taxonomies;
		$this->hide_search = $hide_search;

	}

	/**
	 * 获取分类法过滤链接
	 */
	public function show_filters() {

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

				echo '<ul>';

				if ( $count > 0 ) :

					echo '<a class="' . $is_all . '" href="' . remove_query_arg( $query_var ) . '">所有</a>';
					foreach ( $terms as $term ) :
						echo '<li>';

						if ( get_query_var( $query_var ) == $term->slug ) {
							echo '<a href="' . remove_query_arg( $query_var ) . '" class="selected">' . $term->name . "</a>";
						} else {
							echo '<a href="' . remove_query_arg( [
									'st_by_pop',
									'pop_dir',
									'st_by_date',
									'date_dir',
								], add_query_arg( [ $query_var => $term->slug, 'paged' => false ] ) ) . '">' . $term->name . "</a>";
						}

						echo '</li>';
					endforeach;

				endif;

				echo '</ul>';

				?>
            </div>

			<?php

		endforeach;

		echo '</div>';

	}


	/**
	 * 显示当前选择的过滤项目
	 *
	 */
	public function current_filter() {

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
	public function search_form() {
		if ( ! $this->hide_search ) {

			$q = isset( $_POST[ 'q' ] ) ? $_POST[ 'q' ] : false;

			?>

            <form class="wizhi-form" role="search" method="post" id="searchform" action="<?php echo esc_url( get_current_url() ) ?>">
                <input type="text" name="q" class="wizhi-search" placeholder="" value="<?php echo $q; ?>">
                <input type="hidden" name="paged" value="1">
                <button type="submit" class="wizhi-btn wizhi-btn-primary">搜索</button>
            </form>

			<?php

		}

	}


	/**
	 * 显示排序条件
	 */
	public function sort_links() {
		$date_dir = isset( $_GET[ 'date_dir' ] ) ? $_GET[ 'date_dir' ] : false;
		$pop_dir  = isset( $_GET[ 'pop_dir' ] ) ? $_GET[ 'pop_dir' ] : false;
		?>

        <div class="wizhi-sort">

            <span class="sort-by-date">
				<?php if ( $date_dir == 'ASC' ) : ?>
                    <a href="<?php echo remove_query_arg( [ 'st_by_pop', 'pop_dir' ], add_query_arg( [ 'st_by_date' => 1, 'date_dir' => 'DESC' ] ) ); ?>"
                       class="">按时间降序</a>
				<?php else: ?>
                    <a href="<?php echo remove_query_arg( [ 'st_by_pop', 'pop_dir' ], add_query_arg( [ 'st_by_date' => 1, 'date_dir' => 'ASC' ] ) ); ?>"
                       class="">按时间升序</a>
				<?php endif; ?>
            </span>

            <span class="sort-by-pop">
				<?php if ( $pop_dir == 'ASC' ) : ?>
                    <a href="<?php echo remove_query_arg( [ 'st_by_date', 'date_dir' ], add_query_arg( [ 'st_by_pop' => 1, 'pop_dir' => 'DESC' ] ) ); ?>"
                       class="">按人气降序</a>
				<?php else: ?>
                    <a href="<?php echo remove_query_arg( [ 'st_by_date', 'date_dir' ], add_query_arg( [ 'st_by_pop' => 1, 'pop_dir' => 'ASC' ] ) ); ?>"
                       class="">按人气升序</a>
				<?php endif; ?>
            </span>

        </div>

	<?php }


	/**
	 * 获取当前查询的文章数量
	 *
	 * @return int
	 */
	public function count() {
		$query = $this->get_filtered_object();

		return $query->post_count;
	}


	/**
	 * 输入获取到的文章循环对象
	 *
	 * @return \WP_Query
	 */
	public function get_filtered_object() {

		$post_types = $this->post_types[ 0 ];
		$taxonomies = $this->taxonomies;

		/**
		 * 获取查询变量
		 */
		$q          = isset( $_POST[ 'q' ] ) ? $_POST[ 'q' ] : false;
		$st_by_date = isset( $_GET[ 'st_by_date' ] ) ? $_GET[ 'st_by_date' ] : false;
		$st_by_pop  = isset( $_GET[ 'st_by_pop' ] ) ? $_GET[ 'st_by_pop' ] : false;
		$date_dir   = isset( $_GET[ 'date_dir' ] ) ? $_GET[ 'date_dir' ] : false;
		$pop_dir    = isset( $_GET[ 'pop_dir' ] ) ? $_GET[ 'pop_dir' ] : false;


		/**
		 * 获取分类法查询数组
		 */
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

		$tax_query = [];
		if ( count( $tax_query_array ) > 1 ) {
			$tax_query = [
				'tax_query' => $tax_query_array,
			];
		}


		/**
		 * 排序数据
		 */
		$order_args = [];

		if ( $st_by_date && ! $st_by_pop ) {
			$order_args = [
				'orderby' => 'date',
				'order'   => $date_dir,
			];
		}

		if ( $st_by_pop && ! $st_by_date ) {
			$order_args = [
				'orderby'  => 'meta_value_num',
				'meta_key' => 'views',
				'order'    => $pop_dir,
			];
		}

		if ( $st_by_pop && $st_by_date ) {
			$order_args = [
				'orderby'  => [ 'date' => $date_dir, 'meta_value_num' => $pop_dir ],
				'meta_key' => 'views',
			];
		}


		/**
		 * 搜索数据
		 */
		$search_args = [];
		if ( $q ) {
			$search_args = [
				's' => $q,
			];
		}


		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;


		/**
		 * 默认查询数组
		 */
		$default_args = [
			'post_type'      => $post_types,
			'posts_per_page' => get_option( 'posts_per_page' ),
			'paged'          => $paged,
		];

		$args = array_merge( $default_args, $tax_query, $order_args, $search_args );

		$wp_query = new WP_Query( $args );

		return $wp_query;

	}


}