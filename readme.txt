=== Plugin Name ===
Contributors: Amos Lee
Donate link: 
Tags: admin, post, pages, plugin, CMS, filter
Requires at least: 3.4
Tested up to: 4.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Wizhi Multi Filter 为WordPress的文章类型添加按照自定义分类法进行多条件筛选的功能。

== Description ==

Wizhi Multi Filter 为WordPress的文章类型添加按照自定义分类法进行多条件筛选的功能。


= 使用方法

输出多条件筛选过滤列表

`<?php
    $filters = new Wizhi_Filter();
 	$wp_query = $filters->wizhi_get_filter_object();
?>`

输入多条件筛选过滤列表，就是一个标准的WordPress查询

`<?php if (have_posts()) { ?>
    <?php while (have_posts()) : the_post(); ?>
        <?php get_template_part( 'content', 'lists' ); ?>
    <?php endwhile; ?>
<?php } ?>`


= BUG反馈和功能建议 =

BUG反馈和功能建议请发送邮件至：iwillhappy1314@gmail.com

作者网址：[WordPress智库](http://www.wpzhiku.com/ "WordPress CMS 插件")
插件截图及文档：[WordPress多条件筛选插件-Wizhi Multi Filters](http://www.wpzhiku.com/wizhi-multi-filters/ "WordPress多条件筛选插件-Wizhi Multi Filters")


== Installation ==

1. 上传插件到`/wp-content/plugins/` 目录
2. 在插件管理菜单激活插件

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

= 1.0 =
* The first released