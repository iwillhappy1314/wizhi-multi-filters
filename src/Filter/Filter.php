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


	/**
	 * @var array
	 */
	private $taxonomies = [];

	/**
	 * @var array
	 */
	private $metas = [];


	/**
	 * @var array
	 */
	private $sorts = [];


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
	 * 设置分类方法
	 *
	 * @param $tax
	 *
	 * @return $this
	 */
	function set_tax( $tax )
	{
		$this->taxonomies[] = $tax;

		return $this;
	}


	/**
	 * 添加自定义字段
	 *
	 * @param       $meta_key
	 * @param       $meta_label
	 * @param array $meta_values
	 *
	 * @return $this
	 */
	function add_meta( $meta_key, $meta_label, $meta_values = [] )
	{
		if ( count( $meta_values ) == 0 ) {
			$meta_values = $this->get_all_meta_values( $meta_key );
		}

		$this->metas[] = [
			'meta_key'   => $meta_key,
			'meta_label' => $meta_label,
			'meta_vales' => $meta_values,
		];

		return $this;

	}


	/**
	 * 添加排序数组
	 *
	 * @param        $order
	 * @param string $default
	 *
	 * @return $this
	 */
	function add_sorts( $order, $label, $default = 'DESC' )
	{
		$this->sorts[] = [
			'order'   => $order,
			'label'   => $label,
			'default' => $default,
		];

		return $this;
	}


	/**
	 * 获取自定义字段的所有可选值
	 *
	 * @param $meta_key
	 *
	 * @return array
	 */
	function get_all_meta_values( $meta_key )
	{
		global $wpdb;

		$sql = $wpdb->prepare(
			"
                 SELECT DISTINCT meta_value
                 FROM  $wpdb->postmeta 
                 WHERE meta_key = %s ORDER BY 1
		     ",
			$meta_key );

		$fields = $wpdb->get_results( $sql, ARRAY_N );

		return $fields;
	}


	/**
	 * 获取分类法过滤链接
	 */
	public function show_filters()
	{

		$taxonomies = $this->taxonomies;

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
						$exclude_other_var   = [ 'sort_by', 'dir', 'page', 'paged', ];

						echo '<li><a class="' . $is_all . '" href="' . $this->remove_paged_var( remove_query_arg( $exclude_all_var ) ) . '">所有</a></li>';

						foreach ( $terms as $term ) :

							$include_term_var = [ $query_var => $term->slug, 'paged' => false, ];

							if ( $term_counts[ $term->term_id ] > 0 ) {

								echo '<li>';

								if ( get_query_var( $query_var ) == $term->slug ) {
									echo '<a href="' . $this->remove_paged_var( remove_query_arg( $exclude_current_var ) ) . '" class="selected">' . $term->name . "</a>";
								} else {
									echo '<a href="' . $this->remove_paged_var( remove_query_arg( $exclude_other_var, add_query_arg( $include_term_var ) ) ) . '">' . $term->name . "</a>";
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

	}


	/**
	 * 显示自定义字段过滤链接
	 */
	function show_meta_filter_links()
	{

		$metas = $this->metas;

		foreach ( $metas as $meta ) {

			echo '<div class="wizhi-select">';
			echo '<strong>' . $meta[ 'meta_label' ] . '</strong>';

			$query_var   = $meta[ 'meta_key' ];
			$query_value = isset( $_GET[ $query_var ] ) ? $_GET[ $query_var ] : false;

			$is_all = $query_value ? '' : 'selected';

			$exclude_all_var     = [ $query_var, 'page', 'paged' ];
			$exclude_current_var = [ $query_var, 'page', 'paged' ];

			echo '<ul>';
			echo '<li><a class="' . $is_all . '" href="' . $this->remove_paged_var( remove_query_arg( $exclude_all_var ) ) . '">所有</a></li>';
			foreach ( $meta[ 'meta_vales' ] as $value ) {

				$value             = $value[ 0 ];
				$include_meta_var  = [ $query_var => $value, 'paged' => false, ];
				$exclude_other_var = [ 'sort_by', 'dir', 'page', 'paged', ];

				echo '<li>';

				if ( $query_value == $value ) {
					echo '<a href="' . $this->remove_paged_var( remove_query_arg( $exclude_current_var ) ) . '" class="selected">' . $value . "</a>";
				} else {
					echo '<a href="' . $this->remove_paged_var( remove_query_arg( $exclude_other_var, add_query_arg( $include_meta_var ) ) ) . '">' . $value . "</a>";
				}

				echo '</li>';
			}
			echo '</ul>';

			echo '</div>';

		}

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

		$taxonomies      = $this->taxonomies;
		$metas           = $this->metas;

		echo '<div class="wizhi-btns">';

		/**
		 * 当前所选分类方法
		 */
		foreach ( $taxonomies as $taxonomy ) :

			// 获取查询变量值
			$query_var = $taxonomy;
			$query_value = get_query_var( $query_var );

			// 如果获取的查询变量值非空
			if ( ! empty( $query_value ) ) :

				$term = get_term_by( 'slug', $query_value, $taxonomy ); ?>

                <div class="wizhi-btn-group">
                    <a href="#" class="wizhi-btn wizhi-btn-default"><?php echo $term->name; ?></a>
                    <a href="<?php echo remove_query_arg( $query_var ); ?>" class="wizhi-btn wizhi-btn-close">X</a>
                </div>

			<?php

			endif;

		endforeach;


		/**
		 * 当前所选自定义字段
		 */
		foreach ( $metas as $meta ) :

			// 获取查询变量值
			$query_var = $meta[ 'meta_key' ];;
			$query_value = isset( $_GET[ $query_var ] ) ? $_GET[ $query_var ] : false;

			// 如果获取的查询变量值非空
			if ( ! empty( $query_value ) ) : ?>

                <div class="wizhi-btn-group">
                    <a href="#" class="wizhi-btn wizhi-btn-default"><?= $query_value; ?></a>
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

		global $wp;

		$q           = isset( $_POST[ 'q' ] ) ? $_POST[ 'q' ] : false;
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
	public function show_sort_links()
	{
		$dir   = isset( $_GET[ 'dir' ] ) ? $_GET[ 'dir' ] : 'DESC';
		$dir   = ( $dir == 'DESC' ) ? 'ASC' : 'DESC';
		$sorts = $this->sorts;
		?>

        <div class="wizhi-sort">

			<?php foreach ( $sorts as $sort ): ?>

				<?php
				$sort_query_var   = $sort[ 'order' ];
				$sort_query_value = isset( $_GET[ $sort_query_var ] ) ? $_GET[ $sort_query_var ] : false;
				?>

				<?php if ( $sort_query_value ) : ?>
                    <span class="wizhi-sort sort-by-<?= $sort_query_var; ?>">
                        <a href="<?= add_query_arg( [ 'sort_by' => $sort_query_var, 'dir' => $dir, ] ); ?>">
                            <?= $sort[ 'label' ]; ?>
                        </a>
                    </span>
				<?php else: ?>
                    <span class="wizhi-sort sort-by-<?= $sort_query_var; ?>">
                        <a href="<?= add_query_arg( [ 'sort_by' => $sort_query_var, 'dir' => $dir, ] ); ?>">
                            <?= $sort[ 'label' ]; ?>
                        </a>
                    </span>
				<?php endif; ?>

			<?php endforeach; ?>

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
		$metas      = $this->metas;

		/**
		 * 获取查询变量
		 */
		$q = isset( $_POST[ 'q' ] ) ? $_POST[ 'q' ] : false;

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
		 * 获取自定义字段查询变量
		 */
		$meta_query_array = [ 'relation' => 'AND', ];

		foreach ( $metas as $meta ) :

			// 获取查询变量值
			$query_var = $meta[ 'meta_key' ];;
			$query_value = isset( $_GET[ $query_var ] ) ? $_GET[ $query_var ] : false;

			// 如果获取的查询变量值非空
			if ( $query_value ) :

				$meta_query = [
					'key'   => $query_var,
					'value' => $query_value,
				];

				// 添加新的分类法查询到查询数组
				array_push( $meta_query_array, $meta_query );

			endif;

		endforeach;

		$meta_query = [];
		if ( count( $meta_query_array ) > 1 ) {
			$meta_query = [
				'meta_query' => $meta_query_array,
			];
		}


		/**
		 * 排序数据
		 */
		$dir     = isset( $_GET[ 'dir' ] ) ? $_GET[ 'dir' ] : 'DESC';
		$sort_by = isset( $_GET[ 'sort_by' ] ) ? $_GET[ 'sort_by' ] : false;

		$order_args = [
			'orderby'  => 'meta_value_num',
			'meta_key' => $sort_by,
			'order'    => $dir,
		];


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

		$args = array_merge( $default_args, $tax_query, $meta_query, $order_args, $search_args );

		$wp_query = new \WP_Query( $args );

		return $wp_query;
	}

}