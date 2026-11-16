<?php
/**
 * Template part for displaying a form if password is required for this post 
 *
 */
?>
<div class="text-center container">
    <form action="<?php echo esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ); ?>" class="post-password-form" method="post" autocomplete="off">
        <div class="post-password-form-text"><?php _e('这是一篇受密码保护的文章，您需要提供访问密码', 'argon');?></div>
        <div class="row">
            <div class="form-group col-lg-6 col-md-8 col-sm-10 col-xs-12 post-password-form-input">
                <div class="input-group input-group-alternative">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-key"></i></span>
                    </div>
                    <label class="sr-only" for="argon-post-password-<?php the_ID(); ?>"><?php esc_html_e( '密码', 'argon' ); ?></label>
                    <input id="argon-post-password-<?php the_ID(); ?>" name="post_password" class="form-control" placeholder="<?php esc_attr_e( '密码', 'argon' ); ?>" type="password" spellcheck="false" required value="<?php if (current_user_can('level_7')){global $post;if (isset($post -> post_password)){echo esc_attr($post->post_password);}} ?>">
                </div>
                <?php
                    $post_password_hint = get_post_meta(get_the_ID(), 'password_hint', true);
                    if (!empty($post_password_hint)){
                        echo '<div class="post-password-hint">' . $post_password_hint . '</div>';
                    }
                ?>
            </div>
        </div>
        <input class="btn btn-primary" type="submit" name="Submit" value="<?php _e('确认', 'argon');?>">
    </form>
</div>
