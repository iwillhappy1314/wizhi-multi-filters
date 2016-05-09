## 插件信息

- Contributors: iwillhappy1314


- Donate link: 


- Tags: admin, post, pages, plugin, CMS, filter


- Requires at least: 3.4


- Tested up to: 4.4


- Stable tag: 1.5


- License: GPLv2 or later


- License URI: http://www.gnu.org/licenses/gpl-2.0.html

## 插件功能

Wizhi Multi Filters 为WordPress的文章类型添加按照自定义分类法进行多条件筛选的功能。

## 使用方法

输出多条件筛选过滤列表

```php
<?php 
    $filters = new Wizhi_Filter();
    $wp_query = $filters->wizhi_get_filter_object(); 
?>
```

输入多条件筛选过滤列表，就是一个标准的WordPress查询

```php
<?php if (have_posts()) { ?>
    <?php while (have_posts()) : the_post(); ?>
        <?php get_template_part( 'content', 'lists' ); ?>
    <?php endwhile; ?>
<?php } ?>
```

作者网址：[WordPress智库](http://www.wpzhiku.com/ "WordPress CMS 插件")
插件截图及文档：[WordPress多条件筛选插件-Wizhi Multi Filters](https://www.wpzhiku.com/wizhi-multi-filters/ "WordPress多条件筛选插件-Wizhi Multi Filters")

## 安装

1. 上传插件到`/wp-content/plugins/` 目录
2. 在插件管理菜单激活插件

## 更新日志

###  1.5

* 添加多文章类型支持

###  1.0

* The first released