<?php

namespace Wizhi\Filter;

/**
 * Class Wizhi_Filter
 */
class Filter
{
	/*
	 * 要显示的过滤条件的自定义分类法名称
	 *
	 * @var array
	 */
	private $post_types;

	private $taxonomies = [];

	function __construct( $post_types, $taxonomies )
	{

		// 获取参数
		$this->post_types = $post_types;
		$this->taxonomies = $taxonomies;
	}

	/**
	 * 移除分页参数
	 *
	 * @param $url
	 *
	 * @return null|string|string[]
	 */
	public function remove_paged_var( $url )
	{
		$pattern     = '/(page(.*))\?/';
		$replacement = '?';

		$paged_string = preg_replace( $pattern, $replacement, $url );

		return $paged_string;
	}

	/**
	 * 获取分类法过滤链接
	 */
	public function show_filters()
	{

		$taxonomies = $this->taxonomies;

		echo '<div class="wizhi-selects">';

		foreach ( $taxonomies as $taxonomy ) :

			$terms = get_terms( $taxonomy, [
				'hide_empty' => false,
			] );

			$term_counts = $this->get_filtered_term_post_counts( wp_list_pluck( $terms, 'term_id' ), $taxonomy );

			if ( array_sum( $term_counts ) > 0 ):

				$tax = get_taxonomy( $taxonomy ); ?>

                <div class="wizhi-select">
                    <strong><?= $tax->label; ?></strong>
					<?php

					$query_var = $taxonomy;

					$is_all = get_query_var( $query_var ) ? '' : 'selected';
					$count  = count( $terms );

					echo '<ul>';

					if ( $count > 0 ) :

						$exclude_all_var     = [ $query_var, 'page', 'paged' ];
						$exclude_current_var = [ $query_var, 'page', 'paged' ];
						$exclude_term_var    = [ 'st_by_pop', 'pop_dir', 'st_by_date', 'date_dir', 'page', 'paged', ];

						echo '<li><a class="' . $is_all . '" href="' . $this->remove_paged_var( remove_query_arg( $exclude_all_var ) ) . '">所有</a></li>';

						foreach ( $terms as $term ) :

							$include_term_var = [ $query_var => $term->slug, 'paged' => false, ];

							if ( $term_counts[ $term->term_id ] > 0 ) {

								echo '<li>';

								if ( get_query_var( $query_var ) == $term->slug ) {
									echo '<a href="' . $this->remove_paged_var( remove_query_arg( $exclude_current_var ) ) . '" class="selected">' . $term->name . "</a>";
								} else {
									echo '<a href="' . $this->remove_paged_var( remove_query_arg( $exclude_term_var, add_query_arg( $include_term_var ) ) ) . '">' . $term->name . "</a>";
								}

								echo '</li>';

							}

						endforeach;

					endif;

					echo '</ul>';

					?>
                </div>

			<?php

			endif;
		endforeach;

		echo '</div>';
	}


	/**
	 * 获取已筛选分类中的文章数量
	 *
	 * @param $term_ids
	 * @param $taxonomy
	 *
	 * @return array
	 */
	public function get_filtered_term_post_counts( $term_ids, $taxonomy )
	{
		global $wpdb;
		global $wp_query;

		$post_type = $this->post_types;

		$tax_query  = $wp_query->tax_query->queries;
		$meta_query = $wp_query->meta_query->queries;

		$tax_query  = new \WP_Tax_Query( $tax_query );
		$meta_query = new \WP_Meta_Query( $meta_query );

		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );
		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );

		// Generate query.
		$query             = [];
		$query[ 'select' ] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count, terms.term_id as term_count_id";
		$query[ 'from' ]   = "FROM {$wpdb->posts}";
		$query[ 'join' ]   = "
			INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
			INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
			INNER JOIN {$wpdb->terms} AS terms USING( term_id )
			" . $tax_query_sql[ 'join' ] . $meta_query_sql[ 'join' ];

		$query[ 'where' ] = "
			WHERE {$wpdb->posts}.post_type IN ( 'school' )
			AND {$wpdb->posts}.post_status = 'publish'"
		                    . $tax_query_sql[ 'where' ] . $meta_query_sql[ 'where' ] .
		                    'AND terms.term_id IN (' . implode( ',', array_map( 'absint', $term_ids ) ) . ')';

		// if ( $search = WC_Query::get_main_search_query_sql() ) {
		// 	$query[ 'where' ] .= ' AND ' . $search;
		// }

		$query[ 'group_by' ] = 'GROUP BY terms.term_id';
		$query               = apply_filters( 'woocommerce_get_filtered_term_product_counts_query', $query );
		$query               = implode( ' ', $query );

		// We have a query - let's see if cached results of this query already exist.
		$query_hash = md5( $query );

		$results                      = $wpdb->get_results( $query, ARRAY_A ); // @codingStandardsIgnoreLine
		$counts                       = array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_count_id' ) );
		$cached_counts[ $query_hash ] = $counts;

		return array_map( 'absint', (array) $cached_counts[ $query_hash ] );
	}


	/**
	 * 显示当前选择的过滤项目
	 *
	 */
	public function current_filter()
	{

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
	public function search_form()
	{

		$q = isset( $_POST[ 'q' ] ) ? $_POST[ 'q' ] : false;
		global $wp;
		$current_url = home_url( add_query_arg( [], $wp->request ) );

		?>

        <form class="wizhi-form" role="search" method="post" id="searchform" action="<?php echo $current_url; ?>">
            <input type="text" name="q" class="wizhi-search" placeholder="" value="<?php echo $q; ?>">
            <input type="hidden" name="paged" value="1">
            <button type="submit" class="wizhi-btn wizhi-btn-primary">搜索</button>
        </form>

		<?php

	}

	/**
	 * 显示排序条件
	 */
	public function sort_links()
	{
		$date_dir = isset( $_GET[ 'date_dir' ] ) ? $_GET[ 'date_dir' ] : false;
		$pop_dir  = isset( $_GET[ 'pop_dir' ] ) ? $_GET[ 'pop_dir' ] : false;
		?>

        <div class="wizhi-sort">

            <span class="sort-by-date">
				<?php if ( $date_dir == 'ASC' ) : ?>
                    <a href="<?php echo remove_query_arg( [ 'st_by_pop', 'pop_dir' ], add_query_arg( [
						'st_by_date' => 1,
						'date_dir'   => 'DESC',
					] ) ); ?>"
                       class="">按时间降序</a>
				<?php else: ?>
                    <a href="<?php echo remove_query_arg( [ 'st_by_pop', 'pop_dir' ], add_query_arg( [
						'st_by_date' => 1,
						'date_dir'   => 'ASC',
					] ) ); ?>"
                       class="">按时间升序</a>
				<?php endif; ?>
            </span>

            <span class="sort-by-pop">
				<?php if ( $pop_dir == 'ASC' ) : ?>
                    <a href="<?php echo remove_query_arg( [ 'st_by_date', 'date_dir' ], add_query_arg( [
						'st_by_pop' => 1,
						'pop_dir'   => 'DESC',
					] ) ); ?>"
                       class="">按人气降序</a>
				<?php else: ?>
                    <a href="<?php echo remove_query_arg( [ 'st_by_date', 'date_dir' ], add_query_arg( [
						'st_by_pop' => 1,
						'pop_dir'   => 'ASC',
					] ) ); ?>"
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
	public function total()
	{
		$query = $this->get_filtered_object();

		return $query->found_posts;
	}

	/**
	 * 输入获取到的文章循环对象
	 *
	 * @return \WP_Query
	 */
	public function get_filtered_object()
	{

		$post_types = $this->post_types[ 0 ];
		$taxonomies = $this->taxonomies;

		/**
		 * 获取查询变量
		 */
		$q          = isset( $_POST[ 'q' ] ) ? $_POST[ 'q' ] : false;
		$st_by_date = isset( $_GET[ 'st_by_date' ] ) ? $_GET[ 'st_by_date' ] : false;
		$st_by_pop  = isset( $_GET[ 'st_by_pop' ] ) ? $_GET[ 'st_by_pop' ] : false;
		$date_dir   = isset( $_GET[ 'date_dir' ] ) ? $_GET[ 'date_dir' ] : 'DESC';
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

		$wp_query = new \WP_Query( $args );

		return $wp_query;
	}
}