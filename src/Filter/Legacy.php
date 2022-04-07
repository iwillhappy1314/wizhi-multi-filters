<?php

namespace Wizhi\Filter;

/**
 * Class Wizhi_Filter
 *
 * @deprecated
 */
class Legacy
{


    /*
     * 要显示的过滤条件的自定义分类法名称
     *
     * @var array
     */
    private $post_types;
    private $taxonomies = [];
    private $hide_search;

    function __construct($post_types, $taxonomies, $hide_search)
    {

        // 获取参数
        $this->post_types  = $post_types;
        $this->taxonomies  = $taxonomies;
        $this->hide_search = $hide_search;

        // 显示多条件过滤元素
        $this->wizhi_multi_filters();
        $this->wizhi_filter_search();
        $this->wizhi_current_filter();
    }

    /**
     * 获取分类法过滤链接
     */
    public function wizhi_multi_filters()
    {

        $taxonomies = $this->taxonomies;

        echo '<div class="wizhi-selects">';

        foreach ($taxonomies as $taxonomy) :

            $tax = get_taxonomy($taxonomy); ?>

            <div class="wizhi-select">

                <div class="wizhi-select__label">
                    <?php echo $tax->label; ?>
                </div>

                <div class="wizhi-select__items">
                    <?php
                    $query_var = $taxonomy;
                    $terms = get_terms($taxonomy);
                    $is_all = get_query_var($query_var) ? '' : 'selected';
                    $count  = count($terms);
                    
                    if ($count > 0) :

                        echo '<a class="' . $is_all . '" href="' . remove_query_arg($query_var) . '"><span>所有</span></a>';
                        foreach ($terms as $term) :
                            if (get_query_var($query_var) == $term->slug) {
                                echo '<a href="' . remove_query_arg($query_var) . '" class="selected"><span>' . $term->name . "</span></a>";
                            } else {
                                echo '<a href="' . add_query_arg([$query_var => $term->slug, 'paged' => false]) . '"><span>' . $term->name . "</span></a>";
                            }
                        endforeach;

                    endif;
                    ?>
                </div>
            </div>

        <?php

        endforeach;

        echo '</div>';

    }


    /**
     * 显示当前选择的过滤项目
     *
     */
    public function wizhi_current_filter()
    {

        $taxonomies             = $this->taxonomies;

        echo '<div class="wizhi-buttons">';

        foreach ($taxonomies as $taxonomy) :

            // 获取查询变量值
            $query_var = $taxonomy;
            $query_value        = get_query_var($query_var);
            $wizhi_show_current = get_option('wizhi_show_current');

            // 如果获取的查询变量值非空
            if ( ! empty($query_value) && ! $wizhi_show_current) :

                $term = get_term_by('slug', $query_value, $taxonomy); ?>

                <div class="wizhi-btn-group">
                    <a href="#" class="wizhi-btn wizhi-btn-default"><?php echo $term->name; ?></a>
                    <a href="<?php echo remove_query_arg($query_var); ?>" class="wizhi-btn wizhi-btn-close">X</a>
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
    public function wizhi_filter_search()
    {
        if ( ! $this->hide_search) {
            ?>

            <form class="wizhi-form" role="search" method="get" id="searchform" action="">
                <input type="text" name="q" class="wizhi-search" placeholder="" value="<?php echo get_query_var('q', ''); ?>">
                <input type="hidden" name="paged" value="1">
                <button type="submit" class="wizhi-btn wizhi-btn-primary">搜索</button>
            </form>

            <?php

        }

    }


    /**
     * 输入获取到的文章循环对象
     *
     * @return \WP_Query
     */
    public function wizhi_get_filter_object()
    {

        $post_types = $this->post_types;
        $taxonomies = $this->taxonomies;

        // 初始化分类法查询数组
        $tax_query_array = ['relation' => 'AND'];

        foreach ($taxonomies as $taxonomy) :

            // 获取查询变量值
            $query_var   = $taxonomy;
            $query_value = get_query_var($query_var);

            // 如果获取的查询变量值非空
            if ( ! empty($query_value)) :

                $tax_query = [
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => $query_value,
                ];

                // 添加新的分类法查询到查询数组
                $tax_query_array[] = $tax_query;

            endif;

        endforeach;

        // 添加搜索变量
        $q = get_query_var('q', '');

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args  = [
            'post_type'      => $post_types,
            'posts_per_page' => get_option('posts_per_page'),
            'paged'          => $paged,
            's'              => $q,
            'tax_query'      => $tax_query_array,
        ];

        return new \WP_Query($args);
    }


}