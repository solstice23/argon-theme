<?php
/**
 * 展示说说的摘要
 *
 */
?>
<div class="shuoshuo-content">
    <?php
        // if (get_option("argon_hide_shortcode_in_preview") == 'true'){
        //     $preview = do_shortcode(get_the_content('...'));
        // }else{
        //     $preview = get_the_content('...');
        // }
        if (post_password_required()){
            echo __("这条说说受密码保护，输入密码才能阅读", 'argon');
        }elseif( empty( get_the_content() ) ){
            echo __("这条说说是空白的", 'argon');
        }else{
            the_content();
        }
        // if ($post -> post_excerpt){
        //     $preview = $post -> post_excerpt;
        // }
        // echo $preview;
    ?>
</div>
