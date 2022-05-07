<?php
/**
 * 展示捐赠模块
 * Template part for displaying donate part 
 *
 */
?>
<?php if (get_option("argon_donate_qrcode_url") != '') { ?>
    <div class="post-donate">
        <button class="btn donate-btn btn-danger"><?php _e('赞赏', 'argon');?></button>
        <div class="donate-qrcode card shadow-sm bg-white">
            <img src="<?php echo get_option("argon_donate_qrcode_url"); ?>">
        </div>
    </div>
<?php } ?>
