<?php
/**
 * 
 * 文章的正文部分（无密码时）
 * 替换正文排版的（如在正文部分加入 custom field 的），
 * 可以使用前后 hooks ，也可在新建同目录新建 no-passwd-{post-type}.php
 *
 */

echo argon_get_post_outdated_info();
do_action( 'argon_before_the_content' );
the_content();
do_action( 'argon_after_the_content' );
