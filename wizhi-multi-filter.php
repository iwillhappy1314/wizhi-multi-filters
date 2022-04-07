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

define('WIZHI_MULTI_FILTERS_VERSION', '1.0.0');
define('WIZHI_MULTI_FILTERS_PATH', plugin_dir_path(__FILE__));
define('WIZHI_MULTI_FILTERS_URL', plugin_dir_url(__FILE__));

if (version_compare(phpversion(), '5.6.0', '<')) {

    // 显示警告信息
    if (is_admin()) {
        add_action('admin_notices', function ()
        {
            printf('<div class="error"><p>' . __('您当前的PHP版本（%1$s）不符合插件要求, 请升级到 PHP %2$s 或更新的版本， 否则插件没有任何作用。', 'wizhi') . '</p></div>', phpversion(), '5.6.0');
        });
    }

    return;
}

require_once(plugin_dir_path(__FILE__) . 'vendor/autoload.php');

// 添加更新通知
add_action('in_plugin_update_message-wizhi-multi-filters/wizhi-multi-filter.php', 'wizhi_filter_show_update_notice', 10, 2);
function wizhi_filter_show_update_notice($current_plugin_data, $new_plugin_data)
{
    if (isset($new_plugin_data->upgrade_notice) && strlen(trim($new_plugin_data->upgrade_notice)) > 0) {
        echo '<p style="background-color: #d54e21; padding: 10px; color: #f9f9f9; margin-top: 10px"><strong><span class="dashicons dashicons-warning"></span>警告: </strong> ';
        echo esc_html($new_plugin_data->upgrade_notice), '</p>';
    }
}


