<?php
/*
Plugin Name:        Wizhi Multi Filters
Plugin URI:         https://www.wpzhiku.com/wizhi-multi-filters/
Description:        为WordPress的文章类型添加按照自定义分类法进行多条件筛选的功能。
Version:            1.8.4
Author:             WordPress 智库
Author URI:         https://www.wpzhiku.com/
License:            MIT License
License URI:        http://opensource.org/licenses/MIT
*/

if ( version_compare( phpversion(), '5.6.0', '<' ) ) {

	// 显示警告信息
	if ( is_admin() ) {
		add_action( 'admin_notices', function () {
			printf( '<div class="error"><p>' . __( '您当前的PHP版本（%1$s）不符合插件要求, 请升级到 PHP %2$s 或更新的版本， 否则插件没有任何作用。', 'wizhi' ) . '</p></div>', phpversion(), '5.6.0' );
		} );
	}

	return;
}

require_once( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );
require_once( plugin_dir_path( __FILE__ ) . 'src/helper.php' );