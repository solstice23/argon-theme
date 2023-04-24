<?php
/**
 * 展示文章的摘要，主要是文章列表里使用
 * Template part for displaying post excerpt
 *
 */
?>
<?php
    $trim_words_count = get_option('argon_trim_words_count', 175);
    if ($trim_words_count > 0): ?>
        <div class="post-content">
            <?php
                $content = get_post_meta( get_post()->ID, "argon_ai_summary", true );
                if ( get_option( 'argon_ai_post_summary', false ) == 'false' || get_post_meta( get_post()->ID, "argon_ai_post_summary", true ) == 'false' || get_option('argon_ai_show_post_summary_in_home', true) == 'false' || $content == '' ) {
                    $content = get_the_content( '...' );
                }
	            if ( get_option( "argon_hide_shortcode_in_preview" ) == 'true' ) {
		            $preview = wp_trim_words( do_shortcode( $content ), $trim_words_count );
	            } else {
		            $preview = wp_trim_words( $content, $trim_words_count );
	            }
                if (post_password_required()){
                    $preview = __("这篇文章受密码保护，输入密码才能阅读", 'argon');
                }
                if ($preview == ""){
                    $preview = __("这篇文章没有摘要", 'argon');
                }
                if ($post -> post_excerpt){
                    $preview = $post -> post_excerpt;
                }
                echo $preview;
            ?>
        </div>
    <?php endif ?>