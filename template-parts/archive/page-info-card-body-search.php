<h3 class="text-black mr-2 d-inline-block">	<?php echo get_search_query();?> </h3>
<p class="lead text-black mt-0 d-inline-block">
    <?php _e('的搜索结果', 'argon');?>
</p>
<?php 
    if (get_option('argon_enable_search_filters', 'true') == 'true'){ ?>
        <div class="search-filters">
            <?php
                $all_post_types= get_post_types(array(
                    'public'   => true,
                ), 'objects');
                $search_filters_type = explode(',', get_option('argon_search_filters_type', '*post,*page,shuoshuo'));
                $current_filters_type = argon_get_search_post_type_array();
                foreach ($search_filters_type as $filter_type) {
                    if ($filter_type[0] == '*'){ $filter_type = substr($filter_type, 1); }
                    $checked = in_array($filter_type, $current_filters_type);
                    if (isset($all_post_types[$filter_type])){
                        $filter_name = $all_post_types[$filter_type] -> labels -> name;
                    ?>
                        <div class="custom-control custom-checkbox search-filter-wrapper">
                            <input class="custom-control-input search-filter" name="<?php echo $filter_type; ?>" id="search_filter_<?php echo $filter_type; ?>" type="checkbox" <?php echo $checked ? 'checked="true"' : ''; ?>>
                            <label class="custom-control-label" for="search_filter_<?php echo $filter_type; ?>"><?php echo $filter_name; ?></label>
                        </div>
                    <?php
                    }
                }
            ?>
        </div>
        <script>
            $(".search-filter").prop("checked", false);
            $("<?php echo "#search_filter_" . implode(",#search_filter_", $current_filters_type); ?>").prop("checked", true);
        </script>
<?php } ?>
<p class="text-black mt-3 mb-0 opacity-8">
    <i class="fa fa-file mr-1"></i>
    <?php global $wp_query; echo $wp_query -> found_posts; ?> <?php _e('个结果', 'argon');?>
</p>
