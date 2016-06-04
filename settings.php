<?php

add_action( 'admin_init', 'wizhi_multi_filter_admin_init' );
add_action( 'admin_menu', 'wizhi_multi_filter_menu' );

function wizhi_multi_filter_menu() {
	$page_hook_suffix = add_options_page( '多条件筛选', '多条件筛选', 'manage_options', 'wizhi-multi-filter.php', 'wizhi_multi_filter_management_page' );

	add_action( 'admin_print_scripts-' . $page_hook_suffix, 'wizhi_multi_filter_admin_scripts' );
	add_action( 'admin_print_styles-' . $page_hook_suffix, 'wizhi_multi_filter_admin_styles' );
}

// 加载js和样式
function wizhi_multi_filter_admin_init() {
	wp_register_style( 'wizhi-multi-filter-style', plugins_url( 'assets/style.css', __FILE__ ) );
	wp_register_script( 'wizhi-multi-filter-script', plugins_url( 'assets/script.js', __FILE__ ) );
}

// 挂载插件js
function wizhi_multi_filter_admin_scripts() {
	wp_enqueue_script( 'wizhi-multi-filter-script' );
}

// 挂载插件样式
function wizhi_multi_filter_admin_styles() {
	wp_enqueue_style( 'wizhi-multi-filter-style' );
}

/**
 * 添加验证页面到后台
 */
function wizhi_multi_filter_management_page() {

	echo '<div class="wrap">';
	echo '<h2>选择需要过滤的自定义分类法和文章类型</h2>';

	// 已选择的文章类型和分类法
	$wizhi_saved_tax = get_option( 'wizhi_tax' );

	if ( ! $wizhi_saved_tax ) {
		$wizhi_saved_tax = [
			'name'  => [ '品牌', '产地' ],
			'label' => [ 'brand', 'area' ],
		];
	}

	/*** 存设置选项到数据库 ***/
	if ( isset( $_POST[ 'save_filters' ] ) ) :

		// 获取选项数据
		$hide_css           = isset( $_POST[ "hide_css" ] ) ? $_POST[ "hide_css" ] : '';
		$hide_search        = isset( $_POST[ "hide_search" ] ) ? $_POST[ "hide_search" ] : '';
		$wizhi_show_current = isset( $_POST[ "wizhi_show_current" ] ) ? $_POST[ "wizhi_show_current" ] : '';
		$wizhi_use_type_tax = isset( $_POST[ "wizhi_use_type_tax" ] ) ? $_POST[ "wizhi_use_type_tax" ] : '';
		$wizhi_type_name    = isset( $_POST[ "wizhi_type_name" ] ) ? $_POST[ "wizhi_type_name" ] : '';
		$wizhi_type_label   = isset( $_POST[ "wizhi_type_label" ] ) ? $_POST[ "wizhi_type_label" ] : '';
		$wizhi_tax          = isset( $_POST[ "wizhi_tax" ] ) ? $_POST[ "wizhi_tax" ] : '';

		// 保存设置选项到数据库
		update_option( 'wizhi_type_name', sanitize_text_field( $wizhi_type_name ) );
		update_option( 'wizhi_type_label', sanitize_text_field( $wizhi_type_label ) );
		update_option( 'wizhi_tax', $wizhi_tax );
		update_option( 'wizhi_use_type_tax', sanitize_text_field( $wizhi_use_type_tax ) );
		update_option( 'hide_css', sanitize_text_field( $hide_css ) );
		update_option( 'hide_search', sanitize_text_field( $hide_search ) );
		update_option( 'wizhi_show_current', sanitize_text_field( $wizhi_show_current ) );

		echo '<div class="updated"><p>恭喜！保存成功。</p></div>';

	endif;

	?>

	<form action="" method="post">

	<p>使用方法请参考：<a target="_blank" href="http://www.wpzhiku.com/wizhi-multi-filters/">插件文档</a></p>

	<table class="form-table">

		<tr>
			<th scope="row"><label>使用内置的文章类型和分类法</label></th>
			<td>
				<label>
					<input type="checkbox" name="wizhi_use_type_tax" value="1" <?php echo ( get_option( 'wizhi_use_type_tax' ) ) ? 'checked' : ''; ?>>
					使用内置的文章类型和分类法
				</label>
			</td>
		</tr>

		<tr>
			<th scope="row"><label>文章类型名称</label></th>
			<td>
				<table class="form-table">
					<tr>
						<td>
							<input type="text" name="wizhi_type_name"
							       value="<?php echo ( get_option( 'wizhi_type_name' ) ) ? get_option( 'wizhi_type_name' ) : '产品'; ?>"/>
							<input type="text" name="wizhi_type_label"
							       value="<?php echo ( get_option( 'wizhi_type_label' ) ) ? get_option( 'wizhi_type_label' ) : 'prod'; ?>"/>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<th scope="row"><label>自定义分类方法</label></th>
			<td>
				<table class="form-table">
					<tbody>

					<?php $saved_tax = array_combine( $wizhi_saved_tax[ 'label' ], $wizhi_saved_tax[ 'name' ] ); ?>

					<?php foreach ( $saved_tax as $label => $name ) : ?>
						<tr class="repeatable-fieldset">
							<td>
								<input type="text" name="wizhi_tax[name][]" value="<?php echo $name; ?>"/>
								<input type="text" name="wizhi_tax[label][]" value="<?php echo $label; ?>"/>
								<a class="button remove-row" href="#">删除</a>
							</td>
						</tr>
					<?php endforeach; ?>

					<tr>
						<td><a id="add-row" class="button" href="#">添加</a></td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>

		<tr>
			<th scope="row"><label>不显示CSS</label></th>
			<td>
				<label>
					<input type="checkbox" name="hide_css"
					       value="1" <?php echo ( get_option( 'hide_css' ) ) ? 'checked' : ''; ?>>
					不显示CSS
				</label> <br/>

				<p class="description">是否显示CSS，如果选择不显示，需要在主题中自定义CSS</p>
			</td>
		</tr>

		<tr>
			<th scope="row"><label>不显示搜索功能</label></th>
			<td>
				<label>
					<input type="checkbox" name="hide_search"
					       value="1" <?php echo ( get_option( 'hide_search' ) ) ? 'checked' : ''; ?>>
					不显示搜索功能
				</label> <br/>
			</td>
		</tr>

		<tr>
			<th scope="row"><label>不显示当前已选条件</label></th>
			<td>
				<label>
					<input type="checkbox" name="wizhi_show_current"
					       value="1" <?php echo ( get_option( 'wizhi_show_current' ) ) ? 'checked' : ''; ?>>
					不显示当前已选条件
				</label>
			</td>
		</tr>

	</table>

	<p class="submit">
		<input type="submit" name="save_filters" value="<?php esc_attr_e( '保存' ); ?>" class="button-primary"/>
	</p>

	</form>

	<h2>使用方法</h2>

	<ol>
		<li>复制主题的 <code>archive.php</code> 为 <code>archive-prod.php</code>, "prod" 为上面设置中的 “文章类型名称” 的英文名称</li>
		<li>添加以下代码到 <code>archive-prod.php</code> 模板中的 <code>&lt;?php while ( have_posts() ) : the_post(); ?&gt</code> 之前</li>
	</ol>

	<code>
		&lt;?php if ( function_exists( "wizhi_multi_filters" ) ) { wizhi_multi_filters(); } ?&gt;
	</code>

	<p>详细说明请参考：<a target="_blank" href="http://www.wpzhiku.com/wizhi-multi-filters/">插件文档</a></p>

	<?php

	echo '</div>';

}