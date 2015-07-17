<?php

add_action( 'admin_init', 'wizhi_multi_filter_admin_init' );
add_action('admin_menu', 'wizhi_multi_filter_menu');

function wizhi_multi_filter_menu() {
    $page_hook_suffix = add_options_page(
        '多条件筛选',
        '多条件筛选',
        'manage_options',
        'wizhi-multi-filter.php',
        'wizhi_multi_filter_management_page'
    );

    add_action('admin_print_scripts-' . $page_hook_suffix, 'wizhi_multi_filter_admin_scripts');
    add_action( 'admin_print_styles-' . $page_hook_suffix, 'wizhi_multi_filter_admin_styles' );
}

// 加载js和样式
function wizhi_multi_filter_admin_init() {
    wp_register_style( 'wizhi-multi-filter-style', plugins_url('assets/style.css', __FILE__) );
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

    /*** 存设置选项到数据库 ***/
    if (isset($_POST['save_filters'])) :

        // 获取选项数据
        $to_filter_type = isset( $_POST["to_filter_type"] ) ? $_POST["to_filter_type"] : '';
        $to_filter_tax = isset( $_POST["to_filter_tax"] ) ? $_POST["to_filter_tax"] : '';
        $hide_css = isset( $_POST["hide_css"] ) ? $_POST["hide_css"] : '';
        $hide_search = isset( $_POST["hide_search"] ) ? $_POST["hide_search"] : '';
        $wizhi_show_current = isset( $_POST["wizhi_show_current"] ) ? $_POST["wizhi_show_current"] : '';
        $wizhi_use_type_tax = isset( $_POST["wizhi_use_type_tax"] ) ? $_POST["wizhi_use_type_tax"] : '';
        $wizhi_type_name = isset( $_POST["wizhi_type_name"] ) ? $_POST["wizhi_type_name"] : '';
        $wizhi_type_label = isset( $_POST["wizhi_type_label"] ) ? $_POST["wizhi_type_label"] : '';
        $wizhi_tax = isset( $_POST["wizhi_tax"] ) ? $_POST["wizhi_tax"] : '';


        // 保存设置选项到数据库
        update_option('wizhi_type_name', $wizhi_type_name);
        update_option('wizhi_type_label', $wizhi_type_label);
        update_option('wizhi_tax', $wizhi_tax);
        update_option('wizhi_use_type_tax', $wizhi_use_type_tax);
        update_option('to_filter_type', $to_filter_type);
        update_option('to_filter_tax', $to_filter_tax);
        update_option('hide_css', $hide_css);
        update_option('hide_search', $hide_search);
        update_option('wizhi_show_current', $wizhi_show_current);

        echo '<div class="updated"><p>恭喜！保存成功。</p></div>';

    endif;

    ?>

    <?php
    // 获取所有文章类型
    $args_type = array(
        'public'   => true,
        '_builtin' => false
    );

    $post_types = get_post_types( $args_type, 'objects', 'and');

    // 根据已选中的文章类型获取分类法
    $select_type = get_option('to_filter_type');

    // 获取所有分类法
    $args_tax = array(
        'object_type' => array( $select_type ),
        'public'   => true,
        '_builtin' => false
    );
    $taxonomies = get_taxonomies( $args_tax, 'objects', 'and' );

    // 设置默认的分类方法
    $wizhi_default_tax = array(
        'name' => array('品牌', '产地'),
        'label' => array('brand', 'area'),
    );

    // 已选择的文章类型和分类法
    $selected_type = get_option('to_filter_type');
    $selected_tax = ( get_option('to_filter_tax') ) ? get_option('to_filter_tax') : array();
    $wizhi_saved_tax = ( get_option('wizhi_tax') ) ? get_option('wizhi_tax') : $wizhi_default_tax;

    ?>

    <form action="" method="post">

        <p>使用方法请参考：<a target="_blank" href="http://www.wpzhiku.com/wizhi-multi-filters/">插件文档</a></p>

        <table class="form-table">

            <tr>
                <th scope="row"><label>使用内置的文章类型和分类法</label></th>
                <td>
                    <label>
                        <input type="checkbox" name="wizhi_use_type_tax" value="1" <?php echo ( get_option('wizhi_use_type_tax') ) ? 'checked' : ''; ?>>
                        使用内置的文章类型和分类法
                    </label> <br/>
                </td>
            </tr>

            <tr>
                <th scope="row"><label>文章类型定义</label></th>
                <td>
                    <table class="form-table">
                        <tr>
                            <td>
                                <input type="text" name="wizhi_type_name" value="<?php echo ( get_option('wizhi_type_name') ) ? get_option('wizhi_type_name') : '产品'; ?>" />
                                <input type="text" name="wizhi_type_label" value="<?php echo ( get_option('wizhi_type_label') ) ? get_option('wizhi_type_label') : 'prod'; ?>" />
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

                            <?php $saved_tax = array_combine( $wizhi_saved_tax['label'], $wizhi_saved_tax['name'] ); ?>

                            <?php foreach ( $saved_tax as $label=>$name ) : ?>
                                <tr class="repeatable-fieldset">
                                    <td>
                                        <input type="text" name="wizhi_tax[name][]" value="<?php echo $name; ?>" />
                                        <input type="text" name="wizhi_tax[label][]" value="<?php echo $label; ?>" />
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
                <th scope="row"><label>选择需要筛选的文章类型</label></th>
                <td>
                    <select name="to_filter_type" id="wizhi-type">
                    <?php foreach ( $post_types as $post_type ) : ?>
                        <?php $is_selected = ( $post_type->name == $selected_type  ) ? "selected" : "" ; ?>
                        <option value="<?php echo $post_type->name; ?>" <?php echo $is_selected; ?>>
                            <?php echo $post_type->label; ?>
                        </option>
                        <br/>
                    <?php endforeach; ?>
                    </select>
                    <p class="description">选择需要筛选的文章类型，必须是有分类方法的文章类型才能实现筛选。</p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label>选择需要筛选的分类方法</label></th>
                <td id="wizhi-tax">
                    <?php foreach ( $taxonomies as $taxonomy ) : ?>
                        <?php $is_selected = ( in_array($taxonomy->name, $selected_tax) ) ? "checked" : "" ; ?>
                        <label>
                            <input type="checkbox" name="to_filter_tax[]" value="<?php echo $taxonomy->name; ?>" <?php echo $is_selected; ?>>
                            <?php echo $taxonomy->label; ?>
                        </label> <br/>
                    <?php endforeach; ?>
                </td>
            </tr>

            <tr>
                <th scope="row"><label>不显示CSS</label></th>
                <td>
                    <label>
                        <input type="checkbox" name="hide_css" value="1" <?php echo ( get_option('hide_css') ) ? 'checked' : ''; ?>>
                        不显示CSS
                    </label> <br/>
                    <p class="description">是否显示CSS，如果选择不显示，需要在主题中自定义CSS</p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label>不显示搜索功能</label></th>
                <td>
                    <label>
                        <input type="checkbox" name="hide_search" value="1" <?php echo ( get_option('hide_search') ) ? 'checked' : ''; ?>>
                        不显示搜索功能
                    </label> <br/>
                </td>
            </tr>

          <tr>
            <th scope="row"><label>不显示当前已选条件</label></th>
            <td>
              <label>
                <input type="checkbox" name="wizhi_show_current" value="1" <?php echo ( get_option('wizhi_show_current') ) ? 'checked' : ''; ?>>
                不显示当前已选条件
              </label> <br/>
              <p class="description">不显示当前已选条件</p>
            </td>
          </tr>

        </table>

        <p class="submit">
            <input type="submit" name="save_filters" value="保存" class="button-primary" />
        </p>

    </form>

    <?php

    echo '</div>';
}

?>
