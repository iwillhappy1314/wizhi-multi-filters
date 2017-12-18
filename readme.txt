=== Plugin Name ===
Contributors: iwillhappy1314
Donate link: https://www.wpzhiku.com
Tags: admin, post, pages, plugin, CMS, filter
Requires at least: 3.4
Tested up to: 4.4
Requires PHP: 5.6
Stable tag: 1.8.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Wizhi Multi Filters 为 WordPress 的自定义文章类型添加按照自定义分类法进行多条件筛选的功能。

== Description ==

Wizhi Multi Filters 为 WordPress 的自定义文章类型添加按照自定义分类法进行多条件筛选的功能。


= 使用方法

1. 添加自定义文章类型和分类方法

````
if ( function_exists( 'wizhi_create_types' ) ) {
    wizhi_create_types( "prod", "产品", [ 'title', 'editor', 'author', 'excerpt', 'thumbnail', 'custom-fields', 'comments' ], true );
    wizhi_create_taxs( "prod_cat", 'prod', "类别", true );
    wizhi_create_taxs( "prod_area", 'prod', "产地", true );
    wizhi_create_taxs( "prod_size", 'prod', "尺寸", true );
}
````

2. 复制主题的 `archive.php` 为 `archive-prod.php`, "prod" 为上面设置中的 “文章类型名称” 的英文名称，在 `have_posts()` 代码前添加代码：

````
<?php
    if ( function_exists( "wizhi_multi_filters" ) ) {
        $filters   = wizhi_multi_filters();
        $new_query = $filters->get_filtered_object();
    }
?>
````

然后按需要使用下面的代码显示对应的筛选组件到合适的位置。

*显示分类法筛选链接*

````
<?php $filters->show_filters(); ?>
````

*显示排序链接*

````
<?php $filters->sort_links(); ?>
````

*显示搜索表单*

````
<?php $filters->search_form(); ?>
````

注意：插件需要需要 PHP 5.4 以上的版本才能运行，建议使用 PHP 5.6


= BUG反馈和功能建议 =

BUG反馈和功能建议请发送邮件至：iwillhappy1314@gmail.com

作者网址：[WordPress智库](http://www.wpzhiku.com/ "WordPress CMS 插件")
插件截图及文档：[WordPress多条件筛选插件-Wizhi Multi Filters](https://www.wpzhiku.com/wizhi-multi-filters/ "WordPress多条件筛选插件-Wizhi Multi Filters")


== Installation ==

1. 上传插件到`/wp-content/plugins/` 目录
2. 在插件管理菜单激活插件

== Frequently Asked Questions ==

= 插件可以筛选多个文章类型里面的文章吗 =

可以, 每个文章类型需要单独建一个存档页面模板

= 插件支持使用现有的自定义文章类型吗 =

可以, 现有的自定义文章类型和插件内置的没有区别, 一样使用

= 可以在首页显示筛选条件吗 =

可以, 不过设置比较复杂, 请咨询插件作者


== Screenshots ==


== Changelog ==

= 1.8.4 =
* 增加按时间或浏览量排序功能、移除不再需要的后台设置和相关代码

= 1.8 =
* 增加 PHP 版本检测，防止低版本 PHP 导致致命错误。

= 1.7 =
* 增加更新提示

= 1.6 =
* 修正设置页面错误
* 增加 PHP 版本要求

= 1.5 =
* 添加多文章类型支持

= 1.0 =
* The first released