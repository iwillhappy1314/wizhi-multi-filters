<?php
/*
Plugin Name:        Wizhi Multi Filters
Plugin URI:         http://www.wpzhiku.com/wizhi-multi-filters/
Description:        为WordPress的文章类型添加按照自定义分类法进行多条件筛选的功能。
Version:            1.4
Author:             Amos Lee
Author URI:         http://www.wpzhiku.com/
License:            MIT License
License URI:        http://opensource.org/licenses/MIT
*/

define('WIZHI_FILTER', plugin_dir_path(__FILE__));

//快速添加文章类型和分类法
require_once(WIZHI_FILTER . 'modules/post_types.php');
require_once(WIZHI_FILTER . 'modules/ajax.php');
require_once(WIZHI_FILTER . 'modules/filter.php');
require_once(WIZHI_FILTER . 'modules/rewrite.php');

require_once(WIZHI_FILTER . 'settings.php');

$hide_css = get_option('hide_css');

// 设置默认的分类方法
$wizhi_default_tax = array(
    'name' => array('品牌', '产地'),
    'label' => array('brand', 'area'),
);


/**
 * 获取默认的文章类型名称
 * @return mixed|string|void
 */
function wizhi_get_type_name(){
	$wizhi_type_name = ( get_option('wizhi_type_name') ) ? get_option('wizhi_type_name') : '产品';
	return $wizhi_type_name;
}


/**
 * 获取默认的文章类型标签
 * @return string   分类法标签
 */
function wizhi_get_type_label(){
	$wizhi_type_label = ( get_option('wizhi_type_label') ) ? get_option('wizhi_type_label') : 'prod';
	return $wizhi_type_label;
}


/**
 * 获取默认的分类法
 *
 * @return array 自定义分类法 label=>name 数组
 */
function wizhi_get_taxs(){

	// 设置默认的分类方法
	$wizhi_default_tax = array(
		'name' => array('品牌', '产地'),
		'label' => array('brand', 'area'),
	);

	$wizhi_saved_tax = ( get_option('wizhi_tax') ) ? get_option('wizhi_tax') : $wizhi_default_tax;
	$saved_tax = array_combine( $wizhi_saved_tax['label'], $wizhi_saved_tax['name'] );

	return $saved_tax;
}


/**
 * 添加默认的文章类型和分类法
 */
add_action( 'init', 'wizhi_filter_add_types' );
function wizhi_filter_add_types(){
	
	$wizhi_use_type_tax = get_option('wizhi_use_type_tax');
	
	if($wizhi_use_type_tax){

		if ( function_exists ("wizhi_create_types")) {
		    wizhi_create_types( 
		    	wizhi_get_type_label(),
		    	wizhi_get_type_name(),
		    	array( 'title', 'editor', 'author', 'thumbnail', 'comments' ),
		    	true
	    	);
		}
	
		if (function_exists ("wizhi_create_taxs") ) {
			foreach ( wizhi_get_taxs() as $label=>$name ) :
		    wizhi_create_taxs(
		    	$label,
		    	wizhi_get_type_label(),
		    	$name,
		    	true
	    	);
	    	endforeach;
		}
		
	}

}


/**
 * 显示默认的CSS
 */
if( !$hide_css ){ add_action( 'wp_head', 'admin_css' ); }
function admin_css(){ ?>
	<style>
		.wizhi-select { padding: 0 0 8px 0;margin: 0 0 8px 0; border-bottom: 1px solid #eee; }
		.wizhi-select a{ padding: 2px 5px; }
        .wizhi-select strong{padding-right: 5px;}
		.wizhi-select a.selected{ color: #337AB7; }
		.btn-group{ margin-top: 10px; display: inline-block; }
		.btn{ color: #333; background-color: #efefef; border: 1px solid #ccc; padding: 3px 8px; font-size: 14px; line-height: 1.42857143;}
		.btn-close{ margin-left: -5px;}
	</style>
<?php
}


// 添加重定向规则
add_action( 'generate_rewrite_rules', 'add_rewrite_rules' );
function add_rewrite_rules(){

 	global $wp_rewrite;
 	// 获取需要筛选的文章类型和分类法
 	$post_types = array('codex', 'filter');
 	$taxonomies = array('function', 'template', 'brand', 'area', 'vendor', 'price1');

 	// 排除默认分类法方法
	if(is_array($taxonomies)){
		array_push($taxonomies, 'category', 'post_tag');
	}else{
		$taxonomies = array(
			'category',
			'post_tag'
		);
	}
	
	// Polylang 支持
	if(function_exists('pll_current_language')){
		array_push($taxonomies, 'language');
	}
	
	$addtion_var = array('q');
	
 	if($post_types){
 		// 初始化重定向类
	 	$rewrite_rule_class = new Wizhi_Filters_Rewrite_Rules();
	 	foreach($post_types as $post_type){
	 		// 为每个文章类型运行此功能
			$new_rules = $rewrite_rule_class->generate_rewrite_rules($post_type, $taxonomies, $addtion_var);
			$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
		}
	}
			
}